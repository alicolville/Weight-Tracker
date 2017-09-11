<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
	Load data for user table (admin)
**/
function ws_ls_get_table_data()
{
  	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	// Filter?
	$max_entries = ws_ls_get_numeric_post_value('max_entries');
	$user_id = ws_ls_get_numeric_post_value('user_id');
	$table_id = ws_ls_ajax_post_value('table_id');
	$small_width = ('true' === ws_ls_ajax_post_value('small_width')) ? true : false;
	$front_end = ('true' === ws_ls_ajax_post_value('front-end')) ? true : false;

	$data = array(
					'columns' => ws_ls_data_table_get_columns($small_width, $front_end),
					'rows' => ws_ls_data_table_get_rows($user_id, $max_entries, $small_width, $front_end),
					'table_id' => $table_id
				);

  	 wp_send_json($data);

}
add_action( 'wp_ajax_table_data', 'ws_ls_get_table_data' );

/**
	Ajax handler used for deleting rows in a footable
**/
function ws_ls_footable_delete_entry() {

  	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$row_id = ws_ls_get_numeric_post_value('row_id');
	$user_id = ws_ls_get_numeric_post_value('user_id');

	// IF we have valid inputs, try and delete from DB.
	if ($row_id && $user_id && ws_ls_delete_entry($user_id, $row_id)) {
		wp_send_json(1);
	}

	wp_send_json(0);
}
add_action( 'wp_ajax_delete_entry', 'ws_ls_footable_delete_entry' );

function ws_ls_get_numeric_post_value($key, $default = false) {
	return (isset($_POST[$key]) && is_numeric($_POST[$key])) ? $_POST[$key] : $default;
}
