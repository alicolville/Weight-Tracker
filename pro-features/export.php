<?php

defined('ABSPATH') or die('Jog on.');

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
    ws_ls_export_permission_check();

	// Do we have a user ID? If so limit data
	$filters = (false === empty($_GET['user-id']) && is_numeric($_GET['user-id'])) ? ['user-id' => (int) $_GET['user-id'] ] : false;

	// Fetch all relevant weight entries that we're interested in
	$export_data = ws_ls_user_data($filters);

	// If meta fields are enabled and we have some, then add to row for easy matching to columns
    if ( true === ws_ls_meta_fields_is_enabled() ) {
        $export_data = ws_ls_export_process_meta_fields( $export_data );
    }

	// Fetch whether CSV or JSON
	$file_type = (false === empty($_GET['file-type'])) ? ws_ls_export_verify_type($_GET['file-type']) : 'text/csv';

	$output = '';

	if(false === empty($export_data)) {

		switch ($file_type) {
			case 'text/csv':
				$output = ws_ls_csv_from_array( $export_data['weight_data'] );
				break;
			default:
				$output = ws_ls_export_to_json( $export_data['weight_data'] );
				break;
		}

	} else {
		$output .= __('No data was found for the given criteria.', WE_LS_SLUG);
	}

	// Output file to CSV
	ws_ls_export_to_browser($output, ('text/csv' == $file_type) ? 'weight-loss-tracker.csv' : 'weight-loss-tracker.json');

	die();
}
add_action( 'admin_post_export_data', 'ws_ls_export_data' );


/**
 * Manipulate data rows so Meta fields at same level as others.
 *
 * @param $rows
 * @return mixed
 */
function ws_ls_export_process_meta_fields( $rows ) {

    if ( false === empty( $rows['weight_data'] ) ) {

        // Loop through each CSV row
        for( $i = 0; $i < count( $rows['weight_data'] ); $i++ ) {

            // If the row has meta fields, then process.
            if ( false === empty( $rows['weight_data'][ $i ][ 'meta-fields'] )) {

                foreach( $rows['weight_data'][ $i ][ 'meta-fields'] as $meta_Field ) {

                    $rows['weight_data'][ $i ]['meta-' . $meta_Field['meta_field_id'] ] = ws_ls_fields_display_field_value( $meta_Field['value'], $meta_Field['meta_field_id'] );

                }
            }
        }
    }

    return $rows;
}


/**
 * Export to JSON
 *
 * @param  array  $data                 Data
 * @return string						Contents of JSON file
 */
