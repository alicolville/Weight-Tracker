<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
 * AJAX handler for adding a note
 */
function ws_ls_note_ajax_add() {

	check_ajax_referer( 'ws-ls-add-note', 'security' );

	$user_id    = ws_ls_post_value('user-id' );
	$note       = ws_ls_post_value('note' );

	if ( false === ws_ls_note_add( $user_id, $note ) ) {
		return 0;
	}

	$stats = ws_ls_messages_db_stats( $user_id );

	wp_send_json( $stats[ 'notes-count' ] );
}
add_action( 'wp_ajax_ws_ls_add_note', 'ws_ls_note_ajax_add' );
