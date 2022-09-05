<?php

defined( 'ABSPATH' ) or die( 'Jog on!' );

/**
 * Fetch notes / messages for user
 *
 * @param $to
 * @param null $from
 * @param bool $is_note
 * @param bool $is_notification
 * @param null $visible_to_user
 * @param null $offset
 * @param null $limit
 * @param bool $ignore_cache
 *
 * @return array|bool|object|stdClass[]|null
 */
function ws_ls_messaging_db_select( $to, $from = NULL, $is_note = true, $is_notification = false, $visible_to_user = NULL, $offset = NULL, $limit = NULL, $ignore_cache = false ) {

	if ( false === WS_LS_IS_PRO ) {
		return NULL;
	}

	$cache_key = 'ws-ls-messaging-' . md5($from . $limit . $is_note . $visible_to_user . $is_notification . $offset . $limit );

	if ( false === $ignore_cache && $cache = ws_ls_cache_user_get( $to, $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES;

	// -------------------------------------------------
	// Build where clause
	// -------------------------------------------------
	$where = [];

	$where[]    = '`to` = ' . (int) $to;

	if ( false === empty( $from ) ) {
		$where[] = '`from` = ' . (int) $from;
	}

	if ( true === $is_note ) {
		$where[] = 'note = 1';
	} else {
		$where[] = 'message = 1';
	}

	if ( NULL !== $visible_to_user ) {
		$where[] = 'visible_to_user = ' . (int) $visible_to_user;
	}

	if ( NULL !== $is_notification ) {
		$where[] = 'notification = ' . (int) $is_notification;
	}

	// Add where
	if ( false === empty( $where ) ) {
		$sql .= ' where ' . implode( ' and ', $where );
	}

	$sql .= ' order by created desc';

	if ( false === empty( $limit ) ) {
		$sql .= sprintf( ' limit %d, %d', $offset, $limit );
	}

	$results = $wpdb->get_results( $sql, ARRAY_A );

	ws_ls_cache_user_set( $to, $cache_key, $results, HOUR_IN_SECONDS );

	return $results;
}


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
 * @param bool $notification
 *
 * @return bool
 */
function ws_ls_messaging_db_add( $to, $from, $message, $is_note = false, $visible_to_user = false, $notification = false ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	if ( true === empty( $message ) ) {
		return false;
	}

	$data       = [ 'to'                => $to,
	                'from'              => $from,
	                'message_text'      => $message,
	                'visible_to_user'   => $visible_to_user,
	                'notification'      => $notification ];

	$formats    = [ '%d', '%d', '%s', '%d', '%d' ];

	$key                    = ( true === $is_note ) ? 'note' : 'message';
	$data[ $key ]           = 1;
	$formats[]              = '%d';

	global $wpdb;

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_MESSAGES, $data, $formats );

	return ! empty( $result );
}

/**
 * Delete a message
 *
 * @param $message_id
 *
 * @param bool $is_notification
 *
 * @return bool
 */
function ws_ls_messaging_db_delete( $message_id, $is_notification = false ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	if ( true === empty( $message_id ) ) {
		return false;
	}

	global $wpdb;

	$message = ws_ls_messaging_db_get( $message_id );

	if ( false === empty( $message[ 'to' ] ) ) {
		ws_ls_cache_user_delete( $message[ 'to' ] );
	}

	$data   = [ 'id' => $message_id ];
	$format = [ '%d' ];

	if ( true === $is_notification ) {
		$data[ 'notification' ] = 1 ;
		$format[]               = '%d';
	}

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_MESSAGES, $data, $format );

	ws_ls_delete_cache( 'message-' . (int) $message_id );

	return ! empty( $result );
}

/**
 * Delete a message
 *
 * @param $user_id
 *
 * @return bool
 */
function ws_ls_messaging_db_delete_all_for_user( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return false;
	}

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_MESSAGES,
		[ 'to' => $user_id ],
		[ '%d' ]
	);

	ws_ls_cache_user_delete( $user_id );

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

	$stats                              = [ 'notes-count' => NULL, 'notes-count-visible' => NULL ];
	$user_id                            = (int) $user_id;
	$stats[ 'notes-count' ]             = $wpdb->get_var( 'SELECT count( id ) FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES . ' WHERE `note` = 1 and `to` = ' . $user_id );
	$stats[ 'notes-count-visible' ]     = $wpdb->get_var( 'SELECT count( id ) FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES . ' WHERE `note` = 1 and `visible_to_user` = 1 and `to` = ' . $user_id );
	$stats[ 'notes-latest-id' ]         = $latest_note_id;
	$stats[ 'notes-latest-text' ]       = ( false === empty( $latest_note[ 'message_text' ] ) ) ? $latest_note[ 'message_text' ] : '';

	ws_ls_cache_user_set( $user_id, 'message-stats', $stats );

	return $stats;
}

/**
 * Delete all notifications entries older than 31 days
 *
 * @return mixed
 */
function ws_ls_messages_db_delete_old() {

	global $wpdb;
	return $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . WE_LS_MYSQL_MESSAGES . ' WHERE ( `created` < DATE_SUB(now(), INTERVAL 31 DAY ) and `notification` = 1 );' );
}
