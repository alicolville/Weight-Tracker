<?php

defined('ABSPATH') or die('Jog on.');

/**
 * Link to export page
 *
 * @param string $mode
 *
 * @return string|void
 */
function ws_ls_export_link( $mode = 'view' ) {

	$url = sprintf( 'admin.php?page=ws-ls-export-data&mode=%1$s', $mode );

	$url = admin_url( $url );

	return $url;
}

/**
 * Export date range options
 * @return array
 */
function ws_ls_export_date_ranges() {

	return [
				'' 			=> '',
				'today' 	=> __( 'Today', WE_LS_SLUG ),
				'last-7' 	=> __( 'Last 7 Days', WE_LS_SLUG ),
				'last-31' 	=> __( 'Last 31 Days', WE_LS_SLUG ),
				'custom'	=> __( 'Custom date range', WE_LS_SLUG )
	];
}

/**
 * Insert new criteria for an export
 * @param $options
 * @return bool
 */
function ws_ls_export_insert( $options ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	return  ws_ls_db_export_insert( $options,
									ws_ls_export_file_generate_folder_name( $options ),
									ws_ls_export_file_generate_file_name( $options )
	);
}

/**
 * Generate a file for export
 * @param $options
 * @return string
 */
function ws_ls_export_file_generate_file_name( $options ) {

	$file_name = ( false === empty( $options[ 'title'] ) ) ? sanitize_title( $options[ 'title'] ) :  mt_rand();

	return sprintf( '%s.%s', $file_name, $options[ 'format' ] );
}

/**
 * Generate folder name
 * @param $options
 * @return string
 */
function ws_ls_export_file_generate_folder_name( $options ) {

	return sprintf( 'weight-tracker/%s/', mt_rand() );
}

/**
 * Fetch physical path for export files
 * @param $id
 * @return string|null
 */
function ws_ls_export_file_physical_path( $id ) {

	$export = ws_ls_db_export_criteria_get( $id );

	if( true === empty( $export[ 'file' ] ) ) {
		return NULL;
	}

	return sprintf( '%s/%s', ws_ls_export_file_physical_folder( $id ), $export[ 'file' ] );
}

/**
 * Fetch physical path for export folder
 * @param $id
 * @return string|null
 */
function ws_ls_export_file_physical_folder( $id ) {

	$export = ws_ls_db_export_criteria_get( $id );

	if( true === empty( $export[ 'folder' ] ) ) {
		return NULL;
	}

	$upload_dir = wp_upload_dir();

	return sprintf( '%s/%s', $upload_dir[ 'basedir' ], $export[ 'folder' ] );
}

/**
 * Update a Weight Entry row with the required report data
 *
 * @param $export_criteria
 * @param $data
 *
 * @return bool
 */
function ws_ls_export_update_export_row( $export_criteria, $data ) {
	
	$data[ 'user_nicename' ]                 = ws_ls_user_display_name( $data[ 'user_id' ] );
	$data[ 'date-display' ]                  = ws_ls_convert_ISO_date_into_locale( $data[ 'weight_date' ], 'display-date' );
	$data[ 'weight' ]                        = ws_ls_weight_display( $data['kg'], $data[ 'user_id' ], 'display', true );

	$options = ( false === empty( $export_criteria[ 'options' ] ) ) ? $export_criteria[ 'options' ] : NULL;

	if ( false === empty( $options[ 'fields' ] ) ) {

		// Difference from start weights
		if ( true === in_array( 'weight-diff-start', $options[ 'fields' ] ) ) {
			$data[ 'difference_from_start_display' ] = ws_ls_weight_difference_from_start( $data[ 'user_id' ], $data['kg'] );
			$data[ 'difference_from_start_display' ] = ws_ls_weight_display( $data[ 'difference_from_start_display' ],  NULL, 'display', true, true );
		}

		if ( true === in_array( 'bmi-value', $options[ 'fields' ] ) ||
				 ( true === in_array( 'bmi-label', $options[ 'fields' ] ) ) ) {

			$height                 = ws_ls_user_preferences_get( 'height', $data[ 'user_id' ] );
			$data[ 'bmi' ]          = ws_ls_calculate_bmi( $height, $data[ 'kg' ] ) ;
			$data[ 'bmi-readable' ] = ws_ls_calculate_bmi_label( $data[ 'bmi' ] );
		}
	}

	// Enabled meta fields?
	if ( false === empty( $options[ 'fields-meta' ] ) ) {

		$meta_data = ws_ls_meta( $data[ 'id' ] );

		// Pluck to meta_id => value
		if ( false === empty( $meta_data ) ) {
			$meta_data = wp_list_pluck( $meta_data, 'value', 'meta_field_id' );

			foreach ( $meta_data as $key => $field ) {

				if ( true === in_array( $key, $options[ 'fields-meta' ] ) ) {
					$data[ 'meta' ][ $key ] = ws_ls_fields_display_field_value( $field, $key, true );
				}
			}
		}
	}

	return ws_ls_db_export_rows_update( $export_criteria, $data );
}
