<?php

defined('ABSPATH') or die('Naw ya dinnie!');

$ws_ls_request_from_admin_screen = false;

/**
 * AJAX handler for clearing Target Weight
 */
function ws_ls_clear_target_callback() {

   	check_ajax_referer( 'ws-ls-nonce', 'security' );

  	$user_id = ws_ls_post_value('user-id');

	if( true == ws_ls_db_target_delete( $user_id ) ){
		wp_send_json(1);
	}

	wp_send_json(0);
}
add_action( 'wp_ajax_ws_ls_clear_target', 'ws_ls_clear_target_callback' );

/**
 * AJAX handler for deleting a notification
 */
function ws_ls_delete_notification_callback() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$notification_id = ws_ls_post_value('notification-id');

	wp_send_json( ws_ls_messaging_db_delete( $notification_id, true ) );
}
add_action( 'wp_ajax_ws_ls_delete_notification', 'ws_ls_delete_notification_callback' );

/**
 * AJAX handler for saving user preferences.
 */
function ws_ls_save_preferences_callback() {

	if ( false === WS_LS_IS_PRO ) {
		wp_send_json( 0 );
	}

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$in_admin_area = ( NULL !== ws_ls_post_value('we-ls-in-admin' ) ) ? true : false;

	$fields = [];

	if ( true === isset( $_POST[ 'WE_LS_DATA_UNITS' ] ) ) {
		$fields[ 'settings' ] = [ 'WE_LS_DATA_UNITS'  => ws_ls_post_value( 'WE_LS_DATA_UNITS' ),
		                          'WE_LS_US_DATE'     => ws_ls_post_value_to_bool( 'WE_LS_US_DATE' )
								];
	}

	$fields[ 'height' ]         = ws_ls_post_value( 'ws-ls-height', NULL, false, false, 'int' );
	$fields[ 'gender' ]         = ws_ls_post_value( 'ws-ls-gender', NULL, false, false, 'int' );
	$fields[ 'aim' ]            = ws_ls_post_value( 'ws-ls-aim', NULL, false, false, 'int' );
	$fields[ 'activity_level' ] = ws_ls_post_value( 'ws-ls-activity_level', NULL, false, false, 'float' );
	$fields[ 'dob' ]            = ws_ls_post_value( 'ws-ls-dob' );
	$fields[ 'user_id' ]        = ws_ls_post_value( 'user-id', NULL, false, false, 'int'  );

	$fields = array_filter( $fields, function( $v, $k ) {
		return NULL !== $v;
	}, ARRAY_FILTER_USE_BOTH );

	$fields = apply_filters( 'wlt-filter-user-settings-save-fields', $fields );

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

	global $ws_ls_request_from_admin_screen;

	// TODO: Refactor the code below - grown messy!

	// Filter?
	$max_entries                        = ws_ls_get_numeric_post_value('max_entries');
	$user_id                            = ws_ls_get_numeric_post_value('user_id');
	$week_number                        = ws_ls_get_numeric_post_value('week');
	$table_id                           = ws_ls_post_value('table_id');
	$small_width                        = ws_ls_post_value_to_bool( 'small_width' );
	$front_end                          = ws_ls_post_value_to_bool( 'front-end' );
	$enable_bmi                         = ws_ls_post_value_to_bool( 'enable-bmi' );
	$enable_notes                       = ws_ls_post_value_to_bool( 'enable-notes' );
	$enable_meta                        = ws_ls_post_value_to_bool( 'enable-meta-fields' );
	$enable_weight                      = ws_ls_post_value_to_bool( 'enable-weight' );
	$ws_ls_request_from_admin_screen    = ws_ls_post_value_to_bool( 'in-admin' );
	$bmi_format                         = ws_ls_post_value('bmi-format', 'label' );
	$custom_field_col_size              = ws_ls_post_value('custom-field-col-size' );
	$custom_field_groups                = ws_ls_post_value('custom-field-groups' );
	$custom_field_slugs                 = ws_ls_post_value('custom-field-slugs' );
	$custom_field_restrict_rows         = ws_ls_post_value('custom-field-restrict-rows' );
	$custom_field_value_exists          = '';

	// If we have a user ID and we're in admin then hide the name from the user entry page
	if ( true === ws_ls_datatable_is_user_profile() ) {
		$front_end = true();
	}

	// Do we need to restrict which database rows we fetch from the database?
	if ( false === empty( $custom_field_restrict_rows ) ) {
		$custom_field_value_exists = ws_ls_meta_fields_slugs_and_groups_to_id( [ 'custom-field-groups' => $custom_field_groups, 'custom-field-slugs' => $custom_field_slugs ] ) ;
	}

	$data = [
		'columns'   => ws_ls_datatable_columns( [ 'small-width' => $small_width, 'front-end' => $front_end, 'enable-meta' => $enable_meta, 'enable-bmi' => $enable_bmi, 'enable-weight' => $enable_weight, 'enable-notes' => $enable_notes,
		                                          'custom-field-col-size' => $custom_field_col_size, 'custom-field-slugs' => $custom_field_slugs, 'custom-field-groups' => $custom_field_groups ] ),
		'rows'      => ws_ls_datatable_rows( [ 'user-id'  => $user_id, 'limit' => $max_entries, 'smaller-width' => $small_width, 'front-end' => $front_end, 'enable-bmi' => $enable_bmi, 'enable-weight' => $enable_weight, 'enable-notes' => $enable_notes,
		                                        'enable-meta' => $enable_meta, 'in-admin' => $ws_ls_request_from_admin_screen, 'week' => $week_number, 'bmi-format' => $bmi_format,
		                                            'custom-field-restrict-rows' => $custom_field_restrict_rows, 'custom-field-value-exists' => $custom_field_value_exists ] ),
		'table_id'  => $table_id
	];

	wp_send_json( $data );
}
add_action( 'wp_ajax_table_data', 'ws_ls_get_table_data' );

