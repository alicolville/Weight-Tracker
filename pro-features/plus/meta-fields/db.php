<?php

defined('ABSPATH') or die("Jog on!");

// TODO: Add debug?


/**
 *
 * Return all meta fields for weight entry
 *
 * @param $entry_id
 * @return array
 */
function ws_ls_meta( $entry_id ) {

	if ( $cache = ws_ls_cache_user_get( 'meta-fields-' . $entry_id , 'entries' ) ) {
		return $cache;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' where entry_id = %d', $entry_id );

	$data = $wpdb->get_results( $sql, ARRAY_A );

	ws_ls_cache_user_set( 'meta-fields-' . $entry_id , 'entries' , $data );

	return $data;
}

/**
 *
 * Add a meta value to a given weight entry
 *
 * @param $data array:
 * 						entry_id
 * 						key
 * 						value
 *
 * @return bool
 */
function ws_ls_meta_add_to_entry( $data ) {

	// Ensure we have the expected fields.
	if ( false === ws_ls_meta_check_fields( $data, [ 'entry_id', 'key', 'value' ] ) ) {
		return false;
	}

	// Ensure the meta key exists!
	if ( false === ws_ls_meta_fields_key_exist( $data['key'] ) ) {
		return false;
	}

	// Fetch information about the meta field
	$meta_field = ws_ls_meta_fields_get( $data['key'] );

	if ( true === empty( $meta_field ) ) {
		return false;
	}

	// Remove any existing values for this weight entry / key
	ws_ls_meta_delete( $data['entry_id'], $meta_field['id'] );

	unset( $data['key'] );

	$data['meta_field_id'] = $meta_field['id'];

	global $wpdb;

	$formats = ws_ls_meta_formats( $data );

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY , $data, $formats );

	ws_ls_cache_user_delete( 'meta-fields-' . $data['entry_id'] );

	return ( false === $result ) ? false : $wpdb->insert_id;

}

/**
 * Delete given meta field value
 *
 * @param $entry_id
 * @param meta_field_id
 * @return bool
 */
function ws_ls_meta_delete( $entry_id, $meta_field_id ) {

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY, [ 'entry_id' => $entry_id, 'meta_field_id' => $meta_field_id ], [ '%d', '%d' ] );

	ws_ls_cache_user_delete( 'meta-fields-' . $entry_id );

	return ( 1 === $result );
}

/**
 * Delete all meta entries for given weight entry
 *
 * @param $entry_id
 * @return bool
 */
function ws_ls_meta_delete_for_entry( $entry_id ) {

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY, [ 'entry_id' => $entry_id ], [ '%d' ] );

	ws_ls_cache_user_delete( 'meta-fields-' . $entry_id );

	return ( 1 === $result );

}

/**
 * Delete all entries for given meta field
 *
 * @param $meta_field_id
 * @return bool
 */
function ws_ls_meta_delete_for_meta_field( $meta_field_id ) {

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY, [ 'meta_field_id' => $meta_field_id ], [ '%d' ] );

	//TODO: Need to clear all cache for entries?
	// ws_ls_cache_user_delete( 'meta-fields-' . $entry_id );

	return ( 1 === $result );

}

/**
 * Fetch all meta fields
 *
 * @return array
 */
function ws_ls_meta_fields() {

    global $wpdb;

    if ( $cache = ws_ls_cache_user_get( 'meta-fields', 'fields' ) ) {
    	return $cache;
	}

    $data = $wpdb->get_results( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' order by field_name asc', ARRAY_A );

	ws_ls_cache_user_set( 'meta-fields', 'fields' , $data );

	return $data;
}

/**
 *
 * Update a field.
 *
 * @param $unit    array:   field_key
 *                          field_name
 *                          abv
 *                          display_on_chart
 *                          system
 *                          unit_id
 *
 * @return bool     true if success
 */
