<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Process upto 50 users per challenge.
 */
function ws_ls_challenges_cron() {
    ws_ls_challenges_process();
}
add_action( 'weight_loss_tracker_hourly', 'ws_ls_challenges_cron' );



// TODO:

// Every day, run tidy up to remove old challenge data!