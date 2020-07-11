<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Refactor complete: 14/06/2020
 */

/**
 * Return the user's target weight in Kg
 * @param $user_id
 * @return string|null
 */
function ws_ls_db_target_get( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return NULL;
	}

	// Cached?
	if ( $cache = ws_ls_cache_user_get( $user_id, 'target-kg' ) ) {
		return $cache;
	}

	global $wpdb;

	$sql 	= $wpdb->prepare('SELECT target_weight_weight as kg FROM ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME . ' where weight_user_id = %d ', $user_id );
	$kg 	= $wpdb->get_var( $sql );

	ws_ls_cache_user_set( $user_id, 'target-kg', $kg );

	return $kg;
}

/**
 * Delete a user's target
 *
 * @param $user_id
 * @return bool
 */
function ws_ls_db_target_delete( $user_id ) {

	global $wpdb;

	if ( true === empty( $user_id ) ) {
		return false;
	}

	$result = $wpdb->delete($wpdb->prefix . WE_LS_TARGETS_TABLENAME, [ 'weight_user_id' => $user_id ], [ '%d' ] );

	ws_ls_cache_user_delete( $user_id );

	return $result;
}

/**
 * Set a user's target weight
 * @param $user_id
 * @param $kg
 */
function ws_ls_db_target_set( $user_id, $kg ) {

	if ( true === empty( $user_id ) || true === empty( $kg ) ) {
		return false;
	}

	global $wpdb;

	// Does the user have an existing target?
	$target_exist   = ( false === empty( ws_ls_db_target_get( $user_id ) ) );
	$result         = false;
	$table_name     = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;

	if ( true === $target_exist ) {
		$result = $wpdb->update( $table_name, [ 'target_weight_weight' => $kg ], [ 'weight_user_id' => $user_id ], [ '%f' ], [ '%d' ] );
	} else {
		$result = $wpdb->insert( $table_name, [ 'target_weight_weight' => $kg, 'weight_user_id' => $user_id ], [ '%f', '%d' ] );
	}

	ws_ls_cache_user_delete( $user_id );

	return $result;
}

/**
 * Insert or update an entry
 * @param $data
 * @param $user_id
 * @param null $existing_id
 *
 * @return bool|false|int|mixed|null
 */
function ws_ls_db_entry_set( $data, $user_id, $existing_id = NULL ) {

	if ( true === empty( $data ) ) {
		return false;
	}

	if ( true === empty( $user_id ) ) {
		return false;
	}

	$data[ 'weight_user_id' ] = (int) $user_id;

	$formats = ws_ls_db_get_formats( $data );

	global $wpdb;

	// Do a quick sanity check. If we're going to insert a new record, ensure there isn't already an entry for this user for that date
	if ( null === $existing_id ) {
		$existing_id = ws_ls_db_entry_for_date( $user_id, $data[ 'weight_date' ] );
	}

	$result     = false;
	$table_name = $wpdb->prefix . WE_LS_TABLENAME;

	if ( NULL !== $existing_id ) {

		$result = $wpdb->update( $table_name, $data, [ 'weight_user_id' => $user_id, 'id' => $existing_id ], $formats, [ '%d', '%d' ] );

		// Set result to row ID if a success
		if ( false !== $result ) {
			$result = $existing_id;
		}

	} else {

		if ( false !== $wpdb->insert( $table_name, $data, $formats ) ) {
			$result = $wpdb->insert_id;
		}
	}

	if ( false !== $result ) {
		ws_ls_delete_cache( sprintf( 'entry-%d', $result ) );
	}

	ws_ls_stats_update_for_user( $user_id );

	return $result;
}

/**
 * Fetch a weight entry for the given ID
 * @param array $arguments
 *
 * @return array|object|void|null
 */
