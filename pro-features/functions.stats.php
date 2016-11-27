<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_stats_run_cron() {

	// If disabled, don't bother
	if(WE_LS_DISABLE_USER_STATS) {
		return;
	}

	// Copy across anyone missing from the stats table!
	ws_ls_stats_insert_missing_user_ids_into_stats();

	// Remove any old IDs from stats table
	ws_ls_stats_remove_deleted_user_ids_from_stats();

	// Fetch some records to process
	if($users = ws_ls_stats_fetch_those_that_need_update()) {
		foreach ($users as $user) {
			ws_ls_stats_update_for_user($user['user_id']);
		}
	}

	ws_ls_stats_refresh_summary_stats();
}
add_action(WE_LS_CRON_NAME, 'ws_ls_stats_run_cron');

/*
	Fetch from cache the summary stats
*/
function ws_ls_stats_get_summary_stats() {

	// If disabled, don't bother
	if(WE_LS_DISABLE_USER_STATS) {
		return;
	}

	// Cached?
	if ($stats = get_option(WE_LS_CACHE_KEY_STATS_SUMMARY))	{
		return $stats;
	}

	// If not cached, generate, cache and return; this should only ever happen once!
	return ws_ls_stats_refresh_summary_stats();
}

/*
	Cache the latest weight difference count
*/
function ws_ls_stats_refresh_summary_stats() {

	// If disabled, don't bother
	if(WE_LS_DISABLE_USER_STATS) {
		return;
	}

	$difference = ws_ls_stats_sum_weight_difference();

	update_option(WE_LS_CACHE_KEY_STATS_SUMMARY, $difference);

	return $difference;
}

/*
	Generate stats for user
*/
function ws_ls_stats_update_for_user($user_id) {

	// If disabled, don't bother
	if(WE_LS_DISABLE_USER_STATS) {
		return;
	}

	if(is_numeric($user_id)) {

		$stats = [];

		$stats['user_id'] = $user_id;
		$stats['start_weight'] = ws_ls_get_weight_extreme($user_id);
		$stats['recent_weight'] = ws_ls_get_weight_extreme($user_id, true);
		$stats['weight_difference'] = (is_numeric($stats['start_weight']) && is_numeric($stats['recent_weight'])) ? $stats['recent_weight'] - $stats['start_weight'] : 0;
		$stats['last_update'] = current_time('mysql', 1);

		global $wpdb;
		$wpdb->replace( $wpdb->prefix . WE_LS_USER_STATS_TABLENAME, $stats, ['%d', '%f', '%f', '%f', '%s'] );

		ws_ls_stats_refresh_summary_stats();
	}

	return;
}
