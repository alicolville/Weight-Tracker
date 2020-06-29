<?php

defined('ABSPATH') or die('Jog on.');

$ws_ls_export_config = [ 'meta' => false ];

/**
 *	Script for determing export type and loading data
 *
 * 	This function is hooked on to admin-post.php (https://codex.wordpress.org/Plugin_API/Action_Reference/admin_post_(action)).
 *
 * If the following URL is found then fetch the report data from DB and send it to browser as CSV:
 *
 * wp-admin/admin-post.php?action=export_data&user-id=12
**/
function ws_ls_export_data() {

	// Ensure the user has relevant permissions
	ws_ls_permission_check_message();

	// Only render body of report if Pro!
	if ( true !== WS_LS_IS_PRO ) {
		wp_die( __( 'You must have a Pro License to export user data.', WE_LS_SLUG ) );
	}

	if( false === WE_LS_CACHE_ENABLED ) {
		wp_die( __( 'You must have "Caching" enabled in Weight Tracker Settings to perform exports.' , WE_LS_SLUG ) );
	}

	global $ws_ls_export_config;

	$export_data = ws_ls_db_entries_get( [ 'user-id' => ws_ls_querystring_value( 'user-id', true, 0 ), 'limit' => NULL ] );

	$ws_ls_export_config[ 'meta' ] = ws_ls_meta_fields_is_enabled();

	// Fetch whether CSV or JSON
	$file_type = ( false === empty( $_GET[ 'file-type' ] ) ) ?
					ws_ls_export_verify_type( $_GET[ 'file-type' ] ) :
						'text/csv';

	$output = '';

	if( false === empty( $export_data ) ) {

		$export_data = array_map( 'ws_ls_export_row_prep', $export_data );

		switch ( $file_type ) {
			case 'text/csv':
				$output = ws_ls_csv_from_array( $export_data );
				break;
			default:
				$output = ws_ls_export_to_json( $export_data );
				break;
		}

	} else {
		$output .= __( 'No data was found for the given criteria.', WE_LS_SLUG );
	}

	// Output file to CSV
	ws_ls_export_to_browser( $output, ( 'text/csv' == $file_type ) ? 'weight-loss-tracker.csv' : 'weight-loss-tracker.json' );

	die();
}
add_action( 'admin_post_export_data', 'ws_ls_export_data' );

/**
 * Take a database row and expand with useful information
 * @param $row
 *
 * @return mixed
 */
function ws_ls_export_row_prep( $row ) {

	global $ws_ls_export_config;

	$row[ 'user_nicename' ]                 = ws_ls_user_display_name( $row[ 'user_id' ] );
	$row[ 'date-display' ]                  = ws_ls_convert_ISO_date_into_locale( $row[ 'weight_date' ], 'display-date' );
	$row[ 'weight' ]                        = ws_ls_weight_display( $row['kg'], $row[ 'user_id' ], 'display', true );
	$row[ 'difference_from_start_display' ] = ws_ls_weight_difference_from_start( $row[ 'user_id' ], $row['kg'] );
	$row[ 'difference_from_start_display' ] = ws_ls_weight_display( $row[ 'difference_from_start_display' ],  NULL, 'display', true, true );
	$height                                 = ws_ls_user_preferences_get( 'height', $row[ 'user_id' ] );

	if ( false === empty( $height ) ) {
		$row['bmi']                         = ws_ls_calculate_bmi( $height, $row['kg'] ) ;
		$row['bmi-readable']                = ws_ls_calculate_bmi_label( $row['bmi'] );
	}

	if ( true === $ws_ls_export_config[ 'meta' ] ) {

		$meta_data = ws_ls_meta( $row[ 'id' ] );

		// Pluck to meta_id => value
		if ( false === empty( $meta_data ) ) {
			$meta_data = wp_list_pluck( $meta_data, 'value', 'meta_field_id' );

			foreach ( $meta_data as $key => $field ) {
				$row[ 'meta-' . $key ] = ws_ls_fields_display_field_value( $field, $key, true );
			}
		}
	}

	$row = apply_filters( 'wlt-export-row', $row );

	return $row;
}

/**
 * Export to JSON
 *
 * @param $rows
 * @return string Contents of JSON file
 */
function ws_ls_export_to_json( $rows ) {

	// Ensure we have some data!
	if( is_array($rows) && count($rows) > 0) {

		$output = ['columns' => ws_ls_column_names(), 'rows' => []];

		$data = [];

        // Only render body of report if Pro!
        if ( true !== WS_LS_IS_PRO ) {

            $output['rows'] =  __( 'You must have a Pro License to export user data into JSON format.', WE_LS_SLUG );

        } else {

            foreach ( $rows as $row ) {
                foreach ( $output['columns'] as $key => $value ) {
                	$data[ $key ] = ( true === isset( $row[ $key ] ) ) ? $row[ $key ] : '';
                }

                $data = apply_filters( 'wlt-export-row', $data );

                $output['rows'][] = $data;
            }

        }

		return json_encode($output);
	}

	return '';
}