function ws_ls_db_entry_get( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [   'user-id'   => get_current_user_id(),
	                                            'id'        => NULL,
	                                            'prep'      => true
	] );

	if ( true === empty( $arguments[ 'user-id' ] ) || true === empty( $arguments[ 'id' ] ) ) {
		return NULL;
	}

	$cache_key = 'weight-' . md5( json_encode( $arguments ) );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id'], $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sql    =  $wpdb->prepare('SELECT id, weight_date, weight_weight as kg, weight_notes as notes, weight_user_id as user_id FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d and id = %d',
		$arguments[ 'user-id' ],
		$arguments[ 'id' ]
	);

	$entry  = $wpdb->get_row( $sql, ARRAY_A );

	ws_ls_cache_user_set( $arguments[ 'user-id' ], $cache_key, $entry );

	return $entry;
}

/**
 * Fetch the entry ID for the oldest or latest entry
 * @param $arguments
 *
 * @return string|null
 */
function ws_ls_db_entry_latest_or_oldest( $arguments ) {

	$arguments = wp_parse_args( $arguments, [   'user-id'   => get_current_user_id(),
	                                            'which'     => 'latest',                // 'oldest' / 'latest'
	                                            'prep'      => true
	] );

	$cache_key = 'extreme-' . md5( json_encode( $arguments ) );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id'], $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sort_order = ( 'latest' === $arguments[ 'which' ] ) ? 'desc' : 'asc';

	$sql        = $wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d order by weight_date ' . $sort_order . ' limit 0, 1', $arguments[ 'user-id' ] );
	$entry_id   = $wpdb->get_var( $sql );

	ws_ls_cache_user_set( $arguments[ 'user-id' ], $cache_key, $entry_id );

	return $entry_id;
}

/**
 * Fetch the ID for the previous entry
 * @param $arguments
 *
 * @return string|null
 */
function ws_ls_db_entry_previous( $arguments ) {

	$arguments = wp_parse_args( $arguments, [   'user-id'   => get_current_user_id(),
	                                            'prep'      => true
	] );

	$cache_key = 'previous-entry-' . md5( json_encode( $arguments ) );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id'], $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sql        = $wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d order by weight_date desc limit 1, 1', $arguments[ 'user-id' ] );
	$entry_id   = $wpdb->get_var( $sql );

	ws_ls_cache_user_set( $arguments[ 'user-id' ], $cache_key, $entry_id );

	return $entry_id;
}

/**
 * If an entry exists for this date, then return an ID
 *
 * @param $user_id
 * @param $date
 *
 * @return bool|mixed
 */
function ws_ls_db_entry_for_date( $user_id, $date ) {

	if ( $cache = ws_ls_cache_user_get( $user_id, 'exist-' . $date ) ) {
		return $cache;
	}

	global $wpdb;

	$sql        = $wpdb->prepare('SELECT id FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' WHERE weight_date = %s and weight_user_id = %d', $date, $user_id );
	$entry_id   = $wpdb->get_var($sql);

	ws_ls_cache_user_set( $user_id, 'exist-' . $date, $entry_id );

	return $entry_id;
}

/**
 * Fetch weight entries for user
 *
 * Don't call this function directly. Call: ws_ls_entries_get()
 *
 * @param array $arguments
 *
 * @return array|object|null
 * @throws Exception
 */
function ws_ls_db_entries_get( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [   'user-id'   => get_current_user_id(),
	                                            'limit'     => ws_ls_option( 'ws-ls-max-points', '25', true ),
	                                            'week'      => NULL,
	                                            'sort'      => 'desc',
												'start'     => 0
	] );

	$cache_key = 'weights-' . md5( json_encode( $arguments ) );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id'], $cache_key ) ) {
		return $cache;
	}

	global $wpdb;
	$additional_sql = '';

	// Has the user selected a week to look at in UI?
	if ( false === empty( $arguments[ 'week' ] ) ){

		$week_number = (int) $arguments[ 'week' ];
		$week_ranges = ws_ls_week_ranges_get();

		if( false === empty( $week_ranges[ $week_number ] ) ) {
			$additional_sql = $wpdb->prepare( ' and ( weight_date BETWEEN %s AND %s )', $week_ranges[ $week_number ][ 'start' ], $week_ranges[ $week_number ][ 'end' ] );
		}
	}

	// User ID specified? IF empty or set to 0 then don't add into where clause
	if ( false === empty( $arguments[ 'user-id' ] ) ) {
		$additional_sql .= $wpdb->prepare( ' and weight_user_id = %d', $arguments[ 'user-id' ] );
	}

	$sort_order = ( true === in_array( $arguments[ 'sort' ], ws_ls_db_lookup_sort_orders() ) ) ? $arguments[ 'sort' ] : 'asc';

	$sql =  'SELECT id, weight_date, weight_weight as kg, weight_notes as notes, weight_user_id as user_id FROM ' . $wpdb->prefix . WE_LS_TABLENAME .
	                       ' where 1 = 1' . $additional_sql. ' order by weight_date ' . $sort_order;

	if ( false === empty( $arguments[ 'limit'] ) ) {
		$sql .=  $wpdb->prepare( ' limit %d, %d', $arguments[ 'start' ], $arguments[ 'limit' ] );
	}

	$results = $wpdb->get_results( $sql, ARRAY_A );

	ws_ls_cache_user_set( $arguments[ 'user-id'], $cache_key, $results );

	return $results;
}

