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

    $weight_entries = ws_ls_challenges_get_weight_entries( $user_id, $challenge[ 'start_date' ], $challenge[ 'end_date' ] );

    $data = []; $formats = [ '%d', '%f', '%f' ];

    // Weight Data
    $data[ 'count_wt_entries' ] = count( $weight_entries );
    $data[ 'weight_start' ]     = $weight_entries[ 0 ][ 'kg' ];
    $data[ 'weight_latest' ]    = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'kg' ];

    // BMI
    $user_height = ws_ls_get_user_height( $user_id );

    var_dump($user_height);

    //ws_ls_calculate_bmi($cm, $kg)

    print_r($data);

    /*
     * TODO:
     *

     * 3) Fetch the user's preferences. Using start and end weight, calculate starting BMI, ending BMI and difference.
     * 4) Count Weight Entries for given period
     * 5) Fetch and count Meal Tracker entries for this period
     * 6) Set last_processed date
     * 7) With the given data, update the record
     */
}

function t() {

    ws_ls_challenges_add( 708, '2019-08-01', '2019-08-28' );

    // ws_ls_challenges_identify_entries( 708, '2019-08-01', '2019-08-28' );

    $t = ws_ls_challenges_data_update_row( 1,708 );
    print_r( $t );
    die;
}
add_action( 'init', 't' );