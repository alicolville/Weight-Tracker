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
 * Return difference between two dayes in weels
 * @param $date1
 * @param $date2
 * @return float
 */
function ws_ls_challenges_diff_between_dates_in_weeks( $date1, $date2 ) {

    if( $date1 > $date2 ) {
        return ws_ls_challenges_diff_between_dates_in_weeks( $date2, $date1 );
    }

    $first = DateTime::createFromFormat( 'Y-m-d h:i:s', $date1 );
    $second = DateTime::createFromFormat( 'Y-m-d h:i:s', $date2 );

    return floor($first->diff( $second )->days/7 );
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

    $data = []; $formats = [ '%d', '%s', '%s', '%d', '%f', '%f', '%f', '%f', '%d', '%f', '%f', '%f', '%f', '%d', '%d', '%d', '%d', '%s' ];

    // Weight Data
    $data[ 'count_wt_entries' ]         = count( $weight_entries );
    $data[ 'date_start' ]               = $weight_entries[ 0 ][ 'weight_date' ];
    $data[ 'date_latest' ]              = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'weight_date' ];
    $data[ 'count_wt_entries_week' ]    = ws_ls_challenges_diff_between_dates_in_weeks( $data[ 'date_start' ], $data[ 'date_latest' ] );
    $data[ 'weight_start' ]             = $weight_entries[ 0 ][ 'kg' ];
    $data[ 'weight_latest' ]            = $weight_entries[ $data[ 'count_wt_entries' ] - 1 ][ 'kg' ];
    $data[ 'weight_diff' ]              = number_format( $data[ 'weight_latest' ] - $data[ 'weight_start' ], 3 );
    $data[ 'weight_percentage' ]        = ws_ls_calculate_percentage_difference_as_number( $data[ 'weight_start' ], $data[ 'weight_latest' ] );

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
    $data[ 'bmi_diff' ]         = number_format( $data[ 'bmi_latest' ] - $data[ 'bmi_start' ], 1 );

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
                <th data-type="number"><?php echo __( 'ID', WE_LS_SLUG ); ?></th>
                <th data-type="text"><?php echo __( 'Name', WE_LS_SLUG ); ?></th>
                <th data-breakpoints="xs" data-type="date" data-format-string="D/M/Y"><?php echo __( 'Start Date', WE_LS_SLUG ); ?></th>
                <th data-breakpoints="xs" data-type="date" data-format-string="D/M/Y"><?php echo __( 'End Date', WE_LS_SLUG ); ?></th>
                <th data-breakpoints="xs" data-type="number"><?php echo __( 'No. entries', WE_LS_SLUG ); ?></th>
                <th data-breakpoints="xs" data-type="number"><?php echo __( 'Entries to process', WE_LS_SLUG ); ?></th>
                <th data-breakpoints="xs" data-type="text"><?php echo __( 'Closed', WE_LS_SLUG ); ?></th>
                <th data-breakpoints="xs"></th>
            </tr>
        </thead>
        <tbody>
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
                        ( 1 === (int) $challenge[ 'enabled' ] ) ? __( 'No', WE_LS_SLUG ) : __( 'Yes', WE_LS_SLUG ),
                        __( 'View challenge data', WE_LS_SLUG ),
                        __( 'Close challenge', WE_LS_SLUG ),
                        __( 'Delete challenge', WE_LS_SLUG ),
                        ws_ls_challenge_link( $challenge[ 'id' ] ),
                        ws_ls_challenge_link( $challenge[ 'id' ], 'close' ),
                        ws_ls_challenge_link( $challenge[ 'id' ], 'delete' )
                    );
                }
            ?>
        </tbody>
    </table>
    <?php
}

/**
 * Return all columns
 * @return array
 */
function ws_ls_challenges_entry_columns_defaults() {

    return [
        'weight_diff'           => [ 'title' => __( 'Total Weight Loss', WE_LS_SLUG ), 'type' => 'text' ],
        'bmi_diff'              => [ 'title' => __( 'BMI Change', WE_LS_SLUG ) ],
        'weight_percentage'     => [ 'title' => __( '% Body Weight', WE_LS_SLUG ) ],
        'count_mt_entries'      => [ 'title' => __( 'Meal Tracker Streaks (Days)', WE_LS_SLUG ) ],
        'count_wt_entries_week' => [ 'title' => __( 'Weight Tracker Streaks (Weeks)', WE_LS_SLUG ) ]
    ];
}

