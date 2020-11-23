<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Register Weight Tracker endpoints
 */
add_action( 'rest_api_init', function () {

	// Target
	register_rest_route( 'yeken-weight-tracker/v1', '/target/',	[	[
																										'methods' 				=> WP_REST_Server::READABLE,
																										'callback' 				=> 'ws_ls_api_target_get',
																										'permission_callback' 	=> 'ws_ls_api_permission_check',
																									],
																									[
																										'methods' 				=> WP_REST_Server::DELETABLE,
																										'callback' 				=> 'ws_ls_api_target_delete',
																										'permission_callback' 	=> 'ws_ls_api_permission_check',
																									]
																				]

	);


} );

/**
 * Security check for when calling API
 * @return bool
 */
function ws_ls_api_permission_check() {
	return is_user_logged_in();
}

/**
 * Return the target weight
 * @return void|null
 */
function ws_ls_api_target_get() {
	return ws_ls_target_get( get_current_user_id() );
}

function ws_ls_api_target_set() {

	//$param = $request['some_param'];

	// Or via the helper method:
	//$param = $request->get_param('some_param');
}

/**
 * Delete the target weight for given user
 */
function ws_ls_api_target_delete() {
	return ws_ls_db_target_delete( get_current_user_id() );
}
