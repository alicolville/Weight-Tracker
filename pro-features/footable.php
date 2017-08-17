<?php
	defined('ABSPATH') or die('Jog on!');


function ws_ls_data_table_placeholder($user_id = false, $max_entries = false, $smaller_width = false) {

	ws_ls_data_table_enqueue_scripts();

?>
	<table class="ws-ls-user-data-ajax table ws-ls-loading-table" id="<?php echo uniqid('ws-ls-'); ?>"
		data-paging="true"
		data-filtering="true"
		data-sorting="true"
		data-editing="true"
		data-cascade="true"
		data-toggle="true"
	  	data-use-parent-width="true"
		data-user-id="<?php echo (is_numeric($user_id) ? $user_id : 'false') ?>",
		data-max-entries="<?php echo (is_numeric($max_entries) ? $max_entries : 'false') ?>"
		data-small-width="<?php echo ($smaller_width) ? 'true' : 'false' ?>">
	</table>
	<?php if (WE_LS_MEASUREMENTS_ENABLED):  ?>
		<p><em>Measurements are in <?php echo ('inches' == ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT')) ? __('Inches', WE_LS_SLUG) : __('CM', WE_LS_SLUG); ?>.</em></p>
	<?php endif;  ?>
<?php
}

function ws_ls_data_table_get_rows($user_id = false, $max_entries = false, $smaller_width = false) {

	// Fetch all columns that will be displayed in data table.
	$columns = ws_ls_data_table_get_columns($smaller_width);

	// Build any filters
	$filters = array();
	if(is_numeric($max_entries)) {
		$filters['start'] = 0;
		$filters['limit'] = $max_entries;
	}
	if(is_numeric($user_id)) {
		$filters['user-id'] = $user_id;
	}

	$filters['sort-column'] = 'weight_date';
    $filters['sort-order'] = 'asc';

	// Fetch all relevant weight entries that we're interested in
	$user_data = ws_ls_user_data($filters);

    // get a list of active measurment fields (needed later)
	$measurement_fields = ws_ls_get_keys_for_active_measurement_fields();

	// Loop through the data and expected columns and build a clean array of row data for HTML table.
	$rows = array();

	$previous_user_weight = [];

	foreach ($user_data['weight_data'] as $data) {

		// Build a row up for given columns
		$row = array();

		foreach ($columns as $column) {

			$column_name = $column['name'];

			if('gainloss' == $column_name) {

				// Compare to previous weight and determine if a gain / loss in weight
				$gain_loss = '';
				$gain_class = '';

				if(false === empty($previous_user_weight[$data['user_id']])) {

					if ($data['kg'] > $previous_user_weight[$data['user_id']]) {
						$gain_class = 'gain';
						$gain_loss = ws_ls_convert_kg_into_relevant_weight_String($data['kg'] - $previous_user_weight[$data['user_id']]);
					} elseif ($data['kg'] < $previous_user_weight[$data['user_id']]) {
						$gain_class = 'loss';
						$gain_loss = ws_ls_convert_kg_into_relevant_weight_String($data['kg'] - $previous_user_weight[$data['user_id']]);
					} elseif ($data['kg'] == $previous_user_weight[$data['user_id']]) {
						$gain_class = 'same';
					}
				} else {
					$gain_loss = __('First entry', WE_LS_SLUG);
				}

				$previous_user_weight[$data['user_id']] = $data['kg'];
			}

			// Is this a measurement field?
			if(in_array($column_name, $measurement_fields) && !empty($data['measurements'][$column_name])) {
				$row[$column_name]['options']['sortValue'] = $data['measurements'][$column_name];
				$row[$column_name]['value'] = ws_ls_prep_measurement_for_display($data['measurements'][$column_name]);
			} else if ('gainloss' === $column_name) {
				$row[$column_name]['value'] = $gain_loss;
				$row[$column_name]['options']['classes'] = 'ws-ls-' . $gain_class; // Can use this method for icons
			} else if ('bmi' === $column_name) {
                $row[$column_name]['value'] =  ws_ls_get_bmi_for_table(ws_ls_get_user_height($data['user_id']), $data['kg'], __('No height', WE_LS_SLUG)) ;
                $row[$column_name]['options']['classes'] = 'ws-ls-' . sanitize_key($row[$column_name]['value']); // Can use this method for icons
            } else if (!empty($data[$column_name])) {
				switch ($column_name) {
					case 'kg':
						$row[$column_name]['options']['sortValue'] = $data['kg'];
						$row[$column_name]['value'] = $data['display'];
						break;
					case 'user_nicename':
						$row[$column_name]['options']['sortValue'] = $data['user_nicename'];
						$row[$column_name]['value'] = sprintf('<a href="%s">%s</a>', ws_ls_get_link_to_user_profile($data['user_id']), $data['user_nicename']);
						break;
					default:
						$row[$column_name] = esc_html($data[$column_name]);
						break;
				}
			}
		}
		array_push($rows, $row);
	}

    // Reverse the array so most recent entries are shown first (as default)
    $rows = array_reverse($rows);

    return $rows;
}


/**
 * Depending on settings, return relevant columns for data table
 * @return array - column definitions
 */
function ws_ls_data_table_get_columns($smaller_width = false) {

	$columns = array (
		array('name' => 'db_row_id', 'title' => 'ID', 'visible'=> false, 'type' => 'number'),
		array('name' => 'user_id', 'title' => 'USER ID', 'visible'=> false, 'type' => 'number'),
		array('name' => 'user_nicename', 'title' => 'User', 'breakpoints'=> '', 'type' => 'text'),
		array('name' => 'date', 'title' => 'Date', 'breakpoints'=> '', 'type' => 'date'),
		array('name' => 'kg', 'title' => 'Weight', 'visible'=> true, 'type' => 'text'),
		array('name' => 'gainloss', 'title' => ws_ls_tooltip('+/-', __('+', WE_LS_SLUG)), 'visible'=> true, 'type' => 'text')
	);

	// Add BMI?
	if(WE_LS_DISPLAY_BMI_IN_TABLES) {
		array_push($columns, array('name' => 'bmi', 'title' => ws_ls_tooltip('BMI', __('Body Mass Index', WE_LS_SLUG)), 'breakpoints'=> '', 'type' => 'text'));
	}

	// Add measurements?
	if(WE_LS_MEASUREMENTS_ENABLED) {

		$unit = ws_ls_admin_measurment_unit();

		foreach (ws_ls_get_active_measurement_fields() as $key => $data) {
			array_push($columns, array('name' => esc_attr($key), 'title' => ws_ls_tooltip($data['abv'], $data['title'] . ' (' . $unit . ')' ), 'breakpoints'=> (($smaller_width) ? 'lg' : 'md'), 'type' => 'text'));
		}
	}

	// Add notes;
	array_push($columns, array('name' => 'notes', 'title' => 'Notes', 'breakpoints'=> 'lg', 'type' => 'text'));

	return $columns;
}


/**
 * Enqueue relevant CSS / JS when needed to make footables work
 * @return nothing
 */
function ws_ls_data_table_enqueue_scripts() {

	$minified = ws_ls_use_minified();

	wp_enqueue_style('ws-ls-footables', plugins_url( '/css/footable.standalone.min.css', dirname(__FILE__)  ), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-footables-js', plugins_url( '/js/footable.min.js', dirname(__FILE__) ), array('jquery'), WE_LS_CURRENT_VERSION, true);
	wp_enqueue_script('ws-ls-footables-admin', plugins_url( '/js/admin.footable' .     $minified . '.js', dirname(__FILE__) ), array('ws-ls-footables-js'), WE_LS_CURRENT_VERSION, true);
	wp_localize_script('ws-ls-footables-admin', 'ws_user_table_config', ws_ls_data_js_config());
}

/**
 * Used to embed config settings for jQuery front end
 * @return array of settings
 */
function ws_ls_data_js_config() {
	return array(
					'security' => wp_create_nonce('ws-ls-user-tables'),
					'us-date' => (WE_LS_US_DATE) ? 'true' : 'false',
					'base-url' => ws_ls_get_link_to_user_data(),
					'current-url-base64' => ws_ls_get_url(true),
					'label-confirm-delete' =>  __('Are you sure you want to delete the row?', WE_LS_SLUG),
					'label-error-delete' =>  __('Unfortunately there was an error deleting the row.', WE_LS_SLUG)
				);
}
