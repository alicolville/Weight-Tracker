<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Add an admin note for a user
 * @param $user_id
 * @param $note
 *
 * @return bool
 */
function ws_ls_note_add( $user_id, $note ) {
	return ws_ls_messaging_db_add( $user_id, get_current_user_id(), $note, true );
}



function test() {
	//ws_ls_note_add(1, 'test 12345');
	var_dump(ws_ls_messages_db_stats( 1));
	die;
}
add_action( 'init', 'test' );
