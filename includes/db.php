<?php

defined('ABSPATH') or die("Jog on!");

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

	ws_ls_cache_user_delete( $user_id, 'target-kg' );

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

	ws_ls_cache_user_delete( $user_id, 'target-kg' );

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
		$existing_id = ws_does_weight_exist_for_this_date( $user_id, $data[ 'weight_date' ] );
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
	                                            'row-id'    => NULL,
	                                            'prep'      => true
	] );

	if ( true === empty( $arguments[ 'user-id' ] ) || true === empty( $arguments[ 'row-id' ] ) ) {
		return NULL;
	}

	$cache_key = 'weight-' . md5( json_encode( $arguments ) );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id'], $cache_key ) ) {
		return $cache;
	}

	global $wpdb;

	$sql    =  $wpdb->prepare('SELECT id, weight_date, weight_weight as kg, weight_notes as notes, weight_user_id as user_id FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' where weight_user_id = %d and id = %d',
		$arguments[ 'user-id' ],
		$arguments[ 'row-id' ]
	);

	$entry  = $wpdb->get_row( $sql, ARRAY_A );

	if( true === $arguments[ 'prep' ] ) {
		$entry = ws_ls_db_weight_prep( $entry );
	}

	ws_ls_cache_user_set( $arguments[ 'user-id' ], $cache_key, $entry );

	return $entry;
}


/**
 * Fetch weight entries for user
 * @param array $arguments
 *
 * @return array|object|null
 * @throws Exception
 */
function ws_ls_db_entries_get( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [   'user-id'   => get_current_user_id(),
	                                            'limit'     => ws_ls_option( 'ws-ls-max-points', '25', true ),
	                                            'week'      => NULL,
	                                            'sort'      => 'asc',
	                                            'prep'      => false
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
		$week_ranges = ws_ls_get_week_ranges();

		if( false === empty( $week_ranges[ $week_number ] ) ) {
			$additional_sql =  $wpdb->prepare('and ( weight_date BETWEEN %s AND %s )', $week_ranges[ $week_number ][ 'start' ], $week_ranges[ $week_number ][ 'end' ] );
		}
	}

	$sort_order = ( true === in_array( $arguments[ 'sort' ], ws_ls_db_lookup_sort_orders() ) ) ? $arguments[ 'sort' ] : 'asc';

	$sql =  $wpdb->prepare('SELECT id, weight_date, weight_weight as kg, weight_notes as notes FROM ' . $wpdb->prefix . WE_LS_TABLENAME .
	                       ' where weight_user_id = %d ' . $additional_sql. ' order by weight_date ' . $sort_order .
	                       ' limit 0, %d', $arguments[ 'user-id' ],  $arguments[ 'limit' ] );

	$results = $wpdb->get_results( $sql, ARRAY_A );

	if ( true === ( $arguments[ 'prep' ] ) ) {
		$results = array_map( 'ws_ls_db_weight_prep', $results );
	}

	ws_ls_cache_user_set( $arguments[ 'user-id'], $cache_key, $results );

	return $results;
}

/**
 * Prep a weight result if further detail needed
 * @param $weight
 *
 * @return array
 */
function ws_ls_db_weight_prep( $weight ) {

	if ( false === empty( $weight ) ) {

		// Add dates to weight entry
		$dates = ws_ls_convert_ISO_date_into_locale( $weight[ 'weight_date' ] );
		$weight = array_merge( $weight, $dates );

		// Add Weight display values
		$display_values = ws_ls_weight_display( $weight[ 'kg' ] );
		$weight = array_merge( $weight, $display_values );

	}

	return $weight;
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

	ws_ls_delete_cache_for_given_user( $user_id );

	return $result;
}

/**
 * Delete all entries
 * @return null
 */
function ws_ls_db_entry_delete_all() {

	// Extra check! Should only be done in Admin
	if ( true === is_admin() ) {
		return NULL;
	}

	global $wpdb;

	$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME );
	$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_TABLENAME );
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