function ws_ls_meta_fields_update( $field ) {

    // Ensure we have the expected fields.
    if ( false === ws_ls_meta_check_fields( $field, [ 'id', 'abv', 'display_on_chart', 'field_key', 'field_name', 'field_type', 'suffix', 'unit_id', 'system' ] ) ) {
        return false;
    }

    // May seem daft, but for now, do not allow field keys to be updated once inserted.
    unset( $field[ 'field_key' ] );

    // Extract ID
    $id = $field[ 'id' ];

    unset( $field[ 'id' ] );

    global $wpdb;

    $formats = ws_ls_meta_formats( $field );

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS, $field, [ 'id' => $id ], $formats, [ '%d' ] );

	ws_ls_cache_user_delete( 'meta-fields' );

    return ( 1 === $result );
}

/**
 *
 * Add a field.
 *
 * @param $unit    array:   field_key
 *                          field_name
 *                          abv
 *                          display_on_chart
 *                          system
 *                          unit_id
 *
 * @return bool     true if success
 */
function ws_ls_meta_fields_add( $field ) {

    // Ensure we have the expected fields.
    if ( false === ws_ls_meta_check_fields( $field, [ 'abv', 'display_on_chart', 'field_name', 'field_type', 'suffix', 'system' ] ) ) {
        return false;
    }

    // Sluggify key
    $field['field_key'] = ws_ls_meta_fields_generate_field_key( $field['field_name'] );

    global $wpdb;

    $formats = ws_ls_meta_formats( $field );

    $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS , $field, $formats );

	ws_ls_cache_user_delete( 'meta-fields' );

    return ( false === $result ) ? false : $wpdb->insert_id;
}

/**
 * Delete a meta field
 *
 * @param $id       meta field ID to delete
 * @return bool     true if success
 */
function ws_ls_meta_fields_delete( $id ) {

    global $wpdb;

    $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS, [ 'id' => $id ], [ '%d' ] );

	// TODO: What do we do with data that associated with this unit?

	ws_ls_cache_user_delete( 'meta-fields' );

    return ( 1 === $result );
}
//TODO: Need key based stuff?
/**
 * Does unit field_key already exist?
 *
 * @param $key
 */
function ws_ls_meta_fields_key_exist( $key ) {

    global $wpdb;

    //TODO: CACHE

    $sql = $wpdb->prepare('Select count(*) from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where field_key = %s', $key );

    $count = $wpdb->get_var( $sql );

    return ( 0 === intval( $count ) ) ? false : true;
}
//TODO: Need key based stuff?
/**
 * Get details for given meta field
 *
 * @param $key
 */
function ws_ls_meta_fields_get( $key ) {

	global $wpdb;

	//TODO: CACHE

	$sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where field_key = %s limit 0, 1', $key );

	$meta_field = $wpdb->get_row( $sql, ARRAY_A );

	return ( false === empty( $meta_field ) ) ? $meta_field : false;
}

/**
 * Get details for given meta field
 *
 * @param $key
 */
function ws_ls_meta_fields_get_by_id( $id ) {

    global $wpdb;

    //TODO: CACHE

    $sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where id = %d limit 0, 1', $id );

    $meta_field = $wpdb->get_row( $sql, ARRAY_A );

    return ( false === empty( $meta_field ) ) ? $meta_field : false;
}

/**
 * Fetch all units
 *
 * @return array
 */
//function ws_ls_meta_units() {
//
//    global $wpdb;
//
//    return $wpdb->get_results( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_UNITS . ' order by field_name asc', ARRAY_A );
//
//}

/**
 *
 * Update a unit.
 *
 * @param $unit    array:   id
 *                          field_key
 *                          field_name
 *                          abv
 *                          chartable
 *
 * @return bool     true if success
 */
//function ws_ls_meta_unit_update( $unit ) {
//
//    // Ensure we have the expected fields.
//    if ( false === ws_ls_meta_check_fields( $unit, [ 'abv', 'chartable', 'field_name', 'id' ] ) ) {
//        return false;
//    }
//
//    // May seem daft, but for now, do not allow field keys to be updated once inserted.
//    unset( $unit[ 'field_key' ] );
//
//    // Extract ID
//    $id = $unit[ 'id' ];
//
//    unset( $unit[ 'id' ] );
//
//    global $wpdb;
//
//    $formats = ws_ls_meta_formats( $unit );
//
//    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_META_UNITS, $unit, [ 'id' => $id ], $formats, [ '%d' ] );
//
//    return ( 1 === $result );
//}

