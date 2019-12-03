<?php

defined('ABSPATH') or die("Jog on!");

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

function t() {

    $r = ws_ls_challenges_add( 22, '2019-12-01', '2019-12-17' );
    var_dump($r);

}
add_action( 'init', 't' );