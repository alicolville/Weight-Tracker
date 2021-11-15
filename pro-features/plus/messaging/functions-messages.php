<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Add a message between two people
 * @param $to
 * @param $from
 * @param $message
 *
 * @return bool
 */
function ws_ls_message_add( $to, $from, $message ) {

	if ( false === WS_LS_IS_PRO_PLUS ) {
		return false;
	}

	$from = ( true === empty( $from ) ) ? get_current_user_id() : $from;

	return ws_ls_messaging_db_add( $to, $from, $message );
}
