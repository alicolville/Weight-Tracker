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
																										'methods' 				=> WP_REST_Server::EDITABLE,
																										'callback' 				=> 'ws_ls_api_target_set',
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

/**
 * Take a new Target weight and update database
 * @param $request
 * @return float|mixed|null
 */
function ws_ls_api_target_set( $request ) {

	$kg = NULL;

	// Are we lucky? Metric by default?
	if ( true === isset( $request[ 'kg' ] ) ) {
		$kg = $request[ 'kg' ];
	}

	// Imperial?
	if ( NULL === $kg ) {

		$stones = ( true === isset( $request[ 'stones' ] ) ) ? $request[ 'stones' ] : NULL;
		$pounds = ( true === isset( $request[ 'pounds' ] ) ) ? $request[ 'pounds' ] : NULL;

		// Stones and Pounds
		if ( NULL !== $stones ) {

			// Force pounds to zero if not specified
			$pounds = ( true === empty( $pounds ) ) ? 0 : $pounds;

			$kg = ws_ls_convert_stones_pounds_to_kg( $stones, $pounds );

		} elseif ( NULL !== $pounds ) {

			$kg = ws_ls_convert_pounds_to_kg( $pounds );

		}
	}

	if ( NULL === $kg ) {
		return false;
	}

	return ws_ls_db_target_set( get_current_user_id(), $kg );
}

/**
 * Delete the target weight for given user
 */
function ws_ls_api_target_delete() {
	return ws_ls_db_target_delete( get_current_user_id() );
}
