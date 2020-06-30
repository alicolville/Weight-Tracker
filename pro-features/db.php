<?php

defined('ABSPATH') or die("Jog on!");

// -----------------------------------------------------------------
// Stats
// -----------------------------------------------------------------
/*
	Fetch records that haven't been updated in the last hour
*/
function ws_ls_stats_fetch_those_that_need_update() {

	global $wpdb;
	$table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$sql        = 'SELECT * FROM ' . $table_name . ' where last_update < DATE_SUB(NOW(),INTERVAL 6 HOUR) or last_update is null ORDER BY RAND() ';
	$rows       = $wpdb->get_results( $sql, ARRAY_A );

	if (is_array($rows) && count($rows) > 0) {
		return $rows;
	}

	return false;
}

/*
	Refresh total lost count
*/
function ws_ls_stats_sum_weight_difference() {

	global $wpdb;
	$result = $wpdb->get_var( 'SELECT sum(weight_difference) FROM ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME );

	if (!is_null($result)) {
		return floatval($result);
	}

	return false;
}

/*
	Copy user IDs of those that have entered weights into stats table (assuming they aren't they're already)
*/
function ws_ls_stats_insert_missing_user_ids_into_stats() {

	global $wpdb;
	$stats_table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$data_table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql = "INSERT INTO $stats_table_name (user_id, start_weight, recent_weight, weight_difference, last_update)
			Select distinct weight_user_id, NULL, NULL, NULL, NULL from $data_table_name where weight_user_id not in (Select user_id from $stats_table_name)";
	$wpdb->query($sql);
	return;
}

/*
	Copy user IDs of those that have entered weights into stats table (assuming they aren't they're already)
*/
function ws_ls_stats_remove_deleted_user_ids_from_stats() {

	global $wpdb;

	$stats_table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$data_table_name = $wpdb->prefix . 'users';

	$sql = "Delete from $stats_table_name Where user_id not in ( Select ID from $data_table_name )";

	$wpdb->query( $sql );

	return;
}

/*
	Select league table
*/
function ws_ls_stats_league_table_fetch($ignore_cache = false, $limit = 10, $losers_only = false, $order = 'asc') {

	$cache_key = 'ws-ls-stats-table-' . md5($ignore_cache . $limit . $losers_only . $order);

	// Return cache if found!
    if (!$ignore_cache && $cache = ws_ls_cache_get($cache_key)) {

		return $cache;
    }

	global $wpdb;

	$sql = 'SELECT * FROM ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;

	// -------------------------------------------------
	// Build where clause
	// -------------------------------------------------
	$where = array();

	// Select only users that have lost weight?
	if(true == ws_ls_force_bool_argument($losers_only)) {
		$where[] = 'weight_difference <= 0';
	}

	// Add where
	if (!empty($where)) {
		$sql .= ' where ' . implode(' and ', $where);
	}
	// -------------------------------------------------
	// Order
	// -------------------------------------------------
	$sql .= ' order by weight_difference ' . ((empty($order) || !in_array($order, array('desc', 'asc'))) ? 'desc' : $order);
	// -------------------------------------------------
	// Limit
	// -------------------------------------------------
	$sql .= $wpdb->prepare(
							' limit 0, %d',
							(empty($limit) || !is_numeric($limit)) ? 10 : (int) $limit
						);

	$results = $wpdb->get_results( $sql, ARRAY_A );

	if(!empty($results)) {
		ws_ls_cache_set($cache_key, $results, 5 * MINUTE_IN_SECONDS);
		return $results;
	}

	return false;
}
