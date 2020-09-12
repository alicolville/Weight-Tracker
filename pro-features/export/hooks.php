<?php

defined('ABSPATH') or die('Jog on.');

/**
 * Admin Menu
 */
function ws_ls_export_admin_menu() {

	add_submenu_page( 'ws-ls-data-home', __( 'Export Data', WE_LS_SLUG ),  __( 'Export Data', WE_LS_SLUG ), 'manage_options', 'ws-ls-export-data', 'ws_ls_export_admin_page', 6 );
}
add_action( 'admin_menu', 'ws_ls_export_admin_menu' );

/**
 * Process Report
 */
function ws_ls_export_ajax_process() {

	if ( false === WS_LS_IS_PRO ) {
		return;
	}

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$return = [ 'continue' => true, 'error' => false, 'message' => '', 'percentage' => 0, 'url' => '' ];
	$id     = ws_ls_post_value( 'id' );

	if ( true === empty( $id ) ) {
		ws_ls_export_ajax_error( $return, __( 'Export ID could not be determined.' , WE_LS_SLUG ) );
	}

	$export = ws_ls_db_export_criteria_get( $id );

	if ( true === empty( $export ) ) {
		ws_ls_export_ajax_error( $return, __( 'Export criteria could not be loaded.' , WE_LS_SLUG ) );
	}

	$current_step = (int) $export[ 'step' ];

	// ------------------------------------------------------------------------------------------------------
	// Identify based on criteria
	// ------------------------------------------------------------------------------------------------------
	if ( 0 === $current_step ) {

		ws_ls_db_export_identify_weight_entries( $id );
		$return[ 'message' ]    = __( 'Initialising: Rows have been identified for the export.', WE_LS_SLUG );
		$return[ 'percentage' ] = 40;
		ws_ls_db_export_criteria_step( $id, 1 );

	// ------------------------------------------------------------------------------------------------------
	// Ensure we can write to uploads folder by creating the file we need
	// ------------------------------------------------------------------------------------------------------
	} else if ( 1 === $current_step ) {

		$physical_path = ws_ls_export_file_physical_folder( $id );

		if ( false === wp_mkdir_p( $physical_path ) ) {
			ws_ls_export_ajax_error( $return, __( 'There was an issue creating the export folder: ' , WE_LS_SLUG ) . $physical_path );
		}

		$physical_path_to_file = ws_ls_export_file_physical_path( $id );

		if ( false === touch( $physical_path_to_file ) ) {
			ws_ls_export_ajax_error( $return, __( 'There was an issue creating the export file: ' , WE_LS_SLUG ) . $physical_path_to_file );
		}

		$return[ 'message' ]    = __( 'Initialising: created empty file on disk.', WE_LS_SLUG );
		$return[ 'percentage' ] = 70;

		ws_ls_db_export_criteria_step( $id, 2 );

	// ------------------------------------------------------------------------------------------------------
	// Count records going into the report
	// ------------------------------------------------------------------------------------------------------
	} else if ( 2 === $current_step ) {

		$number_of_records = ws_ls_db_export_report_count( $id );

		ws_ls_db_export_criteria_count( $id, $number_of_records );

		$return['message']    = sprintf( 'Initialising: %d %s', $number_of_records, __( 'records have been identified for this report.', WE_LS_SLUG ) );
		$return['percentage'] = 100;

		ws_ls_db_export_criteria_step( $id, 20 );

	// ------------------------------------------------------------------------------------------------------
	// Prepare rows. Take a set of rows and pre-process for the report
	// ------------------------------------------------------------------------------------------------------
	} else if ( 20 === $current_step ) {

		// Fetch some entries to process
		$rows_to_process = ws_ls_db_export_report_incomplete_rows( $id );

		// There are no more rows to process
		if ( true === empty( $rows_to_process ) ) {

			$return['message']    = __( 'Preparing data: Complete.', WE_LS_SLUG );
			$return['percentage'] = 100;

			ws_ls_db_export_criteria_step( $id, 40 );

		} else {

			foreach ( $rows_to_process as $row ) {

				if ( false === ws_ls_export_update_export_row( $export, $row ) ) {
					ws_ls_export_ajax_error( $return, __( 'There was an error processing weight entry', WE_LS_SLUG ) . ': ' . $row['entry_id'] );
				}
			}

			$return['total']      = ws_ls_db_export_report_count( $id );
			$return['remaining']  = ws_ls_db_export_report_to_be_processed_count( $id );
			$return['processed']  = $return['total'] - $return['remaining'];
			$percentage           = ( $return['processed'] / $return['total'] ) * 100.0;
			$return['percentage'] = (int) $percentage;

			$return['message'] = sprintf( 'Preparing data: %d of %d entries', $return['processed'], $return['total'] );
		}

		// ------------------------------------------------------------------------------------------------------
		// Write column headers to file
		// ------------------------------------------------------------------------------------------------------
	} else if ( 40 === $current_step ) {

		$column_names = ws_ls_export_column_names( $export );

		// CSV?
		$is_csv = ( false === empty( $export[ 'options' ][ 'format'] ) &&
		               'csv' === $export[ 'options' ][ 'format'] );

		if ( true === $is_csv ) {

			$column_headers = ws_ls_export_csv_row_header( $column_names );
			ws_ls_export_file_write( $id, $column_headers );

		} else {

			ws_ls_export_file_write( $id, '{"columns":' . json_encode( $column_names ) . ',"rows":[' );

		}

		ws_ls_db_export_criteria_step( $id, 42 );
		$return['message']    = __( 'Saving to disk: Column headers', WE_LS_SLUG );
		$return['percentage'] = 5;

		// ------------------------------------------------------------------------------------------------------
		// Write rows to file
		// ------------------------------------------------------------------------------------------------------
	} else if ( 42 === $current_step ) {

		$rows_to_write = ws_ls_db_export_report_unsaved_rows( $id );

		if ( true === empty( $rows_to_write ) ) {

			$return['message']    = __( 'Saving to disk: Complete.', WE_LS_SLUG );
			$return['percentage'] = 100;

			ws_ls_db_export_criteria_step( $id, 47 );

		} else {

			$column_names = ws_ls_export_column_names( $export );

			// CSV?
			$is_csv = ( false === empty( $export[ 'options' ][ 'format'] ) &&
				'csv' === $export[ 'options' ][ 'format'] );

			if ( true === $is_csv ) {

				foreach ( $rows_to_write as $row ) {

					$row[ 'data' ] = json_decode( $row[ 'data' ], true );

					$row = ws_ls_export_csv_row_write( $column_names, $row[ 'data' ] );

					ws_ls_export_file_write( $id, $row );
				}
			} else {

				foreach ( $rows_to_write as $row ) {

					$data			= [];
					$row[ 'data' ] 	= json_decode( $row[ 'data' ], ARRAY_A );

					foreach ( $column_names as $key => $value ) {
						$data[ $key ] = ( true === isset( $row[ 'data' ][ $key ] ) ) ? $row[ 'data' ][ $key ] : '';
					}

					ws_ls_export_file_write( $id, json_encode( $data ) . ',');

				}
			}

			$ids_processed = wp_list_pluck( $rows_to_write, 'id' );

			$return['total']      = ws_ls_db_export_report_count( $id );
			$return['remaining']  = ws_ls_db_export_report_to_be_processed_count( $id, true );
			$return['processed']  = $return['total'] - $return['remaining'];
			$percentage           = ( $return['processed'] / $return['total'] ) * 100.0;
			$return['percentage'] = (int) $percentage;

			$return['message'] = sprintf( 'Saving to disk: %d of %d entries', $return['processed'], $return['total'] );

			ws_ls_db_export_report_saved_rows_mark( $id, $ids_processed );
		}

	} else if ( 47 === $current_step ) {

		// JSON?
		if ( ( false === empty( $export[ 'options' ][ 'format'] ) &&
				'json' === $export[ 'options' ][ 'format'] ) ) {
			ws_ls_export_file_write( $id, ']}' );
		}

		ws_ls_db_export_criteria_step( $id, 99 );

	} else if ( 99 === $current_step ) {

		ws_ls_db_export_criteria_step( $id, 100 );

		$return[ 'message' ]        = sprintf( '<a href="%s">%s</a>', ws_ls_export_file_url( $id ), __( 'Download', WE_LS_SLUG ) );
		$return[ 'percentage' ]     = 100;
		$return[ 'continue' ]       = false;
	}

	wp_send_json( $return );
}
add_action( 'wp_ajax_process_export', 'ws_ls_export_ajax_process' );

/**
 * Send error message back
 * @param $obj
 * @param string $message
 */
function ws_ls_export_ajax_error( $obj, $message = '' ) {

	$obj[ 'error' ]      = true;
	$obj[ 'message' ]    = $message;

	wp_send_json( $obj );
}

