<?php
	defined('ABSPATH') or die('Jog on!');

function ws_ls_admin_delete_entry_callback()
{
  $ajax_response = 0;

  check_ajax_referer( 'ajax-security-nonce', 'security' );

  $user_id = ws_ls_ajax_post_value('user-id');
  $row_id = ws_ls_ajax_post_value('row-id');

  if(true == ws_ls_delete_entry($user_id, $row_id)){
    $ajax_response = 1;
  }
  echo $ajax_response;
	wp_die();
}
add_action( 'wp_ajax_ws_ls_admin_delete_entry', 'ws_ls_admin_delete_entry_callback' );

function ws_ls_user_data_callback()
{
  	$ajax_response = 0;
	$filters = array();

	check_ajax_referer( 'ajax-security-nonce', 'security' );

	$draw_id = ws_ls_ajax_get_value('draw', true);
	$table_columns = ws_ls_ajax_get_value('columns');

	// Defaults
	$filters['start'] = ws_ls_ajax_get_value('start', true, 0);
	$filters['limit'] = ws_ls_ajax_get_value('length', true, 10);

	// Sort specified?
	$sort = ws_ls_ajax_get_value('order');
	if (is_array($sort)){
		$filters['sort-order'] = $sort[0]['dir'];
		$filters['sort-column'] = $table_columns[$sort[0]['column'][0]]['name'];
	} else {
			$filters['sort-order'] = 'desc';
			$filters['sort-column'] = 'weight_date';
	}

	// Search?
	$search = ws_ls_ajax_get_value('search');
	if (is_array($search)){
		$filters['search'] = $search['value'];
	}

	echo ws_ls_load_json($draw_id, $filters);

	wp_die();
}
add_action( 'wp_ajax_ws_ls_user_data', 'ws_ls_user_data_callback' );

function ws_ls_load_json($draw_id, $filters)
{
	$data_from_db = ws_ls_user_data($filters);

	$data['draw'] = $draw_id;
	$data['recordsTotal'] = 0;
	$data['recordsFiltered'] = 0;
	$data['data'] = array();

	if ($data_from_db != false) {

		$data['recordsTotal'] = ws_ls_user_data_count();
		$data['recordsFiltered'] = $data_from_db['count'];

		$delete_image = plugins_url( '../css/images/delete.png', __FILE__ );

		foreach ($data_from_db['weight_data'] as $weight) {

			$row = array();
			$row[] = $weight['user_nicename'];
			$row[] = ws_ls_render_date($weight);
			$row[] = $weight['display'];
			$row[] = $weight['notes'];

			// Measurement Columns?
	 		$measurement_columns = ws_ls_get_active_measurement_fields();
			if ($measurement_columns) {
	 			foreach ($measurement_columns as $key => $blah) {
					$measure = ws_ls_prep_measurement_for_display($weight['measurements'][$key]);
					$row[] = (is_numeric($measure)) ? $measure : '-';
	 			}
			}
			$row[] = '<img src="' . $delete_image . '" width="15" height="15" border="0"  class="ws-ls-admin-delete-weight" id="ws-ls-delete-row-' . $weight['db_row_id'] . '" data-user-id="' . $weight['user_id'] . '" data-row-id="' . $weight['db_row_id'] . '" />';


			$row['row-id'] = $weight['db_row_id'];
			$row['user-id'] = $weight['user_id'];

			array_push($data['data'], $row);
		}
	}

	return json_encode($data);
}

function ws_ls_ajax_get_value($key, $force_to_int = false, $default = false)
{
		$return_value = NULL;

	    if(isset($_GET[$key]) && $force_to_int) {
	        return intval($_GET[$key]);
	    }
	    elseif(isset($_GET[$key])) {
	    	return $_GET[$key];
	    }

		// Use default if aval
		if ($default && is_null($return_value)) {
			$return_value = $default;
		}
    return $return_value;
}
