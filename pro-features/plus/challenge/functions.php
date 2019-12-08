<?php

defined('ABSPATH') or die("Jog on!");

/**
 * For each active challenge, do the following:
 *
 *   - Identify and add user's that have made one or more weight entries within the given challenge period.
 *   - Refresh stat data for each user that requires it (i.e. last_processed) is null
 *
 * @param int $max_entries_per_challenge_to_process
 */
function ws_ls_challenges_process( $max_entries_per_challenge_to_process = 50 ) {

    // Fetch all challenges to consider for processing
    $challenges = ws_ls_challenges();

    if ( true === empty( $challenges ) ) {
        return false;
    }

    foreach ( $challenges as $challenge ) {

        // Ensure all user's that have one entry or more within the time period are included in the challenge.
        ws_ls_challenges_identify_entries( $challenge[ 'id' ], $challenge[ 'start_date' ], $challenge[ 'end_date' ] );

        // Fetch user's for the challenge that need their stats updated
        $users = ws_ls_challenges_data_awaiting_processing( $challenge[ 'id' ], (int) $max_entries_per_challenge_to_process );

        if ( true === empty( $users ) ) {
            continue;
        }

        foreach ( $users as $user ) {
            ws_ls_challenges_data_update_row( $user[ 'user_id' ], $user[ 'challenge_id' ] );
        }

        return count( $users );
    }

    return false;
}

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

    $data = []; $formats = [ '%d', '%s', '%s', '%f', '%f', '%f', '%d', '%f', '%f', '%f', '%f', '%d', '%d', '%d', '%s' ];

    // Weight Data
    $data[ 'count_wt_entries' ] = count( $weight_entries );
    $data[ 'date_start' ]       = $weight_entries[ 0 ][ 'weight_date' ];
    $data[ 'date_latest' ]      = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'weight_date' ];
    $data[ 'weight_start' ]     = $weight_entries[ 0 ][ 'kg' ];
    $data[ 'weight_latest' ]    = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'kg' ];
    $data[ 'weight_diff' ]      = $data[ 'weight_latest' ] - $data[ 'weight_start' ];

    // Meal Tracker
    $data[ 'count_mt_entries' ] = 0;

    if ( true === wlt_yk_mt_is_active() ) {

        $meals =  ws_ls_challenges_get_meal_tracker_entries( $user_id, $challenge[ 'start_date' ], $challenge[ 'end_date' ] );

        $data[ 'count_mt_entries' ] = ( false === empty( $meals ) ) ? count( $meals ) : 0;
    }

    // BMI
    $data[ 'height' ]           = ws_ls_get_user_height( $user_id );
    $data[ 'bmi_start' ]        = ws_ls_calculate_bmi( $data[ 'height' ], $data[ 'weight_start' ] );
    $data[ 'bmi_latest' ]       = ws_ls_calculate_bmi( $data[ 'height' ], $data[ 'weight_latest' ] );
    $data[ 'bmi_diff' ]         = $data[ 'bmi_latest' ] - $data[ 'bmi_start' ];

    // Handy user preferences
    $data[ 'gender' ]   = ws_ls_get_user_setting( $field = 'gender', $user_id );
    $data[ 'age' ]      = ws_ls_get_age_from_dob( $user_id );

    $data[ 'group_id' ]    = NULL;

    // Group
    if ( true === ws_ls_groups_enabled() ) {

        $groups = ws_ls_groups_user( $user_id );

        if ( false === empty( $groups ) ) {
            $data[ 'group_id' ] = $groups[ 0 ][ 'id' ];
        }
    }

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
}

function t() {

    if ( true === is_admin() ) {
        return;
    }

    $t = ws_ls_challenges_process();

    var_dump( $t );

 //  ws_ls_challenges_add( 708, '2019-08-01', '2019-08-28' );

    // ws_ls_challenges_identify_entries( 708, '2019-08-01', '2019-08-28' );

//    $t = ws_ls_challenges_data_update_row( 1,708 );
  //  print_r( $t );
    die;
}
add_action( 'init', 't' );