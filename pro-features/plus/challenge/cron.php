<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Process upto 50 users per challenge.
 */
function ws_ls_challenges_cron() {

    if ( false === ws_ls_challenges_is_enabled() ) {
        return;
    }

    ws_ls_challenges_process();
}
add_action( 'weight_loss_tracker_hourly', 'ws_ls_challenges_cron' );
