<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Insert a new challenge
 * @param $challenge_id
 * @param null $start_date
 * @param null $end_date
 * @return bool
 */
function ws_ls_challenges_add( $challenge_id, $start_date = NULL, $end_date = NULL ) {

    if ( true === empty( $challenge_id ) ) {
        return false;
    }

    $data       = [ 'id' => (int) $challenge_id ];
    $formats    = [ '%d' ];

    if ( false === empty( $start_date ) && false === empty( $end_date ) ) {
        $data[ 'start_date' ]   = $start_date;
        $formats[]              = '%s';
        $data[ 'end_date' ]     = $end_date;
        $formats[]              = '%s';
    }

    global $wpdb;

    $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES, $data, $formats );

    return ! empty( $result );
}

/**
 * Update enabled flag for a challenge
 * @param $challenge_id
 * @param bool $enabled
 * @return bool
 */
function ws_ls_challenges_enabled( $challenge_id, $enabled = true ) {

    if ( true === empty( $challenge_id ) ) {
        return false;
    }

    global $wpdb;

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES,
        [ 'enabled' => ( true === $enabled ) ? 1 : 0 ],
        [ 'id' => $challenge_id ],
        [ '%d' ],
        [ '%d' ]
    );

    ws_ls_delete_cache( 'challenge-' . (int) $challenge_id );

    return ! empty( $result );
}

/**
 * Fetch a challenge
 * @param $challenge_id
 * @return bool
 */
function ws_ls_challenges_get( $challenge_id ) {

    if ( true === empty( $challenge_id ) ) {
        return false;
    }

    if ( $cache = ws_ls_get_cache( 'challenge-' . (int) $challenge_id ) ) {
        return $cache;
    }

    global $wpdb;

    $sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES . ' WHERE id = %d', $challenge_id );

    $result = $wpdb->get_row( $sql, ARRAY_A );

    $result = ( false === empty( $result ) ) ? $result : false;

    ws_ls_set_cache( 'challenge-' . (int) $challenge_id, $result );

    return $result;
}

/**
 * Fetch all active challenges
 * @return mixed
 */
function ws_ls_challenges() {

    global $wpdb;

    return $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES . ' WHERE enabled = 1', ARRAY_A );
}

/**
 * Look at the weight entry table and and look for any users that have at least one weight entry for the given time period.
 *
 * @param $challenge_id
 * @param null $start_date
 * @param null $end_date
 * @return int|false - number of entries inserted or false for none.
 */
function ws_ls_challenges_identify_entries( $challenge_id, $start_date = NULL, $end_date = NULL ) {

    if ( true === empty( $challenge_id ) ) {
        return false;
    }

    global $wpdb;

    $sql = $wpdb->prepare( 'INSERT IGNORE INTO ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . ' ( user_id, challenge_id ) 
                            SELECT Distinct weight_user_id AS user_id, %d AS challenge_id FROM ' . $wpdb->prefix . WE_LS_TABLENAME, $challenge_id );

    // Do we have a start and end date?
    if ( false === empty( $start_date ) && false === empty( $end_date ) ) {
        $sql .= $wpdb->prepare( ' WHERE weight_date >= %s and weight_date <= %s', $start_date, $end_date );
    }

    return $wpdb->query( $sql );
}

/**
 * Fetch users for the given challenge that need to be processed.
 * @param $challenge_id
 * @param int $limit
 * @return bool
 */
function ws_ls_challenges_data_awaiting_processing($challenge_id, $limit = 20 ) {

    if ( true === empty( $challenge_id ) ) {
        return false;
    }

    global $wpdb;

    $sql = $wpdb->prepare( 'SELECT user_id, challenge_id FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . ' 
                            WHERE challenge_id = %d and last_processed is NULL
                            limit 0, %d',
                            $challenge_id,
                            $limit
    );

    return $wpdb->get_results( $sql, ARRAY_A );
}

/**
 * Update last processed flag for a user record when processed
 * @param $user_id
 * @param $challenge_id
 * @param bool $set         - If true, set last_processed flag to current timestamp. Otherwise NULL.
 * @return bool
 */
function ws_ls_challenges_data_last_processed( $user_id, $challenge_id, $set = true ) {

    if ( true === empty( $challenge_id ) ) {
        return false;
    }

    if ( true === empty( $user_id ) ) {
        return false;
    }

    global $wpdb;

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES,
        [ 'last_processed' => ( true === $set ) ? date( 'Y-M-d H:m:s' ) : NULL ],
        [ 'challenge_id' => $challenge_id, 'user_id' => $user_id ],
        [ '%s' ],
        [ '%d', '%d' ]
    );

    return ! empty( $result );
}

/**
 * Fetch weight entries (Kg) for the user within the given timeline
 * @param $user_id
 * @param null $start_date
 * @param null $end_date
 * @return bool
 */
function ws_ls_challenges_get_weight_entries( $user_id, $start_date = NULL, $end_date = NULL ) {

    if ( true === empty( $user_id ) ) {
        return false;
    }

    global $wpdb;

    $sql = $wpdb->prepare( 'SELECT weight_weight as kg, weight_date FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' WHERE weight_user_id = %d', $user_id );

    // Do we have a start and end date?
    if ( false === empty( $start_date ) && false === empty( $end_date ) ) {
        $sql .= $wpdb->prepare( ' and weight_date >= %s and weight_date <= %s', $start_date, $end_date );
    }

    $sql .= ' order by weight_date asc';

    $result = $wpdb->get_results( $sql, ARRAY_A );

    return ( false === empty( $result ) ) ? $result : false;
}

/**
 * Fetch meals for given time period
 * @param $user_id
 * @param null $start_date
 * @param null $end_date
 * @return bool
 */
function ws_ls_challenges_get_meal_tracker_entries( $user_id, $start_date = NULL, $end_date = NULL ) {

    if ( false === wlt_yk_mt_is_active() ) {
        return false;
    }

    if ( true === empty( $user_id ) ) {
        return false;
    }

    global $wpdb;

    $sql = $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . YK_WT_DB_ENTRY . ' WHERE user_id = %d', $user_id );

    // Do we have a start and end date?
    if ( false === empty( $start_date ) && false === empty( $end_date ) ) {
        $sql .= $wpdb->prepare( ' and date >= %s and date <= %s', $start_date, $end_date );
    }

    $sql .= ' order by date asc';

    $result = $wpdb->get_results( $sql, ARRAY_A );

    $result = ( false === empty( $result ) ) ? $result : false;

    return $result;
}