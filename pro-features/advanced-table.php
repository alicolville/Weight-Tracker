<?php
	defined('ABSPATH') or die('Jog on!');

$column_number = -1;
function ws_ls_get_column_number() {
	global $column_number;
	$column_number++;
	return $column_number;
}

function ws_ls_get_advanced_table_config() {

	$columns = array(
		array('targets' => ws_ls_get_column_number(), 'sortable' => false, 'searchable' => false, 'visible' => false),									// Kg Field
		array('responsivePriority' => 1, 'targets' => ws_ls_get_column_number(), 'searchable' => false, 'sortable' => false, 'width' => '400px'),		// Options
		array('responsivePriority' => 2,'targets' => ws_ls_get_column_number()),																		// Date
		array('responsivePriority' => 3,'targets' => ws_ls_get_column_number(), "orderData" => array(0,2)),												// Weight
		array('targets' => ws_ls_get_column_number())
	);

	// Add BMI?
	if(WE_LS_DISPLAY_BMI_IN_TABLES) {
		$columns = array_merge($columns, array(
			array('targets' => ws_ls_get_column_number())
		));
	}

	// Measurement Columns?
	$measurement_columns = ws_ls_get_active_measurement_fields();
	if ($measurement_columns) {

		$measure_cols = array();

		foreach ($measurement_columns as $key => $value) {
			$measure_cols[] = array('targets' => ws_ls_get_column_number());
		}

		$columns = array_merge($columns, $measure_cols);
	}

	$columns = array_merge($columns, array(
		array('sortable' => false, 'targets' => ws_ls_get_column_number())
	));

	return array (
		'columns' => $columns
	);
}

function ws_ls_get_advanced_table_admin_config() {

	$columns = array(
		array('name' => 'user_nicename', 'responsivePriority' => 0, 'targets' => ws_ls_get_column_number(), 'searchable' => true, 'sortable' => true),							// Name
		array('name' => 'weight_date', 'responsivePriority' => 1,'targets' => ws_ls_get_column_number(), 'sortable' => true, 'searchable' => true),																		// Date
		array('name' => 'weight_weight', 'responsivePriority' => 2,'targets' => ws_ls_get_column_number(), 'sortable' => true),																		// Weight
		array('name' => 'notes', 'responsivePriority' => 3,'targets' => ws_ls_get_column_number(), 'sortable' => false),								// Notes
		//array('targets' => ws_ls_get_column_number())
	);

	// Measurement Columns?
	$measurement_columns = ws_ls_get_active_measurement_fields();
	if ($measurement_columns) {

		$measure_cols = array();

		foreach ($measurement_columns as $key => $value) {
			$measure_cols[] = array('targets' => ws_ls_get_column_number());
		}

		$columns = array_merge($columns, $measure_cols);
	}

	$columns = array_merge($columns, array(
		array('sortable' => false, 'targets' => ws_ls_get_column_number(), 'width' => '6px')
	));

	return array (
		'columns' => $columns
	);
}


