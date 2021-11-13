<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
 * AJAX handler for adding a note
 */
function ws_ls_note_ajax_add() {

	check_ajax_referer( 'ws-ls-add-note', 'security' );

	$user_id            = ws_ls_post_value('user-id' );
	$note               = ws_ls_post_value('note' );
	$visible_to_user    = ws_ls_post_value_to_bool('visible-to-user' );

	if ( false === ws_ls_note_add( $user_id, $note, $visible_to_user ) ) {
		return 0;
	}

	if ( true === ws_ls_post_value_to_bool('send-email' ) ) {

		$email_template = ws_ls_emailer_get( 'note-added' );

		$current_user = get_userdata( $user_id );

		if (false === empty( $current_user->user_email )) {

			$note = str_replace( PHP_EOL, '<br />', $note );
			ws_ls_emailer_send( $current_user->user_email, $email_template['subject'], $email_template['email'], [ 'data' => $note] );
		}
	}

	$stats = ws_ls_messages_db_stats( $user_id );

	wp_send_json( $stats[ 'notes-count' ] );
}
add_action( 'wp_ajax_ws_ls_add_note', 'ws_ls_note_ajax_add' );

/**
 * Delete notes
 */
function ws_ls_note_ajax_delete() {

	check_ajax_referer( 'ws-ls-delete-note', 'security' );

	$note_id = ws_ls_post_value('id' );

	if ( true === empty( $note_id ) ) {
		wp_send_json_error( 'Missing ID', 400 );
	}

	$response = ws_ls_messaging_db_delete( $note_id );

	wp_send_json( (int) $response );
}
add_action( 'wp_ajax_ws_ls_delete_note', 'ws_ls_note_ajax_delete' );
