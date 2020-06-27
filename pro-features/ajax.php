<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
 * AJAX handler for saving user preferences.
 */
function ws_ls_save_preferences_callback() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$in_admin_area = ( NULL !== ws_ls_post_value('we-ls-in-admin' ) ) ? true : false;

	// Look for globals that user's can override
	$fields             = [ 'settings' =>   [
												'WE_LS_DATA_UNITS'  => ws_ls_post_value( 'WE_LS_DATA_UNITS' ),
												'WE_LS_US_DATE'     => ws_ls_post_value_to_bool( 'WE_LS_US_DATE' )
											]
	];

	$fields[ 'settings' ][ 'WE_LS_IMPERIAL_WEIGHTS' ] = in_array( $fields[ 'settings' ][ 'WE_LS_DATA_UNITS' ], [ 'stones_pounds', 'pounds_only' ] );

	$fields[ 'height' ]         = ws_ls_post_value( 'we-ls-height', NULL, false, false, 'int' );
	$fields[ 'gender' ]         = ws_ls_post_value( 'ws-ls-gender', NULL, false, false, 'int' );
	$fields[ 'aim' ]            = ws_ls_post_value( 'ws-ls-aim', NULL, false, false, 'int' );
	$fields[ 'activity_level' ] = ws_ls_post_value( 'ws-ls-activity-level', NULL, false, false, 'float' );
	$fields[ 'dob' ]            = ws_ls_post_value( 'ws-ls-dob' );
	$fields[ 'user_id' ]        = ws_ls_post_value( 'user-id', NULL, false, false, 'int'  );

	$fields                     = apply_filters( 'wlt-filter-user-settings-save-fields', $fields );

	do_action( 'ws-ls-hook-user-preference-save', (int) $fields['user_id'], $in_admin_area, $fields );

	if( false == ws_ls_set_user_preferences( $in_admin_area, $fields ) ){
		wp_send_json (0 );
	}

	do_action( 'ws-ls-hook-user-preference-saved' );

	wp_send_json (1 );
}
add_action( 'wp_ajax_ws_ls_save_preferences', 'ws_ls_save_preferences_callback' );


/**
	Load data for user table (admin)
**/
function ws_ls_get_table_data() {

		check_ajax_referer( 'ws-ls-user-tables', 'security' );

		// Filter?
		$max_entries    = ws_ls_get_numeric_post_value('max_entries');
		$user_id        = ws_ls_get_numeric_post_value('user_id');
		$table_id       = ws_ls_post_value('table_id');
		$small_width    = ws_ls_post_value_to_bool( 'small_width' );
		$front_end      = ws_ls_post_value_to_bool( 'front-end' );
		$enable_meta    = ws_ls_post_value_to_bool( 'enable-meta-fields' );

		// If we have a user ID and we're in admin then hide the name from the user entry page
		if ( false === empty( $user_id )
		        && true === is_admin() ) {
			$front_end = true;
		}

		$data = [
					'columns'   => ws_ls_datatable_columns( $small_width, $front_end, $enable_meta ),
					'rows'      => ws_ls_datatable_rows( [ 'user-id'  => $user_id, 'limit' => $max_entries, 'smaller-width' => $small_width, 'front-end' => $front_end, 'enable-meta' => $enable_meta ] ),
					'table_id'  => $table_id
				];

  	 wp_send_json( $data );
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
	if ($row_id && $user_id && ws_ls_db_entry_delete($user_id, $row_id)) {
		wp_send_json(1);
	}

	wp_send_json(0);
}
add_action( 'wp_ajax_delete_entry', 'ws_ls_footable_delete_entry' );

/**
	Fetch Errors
 **/
function ws_ls_ajax_get_errors(){

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$table_id = ws_ls_post_value('table_id');

	$columns = [
		[ 'name' => 'timestamp', 'title' => __('Date', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'date' ],
		[ 'name' => 'module', 'title' => __('Module', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ],
		[ 'name' => 'message', 'title' => __('Message', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ]
	];

	$data = [
				'columns' => $columns,
				'rows' => ws_ls_log_all(),
				'table_id' => $table_id
	];

	wp_send_json($data);

}
add_action( 'wp_ajax_get_errors', 'ws_ls_ajax_get_errors' );

/**
 * Deprecated!! Replace with  ws_ls_post_value_numeric
 *
 * @param $key
 * @param bool $default
 *
 * @return bool|mixed
 */
function ws_ls_get_numeric_post_value($key, $default = false) {
	return (isset($_POST[$key]) && is_numeric($_POST[$key])) ? $_POST[$key] : $default;
}
