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

    // TODO

    return [];
}

/**
 *
 * Add / Edit a meta field.
 *
 * @param $unit    array:   id - if specified, update
 *                          key
 *                          display_name
 *                          abbreviation
 *                          system              // if true, hide from UI e.g. photo ID
 *                          display_on_chart
 *                          type
 * @return bool     true if success
 */
function ws_ls_meta_fields_add_update( $unit ) {

    // TODO

    return false;
}

/**
 * Delete a meta field
 *
 * @param $id       meta field ID to delete
 * @return bool     true if success
 */
function ws_ls_meta_fields_delete( $id ) {

    // TODO

    return false;
}


/**
 * Fetch all units
 *
 * @return array
 */
function ws_ls_meta_units() {

    // TODO

    return [];
}

/**
 *
 * Add / Edit a unit.
 *
 * @param $unit    array:   id - if specified, update
 *                          name
 *                          abbreviation
 *                          chartable
 *                          type
 * @return bool     true if success
 */
function ws_ls_meta_unit_add_update( $unit ) {

    // TODO

    return false;
}

/**
 * Delete a Unit
 *
 * @param $id       unit ID to delete
 * @return bool     true if success
 */
function ws_ls_meta_unit_delete( $id ) {

    // TODO

    return false;
}