function ws_ls_export_to_json( $rows ) {

	// Ensure we have some data!
	if( is_array($rows) && count($rows) > 0) {

		$output = ['columns' => ws_ls_column_names(), 'rows' => []];

		$data = [];
		$measurement_keys = ws_ls_get_keys_for_active_measurement_fields();

        // Only render body of report if Pro!
        if ( true !== WS_LS_IS_PRO ) {

            $output['rows'] =  __('You must have a Pro License to export user data into JSON format.', WE_LS_SLUG);

        } else {

            foreach ($rows as $row) {
                foreach ($output['columns'] as $key => $value) {
                    if(in_array($key, $measurement_keys)) {

                        // Do we need to convert the measurement to inches?
                        $data[$key] = ( 'inches' === WE_LS_MEASUREMENTS_UNIT ) ?
                            ws_ls_convert_to_inches( $row['measurements'][$key] ) :
                            $row['measurements'][$key];

                    } else {
                        $data[$key] = $row[$key];
                    }

                }
                $data = ws_ls_export_add_bmi( $data );

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
function ws_ls_csv_from_array($data, $show_column_headers = true, $delimiter = ',', $end_of_line_char = PHP_EOL) {

	// Ensure we have some data!
	if(is_array($data) && count($data) > 0) {

		$csv_output = '';

		$columns = ws_ls_column_names();

        // Include header row with column names?
        if($show_column_headers) {
            $csv_output .= ws_ls_csv_row_header($columns);
        }

        // Only render body of report if Pro!
        if ( true !== WS_LS_IS_PRO ) {

            $csv_output .=  __('You must have a Pro License to export user data into CSV format.', WE_LS_SLUG);

        } else {

           // Build body of CSV
            foreach ($data as $row) {
                $row = ws_ls_export_add_bmi( $row );

                $row = apply_filters( 'wlt-export-row', $row );

                $csv_output .= ws_ls_csv_row_write($columns, $row, $delimiter, $end_of_line_char);
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

	$csv_output = '';

	if (is_array($columns) && !empty($columns)) {

		$columns = array_values($columns);

		// Escape cell contents and encapsulate in double quotes
		$columns = array_map('ws_ls_csv_cell_escape', $columns);

		// Implode and build Row
		$csv_output .= implode(',', $columns) . PHP_EOL;
	}

	return $csv_output;
}

/**
 * Produce a CSV row
 * @param  array  $columns          Columns we want from the row
 * @param  array  $row              Contains the raw data for a given row.
 * @param  string $delimiter        Delimiter between CSV columns
 * @param  string $end_of_line_char End of line character
 * @return string CSV row
 */
function ws_ls_csv_row_write($columns, $row, $delimiter = ',', $end_of_line_char = PHP_EOL) {

	$csv_output = '';

	if (is_array($row) && !empty($row)) {

		$data = [];
		$measurement_keys = ws_ls_get_keys_for_active_measurement_fields();

		foreach ($columns as $key => $value) {
			if(in_array($key, $measurement_keys)) {

				// Do we need to convert the measurement to inches?
				$data[$key] = ( 'inches' === WE_LS_MEASUREMENTS_UNIT ) ?
								ws_ls_convert_to_inches( $row['measurements'][$key] ) :
									$row['measurements'][$key];

			} elseif(isset($row[$key])) {
				$data[$key] = $row[$key];
			} else {
			    // If the column is not found, blank it so CSV ok.
                $data[$key] = '';
           }
        }

		// Escape cell contents and encapsulate in double quotes
		$data = array_map('ws_ls_csv_cell_escape', $data);

		// Implode and build Row
		$csv_output .= implode($delimiter, $data) . $end_of_line_char;
	}

	return $csv_output;
}

/**
 * Helper function to replace column names with something more readable. For example take MySQL column names and make them easier to read
 * @param  array $names An array of column names
 * @return array Prettified fields names
 */
function ws_ls_column_names() {

		$names = [
						'user_id' => 'User ID',
						'user_nicename' => 'Nicename',
						'date-display' => 'Date',
						'kg' => __('kg', WE_LS_SLUG),
						'only_pounds' => __('lbs only', WE_LS_SLUG),
						'stones' => __('st', WE_LS_SLUG),
						'pounds' => __('lbs', WE_LS_SLUG),
                        'difference_from_start_display' => __('Difference from start', WE_LS_SLUG),
						'bmi' => __( 'BMI', WE_LS_SLUG),
						'bmi-readable' => __( 'BMI Label', WE_LS_SLUG),
						'notes' => __('Notes', WE_LS_SLUG)
		];

		// Add measurements
		foreach (ws_ls_get_active_measurement_fields() as $key => $measurement) {
			$names[$key] = $measurement['title'];
		}

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
 * @param  string $data             Data
 * @param  string $file_name        File name
 * @return void
 */
function ws_ls_export_to_browser($data, $file_name = 'weight-loss-tracker.csv', $content_type = 'text/csv') {

	ws_ls_export_permission_check();

	$content_type = ws_ls_export_verify_type($content_type);

	if ( $data ) {
		header("Content-type: " . esc_html($content_type));
		header("Content-Disposition: attachment; filename=" . esc_html($file_name));
		header("Pragma: no-cache");
		header("Expires: 0");
		echo $data;
	    exit;
	}

	return;
}

function ws_ls_export_add_bmi($row) {
	if(false === empty($row['user_id']) && false === empty($row['kg'])) {
		$row['bmi'] =  ws_ls_calculate_bmi(ws_ls_get_user_height($row['user_id']), $row['kg'], __('No height', WE_LS_SLUG)) ;
		$row['bmi-readable'] =  (is_numeric($row['bmi'])) ? ws_ls_calculate_bmi_label($row['bmi']) : '';
	}

	return $row;
}

function ws_ls_export_verify_type($content_type) {
		return (	false === empty($content_type) &&
					in_array($content_type, ['text/csv', 'application/json'])
				) ? $content_type : 'text/csv';
}
/**
 * Helper function to disable admin page if the user doesn't have the correct user role.
 */
function ws_ls_export_permission_check() {
    if ( !current_user_can( WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
    }
}
