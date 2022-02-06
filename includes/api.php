<?php

defined('ABSPATH') or die("Jog on!");


function ws_ls_api_v1_register_routes() {

	register_rest_route( 'weight-tracker/v1', '/entries', array(
		'methods'  => 'GET',
		'callback' => 'ws_ls_api_entries',
	) );

}
add_action( 'rest_api_init', 'ws_ls_api_v1_register_routes' );


function ws_ls_api_v1_entries() {
	return [ 'hello' ];
}