/**
 * Delete all entries for given user
 * @param $user_id
 *
 * @return bool|int|null
 */
function ws_ls_db_entry_delete_all_for_user( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return NULL;
	}

	global $wpdb;

	$sql    = $wpdb->prepare('Delete from ' . $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d', $user_id );
	$result = $wpdb->query($sql);

	ws_ls_cache_user_delete( $user_id );

	return $result;
}

/**
 * Delete all entries
 * @return null
 */
function ws_ls_db_entry_delete_all() {

	// Extra check! Should only be done in Admin
	if ( false === is_admin() ) {
		return NULL;
	}

	global $wpdb;

	$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME );
	$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_TABLENAME );
	$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME );
}

/**
 * Get formats for DB tables
 * @param $db_fields
 *
 * @return array
 */
function ws_ls_db_get_formats( $db_fields ) {

	$formats = [];

	$lookup = [
		'weight_weight'     => '%f',
		'weight_date'       => '%s',
		'migrate'           => '%d',
		'weight_user_id'    => '%d',
		'weight_notes'      => '%s'
	];

	foreach ( $db_fields as $key => $value ) {
		if( false === empty( $lookup[ $key ] ) ) {

			$formats[] = $lookup[ $key ];
		}
	}

	return $formats;
}

/**
 * Fetch a user's starting weight
 * @param $user_id
 *
 * @return string|null
 */
