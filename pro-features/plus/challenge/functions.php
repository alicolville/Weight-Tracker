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
 * Fetch an age range
 *
 * @param $key
 *
 * @return array
 */
function ws_ls_challenges_age_range_get( $key ) {

    if ( true === empty( $key ) ) {
        return NULL;
    }

	$key = (int) $key;

	$ranges = ws_ls_age_ranges();

	return ( true === array_key_exists( $key, $ranges ) ) ? $ranges[ $key ] : NULL;
}

/**
 * Display all challenges
 */
function ws_ls_challenges_table() {

    ws_ls_data_table_enqueue_scripts();

    ?>
    <table class="ws-ls-footable ws-ls-footable-basic widefat" data-paging="true" data-sorting="true" data-state="true">
        <thead>
        <tr>
            <th data-type="number"><?php echo __( 'ID', WS_LS_SLUG ); ?></th>
            <th data-type="text"><?php echo __( 'Name', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-type="date" data-format-string="D/M/Y"><?php echo __( 'Start Date', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-type="date" data-format-string="D/M/Y"><?php echo __( 'End Date', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-type="number"><?php echo __( 'No. entries', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-type="number"><?php echo __( 'Entries to process', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs" data-type="text"><?php echo __( 'Closed', WS_LS_SLUG ); ?></th>
            <th data-breakpoints="xs"></th>
        </tr>
        </thead>
        <?php
            foreach ( ws_ls_challenges( false ) as $challenge ) {

                $stats = ws_ls_challenges_stats( $challenge[ 'id' ] );

                printf ( '    <tr>
                                                    <td>%1$d</td>
                                                    <td>%2$s</td>
                                                    <td>%3$s</td>
                                                    <td>%4$s</td>
                                                    <td>%5$d</td>
                                                    <td>%6$d</td>
                                                    <td>%7$s</td>
                                                    <td>
                                                        <a href="%11$s" class="btn btn-default" title="%8$s"><i class="fa fa-eye"></i></a>
                                                        <a href="%12$s" class="btn btn-default" title="%9$s"><i class="fa fa-lock"></i></a> 
                                                        <a href="%13$s" class="btn btn-default" title="%10$s"><i class="fa fa-trash"></i></a>    
                                                    </td>
                                                </tr>',
                    $challenge[ 'id' ],
                    esc_html( $challenge[ 'name' ] ),
                    ws_ls_iso_date_into_correct_format( $challenge[ 'start_date' ] ),
                    ws_ls_iso_date_into_correct_format( $challenge[ 'end_date' ] ),
                    $stats[ 'count' ],
                    $stats[ 'to-be-processed' ],
                    ( 1 === (int) $challenge[ 'enabled' ] ) ? __( 'No', WS_LS_SLUG ) : __( 'Yes', WS_LS_SLUG ),
                    __( 'View challenge data', WS_LS_SLUG ),
                    __( 'Close challenge', WS_LS_SLUG ),
                    __( 'Delete challenge', WS_LS_SLUG ),
                    ws_ls_challenge_link( $challenge[ 'id' ] ),
                    ws_ls_challenge_link( $challenge[ 'id' ], 'close' ),
                    ws_ls_challenge_link( $challenge[ 'id' ], 'delete' )
                );
            }
        ?>
        </tbody>
    </table>
    <p>
        <p><?php echo __( 'Note: ', WS_LS_SLUG ); ?></p>
    </p>
    <?php
}

/**
 * Link to challenge view / edit
 * @param $challenge_id
 * @param string $mode
 * @return string
 */
function ws_ls_challenge_link( $challenge_id, $mode = 'view' ) {

    if ( false === is_numeric( $challenge_id ) ) {
        return '#';
    }

    $url = sprintf( 'admin.php?page=ws-ls-challenges&mode=%1$s&challenge-id=%2$d', $mode, $challenge_id );

    $url = admin_url( $url );

    return $url;
}

function t() {

    if ( true === is_admin() ) {
        return;
    }
    ws_ls_challenges_process();
    die;
}
add_action( 'init', 't' );