function ws_ls_save_data($user_id, $weight_object, $is_target_form = false, $existing_row_id = false)
{
	global $wpdb;

	$db_prefix = ($is_target_form) ? 'target_' : '';
	$db_is_update = false;
	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$mode = NULL;

	// Ensure each weight field has been populated!
	if(!ws_ls_validate_weight_data($weight_object)) {
		return false;
	}

	// Build array of fields to pass to DB
	$db_fields['weight_user_id'] = $user_id;
	$db_fields[$db_prefix . 'weight_stones'] = $weight_object['stones'];
	$db_fields[$db_prefix . 'weight_pounds'] = $weight_object['pounds'];
	$db_fields[$db_prefix . 'weight_only_pounds'] = $weight_object['only_pounds'];
	$db_fields[$db_prefix . 'weight_weight'] = $weight_object['kg'];

	// Set data types
	$db_field_types = array('%d','%f', '%f', '%f', '%f');

	// Customise depending on whether an update or not
	if($is_target_form) {
		$db_is_update = false; // ws_does_target_weight_exist($user_id);
	    $table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
	} else {

		$db_is_update = false;

		if($existing_row_id && is_numeric($existing_row_id)) {
			$db_is_update = $existing_row_id;
		} else {
			$db_is_update = ws_does_weight_exist_for_this_date($user_id, $weight_object['date']);
		}

		$db_fields['weight_notes'] = $weight_object['notes'];
	    array_push($db_field_types, '%s');
	    $db_fields['weight_date'] = $weight_object['date'];
	    array_push($db_field_types, '%s');
	}

	$entry_id = NULL;

	// Update or insert
	if( false !== $db_is_update ) {

	    $result = $wpdb->update(
                                    $table_name,
                                    $db_fields,
                                    array( 'id' => $db_is_update ),
                                    $db_field_types,
                                    array( '%d' )
		);

        $mode = 'update';

	} else {

	    $result = $wpdb->insert(
	    	                        $table_name,
	                                $db_fields,
	    	                        $db_field_types
	    );

        $mode = 'add';

        $db_is_update = ( false !== $result ) ? $wpdb->insert_id : false;
	 }

	$result = ($result === false) ? false : true;

    // Save Meta Fields?
    if ( true === ws_ls_meta_fields_is_enabled() && false === empty( $weight_object[ 'meta-keys' ] ) ) {

        foreach ( $weight_object[ 'meta-keys' ] as $id => $value ) {

                ws_ls_meta_add_to_entry([
                                            'entry_id' => $db_is_update,
                                            'key' => $id,
                                            'value' => $value
                    ]
                );

        }

        ws_ls_cache_user_delete( 'meta-fields' );

    }

	// Tidy up cache
	ws_ls_delete_cache_for_given_user($user_id);

	// Update User stats table
	if (WS_LS_IS_PRO) {
		ws_ls_stats_update_for_user($user_id);

		// Throw data out in case anyone else wants to process it (also used within plugin for sending emails etc)
		$type = array (
			'user-id' => $user_id,
			'type' => ($is_target_form) ? 'target' : 'weight-measurements',
			'mode' => $mode
		);

		do_action( 'wlt-hook-data-added-edited', $type, $weight_object );
	}

	return $result;
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

		ws_ls_delete_cache_for_given_user( $user_id );

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
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare('SELECT min( weight_date ) as min, max( weight_date ) as max FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ' WHERE weight_user_id = %d', $user_id );
	$row = $wpdb->get_row($sql, ARRAY_A);

	ws_ls_cache_user_set( $user_id, 'min-max-dates', $row );

	return $row;
}

/**
 * TODO: REFACTOR!
 *
 * @param $user_id
 * @param $date
 *
 * @return bool|mixed
 */
function ws_does_weight_exist_for_this_date($user_id, $date)
{
  	global $wpdb;

	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_FOR_DAY;

	// Return cache if found!
    if ($cache = ws_ls_get_cache($cache_key)) {
      //  return $cache;
    }

	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql =  $wpdb->prepare('SELECT id FROM ' . $table_name . ' WHERE weight_date = %s and weight_user_id = %d', $date, $user_id);
	$row = $wpdb->get_row($sql);

	if (!is_null($row)) {
		ws_ls_set_cache($cache_key, true);
		return $row->id;
	}

	ws_ls_set_cache($cache_key, false);
  	return NULL;
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
        $db_fields['height'] = ws_ls_validate_height($db_fields['height']);
    } else {
        $db_fields['height'] = ws_ls_get_user_height($db_fields['user_id'], false);
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

    $result = ($result === false) ? false : true;

    // Tidy up cache
    ws_ls_delete_cache_for_given_user($db_fields['user_id']);
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
    ws_ls_delete_cache_for_given_user( $db_fields['user_id'] );

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
    if ( true === empty( ws_ls_user_preferences_get( $user_id ) ) ) {
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
    ws_ls_delete_cache_for_given_user( $user_id );

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
 * Fetch user preferences
 * @param null $user_id
 * @param bool $use_cache
 * @return array|mixed|string|null
 */
function ws_ls_user_preferences_get( $user_id = NULL, $use_cache = true ) {

  	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	// Cached?
	if ( true === $use_cache &&
			$cache = ws_ls_cache_user_get( $user_id, 'preferences' ) ) {
		return $cache;
	}

	global $wpdb;

  	$sql 		= $wpdb->prepare('SELECT settings FROM ' . $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME . ' WHERE user_id = %d' , $user_id );
  	$settings 	= $wpdb->get_var( $sql );

	if ( false === empty( $settings ) ) {
		$settings = json_decode( $settings, true );
	}

	if ( false === is_array( $settings ) ) {
		$settings = [ 'empty' => true ];	// This is a little hack, if we have no settings for this user, store an empty flag so caching works for DB lookup
	}

	ws_ls_cache_user_set( $user_id, 'preferences', $settings );

	return $settings;
}

/**
 * Fetch a user's height from preferences
 * @param bool $user_id
 * @param bool $use_cache
 * @return bool
 */
function ws_ls_get_user_height( $user_id = false, $use_cache = true ) {

  $user_id      = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;
  $cache_key    = sprintf( '%s-%d', WE_LS_CACHE_KEY_USER_HEIGHT, $user_id );

  // Return cache if found!
  if ( true === $use_cache &&
        $cache = ws_ls_get_cache( $cache_key ) )   {
      return $cache;
  }

  global $wpdb;

  $sql      = $wpdb->prepare( 'SELECT height FROM ' . $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME . ' WHERE user_id = %d' , $user_id );
  $height   = $wpdb->get_var( $sql );

  $height = ( false === empty( $height ) ) ? $height : false;

  ws_ls_set_cache( $cache_key, $height );

  return $height;
}

function ws_ls_get_user_setting($field = 'gender', $user_id = false, $use_cache = true) {

    global $wpdb;

    // Default to logged in user if not user ID not specified.
    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    $valid_settings = apply_filters( 'wlt-filter-setting-fields', ['activity_level', 'gender', 'height', 'dob', 'aim', 'body_type', 'challenge_opt_in' ] );

    // Validate field
    $field = ( in_array($field, $valid_settings) ) ? $field : 'gender';

    $cache_key = WE_LS_CACHE_KEY_USER_PREFERENCE . '-' . $field;

    // Return cache if found!
    $cache = ws_ls_cache_user_get($user_id, $cache_key);
    if (false === empty($cache) && true == $use_cache)   {
    	return $cache;
    }

    $sql =  $wpdb->prepare('SELECT ' . $field . ' FROM ' . $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME . ' WHERE user_id = %d', $user_id);
    $row = $wpdb->get_row($sql, ARRAY_A);

    $result = NULL;

    if($row[$field]) {
		$result = apply_filters( 'wlt-filter-user-setting-' . $field, $row[$field], $user_id, $field );
    }

	ws_ls_cache_user_set($user_id, $cache_key, $result);

    return $result;
}


function ws_ls_get_entry_counts($user_id = false, $use_cache = true) {

    global $wpdb;

    $cache_key = WE_LS_CACHE_KEY_ENTRY_COUNTS;
    $cache = ws_ls_get_cache($cache_key);

    // Return cache if found!
    if ($cache && true == $use_cache && false === $user_id)   {
        return $cache;
    }

    $stats = ['number-of-users' => false, 'number-of-entries' => false, 'number-of-targets' => false];

    $where = (false === empty($user_id) && true === is_numeric($user_id)) ? (int) $user_id : false;

    $stats['number-of-entries'] = $wpdb->get_var('SELECT count(id) FROM ' . $wpdb->prefix . WE_LS_TABLENAME . (($where) ? ' where weight_user_id = ' . $where : ''));
    $stats['number-of-users'] = $wpdb->get_var('SELECT count(ID) FROM ' . $wpdb->prefix . 'users');
    $stats['number-of-targets'] = $wpdb->get_var('SELECT count(id) FROM ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME . (($where) ? ' where weight_user_id = ' . $where : ''));

    ws_ls_set_cache($cache_key, $stats);

    return $stats;
}

function ws_ls_validate_height($height) {
	 // If not a valid height clear value
	 if(!is_numeric($height) || $height < 122 || $height > 201) {
	   $height = 0;
	 }
	 return $height;
}

function ws_ls_set_user_height($height, $user_id = false)
{
  global $wpdb;

  if(false == $user_id){
    $user_id = get_current_user_id();
  }

  // If not a valid height clear value
  $height = ws_ls_validate_height($height);

  $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

  // Build array of fields to pass to DB
  $db_fields['user_id'] = $user_id;
  $db_fields['height'] = $height;
  $db_fields['settings'] = json_encode(ws_ls_user_preferences_get($user_id, false));

  // Set data types
  $db_field_types = array('%d','%d','%s');

  // Update or insert
  $result = $wpdb->replace(
                            $table_name,
                            $db_fields,
                            $db_field_types
                          );

  $result = ($result === false) ? false : true;

  // Tidy up cache
  ws_ls_delete_cache_for_given_user($user_id);
  return $result;
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
