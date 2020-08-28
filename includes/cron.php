<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Add Cron schedules
 * @param $schedules
 *
 * @return mixed
 */
function ws_ls_schedules( $schedules ) {

	$schedules[ 'weight_loss_tracker_weekly' ] = [	    'interval'  => 604800,					// Every Week
														'display'   => 'Weight Tracker Weekly'
	];

    $schedules[ 'wlt-5-minutes' ] = [                   'interval' => 300,					    // Every 5 mins
												        'display' => 'Weight Tracker - 5 Minutes'
    ];

	return $schedules;
}
add_action('cron_schedules', 'ws_ls_schedules');


/**
 * Check if the license is still valid
 */
function ws_ls_licences_cron() {

	$existing_license = ws_ls_license();

	ws_ls_license_apply( $existing_license );
}
add_action( 'weight_loss_tracker_hourly', 'ws_ls_licences_cron' );

/**
 * Delete older log files
 */
function ws_ls_logs_delete_cron() {

    ws_ls_log_delete_old();

}
add_action( 'weight_loss_tracker_hourly', 'ws_ls_logs_delete_cron' );

