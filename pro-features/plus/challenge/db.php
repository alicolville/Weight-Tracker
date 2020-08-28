<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Insert a new challenge
 * @param $name
 * @param null $start_date
 * @param null $end_date
 * @return bool
 */
function ws_ls_challenges_add( $name, $start_date = NULL, $end_date = NULL ) {

	if ( false === ws_ls_challenges_is_enabled() ) {
		return false;
	}

	if ( true === empty( $name ) ) {
		return false;
	}

	$data       = [ 'name' => $name, 'enabled' => 1 ];
	$formats    = [ '%s', '%d' ];

	if ( false === empty( $start_date ) ) {
		$data[ 'start_date' ]   = $start_date;
		$formats[]              = '%s';
	}

	if ( false === empty( $end_date ) ) {
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

	if ( false === ws_ls_challenges_is_enabled() ) {
		return false;
	}

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
 * Delete a challenge
 * @param $challenge_id
 * @return bool
 */
function ws_ls_challenges_delete( $challenge_id ) {

	if ( true === empty( $challenge_id ) ) {
		return false;
	}

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES,
		[ 'id' => $challenge_id ],
		[ '%d' ]
	);

	ws_ls_delete_cache( 'challenge-' . (int) $challenge_id );

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA,
		[ 'challenge_id' => $challenge_id ],
		[ '%d' ]
	);

	return ! empty( $result );
}

/**
 * Deelete all challenge data for this user
 * @param $user_id
 *
 * @return bool
 */
function ws_ls_challenges_delete_for_user( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return false;
	}

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA,
		[ 'user_id' => $user_id ],
		[ '%d' ]
	);

	ws_ls_cache_user_delete( $user_id );

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

	if ( $cache = ws_ls_cache_get( 'challenge-' . (int) $challenge_id ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES . ' WHERE id = %d', $challenge_id );

	$result = $wpdb->get_row( $sql, ARRAY_A );

	$result = ( false === empty( $result ) ) ? $result : false;

	ws_ls_cache_set( 'challenge-' . (int) $challenge_id, $result );

	return $result;
}

/**
 * Fetch all active challenges
 * @param bool $enabled
 * @return mixed
 */
