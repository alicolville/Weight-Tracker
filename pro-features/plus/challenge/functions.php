<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Update the data for the given row
 *
 * @param $user_id
 * @param $challenge_id
 */
function ws_ls_challenges_data_update_row( $user_id, $challenge_id ) {

    if ( true === empty( $challenge_id ) || true === empty( $user_id ) ) {
        return;
    }

    // First, fetch the details of the challenge
    $challenge = ws_ls_challenges_get( $challenge_id );

    if ( true === empty( $challenge ) ) {
        return;
    }

    /*
     * TODO:
     *
     * 1) Fetch all weight entries for this user within the given time period of the challenge
     * 2) Fetch start and end weight. Calculate difference.
     * 3) Fetch the user's preferences. Using start and end weight, calculate starting BMI, ending BMI and difference.
     * 4) Count Weight Entries for given period
     * 5) Fetch and count Meal Tracker entries for this period
     * 6) Set last_processed date
     * 7) With the given data, update the record
     */
}

function t() {

    $t = ws_ls_challenges_data_update_row( 101,22 );
  //  print_r( $t );
    die;
}
add_action( 'init', 't' );