function ws_ls_db_weight_start_get( $user_id ) {

	if ( $cache = ws_ls_cache_user_get( $user_id, 'start-weight' ) ) {
		return $cache;
	}

	global $wpdb;

    $sql    =  $wpdb->prepare('SELECT weight_weight as kg FROM ' .  $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d order by weight_date asc limit 0, 1', $user_id);
    $kg     = $wpdb->get_var( $sql );

	ws_ls_cache_user_set( $user_id, 'start-weight', $kg );

    return $kg;
}

/**
 * Delete an entry for the given user.
 * @param $user_id
 * @param $row_id
 *
 * @return bool
 */
function ws_ls_db_entry_delete( $user_id, $row_id ) {

	if ( true === empty( $user_id ) || true === empty( $row_id ) ) {
		return false;
	}

    global $wpdb;

    $result = $wpdb->delete($wpdb->prefix . WE_LS_TABLENAME, [ 'id' => $row_id, 'weight_user_id' => $user_id ] );

	if ( false !== $result ) {

		ws_ls_cache_user_delete( $user_id );

        // Inform others of deletion!
        do_action( 'wlt-hook-data-entry-deleted', [ 'id' => $row_id, 'user-id' => $user_id ] );

        return true;
	}

    return false;
}

/**
 * Fetch the min and max dates for the user's history
 * @param $user_id
 *
 * @return array|object|void|null
 */
function ws_ls_db_dates_min_max_get( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return NULL;
	}

	if ( $cache = ws_ls_cache_user_get( $user_id, 'min-max-dates' ) ) {
	//	return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare('SELECT min( weight_date ) as min, max( weight_date ) as max FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' WHERE weight_user_id = %d AND weight_date <> "0000-00-00 00:00:00"', $user_id );
	$row = $wpdb->get_row($sql, ARRAY_A);

	ws_ls_cache_user_set( $user_id, 'min-max-dates', $row );

	return $row;
}

function ws_ls_set_user_preferences($in_admin_area, $fields = [])
{
    global $wpdb;

    // Defaults for user preference fields
    $defaults = [
        'user_id' => get_current_user_id(),
        'settings' => [],
        'height' => NULL,
        'activity_level' => NULL,
        'gender' => NULL,
        'aim' => NULL,
        'dob' => false,
    ];

    $db_fields = wp_parse_args($fields, $defaults);

    // Validate arguments
    if ( false === is_array($db_fields['settings']) ) {
        $db_fields['settings'] = [];
    }

    $db_fields['settings'] = json_encode($db_fields['settings']);

    $db_fields['dob'] = (false === empty($db_fields['dob'])) ? ws_ls_convert_date_to_iso($db_fields['dob'], ($in_admin_area) ? false : $db_fields['user_id']) : '0000-00-00 00:00:00';

    // Save Height, if not specified look up.
    if (false !== $db_fields['height']) {
        $db_fields['height'] = ws_ls_height_validate($db_fields['height']);
    } else {
        $db_fields['height'] = ws_ls_user_preferences_get( 'height', $db_fields[ 'user_id' ] );
    }

    // Set data types
    $db_field_types = ws_ls_user_preferences_get_formats($db_fields);

    $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

      // Update or insert
    $result = $wpdb->replace(
                            $table_name,
                            $db_fields,
                            $db_field_types
                          );

    $result = ( $result === false ) ? false : true;

    // Tidy up cache
	ws_ls_cache_user_delete( $db_fields['user_id'] );
    return $result;
}

/**
 * This is a helper function that really needs to be refactored with ws_ls_set_user_preferences()
 *
 * Currently only used by Gravity Form Hook
 *
 * @param $field
 * @param $value
 * @param $user_id
 * @return bool
 */
function ws_ls_set_user_preference( $field, $value, $user_id = NULL ) {

    // Ensure we have a value!
    if ( true === empty( $field ) || true === empty( $value ) ) {
        return false;
    }

	$user_id = $user_id ?: get_current_user_id();

    global $wpdb;

    // Defaults for user preference fields
    $db_fields = [ $field => $value, 'user_id' => $user_id ];

    // Set data types
    $db_field_types = ws_ls_user_preferences_get_formats( $db_fields );

    $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

    // Update or insert
    $result = $wpdb->replace(
        $table_name,
        $db_fields,
        $db_field_types
    );

    $result = ($result === false) ? false : true;

    // Tidy up cache
	ws_ls_cache_user_delete( $db_fields['user_id'] );

    return $result;
}

/**
 * Update a user preference field
 * @param $field
 * @param $value
 * @param null $user_id
 * @return bool
 */
function ws_ls_set_user_preference_simple( $field, $value, $user_id = NULL ) {

    // Ensure we have a value!
    if ( true === empty( $field ) ) {
        return false;
    }

    $user_id = $user_id ?: get_current_user_id();

    // Check for existing settings for this user, if none, then we need to insert the settings row
    if ( true === empty( ws_ls_user_preferences_settings( $user_id ) ) ) {
        return ws_ls_set_user_preference( $field, $value, $user_id );
    }

    global $wpdb;

    $db_fields = [ $field => $value ];

    // Set data types
    $db_field_types = ws_ls_user_preferences_get_formats( $db_fields );

    // Update or insert
    $result = $wpdb->update(
        $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME,
        $db_fields,
        [ 'user_id' => (int) $user_id],
        $db_field_types,
        [ '%d' ]
    );

    $result = ($result === false) ? false : true;

    // Tidy up cache
	ws_ls_cache_user_delete( $user_id );

    return $result;
}

/**
 * Provide a list of formats for user pref database fields
 *
 * @param $db_fields
 * @return array
 */
function ws_ls_user_preferences_get_formats( $db_fields ) {

    $formats = [];

    $lookup = [
			    'activity_level'    => '%f',
			    'aim'               => '%d',
			    'dob'               => '%s',
			    'gender'            => '%d',
			    'height'            => '%d',
		        'settings'          => '%s',
			    'user_group'        => '%d',
                'user_id'           => '%d',
                'challenge_opt_in'  => '%d'
    ];

    $lookup = apply_filters( 'wlt-filter-user-settings-db-formats', $lookup );

    foreach ( $db_fields as $key ) {
        if( false === empty($lookup[$key])) {
            $formats[] = $lookup[$key];
        }
    }

    return $formats;
}

/**
 * Fetch all user preferences for the given user
 * @param null $user_id
 * @param bool $use_cache
 *
 * @return null
 */
function ws_ls_db_user_preferences( $user_id = NULL, $use_cache = true ) {

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	$cache = ws_ls_cache_user_get( $user_id, 'user-preferences' );

	if ( true == $use_cache &&
	     false === empty( $cache )  )   {
		return $cache;
	}

	global $wpdb;

	$sql            = $wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME . ' WHERE user_id = %d', $user_id );
	$preferences    = $wpdb->get_row( $sql, ARRAY_A );

	ws_ls_cache_user_set( $user_id, 'user-preferences', $preferences );

	return $preferences;
}

