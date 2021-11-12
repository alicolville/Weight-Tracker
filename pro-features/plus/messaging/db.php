<?php

defined( 'ABSPATH' ) or die( 'Jog on!' );

/**
 * Insert a message
 *
 * @param $to
 * @param $from
 * @param $message
 * @param bool $is_note
 *
 * @param bool $visible_to_user
 *
 * @return bool
 */
function ws_ls_messaging_db_add( $to, $from, $message, $is_note = false, $visible_to_user = false ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	if ( true === empty( $message ) ) {
		return false;
	}

	$data       = [ 'to' => $to, 'from' => $from, 'message_text' => $message, 'visible_to_user' => $visible_to_user ];
	$formats    = [ '%d', '%d', '%s', '%d' ];

	$key                    = ( true === $is_note ) ? 'note' : 'message';
	$data[ $key ]           = 1;
	$formats[]              = '%d';

	global $wpdb;

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_MESSAGES, $data, $formats );

	return ! empty( $result );
}

/**
 * Delete a message
 * @param $message_id
 *
 * @return bool
 */
function ws_ls_messaging_db_delete( $message_id ) {

	if ( true === empty( $message_id ) ) {
		return false;
	}

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_MESSAGES,
		[ 'id' => $message_id ],
		[ '%d' ]
	);

	ws_ls_delete_cache( 'message-' . (int) $message_id );

	return ! empty( $result );
}


/**
 * Fetch a message
 * @param $message_id
 * @return bool
 */
function ws_ls_messaging_db_get( $message_id ) {

	if ( true === empty( $message_id ) ) {
		return false;
	}

	if ( $cache = ws_ls_cache_get( 'message-' . (int) $message_id ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES . ' WHERE id = %d', $message_id );

	$result = $wpdb->get_row( $sql, ARRAY_A );

	$result = ( false === empty( $result ) ) ? $result : false;

	$result[ 'message_text' ] = stripslashes( $result[ 'message_text' ] );

	ws_ls_cache_set( 'message-' . (int) $message_id, $result );

	return $result;
}

/**
 * Return stats for user
 * @param $user_id
 * @return array|bool
 */
function ws_ls_messages_db_stats( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return false;
	}

	if ( $cache = ws_ls_cache_user_get( $user_id, 'message-stats' ) ) {
		return $cache;
	}

	global $wpdb;

	$latest_note_id = $wpdb->get_var( 'SELECT id FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES . ' WHERE `note` = 1 and `to` = ' . $user_id . ' order by created desc' );
	$latest_note    = ws_ls_messaging_db_get( $latest_note_id );

	$stats                          = [ 'notes-count' => NULL ];
	$user_id                        = (int) $user_id;
	$stats[ 'notes-count' ]         = $wpdb->get_var( 'SELECT count( id ) FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES . ' WHERE `note` = 1 and `to` = ' . $user_id );
	$stats[ 'notes-latest-id' ]     = $latest_note_id;
	$stats[ 'notes-latest-text' ]   = ( false === empty( $latest_note[ 'message_text' ] ) ) ? $latest_note[ 'message_text' ] : '';

	ws_ls_cache_user_set( $user_id, 'message-stats', $stats );

	return $stats;
}
