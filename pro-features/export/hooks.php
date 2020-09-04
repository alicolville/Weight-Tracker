<?php

defined('ABSPATH') or die('Jog on.');

/**
 * Admin Menu
 */
function ws_ls_export_admin_menu() {

	add_submenu_page( 'ws-ls-data-home', __( 'Export Data', WE_LS_SLUG ),  __( 'Export Data', WE_LS_SLUG ), 'manage_options', 'ws-ls-export-data', 'ws_ls_export_admin_page', 6 );
}
add_action( 'admin_menu', 'ws_ls_export_admin_menu' );


function ws_ls_export_ajax_process() {

	if ( false === WE_LS_IS_PRO ) {
		return;
	}

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$return = [ 'continue' => true, 'error' => false, 'message' => '', 'percentage' => rand(1, 100) ];

	$return[ 'continue' ] = true;
	$return[ 'message' ] = 'error';

	wp_send_json( $return );

}
add_action( 'wp_ajax_process_export', 'ws_ls_export_ajax_process' );
