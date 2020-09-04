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

	if ( false === WE_LS_IS_PRO ) {
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

	if ( 0 === $current_step ) {

		ws_ls_db_export_identify_weight_entries( $id );
		$return[ 'message' ]    = __( 'Rows have been identified for the export.', WE_LS_SLUG );
		$return[ 'percentage' ] = 50;
		ws_ls_db_export_criteria_step( $id, 1 );

	} else if ( 1 === $current_step ) {

		$number_of_records = ws_ls_db_export_report_count( $id );

		ws_ls_db_export_criteria_count( $id, $number_of_records );

		$return['message']    = sprintf( '%d %s', $number_of_records, __( 'records have been identified for this report.', WE_LS_SLUG ) );
		$return['percentage'] = 100;

		ws_ls_db_export_criteria_step( $id, 2 );

	} else if ( 2 === $current_step ) {

		// Fetch some entries to process
		$rows = ws_ls_db_export_report_incomplete_rows( $id );

		if ( true === empty( $rows ) ) {

			$return[ 'message' ]        = __( 'All record data has now been processed.', WE_LS_SLUG );
			$return[ 'percentage' ]     = 100;

			ws_ls_db_export_criteria_step( $id, 3 );

		} else {

			//TODO: Actually update row data here.

			$processed_ids              = wp_list_pluck( $rows, 'id' );
			$return[ 'total' ]          = ws_ls_db_export_report_count( $id );
			$return[ 'remaining' ]      = ws_ls_db_export_report_to_be_processed_count( $id );
			$return[ 'processed' ]      = $return[ 'total' ] - $return[ 'remaining' ];
			$percentage                 = ( $return[ 'processed' ] / $return[ 'total' ] ) * 100.0;
			$return[ 'percentage' ]     = (int) $percentage;

			$return[ 'message' ]        = sprintf( 'Prepared %d of %d weight entries for report', $return[ 'processed' ], $return[ 'total' ] );

			ws_ls_db_export_report_complete_rows_mark( $id, $processed_ids );

		}

	} else if ( 3 === $current_step ) {


		ws_ls_db_export_criteria_step( $id, 100 );
		$return[ 'message' ]        = __( 'Done!', WE_LS_SLUG );
		$return[ 'percentage' ]     = 100;
		$return[ 'continue' ] = false;
		$return[ 'step' ] = 100;
		//ws_ls_db_export_report_complete_rows_mark( 1, [3,4,5]);
		//die;

	}



	//$return[ 'continue' ] = false;

	wp_send_json( $return );
}
add_action( 'wp_ajax_process_export', 'ws_ls_export_ajax_process' );

/**
 * Send error message back
 * @param $obj
 * @param string $message
 */
function ws_ls_export_ajax_error( $obj, $message = '' ) {

	$return[ 'error' ]      = true;
	$return[ 'message' ]    = $message;

	wp_send_json( $return );
}

