<?php

defined('ABSPATH') or die("Jog on!");

/**
 *
 * Return all meta fields for weight entry
 *
 * @param $entry_id
 * @return array
 */
function ws_ls_meta( $entry_id ) {

	global $wpdb;

    $cache_key = 'entry-id-data-' . $entry_id;

    if ( $cache = ws_ls_cache_user_get( 'meta-fields', $cache_key ) ) {
        return $cache;
    }

	$sql = $wpdb->prepare( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' where entry_id = %d', $entry_id );

	$data = $wpdb->get_results( $sql, ARRAY_A );

    ws_ls_cache_user_set( 'meta-fields', $cache_key , $data, 30 );

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

	// Get Meta Field ID
    if ( true === is_numeric( $data['key'] ) ) {

        $meta_field['id'] = intval( $data['key'] );

    } else {
        // Fetch information about the meta field
        $meta_field = ws_ls_meta_fields_get( $data['key'] );

        if ( true === empty( $meta_field ) ) {
            return false;
        }
    }

	// Remove any existing values for this weight entry / key
	ws_ls_meta_delete( $data['entry_id'], $meta_field['id'] );

	unset( $data['key'] );

	$data['meta_field_id'] = $meta_field['id'];

	global $wpdb;

	$formats = ws_ls_meta_formats( $data );

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY , $data, $formats );

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

	return ( 1 === $result );

}

/**
 * Fetch all enabled meta fields
 *
 * @return array
 */
function ws_ls_meta_fields_enabled() {

    $fields = ws_ls_meta_fields();

    if ( false === empty( $fields ) ) {

        // Remove any disabled fields!
        $fields = array_filter( $fields, function( $value ) {
            return ( false === empty( $value['enabled'] ) && 2 === intval( $value['enabled'] ) );
        });

        return $fields;
    }

    return [];

}

/**
 * Fetch all meta fields
 *
 * @return array
 */
function ws_ls_meta_fields( $exclude_system = true, $ignore_cache = false ) {

    global $wpdb;

    $cache_key = 'fields-' . $exclude_system;

    if ( false === $ignore_cache && $cache = ws_ls_cache_user_get( 'meta-fields', $cache_key ) ) {
    	return $cache;
	}

	$sql = 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS;

    if ( true === $exclude_system ) {
        $sql .= ' Where system = 0';
    }

    $sql .= ' order by sort, field_name asc';

    $data = $wpdb->get_results( $sql , ARRAY_A );

	ws_ls_cache_user_set( 'meta-fields', $cache_key , $data );

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
    if ( false === ws_ls_meta_check_fields( $field, [ 'id', 'abv', 'field_name', 'field_type', 'suffix', 'mandatory', 'enabled' ] ) ) {
        return false;
    }

    // May seem daft, but for now, do not allow field keys to be updated once inserted.
    unset( $field[ 'field_key' ] );

    // Extract ID
    $id = $field[ 'id' ];

    unset( $field[ 'id' ] );

    $previous_values = ws_ls_meta_fields_get_by_id( $id );

    global $wpdb;

    $formats = ws_ls_meta_formats( $field );

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS, $field, [ 'id' => $id ], $formats, [ '%d' ] );

	ws_ls_cache_user_delete( 'meta-fields' );

    if ( 1 === $result ) {

        // If the field type has changed in this update then delete existing data entries (as they won't relate to the new field type).
        if( intval( $previous_values['field_type'] ) !== intval( $field['field_type'] ) ) {
            ws_ls_meta_delete_for_meta_field( $id );
        }

        return true;
    }

    return false;
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
    if ( false === ws_ls_meta_check_fields( $field, [ 'abv', 'field_name', 'field_type', 'suffix', 'mandatory', 'enabled' ] ) ) {
        return false;
    }

    // Sluggify key
    $field['field_key'] = ws_ls_meta_fields_generate_field_key( $field['field_name'] );

    unset( $field[ 'id' ] );

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

	ws_ls_meta_delete_for_meta_field( $id );

	ws_ls_cache_user_delete( 'meta-fields' );

    return ( 1 === $result );
}

/**
 * Does unit field_key already exist?
 *
 * @param $key
 */
function ws_ls_meta_fields_key_exist( $key ) {

    global $wpdb;

    $sql = $wpdb->prepare('Select count(*) from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where field_key = %s', $key );

    $count = $wpdb->get_var( $sql );

    return ( 0 === intval( $count ) ) ? false : true;
}

/**
 * Get details for given meta field
 *
 * @param $key
 */
function ws_ls_meta_fields_get( $key ) {

	global $wpdb;

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

    $sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where id = %d limit 0, 1', $id );

    $meta_field = $wpdb->get_row( $sql, ARRAY_A );

    return ( false === empty( $meta_field ) ) ? $meta_field : false;
}

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
		'suffix' => '%s',
        'enabled' => '%d',
        'sort' => '%d',
        'mandatory' => '%d'
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