/**
Ajax handler used for deleting rows in a footable
 **/
function ws_ls_footable_delete_entry() {

	if ( false === WS_LS_IS_PRO ) {
		wp_send_json(0 );
	}

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$row_id  = ws_ls_post_value_numeric( 'row_id' );
	$user_id = ws_ls_post_value_numeric( 'user_id' );

	// IF we have valid inputs, try and delete from DB.
	if ( false !== $row_id
	        && false !== $user_id &&
	            true === ws_ls_db_entry_delete( $user_id, $row_id ) ) {

		wp_send_json( 1 );

	}

	wp_send_json(0 );
}
add_action( 'wp_ajax_delete_entry', 'ws_ls_footable_delete_entry' );

/**
 * Fetch error log entries
 */
function ws_ls_ajax_get_errors(){

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$table_id = ws_ls_post_value('table_id');

	$columns = [
					[ 'name' => 'timestamp', 'title' => __( 'Date', WE_LS_SLUG ), 'breakpoints'=> '', 'type' => 'date' ],
					[ 'name' => 'module', 'title' => __( 'Module', WE_LS_SLUG ), 'breakpoints'=> '', 'type' => 'text' ],
					[ 'name' => 'message', 'title' => __( 'Message', WE_LS_SLUG ), 'breakpoints'=> '', 'type' => 'text' ]
	];

	$data = [
		'columns'   => $columns,
		'rows'      => ws_ls_log_all(),
		'table_id'  => $table_id
	];

	wp_send_json( $data );

}
add_action( 'wp_ajax_get_errors', 'ws_ls_ajax_get_errors' );

/**
 * AJAX handler for clearing Target Weight
 */
function ws_ls_ajax_postbox_value() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$postbox_id = ws_ls_post_value('id' );
	$key        = ws_ls_post_value('key' );
	$value      = ws_ls_post_value('value' );
	$result     = update_option( 'ws-ls-postbox-' . $postbox_id . '-' . $key, $value );

	wp_send_json( $result );
}
add_action( 'wp_ajax_postboxes_event', 'ws_ls_ajax_postbox_value' );

/**
 * Load an entry for the given user / date
 */
function ws_ls_ajax_get_entry_for_date() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$user_id = ws_ls_post_value('user-id' );

	if ( true === empty( $user_id ) ) {
		wp_send_json( NULL );
	}

	$date = ws_ls_post_value('date' );

	if ( true === empty( $date ) ) {
		wp_send_json( NULL );
	}

	$date           = ws_ls_convert_date_to_iso( $date );
	$existing_id    = ws_ls_db_entry_for_date( $user_id, $date );

	if ( true === empty( $existing_id ) ) {
		wp_send_json( NULL );
	}

	$entry                  = ws_ls_entry_get( [ 'user-id' => $user_id, 'id' => $existing_id ] );

	wp_send_json( $entry );
}
add_action( 'wp_ajax_ws_ls_get_entry_for_date', 'ws_ls_ajax_get_entry_for_date' );

/**
 * AJAX handler for user search via search component
 * @return array
 */
function ws_ls_ajax_user_search() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$search = ws_ls_post_value('search' );

	$cache_key = md5( $search );

	if ( $cache = ws_ls_cache_user_get( 'user-search', $cache_key ) ) {
		wp_send_json( $cache );
	}

	$users  = ws_ls_user_search( $search, ! empty( $search ) );
	$data   = [];

	foreach ( $users as $user ) {

		$user_meta = get_user_meta( $user->id );
		$user_meta = (array) $user_meta;

		$name = sprintf( '%s %s', get_user_meta( $user->id, 'first_name', true ), get_user_meta( $user->id, 'last_name', true ) );

		if ( true === empty( $name ) || ' ' == $name ) {
			$name = $user->user_nicename;
		} else {
			$name = sprintf( '%s (%s)', $name, $user->user_nicename );
		}

		$data[] = [ 'id'        => $user->ID,
					'title'     => $name,
					'detail'    => $user->user_email .$user_meta->first_name
		];
	}

	ws_ls_cache_user_set( 'user-search', $cache_key, $data, 10 * MINUTE_IN_SECONDS );

	wp_send_json( $data );
}
add_action( 'wp_ajax_ws_ls_user_search', 'ws_ls_ajax_user_search' );
