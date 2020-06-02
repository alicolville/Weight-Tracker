<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
 * AJAX handler for clearing Target Weight
 */
function ws_ls_clear_target_callback() {

   	check_ajax_referer( 'ws-ls-nonce', 'security' );

  	$user_id = ws_ls_post_value('user-id');

	if( true == ws_ls_delete_target( $user_id ) ){
		wp_send_json(1);
	}

	wp_send_json(0);
}
add_action( 'wp_ajax_ws_ls_clear_target', 'ws_ls_clear_target_callback' );