/**
 * Fetch a column name
 * @param $key
 * @return mixed|string
 */
function ws_ls_challenges_entry_column_name( $key ) {

    $cols = ws_ls_challenges_entry_columns_defaults();

    return ( true === array_key_exists( $key, $cols ) ) ? $cols[ $key ][ 'title' ] : '-';
}

/**
 * Fetch columns to display
 * @param null $args
 * @return array
 */
function ws_ls_challenges_entry_columns( $args = NULL ) {

    $cols = ws_ls_challenges_entry_columns_defaults();

    if ( false === wlt_yk_mt_is_active() ) {
        unset( $cols[ 'count_mt_entries' ] );
    }

    // Do we need to filter out any cols?
    if ( false === empty( $args[ 'columns' ] ) && 'all' !== $args[ 'columns' ] ) {

        $columns_wanted = explode( ',', $args[ 'columns' ] );

        foreach ( $cols as $key => $details ) {
            if ( false === in_array( $key, $columns_wanted ) ) {
                unset( $cols[ $key ] );
            }
        }

    }

    return $cols;
}

/**
 * Display all entries for challenges
 * @param $args
 */
function ws_ls_challenges_view_entries( $args ) {

    ws_ls_data_table_enqueue_scripts();

    $columns = ws_ls_challenges_entry_columns( $args );

    $args = wp_parse_args( $args, [
                                        'id'                    => NULL,
                                        'opted-in'              => ( 1 === ws_ls_querystring_value( 'filter-opt-in', true,  1 ) ),
                                        'age-range'             =>  ws_ls_querystring_value( 'filter-age-range', true,  0 ),
                                        'gender'                =>  ws_ls_querystring_value( 'filter-gender', true,  0 ),
                                        'group-id'              =>  ws_ls_querystring_value( 'filter-group-id', true,  NULL ),
                                        'show-filters'          => true,
                                        'sums-and-averages'     => true
    ]);

    $data = ws_ls_challenges_data( $args );

    $html = '';

	if ( true === filter_var( $args[ 'show-filters' ],FILTER_VALIDATE_BOOLEAN ) ) {
		$html .=  ws_ls_challenges_show_filters();
	}

    $html .= sprintf( '
                                <table class="ws-ls-footable ws-ls-footable-basic widefat" data-paging="true" data-sorting="true" data-state="true" data-paging-size="10">
                                    <thead>
                                        <tr>
                                            <th data-type="text">%s</th>',
                                __( 'Name', WE_LS_SLUG )
    );

    foreach ( $columns as $key => $details ) {
        $html .= sprintf( '<th data-breakpoints="xs" data-type="%1$s">%2$s</th>',
                                ( false === empty( $details[ 'type' ] ) ) ? $details[ 'type' ] : 'number',
                                esc_html( $details[ 'title' ] )
        );
    }

    $html .=        '</tr>
                    </thead>
                <tbody>';

    foreach( $data as $row ) {

        $name = ( true === is_admin() ) ?
                    sprintf( '<a href="%1$s">%2$s</a>', ws_ls_get_link_to_user_profile( $row[ 'user_id' ] ),  $row[ 'display_name' ] ) :
                        $row[ 'display_name' ];

        $html .= sprintf( '<tr><td>%s</td>', $name );

        foreach ( $columns as $key => $details ) {

            $value = $row[ $key ];

	        if ( 'weight_diff' == $key ) {
                $row[ 'weight_diff' ] = ws_ls_convert_kg_into_relevant_weight_String( $row[ 'weight_diff' ] );
            }

            $html .= sprintf( '    <td  data-sort-value="%1$s">
                                    %2$s
                                </td>',
                                $value,
                                esc_html( $row[ $key ] )
            );

        }

        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    // Include counts and averages?
    if ( true === filter_var( $args[ 'sums-and-averages' ],FILTER_VALIDATE_BOOLEAN ) ) {

        $counts         = [];
        $html_columns   = '';
        $html_sums      = '';
        $html_averages  = '';

        $average_data = array_filter( wp_list_pluck( $data, 'count_wt_entries' ) , function( $value ) {
            return (int) $value > 1;
        });

        $count_entries_more_than_one_wt_entry = count( $average_data );

        // Loop through each column we're interested in, sum the column and then divide by count
	    foreach ( $columns as $key => $details ) {

	        $column_data                = wp_list_pluck( $data, $key );
		    $counts[ $key ]             = [ 'count' => count( $column_data ), 'count-one-or-more-wt-entries' => $count_entries_more_than_one_wt_entry, 'sum' => array_sum( $column_data ) ];
		    $counts[ $key ][ 'average'] = ( 0 !== $counts[ $key ][ 'count-one-or-more-wt-entries'] ) ?
                                            $counts[ $key ][ 'sum'] / $counts[ $key ][ 'count-one-or-more-wt-entries'] : 0;

            $html_columns   .= sprintf( '<th data-breakpoints="xs" data-type="string">%s</th>', esc_html( ws_ls_challenges_entry_column_name( $key ) ) );

            switch ( $key ){
                case 'weight_diff':
                    $html_sums      .= sprintf( '<td>%s</td>', ws_ls_convert_kg_into_relevant_weight_String( $counts[ $key ][ 'sum' ], true ) );
                    $html_averages  .= sprintf( '<td>%s</td>', ws_ls_convert_kg_into_relevant_weight_String( $counts[ $key ][ 'average' ] ) );
                    break;
                case 'count_mt_entries':
                case 'count_wt_entries_week':
                    $html_sums      .= sprintf( '<td>%d</td>', $counts[ $key ][ 'sum' ] );
                    $html_averages  .= sprintf( '<td>%s</td>', number_format( $counts[ $key ][ 'average' ], 1 ) );
                    break;
                default:
                    $html_sums      .= '<td></td>';
                    $html_averages  .= sprintf( '<td>%s</td>', number_format( $counts[ $key ][ 'average' ], 2 ) );
            }

	    }

        $html .= sprintf( '<br />
                                    <table class="ws-ls-footable ws-ls-footable-basic widefat">
                                        <thead>
                                            <tr><th></th>%1$s</tr>
                                        </thead>
                                        <tbody>
                                            <tr><th>%2$s</th>%3$s</tr>
                                            <tr><th>%4$s</th>%5$s</tr>
                                        </tbody>    
                                   </table>',
                                    $html_columns,
                                    __( 'Sum', WE_LS_SLUG ),
                                    $html_sums,
                                    __( 'Average', WE_LS_SLUG ),
                                    $html_averages
        );
    }

    return $html;
}

/**
 * Render filters for challenge table
 */
function ws_ls_challenges_show_filters() {

    $challenge_id = ws_ls_querystring_value( 'challenge-id', true );

    $html = '<form method="get" class="ws-ls-challenges-filters">';

    // Add some additional fields if admin
    if ( true === is_admin() ) {

        $current_page = ws_ls_querystring_value( 'page', false, 'ws-ls-challenges' );

        $html .= sprintf( '  <input type="hidden" name="mode" value="view" />
                           <input type="hidden" name="challenge-id" value="%1$d" />
                           <input type="hidden" name="page" value="%2$s" />
                           ',
                            $challenge_id,
                            esc_attr( $current_page )
        );
    }

    // Gender
    $html .= ws_ls_select( 'gender', __( 'Gender', WE_LS_SLUG ),  ws_ls_genders() );

    // Age range
    $html .= ws_ls_select( 'age-range', __( 'Age Range', WE_LS_SLUG ), ws_ls_age_ranges( true, true ) );

    // Group
    if ( true === ws_ls_groups_enabled () ) {
        $html .= ws_ls_select( 'group-id', __( 'Group', WE_LS_SLUG ), ws_ls_challenge_filters_group_select_values() );
    }

    // Optin
    if ( true === is_admin() ) {
        $html .= ws_ls_select( 'opt-in', __( 'Opted in', WE_LS_SLUG ), [  1 => __( 'Opted in', WE_LS_SLUG ), 0 => __( 'Everyone', WE_LS_SLUG ) ] );
    }

    $html .= sprintf( '<input type="submit" class="btn button-primary" value="%s" /></form>', __( 'Filter', WE_LS_SLUG ) );

    return $html;
}

/**
 * Simplify group array for <select>
 * @return mixed
 */
function ws_ls_challenge_filters_group_select_values() {

    $groups = ws_ls_groups( true );
    return wp_list_pluck( $groups, 'name', 'id' );
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