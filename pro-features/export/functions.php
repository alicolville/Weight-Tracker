<?php

defined('ABSPATH') or die('Jog on.');

/**
 * Link to export page
 *
 * @param string $mode
 *
 * @param array $querystring_values
 * @return string|void
 */
function ws_ls_export_link( $mode = 'view', $querystring_values = [] ) {

	$url = sprintf( 'admin.php?page=ws-ls-export-data&mode=%1$s', $mode );

	$url = admin_url( $url );

	if ( false === empty( $querystring_values ) ) {
		$url = add_query_arg( $querystring_values, $url );
	}

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

	$cache_key = sprintf( 'filepath-%d', $id );

	if ( $cache = ws_ls_cache_user_get(  'exports', $cache_key ) ) {
		return $cache;
	}

	$export = ws_ls_db_export_criteria_get( $id );

	if( true === empty( $export[ 'file' ] ) ) {
		return NULL;
	}

	$path = sprintf( '%s/%s', ws_ls_export_file_physical_folder( $id ), $export[ 'file' ] );

	ws_ls_cache_user_set( 'exports', $cache_key, $path );

	return $path;
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
 * Fetch Download URL
 * @param $id
 * @return string|null
 */
function ws_ls_export_file_url( $id ) {

	$export = ws_ls_db_export_criteria_get( $id );

	if( true === empty( $export[ 'folder' ] ) ) {
		return NULL;
	}

	if( true === empty( $export[ 'file' ] ) ) {
		return NULL;
	}

	$upload_dir = wp_upload_dir();

	return sprintf( '%s/%s%s', $upload_dir[ 'baseurl' ], $export[ 'folder' ], $export[ 'file' ] );
}

/**
 * Delete a report from disk and clear up database
 * @param $id
 */
function ws_ls_export_delete( $id ) {

	$physical_path = ws_ls_export_file_physical_path( $id );

	// Delete file
	if ( true === file_exists( $physical_path ) ) {
		wp_delete_file( $physical_path );
	}

	// Delete folder
	$physical_folder = ws_ls_export_file_physical_folder( $id );

	if ( true === is_dir( $physical_folder ) ) {
		rmdir( $physical_folder );
	}

	ws_ls_db_export_report_delete( $id );
}

/**
 * Format ISO date for export
 * @param $iso_date
 * @return false|string
 */
function ws_ls_export_render_date( $iso_date ) {

	if ( true === empty( $iso_date ) ) {
		return '';
	}

	$time 		= strtotime( $iso_date );
	$format  	= ( true === ws_ls_setting('use-us-dates', get_current_user_id(), true ) ) ? 'm/d/Y' : 'd/m/Y';

	$format .= ' H:i';

	return date( $format, $time );
}

/**
 * Helper function to replace column names with something more readable. For example take MySQL column names and make them easier to read
 *
 * @param $export_criteria
 *
 * @return array Prettified fields names
 */
function ws_ls_export_column_names( $export_criteria ) {

	$cache_key = sprintf( 'columns-%d', $export_criteria[ 'id' ] );

	if ( $cache = ws_ls_cache_user_get( 'export', $cache_key ) ) {
		return $cache;
	}

	$names = [
		'user_id'                       => 'User ID',
		'user_nicename'                 => 'Nicename',
		'date-display'                  => 'Date',
		'weight'                        => ws_ls_settings_weight_unit_readable(),
		'difference_from_start_display' => __( 'Difference from start', WE_LS_SLUG ),
		'bmi'                           => __( 'BMI', WE_LS_SLUG ),
		'bmi-readable'                  => __( 'BMI Label', WE_LS_SLUG ),
		'weight_notes'                  => __( 'Notes', WE_LS_SLUG )
	];

	$options = ( false === empty( $export_criteria[ 'options' ] ) ) ? $export_criteria[ 'options' ] : NULL;

	// Add meta fields
	if ( ws_ls_meta_fields_number_of_enabled() > 0 &&
	     false === empty( $options[ 'fields-meta' ] ) ) {
		foreach ( ws_ls_meta_fields_enabled() as $meta_field ) {

			if ( true === in_array( $meta_field[ 'id' ], $options[ 'fields-meta' ] ) ) {
				$names[ 'meta-' . $meta_field[ 'id' ] ] = $meta_field['field_name'];
			}
		}
	}

	$cache = apply_filters( 'wlt-export-columns', $names );

	ws_ls_cache_user_set( 'export', $cache_key, $cache );

	return $names;
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
	$data[ 'date-display' ]                  = ( '0000-00-00 00:00:00' !== $data[ 'weight_date' ] ) ? ws_ls_convert_ISO_date_into_locale( $data[ 'weight_date' ], 'display-date' ) : '00/00/0000';
	$data[ 'weight' ]                        = ws_ls_weight_display( $data['kg'], $data[ 'user_id' ], 'display', true );

	$options = ( false === empty( $export_criteria[ 'options' ] ) ) ? $export_criteria[ 'options' ] : NULL;

	if ( false === empty( $options[ 'fields' ] ) ) {

		// Difference from start weights
		if ( true === in_array( 'weight-diff-start', $options[ 'fields' ] ) ) {
			$data[ 'difference_from_start_display' ] = ws_ls_weight_difference_from_start( $data[ 'user_id' ], $data[ 'kg' ] );
			$data[ 'difference_from_start_display' ] = ws_ls_weight_display( $data[ 'difference_from_start_display' ],  NULL, 'display', true, true );
		}

		if ( true === in_array( 'bmi-value', $options[ 'fields' ] ) ||
				 ( true === in_array( 'bmi-label', $options[ 'fields' ] ) ) ) {

			$data[ 'height' ]       = ws_ls_user_preferences_get( 'height', $data[ 'user_id' ] );
			$data[ 'bmi' ]          = ws_ls_calculate_bmi( $data[ 'height' ], $data[ 'kg' ] ) ;
			$data[ 'bmi-readable' ] = ws_ls_calculate_bmi_label( $data[ 'bmi' ] );
		}
	}

	// Enabled meta fields?
	if ( ws_ls_meta_fields_number_of_enabled() > 0 &&
	        false === empty( $options[ 'fields-meta' ] ) ) {

		$meta_data = ws_ls_meta( $data[ 'entry_id' ] );

		// Pluck to meta_id => value
		if ( false === empty( $meta_data ) ) {
			$meta_data = wp_list_pluck( $meta_data, 'value', 'meta_field_id' );

			foreach ( $meta_data as $key => $field ) {

				if ( true === in_array( $key, $options[ 'fields-meta' ] ) ) {
					$data[ 'meta-' . $key ] = ws_ls_fields_display_field_value( $field, $key, true );
				}
			}
		}
	}

	$data = apply_filters( 'wlt-export-row', $data );

	return ws_ls_db_export_rows_update( $export_criteria, $data );
}

/**
 * Produce a CSV header row
 * @param  array  $columns          Columns we want from the row
 * @return string CSV row
 */
function ws_ls_export_csv_row_header($columns) {

	if ( true === empty( $columns ) ) {
		return '';
	}

	$columns = array_values( $columns );

	// Escape cell contents and encapsulate in double quotes
	$columns = array_map( 'ws_ls_export_csv_cell_escape', $columns );

	// Implode and build Row
	return implode( ',', $columns ) . PHP_EOL;
}

/**
 * Produce a CSV row
 * @param  array  $columns          Columns we want from the row
 * @param  array  $row              Contains the raw data for a given row.
 * @param  string $delimiter        Delimiter between CSV columns
 * @param  string $end_of_line_char End of line character
 * @return string CSV row
 */
function ws_ls_export_csv_row_write( $columns, $row, $delimiter = ',', $end_of_line_char = PHP_EOL) {

	$data = [];

	foreach ( $columns as $key => $value ) {
		$data[ $key ] = ( true === isset( $row[ $key ] ) ) ? $row[ $key ] : '';
	}

	// Escape cell contents and encapsulate in double quotes
	$data = array_map('ws_ls_export_csv_cell_escape', $data );

	// Implode and build Row
	return implode( $delimiter, $data ) . $end_of_line_char;
}

/**
 * Write to export file on disk
 * @param $export_id
 * @param $text
 *
 * @return false|int
 */
function ws_ls_export_file_write( $export_id, $text ) {

	$physical_path = ws_ls_export_file_physical_path( $export_id );

	return file_put_contents( $physical_path, $text, FILE_APPEND );
}

/**
 * Escape the contents of a CSV cell to ensure the cell remains intact e.g. escape quotes and encase field in quotes.
 * @param  string $data CSV cell content
 * @return string modified CSV cell content
 */
function ws_ls_export_csv_cell_escape($data) {

	// Escape double quotes within data
	$data = str_replace( '"', '""', $data );

	// UTF encode (recommended by CSV lint when testing CSV output)
	$data = utf8_encode( $data );

	// Enclose fields within quotes.
	return sprintf( '"%s"', $data) ;
}
