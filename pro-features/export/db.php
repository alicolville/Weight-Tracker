<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Insert a new export into DB
 * @param $options
 * @return bool
 */
function ws_ls_db_export_insert( $options ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	if ( true === empty( $options ) ) {
		return false;
	}

	global $wpdb;

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_EXPORT, [ 'options' => json_encode( $options ) ], [ '%s' ] );

	return ! empty( $result );

}
