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

	return ( false !== $result ) ? $result = $wpdb->insert_id : false;

}

/**
 * Fetch an export
 *
 * @param $export_id
 *
 * @return bool
 */
function ws_ls_db_export_criteria_get( $export_id ) {

	if ( true === empty( $export_id ) ) {
		return false;
	}

	if ( $cache = ws_ls_cache_user_get(  'exports', (int) $export_id ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT . ' WHERE id = %d', $export_id );

	$result = $wpdb->get_row( $sql, ARRAY_A );

	$result = ( false === empty( $result ) ) ? $result : false;

	ws_ls_cache_user_set( 'exports', (int) $export_id, $result );

	return $result;
}

/**
 * Identify which records should go into a report
 * @param $export_id
 */
function ws_ls_db_export_identify_weight_entries( $export_id ) {

	//TODO

	global $wpdb;


	$sql = 'INSERT INTO ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT . ' ( export_id, entry_id )
                            SELECT ' . (int) $export_id . ', id
                            FROM ' . $wpdb->prefix . WE_LS_TABLENAME;

	$sql = $wpdb->query( $sql );

}

/**
 * Count number of weight entries for a report
 * @param $export_id
 *
 * @return bool|string|null
 */
function ws_ls_db_export_report_count( $export_id ) {

	$cache_key = sprintf( 'count-%d', $export_id );

	if ( $cache = ws_ls_cache_user_get(  'exports', $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT count(id) FROM ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT .
	                        ' WHERE export_id = %d', $export_id );

	$result = $wpdb->get_var( $sql );

	ws_ls_cache_user_set( 'exports', $cache_key, $result );

	return $result;
}

/**
 * Count remaining rows to processed
 * @param $export_id
 *
 * @return string|null
 */
function ws_ls_db_export_report_to_be_processed_count( $export_id ) {

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT count(id) FROM ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT .
	                       ' WHERE export_id = %d and completed = 0', $export_id );

	return (int) $wpdb->get_var( $sql );
}

/**
 * Fetch some rows to process
 * @param int $limit
 *
 * @return array|object
 */
function ws_ls_db_export_report_incomplete_rows( $export_id, $limit = 1 ) {

	global $wpdb;

	$sql = $wpdb->prepare(  'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT .
	                        ' where completed = 0 and export_id = %d limit 0, %d', $export_id, $limit );

	$result = $wpdb->get_results( $sql, ARRAY_A );

	return ( false === empty( $result ) ) ? $result : [];
}

/**
 * Mark rows as completed
 * @param $export_id
 * @param $ids
 */
function ws_ls_db_export_report_complete_rows_mark( $export_id, $ids ) {

	global $wpdb;

	$sql = $wpdb->prepare( 'UPDATE ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT . ' set completed = 1 WHERE
                            export_id = %d and id in ( ' . implode( ',', $ids ) . ' )', $export_id );

	return $wpdb->query( $sql );
}

/**
 * Update export criteria step
 * @param $export_id
 * @param int $step
 *
 * @return bool|false|int
 */
function ws_ls_db_export_criteria_step( $export_id, $step = 0 ) {

	global $wpdb;

	$result = $wpdb->update(    $wpdb->prefix . WE_LS_MYSQL_EXPORT,
								[ 'step' => $step ],
								[ 'id' => $export_id ],
								[ '%d' ],
								[ '%d' ] );

	ws_ls_cache_user_delete( 'exports', (int) $export_id );

	return $result;
}

/**
 * Update number of record count
 * @param $export_id
 * @param int $step
 *
 * @return bool|false|int
 */
function ws_ls_db_export_criteria_count( $export_id, $count = 0 ) {

	global $wpdb;

	$result = $wpdb->update(    $wpdb->prefix . WE_LS_MYSQL_EXPORT,
		[ 'number_of_records' => $count ],
		[ 'id' => $export_id ],
		[ '%d' ],
		[ '%d' ] );

	ws_ls_cache_user_delete( 'exports', (int) $export_id );

	return $result;
}

/**
 * Fetch export criteria
 * @param int $limit
 *
 * @return array|bool|object|null
 */
function ws_ls_db_export_criteria_all( $limit = 10 ) {

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_EXPORT . ' order by `created` desc limit 0, %d ', $limit );

	$result = $wpdb->get_results( $sql, ARRAY_A );

	$result = ( false === empty( $result ) ) ? $result : [];

	if ( false === empty( $result ) ) {
		$result = array_map( 'ws_ls_db_export_criteria_prep', $result );
	}

	return $result;
}

/**
 * Prep Export Criteria object
 * @param $export
 *
 * @return mixed
 */
function ws_ls_db_export_criteria_prep( $export ) {

	$export[ 'options' ] = json_decode( $export[ 'options' ], true );
	return $export;
}
