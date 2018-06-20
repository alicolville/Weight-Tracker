<?php

defined('ABSPATH') or die("Jog on!");

/**
 *
 * Return all meta fields for weight entry
 *
 * @param $weight_entry_id
 * @return array
 */
function ws_ls_meta( $weight_entry_id ) {

    return [];
}

/**
 *
 * Add a meta value to a given weight entry
 *
 * @param $weight_entry_id
 * @param $meta_id
 * @param $value
 * @return bool
 */
function ws_ls_meta_add_to_entry( $weight_entry_id, $meta_id, $value ) {

    //TODO:

    return false;
}

/**
 * Delete given meta field value
 *
 * @param $id
 * @return bool
 */
function ws_ls_meta_delete( $id ) {

    // TODO

    return false;
}

/**
 * Delete all meta fields for given weight entry
 *
 * @param $weight_entry_id
 * @return bool
 */
function ws_ls_meta_delete_for_entry( $weight_entry_id ) {

    // TODO

    return false;
}

/**
 * Fetch all meta fields
 *
 * @return array
 */
function ws_ls_meta_fields() {

    global $wpdb;

    return $wpdb->get_results( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' order by field_name asc', ARRAY_A );

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
    if ( false === ws_ls_meta_check_fields( $field, [ 'id', 'abv', 'display_on_chart', 'field_key', 'field_name', 'unit_id', 'system' ] ) ) {
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
    if ( false === ws_ls_meta_check_fields( $field, [ 'abv', 'display_on_chart', 'field_key', 'field_name', 'unit_id', 'system' ] ) ) {
        return false;
    }

    // Sluggify key
    $field['field_key'] = ws_ls_meta_fields_key_sanitise( $field['field_key'] );

    // Ensure field_key doesn't already exist
    if ( true === ws_ls_meta_fields_key_exist( $field['field_key'] ) ) {
        return false;
    }

    global $wpdb;

    $formats = ws_ls_meta_formats( $field );

    $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS , $field, $formats );

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
 * Fetch all units
 *
 * @return array
 */
function ws_ls_meta_units() {

    global $wpdb;

    return $wpdb->get_results( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_UNITS . ' order by field_name asc', ARRAY_A );

}

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
function ws_ls_meta_unit_update( $unit ) {

    // Ensure we have the expected fields.
    if ( false === ws_ls_meta_check_fields( $unit, [ 'abv', 'chartable', 'field_name', 'id' ] ) ) {
        return false;
    }

    // May seem daft, but for now, do not allow field keys to be updated once inserted.
    unset( $unit[ 'field_key' ] );

    // Extract ID
    $id = $unit[ 'id' ];

    unset( $unit[ 'id' ] );

    global $wpdb;

    $formats = ws_ls_meta_formats( $unit );

    $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_META_UNITS, $unit, [ 'id' => $id ], $formats, [ '%d' ] );

    return ( 1 === $result );
}

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
function ws_ls_meta_unit_add( $unit ) {

        // Ensure we have the expected fields.
        if ( false === ws_ls_meta_check_fields( $unit, [ 'abv', 'chartable', 'field_key', 'field_name' ] ) ) {
            return false;
        }

        // Sluggify key
        $unit['field_key'] = ws_ls_meta_fields_key_sanitise( $unit['field_key'] );

        // Ensure field_key doesn't already exist
        if ( true === ws_ls_meta_unit_key_exist( $unit['field_key'] ) ) {
            return false;
        }

        global $wpdb;

        $formats = ws_ls_meta_formats( $unit );

        $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_UNITS , $unit, $formats );

        return ( false === $result ) ? false : $wpdb->insert_id;
}

/**
 * Delete a Unit
 *
 * @param $id       unit ID to delete
 * @return bool     true if success
 */
function ws_ls_meta_unit_delete( $id ) {

    global $wpdb;

    $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_UNITS, [ 'id' => $id ], [ '%d' ] );

    return ( 1 === $result );
}

/**
 * Does unit field_key already exist?
 *
 * @param $key
 */
function ws_ls_meta_unit_key_exist( $key ) {

    global $wpdb;

    $sql = $wpdb->prepare('Select count(*) from ' . $wpdb->prefix . WE_LS_MYSQL_META_UNITS . ' where field_key = %s', $key );

    $count = $wpdb->get_var( $sql );

    return ( 0 === intval( $count ) ) ? false : true;
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
        'system' => '%d',
        'unit_id' => '%d'
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