/**
 * Export into CSV
 *
 * @param  array  $data                 Data
 * @param  boolean $show_column_headers Whether or not to include column row
 * @param  string $delimiter        	Delimiter between CSV columns
 * @param  string $end_of_line_char 	End of line character
 * @return string						Contents of CSV file
 */
function ws_ls_csv_from_array($data, $show_column_headers = true, $delimiter = ',', $end_of_line_char = PHP_EOL ) {

	// Ensure we have some data!
	if(is_array($data) && count($data) > 0) {

		$csv_output = '';

		$columns = ws_ls_column_names();

        // Include header row with column names?
        if( $show_column_headers ) {
            $csv_output .= ws_ls_csv_row_header( $columns );
        }

        // Only render body of report if Pro!
        if ( true !== WS_LS_IS_PRO ) {

            $csv_output .=  __( 'You must have a Pro License to export user data into CSV format.', WE_LS_SLUG );

        } else {

           // Build body of CSV
            foreach ( $data as $row ) {

                $csv_output .= ws_ls_csv_row_write( $columns, $row, $delimiter, $end_of_line_char );
            }

        }

		return $csv_output;
	}

	return '';
}

/**
 * Produce a CSV header row
 * @param  array  $columns          Columns we want from the row
 * @return string CSV row
 */
function ws_ls_csv_row_header($columns) {

	if ( true === empty( $columns ) ) {
		return '';
	}

	$columns = array_values( $columns );

	// Escape cell contents and encapsulate in double quotes
	$columns = array_map( 'ws_ls_csv_cell_escape', $columns );

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
function ws_ls_csv_row_write( $columns, $row, $delimiter = ',', $end_of_line_char = PHP_EOL) {

	$data = [];

	foreach ( $columns as $key => $value ) {
		$data[ $key ] = ( true === isset( $row[ $key ] ) ) ? $row[$key] : '';
    }

	// Escape cell contents and encapsulate in double quotes
	$data = array_map('ws_ls_csv_cell_escape', $data );

	// Implode and build Row
	return implode( $delimiter, $data ) . $end_of_line_char;
}

/**
 * Helper function to replace column names with something more readable. For example take MySQL column names and make them easier to read
 * @return array Prettified fields names
 */
function ws_ls_column_names() {

	$names = [
					'user_id'                       => 'User ID',
					'user_nicename'                 => 'Nicename',
					'date-display'                  => 'Date',
					'weight'                        => ws_ls_settings_weight_unit_readable(),
					'difference_from_start_display' => __( 'Difference from start', WE_LS_SLUG ),
					'bmi'                           => __( 'BMI', WE_LS_SLUG ),
					'bmi-readable'                  => __( 'BMI Label', WE_LS_SLUG ),
					'notes'                         => __( 'Notes', WE_LS_SLUG )
	];

	// Add meta fields
    if ( true === ws_ls_meta_fields_is_enabled() ) {
        foreach ( ws_ls_meta_fields_enabled() as $meta_field ) {
            $names[ 'meta-' . $meta_field['id'] ] = $meta_field['field_name'];
        }
    }

    $names = apply_filters( 'wlt-export-columns', $names );

	return $names;
}

/**
 * Escape the contents of a CSV cell to ensure the cell remains intact e.g. escape quotes and encase field in quotes.
 * @param  string $data CSV cell content
 * @return string modified CSV cell content
 */
function ws_ls_csv_cell_escape($data) {

	// Escape double quotes within data
	$data = str_replace('"', '""', $data);

	// UTF encode (recommended by CSV lint when testing CSV output)
	$data = utf8_encode($data);

	// Enclose fields within quotes.
	return sprintf('"%s"', $data);
}

/**
 * Take string and stream it to browser as CSV or JSON
 * @param string $data Data
 * @param string $file_name File name
 * @param string $content_type
 * @return void
 */
function ws_ls_export_to_browser($data, $file_name = 'weight-loss-tracker.csv', $content_type = 'text/csv') {

	ws_ls_permission_check_message();

	$content_type = ws_ls_export_verify_type($content_type);

	if ( $data ) {
		header("Content-type: " . esc_html( $content_type ) );
		header("Content-Disposition: attachment; filename=" . esc_html( $file_name ) );
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $data;
	    exit;
	}

	return;
}

/**
 * Do we have a valid export type?
 * @param $content_type
 *
 * @return string
 */
function ws_ls_export_verify_type($content_type) {
		return ( true === in_array( $content_type, ['text/csv', 'application/json'] ) ) ? $content_type : 'text/csv';
}
