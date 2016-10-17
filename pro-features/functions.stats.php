<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_stats_run_cron() {

	// If disabled, don't bother
	if(WE_LS_DISABLE_STATS_CRON) {
		return;
	}

	// Copy across anyone missing from the stats table!
	ws_ls_stats_insert_missing_user_ids_into_stats();

	// Fetch some records to process
	if($users = ws_ls_stats_fetch_those_that_need_update()) {



	}
}

function ws_ls_stats_fetch_those_that_need_update($max = 50) {

	global $wpdb;
	$table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$sql = 'SELECT * FROM ' . $table_name . ' where last_update < DATE_SUB(NOW(),INTERVAL 1 HOUR) or last_update is null ORDER BY RAND() ';
	$rows = $wpdb->get_results( $sql );

	if (is_array($rows) && count($rows) > 0) {
		return $rows;
	}

	return false;
}

function ws_ls_stats_insert_missing_user_ids_into_stats() {

	global $wpdb;
	$stats_table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$data_table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql = "INSERT INTO $stats_table_name (user_id, start_weight, recent_weight, total_weight_lost, last_update)
			Select weight_user_id, NULL, NULL, NULL, NULL from $data_table_name where ID not in (Select user_id from $stats_table_name)";
	$wpdb->query($sql);
	var_dump($sql);
	return;

}
