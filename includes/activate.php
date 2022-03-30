<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Set up cron jobs upon plugin activation
 */
function ws_ls_activate() {

    // Register user stats / license check cron job
    if ( !wp_next_scheduled( 'weight_loss_tracker_hourly' ) ) {
        wp_schedule_event( time(), 'hourly', 'weight_loss_tracker_hourly' );

    }

	if ( !wp_next_scheduled( 'weight_loss_tracker_daily' ) ) {
		wp_schedule_event( time(), 'daily', 'weight_loss_tracker_daily' );
	}
}

/**
 * Remove cron jobs when plugin deactivated
 */
function ws_ls_deactivate() {

	// Remove cron jobs
	wp_clear_scheduled_hook( 'weight_loss_tracker_hourly' );
    wp_clear_scheduled_hook( 'weight_loss_tracker_daily' );
}

/**
 * When the plugin has been installed or upgraded, then update DB and do relevant tasks.
 */
function ws_ls_upgrade() {

	if( true === update_option('ws-ls-version-number', WE_LS_CURRENT_VERSION ) ) {

		ws_ls_db_create_core_tables();
		ws_ls_activate();

		// This will force all stat entries to be recreated.
		ws_ls_db_stats_clear_last_updated_date();

		// Check the license is still valid
		ws_ls_licences_cron();

		do_action( 'ws-ls-plugin-updated' );
	}
}
add_action('admin_init', 'ws_ls_upgrade');
