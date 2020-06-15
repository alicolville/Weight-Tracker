<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_stats_run_cron() {

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

	do_action( 'wlt-hook-stats-running' );

	ws_ls_stats_refresh_summary_stats();
}
add_action( 'weight_loss_tracker_hourly' , 'ws_ls_stats_run_cron');
add_action( 'wlt-hook-data-all-deleted', 'ws_ls_stats_run_cron' );	// Delete stats if all user data has been deleted
add_action( 'wlt-hook-data-user-deleted', 'ws_ls_stats_run_cron' );	// Tidy up stats if a user deletes their entry

function ws_ls_stats_run_cron_for_first_time() {

	if( false == get_option('ws-ls-stats-run-for-first-time')) {
		ws_ls_stats_run_cron();
		update_option('ws-ls-stats-run-for-first-time', true);
	}
}
add_action('admin_init', 'ws_ls_stats_run_cron_for_first_time');

/**
 * Fetch from cache the summary stats
 * @return mixed|void
 */
function ws_ls_stats_get_summary_stats() {

	// Cached?
	if ( $stats = get_option( 'user-stats-summary' ) )	{
		return $stats;
	}

	// If not cached, generate, cache and return; this should only ever happen once!
	return ws_ls_stats_refresh_summary_stats();
}

/*
	Cache the latest weight difference count
*/
function ws_ls_stats_refresh_summary_stats() {

	$stats = array(
		'difference' => ws_ls_stats_sum_weight_difference(),
		'sum' => 0
	);

	update_option('user-stats-summary', $stats );

	return true;
}

/*
	Generate stats for user
*/
function ws_ls_stats_update_for_user($user_id) {

	if(is_numeric($user_id)) {

		$stats = array();

		$stats['user_id'] = $user_id;
		$stats['start_weight'] = ws_ls_entry_get_oldest_kg($user_id);
		$stats['recent_weight'] = ws_ls_entry_get_latest_kg( $user_id );
		$stats['weight_difference'] = (is_numeric($stats['start_weight']) && is_numeric($stats['recent_weight'])) ? $stats['recent_weight'] - $stats['start_weight'] : 0;
		$stats['last_update'] = current_time('mysql', 1);

		$entry_stats = ws_ls_db_entries_count($user_id, false);
		$stats['no_entries'] = $entry_stats['number-of-entries'];
        $stats['target_added'] = ($entry_stats['number-of-targets'] > 0) ? 1 : 0;

		global $wpdb;
		$wpdb->replace( $wpdb->prefix . WE_LS_USER_STATS_TABLENAME, $stats, array('%d', '%f', '%f', '%f', '%s') );

		ws_ls_stats_refresh_summary_stats();
	}

	return;
}

/**
 * If a user deletes an entry, update their stats.
 * @param $entry
 */
function ws_ls_stats_update_user_stats_on_entry_delete( $entry ) {

	if( false === empty( $entry[ 'user-id' ] ) ) {
		ws_ls_stats_update_for_user( $entry[ 'user-id' ] );
	}

}
add_action( 'wlt-hook-data-entry-deleted', 'ws_ls_stats_update_user_stats_on_entry_delete' );

function ws_ls_get_sum_of_weights_for_user($user_id)
{
	global $wpdb;
	$sql = $wpdb->prepare('Update ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME . ' set sum_of_weights = (Select sum(weight_weight)
							from ' . $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d) where user_id = %d', $user_id, $user_id);
	$wpdb->query($sql);
	return;
}