/**
 *
 * Add a unit.
 *
 * @param $unit    array:   field_key
 *                          field_name
 *                          abv
 *                          chartable
 *
 * @return bool     true if success
 */
//function ws_ls_meta_unit_add( $unit ) {
//
//        // Ensure we have the expected fields.
//        if ( false === ws_ls_meta_check_fields( $unit, [ 'abv', 'chartable', 'field_key', 'field_name' ] ) ) {
//            return false;
//        }
//
//        // Sluggify key
//        $unit['field_key'] = ws_ls_meta_fields_key_sanitise( $unit['field_key'] );
//
//        // Ensure field_key doesn't already exist
//        if ( true === ws_ls_meta_unit_key_exist( $unit['field_key'] ) ) {
//            return false;
//        }
//
//        global $wpdb;
//
//        $formats = ws_ls_meta_formats( $unit );
//
//        $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_UNITS , $unit, $formats );
//
//        return ( false === $result ) ? false : $wpdb->insert_id;
//}

///**
// * Delete a Unit
// *
// * @param $id       unit ID to delete
// * @return bool     true if success
// */
//function ws_ls_meta_unit_delete( $id ) {
//
//    global $wpdb;
//
//    $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_UNITS, [ 'id' => $id ], [ '%d' ] );
//
//    // TODO: What do we do with meta fields, data, etc that associated with this unit?
//
//    return ( 1 === $result );
//}
//
///**
// * Does unit field_key already exist?
// *
// * @param $key
// */
//function ws_ls_meta_unit_key_exist( $key ) {
//
//    global $wpdb;
//
//    $sql = $wpdb->prepare('Select count(*) from ' . $wpdb->prefix . WE_LS_MYSQL_META_UNITS . ' where field_key = %s', $key );
//
//    $count = $wpdb->get_var( $sql );
//
//    return ( 0 === intval( $count ) ) ? false : true;
//}

/**
 * Return data formats
 *
 * @param $data
 * @return array
 */
function ws_ls_meta_formats( $data ) {

    $formats = [
        'id' => '%d',
        'field_key' => '%s',
        'field_name' => '%s',
        'abv' => '%s',
        'chartable' => '%d',
        'display_on_chart' => '%d',
		'entry_id' => '%d',
        'system' => '%d',
        'unit_id' => '%d',
		'meta_field_id' => '%d',
		'value' => '%s',
		'field_type' => '%d',
		'suffix' => '%s'
    ];

    $return = [];

    foreach ( $data as $key => $value) {
        if ( false === empty( $formats[ $key ] ) ) {
            $return[] = $formats[ $key ];
        }
    }

    return $return;
}

/**
 * Helper function to ensure all fields have expected keys
 *
 * @param $data
 * @param $expected_fields
 * @return bool
 */
function ws_ls_meta_check_fields( $data, $expected_fields ) {

    foreach ( $expected_fields as $field ) {
        if ( false === isset( $data[ $field ] ) ) {
            return false;
        }
    }

    return true;
}

/**
 * Sanitise a field key
 *
 * @param $key
 * @return string
 */
function ws_ls_meta_fields_key_sanitise( $key ) {

    if ( false === empty( $key ) ) {

        $key = sanitize_title( $key );

        if ( strlen( $key ) > 40 ) {
            $key = substr( $key, 0, 40 );
        }
    }

    return $key;
}


/**
 * Generate a unique meta field key
 *
 * @param $key
 * @return string
 */
function ws_ls_meta_fields_generate_field_key( $original_key ) {

	$key = ws_ls_meta_fields_key_sanitise( $original_key );

	while ( true === ws_ls_meta_fields_key_exist( $key ) ) {
		$key = $original_key . '-' . rand( 1, 10000 );
		$key = ws_ls_meta_fields_key_sanitise( $key );
	}

	return $key;
}