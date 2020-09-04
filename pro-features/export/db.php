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

/**
 * Fetch an export
 *
 * @param $export_id
 *
 * @return bool
 */
function ws_ls_export_get( $export_id ) {

	if ( true === empty( $export_id ) ) {
		return false;
	}

	if ( $cache = ws_ls_cache_get( 'export-' . (int) $export_id ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT . ' WHERE id = %d', $export_id );

	$result = $wpdb->get_row( $sql, ARRAY_A );

	$result = ( false === empty( $result ) ) ? $result : false;

	ws_ls_cache_set( 'export-' . (int) $export_id, $result );

	return $result;
}
