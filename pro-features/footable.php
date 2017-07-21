<?php
	defined('ABSPATH') or die('Jog on!');


function ws_ls_data_table_placeholder($user_id = false, $max_entries = false) {

	ws_ls_data_table_enqueue_scripts();

	$unit =  ws_ls_admin_measurment_unit();

?>
	<p>Measurements are in <strong><?php esc_html_e($unit); ?></strong>.</p>
	<table class="ws-ls-user-data table ws-ls-loading-table" id="<?php echo uniqid('ws-ls-'); ?>"
		data-paging="true"
		data-filtering="true"
		data-sorting="true"
		data-editing="true"
		data-cascade="true"
		data-user-id="<?php echo (is_numeric($user_id) ? $user_id : 'false') ?>",
		data-max-entries="<?php echo (is_numeric($max_entries) ? $max_entries : 'false') ?>">
	</table>


<?php
}

function ws_ls_data_table_get_rows($user_id = false, $max_entries = false) {

	// Fetch all columns that will be displayed in data table.
	$columns = ws_ls_data_table_get_columns();

	// Build any filters
	$filters = array();
	if(is_numeric($max_entries)) {
		$filters['start'] = 0;
		$filters['limit'] = $max_entries;
	}
	if(is_numeric($user_id)) {
		$filters['user-id'] = $user_id;
	}

	// Fetch all relevant weight entries that we're interested in
	$user_data = ws_ls_user_data($filters);

	// get a list of active measurment fields (needed later)
	$measurement_fields = ws_ls_get_keys_for_active_measurement_fields();

	// Loop through the data and expected columns and build a clean array of row data for HTML table.
	$rows = array();

	foreach ($user_data['weight_data'] as $data) {

		// Build a row up for given columns
		$row = array();

		foreach ($columns as $column) {

			$column_name = $column['name'];

			// Is this a measurement field?
			if(in_array($column_name, $measurement_fields) && !empty($data['measurements'][$column_name])) {
				$row[$column_name]['options']['sortValue'] = $data['measurements'][$column_name];
				$row[$column_name]['value'] = ws_ls_prep_measurement_for_display($data['measurements'][$column_name]);
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
						$row[$column_name] = $data[$column_name];
						break;
				}
			}
		}
		array_push($rows, $row);
	}

	return $rows;
}


/**
 * Depending on settings, return relevant columns for data table
 * @return array - column definitions
 */
function ws_ls_data_table_get_columns() {

	$columns = array (
		array('name' => 'db_row_id', 'title' => 'ID', 'visible'=> false, 'type' => 'number'),
		array('name' => 'user_id', 'title' => 'USER ID', 'visible'=> false, 'type' => 'number'),
		array('name' => 'user_nicename', 'title' => 'User', 'breakpoints'=> '', 'type' => 'text'),
		array('name' => 'date', 'title' => 'Date', 'breakpoints'=> '', 'type' => 'date'),
		array('name' => 'kg', 'title' => 'Weight', 'visible'=> true, 'type' => 'text')
	);

	// Add BMI?
	if(WE_LS_DISPLAY_BMI_IN_TABLES) {
		array_push($columns, array('name' => 'bmi', 'title' => 'BMI', 'breakpoints'=> '', 'type' => 'text'));
	}

	// Add measurements?
	if(WE_LS_MEASUREMENTS_ENABLED) {
		foreach (ws_ls_get_active_measurement_fields() as $key => $data) {
			array_push($columns, array('name' => $key, 'title' => $data['abv'], 'breakpoints'=> 'md', 'type' => 'text'));
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
	wp_enqueue_style('ws-ls-admin-style', plugins_url( '/css/admin.css', dirname(__FILE__) ), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('ws-ls-footables', plugins_url( '/css/footable.standalone.min.css', dirname(__FILE__)  ), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-footables-js', plugins_url( '/js/footable.min.js', dirname(__FILE__) ), array('jquery'), WE_LS_CURRENT_VERSION, true);
	wp_enqueue_script('ws-ls-footables-admin', plugins_url( '/js/admin.footable.js', dirname(__FILE__) ), array('ws-ls-footables-js'), WE_LS_CURRENT_VERSION, true);
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
					'label-confirm-delete' =>  __('Are you sure you want to delete the row?', WE_LS_SLUG),
					'label-error-delete' =>  __('Unfortunately there was an error deleting the row.', WE_LS_SLUG)
				);
}

/**
 * Given a user ID, return a link to the user's profile
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_user_profile($id) {
	return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-wlt-data-home&user=' . $id )) : '#';
}
