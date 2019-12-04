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

    $data = []; $formats = [ '%d', '%f', '%f', '%f', '%d', '%f', '%f', '%f', '%s' ];

    // Weight Data
    $data[ 'count_wt_entries' ] = count( $weight_entries );
    $data[ 'weight_start' ]     = $weight_entries[ 0 ][ 'kg' ];
    $data[ 'weight_latest' ]    = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'kg' ];
    $data[ 'weight_diff' ]      = $data[ 'weight_latest' ] - $data[ 'weight_start' ];

    // Meal Tracker
    $data[ 'count_mt_entries' ] = 0;

    if ( true === wlt_yk_mt_is_active() ) {
        $data[ 'count_mt_entries' ] = 99; //TODO
    }

    // BMI
    $user_height = ws_ls_get_user_height( $user_id );

    $data[ 'bmi_start' ]        = ws_ls_calculate_bmi( $user_height, $data[ 'weight_start' ] );
    $data[ 'bmi_latest' ]       = ws_ls_calculate_bmi( $user_height, $data[ 'weight_latest' ] );
    $data[ 'bmi_diff' ]         = $data[ 'bmi_latest' ] - $data[ 'bmi_start' ];

    // Other
    $data[ 'last_processed' ]   = date( 'Y-m-d H:s');

    global $wpdb;

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA,
        $data,
        [ 'challenge_id' => $challenge_id, 'user_id' => $user_id ],
        $formats,
        [ '%d', '%d' ]
    );

    return ! empty( $result );

    /*
     * TODO:
     *

     * 5) Fetch and count Meal Tracker entries for this period

     */
}

function t() {

    if ( true === is_admin() ) {
        return;
    }

    ws_ls_challenges_add( 708, '2019-08-01', '2019-08-28' );

    // ws_ls_challenges_identify_entries( 708, '2019-08-01', '2019-08-28' );

    $t = ws_ls_challenges_data_update_row( 1,708 );
    print_r( $t );
    die;
}
add_action( 'init', 't' );