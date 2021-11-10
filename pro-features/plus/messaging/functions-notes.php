<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Are notes enabled?
 * @return bool
 */
function ws_ls_note_is_enabled() {
	return WS_LS_IS_PRO;
}

/**
 * Add an admin note for a user
 * @param $user_id
 * @param $note
 *
 * @return bool
 */
function ws_ls_note_add( $user_id, $note ) {

	if ( false === ws_ls_note_is_enabled() ) {
		return false;
	}

	if ( true === empty( $user_id ) ||
	     true === empty( $note ) ) {
		return false;
	}

	return ws_ls_messaging_db_add( $user_id, get_current_user_id(), $note, true );
}