function ws_ls_advanced_data_table($weight_data)
{
	$table_id = 'ws_ls_data_table_' . rand(10,1000) . '_' . rand(10,1000);

	$html_output = '
		<table id="' . $table_id . '" class="display ws-ls-advanced-data-table responsive hover" cellspacing="0" width="100%">
			<thead>
			    <tr>
					<th class="never"></th>
					<th>' . __('Date', WE_LS_SLUG) .'</th>
			      	<th>' . __('Weight', WE_LS_SLUG) . '</th>
			      	<th>+/-</th>';

					// BMI?
					if(WE_LS_DISPLAY_BMI_IN_TABLES) {
						$html_output .= '<th class="tablet-l tablet-p">BMI</th>';
					}

					// Weight Columns?
					$measurement_columns = ws_ls_get_active_measurement_fields();
					if ($measurement_columns) {
						foreach ($measurement_columns as $key => $data) {
							$html_output .= '<th class="none">' . __($data['title'], WE_LS_SLUG) . '</th>';
						}
					}

	$html_output .= '	<th class="tablet-l">' . __('Notes', WE_LS_SLUG) . '</th>
					<th></th>
			    </tr>
			</thead>
			<tbody>';

		$delete_image = plugins_url( '../css/images/delete.png', __FILE__ );
		$edit_image = plugins_url( '../css/images/edit.png', __FILE__ );

  foreach ($weight_data as $weight_object)
  {
	  	$percentage_lost = ((isset($weight_object['difference_from_start']) && is_numeric($weight_object['difference_from_start'])) ? $weight_object['difference_from_start'] . '%' : '');
		$percentage_lost .= (!empty($weight_object['difference_from_start_display'])) ? ' / ' . $weight_object['difference_from_start_display'] : '';

    	$html_output .= sprintf('<tr id="ws-ls-row-%s">
									<td>%s</td>
									<td>%s</td>
									<td>%s</td>
									<td>%s</td>',
									$weight_object['db_row_id'],
									$weight_object['kg'],
									ws_ls_render_date($weight_object),
									$weight_object['display'],
									$percentage_lost
								);

		// BMI?
		if(WE_LS_DISPLAY_BMI_IN_TABLES) {
			$html_output .= '<td>' . ws_ls_get_bmi_for_table(ws_ls_get_user_height(), $weight_object['kg']) . '</td>';
		}

		// Measurement Columns?
		$measurement_columns = ws_ls_get_active_measurement_fields();
		if ($measurement_columns) {
			foreach ($measurement_columns as $key => $data) {
				$html_output .= '<td>' . ws_ls_prep_measurement_for_display($weight_object['measurements'][$key]) . '</td>';
			}
		}

		$html_output .= '	<td style="font-size:0.7em;word-wrap:break-word;">' . $weight_object['notes'] . '</td>
							<td class="ws-ls-table-options">
								<img src="' . $edit_image .'" width="15" height="15" id="ws-ls-edit-' . $weight_object['db_row_id'] . '" data-row-id="' . $weight_object['db_row_id'] . '" class="first ws-ls-edit-row" />
								<img src="' . $delete_image .'" width="15" height="15" id="ws-ls-delete-' . $weight_object['db_row_id'] . '" data-row-id="' . $weight_object['db_row_id'] . '" class="ws-ls-delete-row" />
							</td>
						</tr>';
  }
  $html_output .= '</tbody>

  </table>';

  return $html_output;
}

function ws_ls_advanced_table_locale() {

	return array(
			"decimal"=>         "",
			"emptyTable"=>      __('No data available in table', WE_LS_SLUG),
			"info"=>            __("Showing _START_ to _END_ of _TOTAL_ entries", WE_LS_SLUG),
			"infoEmpty"=>       __("Showing 0 to 0 of 0 entries", WE_LS_SLUG),
			"infoFiltered"=>    __("(filtered from _MAX_ total entries)", WE_LS_SLUG),
			"infoPostFix"=>     "",
			"thousands"=>       ",",
			"lengthMenu"=>      __("Show _MENU_ entries", WE_LS_SLUG),
			"loadingRecords"=>  __("Loading...", WE_LS_SLUG),
			"processing"=>      __("Processing...", WE_LS_SLUG),
			"search"=>          __("Search: ", WE_LS_SLUG),
			"zeroRecords"=>     __("No matching records found", WE_LS_SLUG),
			"paginate"=>  array(
			    "first"=>       __("First", WE_LS_SLUG),
			    "last"=>        __("Last", WE_LS_SLUG),
			    "next"=>        __("Next", WE_LS_SLUG),
			    "previous"=>    __("Previous", WE_LS_SLUG)
			),
			"aria"=>  array(
			    "sortAscending"=>   __(":  activate to sort column ascending", WE_LS_SLUG),
			    "sortDescending"=>  __(": activate to sort column descending", WE_LS_SLUG)
			)
	);
}
