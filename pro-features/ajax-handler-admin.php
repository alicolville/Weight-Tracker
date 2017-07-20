<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_get_table_data()
{
  	$ajax_response = 0;

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	// Filter?
	$max_entries = ws_ls_get_numeric_post_value('max_entries');	// TODO: Reenable
	$max_entries = 30;
	$user_id = ws_ls_get_numeric_post_value('user_id');
	$table_id = ws_ls_ajax_post_value('table_id');

	$data = array(
					'columns' => ws_ls_data_table_get_columns(),
					'rows' => ws_ls_data_table_get_rows($user_id, $max_entries),
					'table_id' => $table_id
				);

  	 wp_send_json($data);

}
add_action( 'wp_ajax_table_data', 'ws_ls_get_table_data' );

function ws_ls_get_numeric_post_value($key, $default = false) {
	return (isset($_POST[$key]) && is_numeric($_POST[$key])) ? $_POST[$key] : $default;
}