/**
 * Fetch entry counts for the given user or for the site as a whole
 * @param null $user_id
 * @param bool $use_cache
 *
 * @return array|null
 */
function ws_ls_db_entries_count( $user_id = NULL, $use_cache = true ) {

    // If not looking up user stats, then store against dummy cache
	$user_id = ( true === empty( $user_id ) ) ? -1 : (int) $user_id;

	$cache = ws_ls_cache_user_get( $user_id, 'entry-counts' );

	if ( true == $use_cache &&
	        false === empty( $cache )  )   {
		return $cache;
	}

	$where = ( -1 !== $user_id ) ? ' where weight_user_id = ' . (int) $user_id : '';

	global $wpdb;

    $stats = [      'number-of-entries'     => $wpdb->get_var('SELECT count( id ) FROM ' . $wpdb->prefix . WE_LS_TABLENAME . $where ),
	                'number-of-users'       => $wpdb->get_var('SELECT count( id ) FROM ' . $wpdb->prefix . 'users'),
					'number-of-targets'     => $wpdb->get_var('SELECT count( id ) FROM ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME. $where )
    ];

	ws_ls_cache_user_set( $user_id, 'entry-counts', $stats );

    return $stats;
}

/**
 * Insert error message into database table
 *
 * @param $module
 * @param $message
 */
function ws_ls_log_add( $module, $message) {

	if ( false === empty( $module ) && false === empty( $message ) ) {

		global $wpdb;

		$wpdb->insert(
						$wpdb->prefix . WE_LS_LOG_TABLENAME,
						[ 'module' => $module, 'message' => $message ],
						[ '%s', '%s' ]
		);

	}
}

/**
 * Return all error logs
 *
 * @return mixed
 */
function ws_ls_log_all() {

	global $wpdb;

	return $wpdb->get_results('Select timestamp, module, message from ' . $wpdb->prefix . WE_LS_LOG_TABLENAME . ' order by id desc', ARRAY_A);
}

/**
 * Delete all log entries
 *
 * @return bool     true if success
 */
function ws_ls_log_delete_all( ) {

    if ( false === is_admin() ) {
        return false;
    }

    global $wpdb;

    $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_LOG_TABLENAME );

    return true;
}

/**
 * Delete all log entries older than x days
 *
 * @return mixed
 */
function ws_ls_log_delete_old() {

    global $wpdb;
    return $wpdb->query( 'DELETE FROM ' . $wpdb->prefix . WE_LS_LOG_TABLENAME . ' WHERE (`timestamp` < DATE_SUB(now(), INTERVAL 31 DAY));' );
}

/**
 * Return an array of allowed sort columns
 * @return array
 */
function ws_ls_db_lookup_sort_columns() {
	return [ 'id', 'weight_date', 'weight_weight', 'user_nicename' ];
}

/**
 * Return an array of allowed sort orcers
 * @return array
 */
function ws_ls_db_lookup_sort_orders() {
	return [ 'asc', 'desc' ];
}
