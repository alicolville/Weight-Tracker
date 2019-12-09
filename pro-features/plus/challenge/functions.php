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
function ws_ls_challenges_process( $user_id = NULL,
                                       $identify_new_entries = true,
                                            $max_entries_per_challenge_to_process = 50 ) {

    // Fetch all challenges to consider for processing
    $challenges = ws_ls_challenges();

    if ( true === empty( $challenges ) ) {
        return false;
    }

    foreach ( $challenges as $challenge ) {

        // Ensure all user's that have one entry or more within the time period are included in the challenge.
        if ( true === $identify_new_entries ) {
            ws_ls_challenges_identify_entries( $challenge[ 'id' ], $challenge[ 'start_date' ], $challenge[ 'end_date' ] );
        }

        // Fetch user's for the challenge that need their stats updated
        $users = ws_ls_challenges_data_awaiting_processing( $challenge[ 'id' ],  $user_id, (int) $max_entries_per_challenge_to_process );

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

    $data = []; $formats = [ '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%d', '%f', '%f', '%f', '%f', '%d', '%d', '%d', '%d', '%s' ];

    // Weight Data
    $data[ 'count_wt_entries' ]     = count( $weight_entries );
    $data[ 'date_start' ]           = $weight_entries[ 0 ][ 'weight_date' ];
    $data[ 'date_latest' ]          = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'weight_date' ];
    $data[ 'weight_start' ]         = $weight_entries[ 0 ][ 'kg' ];
    $data[ 'weight_latest' ]        = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'kg' ];
    $data[ 'weight_diff' ]          = $data[ 'weight_latest' ] - $data[ 'weight_start' ];
    $data[ 'weight_percentage' ]    = ws_ls_calculate_percentage_difference_as_number( $data[ 'weight_start' ], $data[ 'weight_latest' ] );

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
    $data[ 'gender' ]       = ws_ls_get_user_setting( 'gender', $user_id );
    $data[ 'age' ]          = ws_ls_get_age_from_dob( $user_id );
	$data[ 'opted_in' ]     = ws_ls_get_user_setting( 'challenge_opt_in', $user_id );

	$data[ 'group_id' ]    = NULL;

    // Group
    if ( true === ws_ls_groups_enabled() ) {

        $groups = ws_ls_groups_user( $user_id );

        if ( false === empty( $groups ) ) {
            $data[ 'group_id' ] = $groups[ 0 ][ 'id' ];
        }
    }

    // Other
    $data[ 'last_processed' ]   = date("Y-m-d H:i:s");

    global $wpdb;

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA,
        $data,
        [ 'challenge_id' => $challenge_id, 'user_id' => $user_id ],
        $formats,
        [ '%d', '%d' ]
    );

    return ! empty( $result );
}

/**
 * Display all user's entries in a data table
 * @param $args
 */
function ws_ls_table_challenge( $args ) {

    $args = wp_parse_args( $args, [
        'challenge-id'  => NULL
    ]);

    if ( NULL === $args[ 'challenge-id' ] ) {
        return;
    }

    ?>
    <table class="ws-ls-footable ws-ls-footable-basic widefat" data-paging="true" data-sorting="true" data-state="true">
        <thead>
        <tr>
            <th data-type="date" data-format-string="D/M/Y"><?php echo __( 'Date', WS_LS_SLUG ); ?></th>
            <th data-type="text" data-breakpoints="sm"  data-visible="<?php echo ( true == $args[ 'show-username' ] ) ? 'true' : 'false'; ?>">
                <?php echo __( 'User', WS_LS_SLUG ); ?>
            </th>
            <th data-breakpoints="xs" data-type="number"><?php echo __( 'Calories Allowed', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="sm" data-type="number"><?php echo __( 'Calories Used', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-type="number"><?php echo __( 'Calories Remaining', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-sortable="false" width="20"><?php echo __( 'Percentage Used', WS_LS_SLUG ); ?></th>
            <th></th>
        </tr>
        </thead>
        <?php
        foreach ( $args[ 'entries' ] as $entry ) {

            $class = ( $entry[ 'calories_used' ] > $entry[ 'calories_allowed' ] ) ? 'ws-ls-error' : 'ws-ls-ok';

            printf ( '    <tr class="%6$s">
                                                <td>%1$s</td>
                                                <td>%8$s</td>
                                                <td class="ws-ls-blur">%2$s</td>
                                                <td class="ws-ls-blur">%3$s</td>
                                                <td class="ws-ls-blur">%4$s</td>
                                                <td class="ws-ls-blur">%5$s</td>
                                                <td><a href="%7$s" class="btn btn-default footable-edit"><i class="fa fa-eye"></i></a></td>
                                            </tr>',
                yk_mt_date_format( $entry['date' ] ),
                $entry[ 'calories_allowed' ],
                $entry[ 'calories_used' ],
                $entry[ 'calories_remaining' ],
                $entry[ 'percentage_used' ] . '%',
                $class,
                yk_mt_link_admin_page_entry( $entry[ 'id' ] ),
                yk_mt_link_profile_display_name_link( $entry[ 'user_id' ] )
            );
        }
        ?>
        </tbody>
    </table>
    <?php
}

function t() {

    if ( true === is_admin() ) {
        return;
    }

    ws_ls_set_user_preference_simple( 'challenge_opt_in', 2, 13);

   // ws_ls_challenges_data_last_processed_reset( 1 );

  //  $t = ws_ls_challenges_process();
  //  ws_ls_challenges_process( 1 );
    //var_dump( $t );

 //  ws_ls_challenges_add( 708, '2019-08-01', '2019-08-28' );

    // ws_ls_challenges_identify_entries( 708, '2019-08-01', '2019-08-28' );

   // $t = ws_ls_groups_user( 1 );
    //print_r( $t );
    die;
}
//add_action( 'init', 't' );