function ws_ls_challenges( $enabled = true ) {

	if ( false === ws_ls_challenges_is_enabled() ) {
		return NULL;
	}

	global $wpdb;

	$sql = 'SELECT * FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES;

	if ( true === $enabled ) {
		$sql .= ' WHERE enabled = 1';
	}

	return $wpdb->get_results( $sql, ARRAY_A );
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
 * Return possible age ranges
 * < 25, 26-35, 36-45, 46-55, 55+
 *
 * @param bool $as_string
 * @return array
 */
function ws_ls_age_ranges( $as_string = false ) {
	$age_ranges = [
		0 => [ 'min' => NULL, 'max' => NULL ],
		1 => [ 'min' => NULL, 'max' => '25' ],
		2 => [ 'min' => 26, 'max' => '35' ],
		3 => [ 'min' => 36, 'max' => '45' ],
		4 => [ 'min' => 46, 'max' => '55' ],
		5 => [ 'min' => 55, 'max' => NULL ]
	];

	if ( false === $as_string ) {
		return $age_ranges;
	}

	return array_map( 'ws_ls_age_ranges_array_map_to_string', $age_ranges );
}

/**
 * Convert to string
 * @param $element
 * @return string
 */
function ws_ls_age_ranges_array_map_to_string( $element ) {

	$text = ( true === empty( $element[ 'min' ] ) ) ? 0 : $element[ 'min' ];

	$text .= ( true === empty( $element[ 'max' ] ) ) ? '+' : sprintf( ' %s %d', __( 'to', WE_LS_SLUG ), $element[ 'max' ] );

	return ( '0+' === $text )  ? '' : $text;
}

/**
 * Fetch challenge data
 *
 * @param $args
 *
 * @return bool|null
 */
function ws_ls_challenges_data( $args ) {

	$args = wp_parse_args( $args, [
		'id'            	=> NULL,
		'gender'        	=> NULL,
		'age-range'     	=> NULL,
		'group-id'      	=> NULL,
		'opted-in'      	=> true,
		'min-wt-entries'	=> 2
	]);

	if ( true === empty( $args[ 'id' ] ) ) {
		return NULL;
	}

	$cache_key = 'challenge-data-' . md5( json_encode( $args ) );

	if ( $cache = ws_ls_cache_get( $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . ' where challenge_id = %d', $args[ 'id' ] );

	// Gender
	if ( false === empty( $args[ 'gender' ] ) ) {
		$sql .= $wpdb->prepare( ' and gender = %d', $args[ 'gender' ] );
	}

	// Min number of weight entries
	if ( false === empty( $args[ 'min-wt-entries' ] ) ) {
		$sql .= $wpdb->prepare( ' and count_wt_entries >= %d', $args[ 'min-wt-entries' ] );
	}

	// Age range
	if ( false === empty( $args[ 'age-range' ] ) ) {

		$age_range = ws_ls_challenges_age_range_get( $args[ 'age-range' ] );

		if ( false === empty( $age_range[ 'min' ] ) ) {
			$sql .= $wpdb->prepare( ' and age >= %d', $age_range[ 'min' ] );
		}

		if ( false === empty( $age_range[ 'max' ] ) ) {
			$sql .= $wpdb->prepare( ' and age <= %d', $age_range[ 'max' ] );
		}

	}

	// Group
	if ( false === empty( $args[ 'group-id' ] ) ) {
		$sql .= $wpdb->prepare( ' and group_id = %d', $args[ 'group-id' ] );
	}

	if ( true === $args[ 'opted-in' ] || 1 === (int) $args[ 'opted-in' ] ) {
		$sql .= ' and opted_in = 1';
	}

	$data = $wpdb->get_results( $sql, ARRAY_A );

	if ( false === empty( $data ) ) {
		$data = array_map( 'ws_ls_challenges_data_expand_row', $data );
	}

	ws_ls_cache_set( $cache_key, $data );

	return $data;
}

/**
 * Expand rows
 * @param $row
 * @return mixed
 */
function ws_ls_challenges_data_expand_row( $row ) {

	$row[ 'display_name' ] = ws_ls_user_display_name( $row[ 'user_id'] );

	return $row;
}

/**
 * Fetch users for the given challenge that need to be processed.
 * @param $challenge_id
 * @param null $user_id
 * @param int $limit
 * @return bool
 */
function ws_ls_challenges_data_awaiting_processing( $challenge_id, $user_id = NULL, $limit = 20 ) {

	if ( true === empty( $challenge_id ) ) {
		return false;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT user_id, challenge_id FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . '
                            WHERE challenge_id = %d and last_processed is NULL',
		$challenge_id
	);

	if ( NULL !== $user_id ) {
		$sql .= $wpdb->prepare( ' AND user_id = %d', $user_id );
	}

	$sql .= $wpdb->prepare( ' limit 0, %d', $limit );

	return $wpdb->get_results( $sql, ARRAY_A );
}

/**
 * Return basic stats for a challenge
 * @param $challenge_id
 * @return array|bool
 */
function ws_ls_challenges_stats( $challenge_id ) {

	if ( true === empty( $challenge_id ) ) {
		return false;
	}

	global $wpdb;

	$stats = [ 'count' => NULL, 'to-be-processed' => NULL, 'processed' => NULL ];

	$challenge_id = (int) $challenge_id;

	$stats[ 'count' ]           = $wpdb->get_var( 'SELECT count( user_id ) FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . ' WHERE challenge_id = ' . $challenge_id );
	$stats[ 'processed' ]       = $wpdb->get_var( 'SELECT count( user_id ) FROM ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . ' WHERE last_processed is not NULL and challenge_id = ' . $challenge_id );
	$stats[ 'to-be-processed' ] = $stats[ 'count' ] - $stats[ 'processed' ];

	return $stats;
}


/**
 * Update last processed flag for a user record when processed
 * @param $user_id
 * @return bool
 */
function ws_ls_challenges_data_last_processed_reset( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return false;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'UPDATE ' . $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA . ' SET last_processed = NULL where user_id = %d;', $user_id );

	return $wpdb->query( $sql );
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

	$sql = $wpdb->prepare( 'SELECT id FROM ' . $wpdb->prefix . YK_WT_DB_ENTRY . ' WHERE user_id = %d and calories_used > 0', $user_id );

	// Do we have a start and end date?
	if ( false === empty( $start_date ) && false === empty( $end_date ) ) {
		$sql .= $wpdb->prepare( ' and date >= %s and date <= %s', $start_date, $end_date );
	}

	$sql .= ' order by date asc';

	$result = $wpdb->get_results( $sql, ARRAY_A );

	$result = ( false === empty( $result ) ) ? $result : false;

	return $result;
}
