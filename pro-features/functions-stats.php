<?php

defined('ABSPATH') or die('Jog on!');

/**
 * User records where their stats could do with a refresh
 */
function ws_ls_stats_run_cron() {

	// Copy across anyone missing from the stats table!
	ws_ls_db_stats_insert_missing_user_ids_into_stats();

	// Remove any old IDs from stats table
	ws_ls_db_stats_remove_deleted_user_ids_from_stats();

	$users_to_update = ws_ls_db_stats_fetch_those_that_need_update();

	// Fetch some records to process
	if( false === empty( $users_to_update ) ) {
		foreach ( $users_to_update as $user ) {
			ws_ls_stats_update_for_user( $user['user_id'] );
		}
	}

	do_action( 'wlt-hook-stats-running' );

	ws_ls_stats_refresh_summary_stats();
}
add_action( 'weight_loss_tracker_hourly' , 'ws_ls_stats_run_cron');
add_action( 'wlt-hook-data-all-deleted', 'ws_ls_stats_run_cron' );	// Delete stats if all user data has been deleted
add_action( 'wlt-hook-data-user-deleted', 'ws_ls_stats_run_cron' );	// Tidy up stats if a user deletes their entry

/**
 * Generate initial stats
 */
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
		'difference' => ws_ls_db_stats_sum_weight_difference(),
		'sum' => 0
	);

	update_option('user-stats-summary', $stats );

	return true;
}

/**
 * Generate stats for the given user
 * @param $user_id
 */
function ws_ls_stats_update_for_user( $user_id ) {

	if ( false === empty( $user_id ) ) {
		return;
	}

	$stats =  [];

	$stats[ 'user_id' ]           = $user_id;
	$stats[ 'start_weight' ]      = ws_ls_entry_get_oldest_kg( $user_id );
	$stats[ 'recent_weight' ]     = ws_ls_entry_get_latest_kg( $user_id );
	$stats[ 'weight_difference' ] = ( true === is_numeric( $stats[ 'start_weight' ] ) && true === is_numeric( $stats[ 'recent_weight' ] ) ) ? $stats[ 'recent_weight' ] - $stats[ 'start_weight' ] : 0;
	$stats[ 'last_update' ]       = current_time( 'mysql', 1 );

	$entry_stats                  = ws_ls_db_entries_count( $user_id, false );
	$stats[ 'no_entries' ]        = $entry_stats[ 'number-of-entries' ];
    $stats[ 'target_added' ]      = ( $entry_stats[ 'number-of-targets' ] > 0) ? 1 : 0;

	global $wpdb;
	$wpdb->replace( $wpdb->prefix . WE_LS_USER_STATS_TABLENAME, $stats, array('%d', '%f', '%f', '%f', '%s') );

		ws_ls_stats_refresh_summary_stats();

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
