<?php

defined('ABSPATH') or die("Jog on!");

/**
 *
 * Return all meta enties for weight entry
 *
 * @param $entry_id
 * @return array
 */
function ws_ls_meta( $entry_id ) {

	global $wpdb;

	$sql = $wpdb->prepare( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' where entry_id = %d', $entry_id );

	$data = $wpdb->get_results( $sql, ARRAY_A );

	return $data;
}

/**
 *
 * Return all meta entries for meta field
 *
 * @param $meta_field_id
 * @return array
 */
function ws_ls_meta_for_given_meta_field( $meta_field_id ) {

	global $wpdb;

	$sql = $wpdb->prepare( 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' where meta_field_id = %d', $meta_field_id );

	$data = $wpdb->get_results( $sql, ARRAY_A );

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
	if ( false === ws_ls_array_check_fields( $data, [ 'entry_id', 'key', 'value' ] ) ) {
		return false;
	}

	// Get Meta Field ID
    if ( true === is_numeric( $data['key'] ) ) {

        $meta_field['id'] = (int) $data['key'];

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
 * @param $meta_field_id
 * @return bool
 */
function ws_ls_meta_delete( $entry_id, $meta_field_id ) {

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY, [ 'entry_id' => $entry_id, 'meta_field_id' => $meta_field_id ], [ '%d', '%d' ] );

    do_action( 'wlt-meta-delete', $entry_id );

	return ( 1 === $result );
}

/**
 * Delete previous migrated values
 *
 * @return bool
 */
function ws_ls_meta_delete_migrated() {

	if ( false === is_admin() ) {
		return false;
	}

	global $wpdb;

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_ENTRY, [ 'migrate' => 1 ], [ '%d' ] );

	return ( 1 === $result );
}

/**
 * Delete all meta entries for given weight entry
 *
 * @param $entry_id
 * @return bool
 */
function ws_ls_meta_delete_for_entry( $entry_id ) {

	if ( false === is_admin() ) {
		return false;
	}

	global $wpdb;

    do_action( 'wlt-meta-entries-delete', $entry_id );

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

	if ( false === is_admin() ) {
		return false;
	}

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
            return ( false === empty( $value['enabled'] ) && 2 === (int) $value['enabled'] );
        });

        return $fields;
    }

    return [];

}

/*
 * Fetch all enabled plottable meta fields
 *
 * @return array
 */
function ws_ls_meta_fields_plottable() {

	$fields = ws_ls_meta_fields_enabled();

	if ( true === empty( $fields ) ) {
		return [];
	}

	$fields = array_filter( $fields, function( $field ) {
		return ! empty( $field[ 'plot_on_graph' ] );
	});

	return array_values( $fields );
}

/**
 * Fetch all meta fields
 *
 * @param bool $exclude_system
 * @param bool $ignore_cache
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
        $sql .= ' Where `system` = 0';
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
 * @param $field
 * @return bool     true if success
 */
function ws_ls_meta_fields_update( $field ) {

	if ( false === is_admin() ) {
		return false;
	}

    // Ensure we have the expected fields.
    if ( false === ws_ls_array_check_fields( $field, [ 'id', 'abv', 'field_name', 'field_type', 'suffix', 'mandatory', 'enabled' ] ) ) {
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
	ws_ls_cache_user_delete( 'custom-fields-groups' );

	do_action( 'wlt-meta-fields-updating-meta-field', $id );

    if ( 1 === $result ) {

        // If the field type has changed in this update then delete existing data entries (as they won't relate to the new field type).
        if( (int) $previous_values['field_type'] !== (int) $field['field_type'] ) {
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
 * @param $field    array:  field_key
 *                          field_name
 *                          abv
 *                          system
 *                          unit_id
 *
 * @return bool     true if success
 */
function ws_ls_meta_fields_add( $field ) {

	if ( false === is_admin() ) {
		return false;
	}

    // Ensure we have the expected fields.
    if ( false === ws_ls_array_check_fields( $field, [ 'abv', 'field_name', 'field_type', 'suffix', 'mandatory', 'enabled' ] ) ) {
        return false;
    }

    // Sluggify key
    $field['field_key'] = ws_ls_meta_fields_generate_field_key( $field['field_name'] );

    unset( $field[ 'id' ] );

    global $wpdb;

    $formats = ws_ls_meta_formats( $field );

    $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS , $field, $formats );

	ws_ls_cache_user_delete( 'meta-fields' );
	ws_ls_cache_user_delete( 'custom-fields-groups' );

    return ( false === $result ) ? false : $wpdb->insert_id;
}

/**
 * Delete a meta field
 *
 * @param $id       meta field ID to delete
 * @return bool     true if success
 */
function ws_ls_meta_fields_delete( $id ) {

	if ( false === is_admin() ) {
		return false;
	}

    global $wpdb;

    do_action( 'wlt-meta-fields-deleting-meta-field', $id );

    $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_FIELDS, [ 'id' => $id ], [ '%d' ] );

	ws_ls_meta_delete_for_meta_field( $id );

	ws_ls_cache_user_delete( 'meta-fields' );
	ws_ls_cache_user_delete( 'custom-fields-groups' );

    return ( 1 === $result );
}

/**
 * Does unit field_key already exist?
 *
 * @param $key
 * @return bool
 */
function ws_ls_meta_fields_key_exist( $key ) {

    global $wpdb;

    $sql = $wpdb->prepare('Select count(*) from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where field_key = %s', $key );

    $count = $wpdb->get_var( $sql );

    return ( 0 === (int) $count ) ? false : true;
}

/**
 * Get details for given meta field
 *
 * @param $key
 * @return array|bool|object|void|null
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
 * @param $id
 * @return array|bool|object|void|null
 */
function ws_ls_meta_fields_get_by_id( $id ) {

    global $wpdb;

    $sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' where id = %d limit 0, 1', $id );

    $meta_field = $wpdb->get_row( $sql, ARRAY_A );

    return ( false === empty( $meta_field ) ) ? $meta_field : false;
}

/**
 * Fetch all user IDs that have a reference to this field (allows us to clear cache)
 *
 * @param $meta_field_id
 * @return array|object|null
 */
function ws_ls_meta_fields_get_user_ids_for_this_meta_field( $meta_field_id ) {

    global $wpdb;

    $sql = 'Select distinct weight_user_id from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' e ' .
        ' inner join ' . $wpdb->prefix . WE_LS_TABLENAME . ' d on e.entry_id = d.id
	         inner join ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' f on f.id = e.meta_field_id
	         where meta_field_id = %d';

    $sql = $wpdb->prepare( $sql, $meta_field_id );

    $results = $wpdb->get_results($sql, ARRAY_A );

    return ( false === empty( $results ) ) ? wp_list_pluck( $results, 'weight_user_id' ) : $results;
}

/**
 * Return data formats
 *
 * @param $data
 * @return array
 */
function ws_ls_meta_formats( $data ) {

    $formats = [
        'id' 					=> '%d',
        'field_key'				=> '%s',
        'field_name' 			=> '%s',
        'abv' 					=> '%s',
        'entry_id' 				=> '%d',
        'system' 				=> '%d',
        'unit_id' 				=> '%d',
		'meta_field_id' 		=> '%d',
		'value' 				=> '%s',
		'field_type' 			=> '%d',
		'suffix' 				=> '%s',
        'enabled'				=> '%d',
        'sort' 					=> '%d',
        'mandatory' 			=> '%d',
        'hide_from_shortcodes' 	=> '%d',
		'plot_on_graph'			=> '%d',
		'plot_colour'			=> '%s',
		'migrate'				=> '%d',
        'group_id'				=> '%d'
    ];

    foreach ( $data as $key => $value) {
        if ( false === empty( $formats[ $key ] ) ) {
            $return[] = $formats[ $key ];
        }
    }

    return $return;
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
 * @param $original_key
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

/**
 * Check if the slug already exists
 *
 * @param $slug
 * @param $existing_id
 *
 * @return bool
 */
function ws_ls_meta_fields_slug_is_unique( $slug, $existing_id = NULL ) {

	if ( true === empty( $slug ) ) {
		return false;
	}

	global $wpdb;

	$sql = $wpdb->prepare( 'SELECT count( slug ) FROM ' . $wpdb->prefix . WE_LS_MYSQL_META_GROUPS . ' where slug = %s', $slug );

	if ( false === empty( $existing_id ) ) {
		$sql .= $wpdb->prepare( ' and id <> %d', $existing_id );
	}

	$row = $wpdb->get_var( $sql );

	return ( empty( $row ) );
}

/**
 * Fetch all groups
 *
 * @param bool $include_none
 * @return array
 */
function ws_ls_meta_fields_groups( $include_none = true ) {

	global $wpdb;

	if ( false === is_admin() && $cache = ws_ls_cache_user_get( 'custom-fields-groups', 'all' ) ) {
		return $cache;
	}

	$sql = 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_GROUPS . ' order by name asc';

	$data = $wpdb->get_results( $sql , ARRAY_A );

	ws_ls_cache_user_set( 'custom-fields-groups', 'all' , $data );

	if ( true === $include_none ) {
		$data = array_merge( [ [ 'id' => 0, 'name' => __('None', WE_LS_SLUG ) ] ], $data );
	}

	return $data;
}

/**
 * Delete a group
 *
 * @param $id       award ID to delete
 * @return bool     true if success
 */
function ws_ls_meta_fields_groups_delete( $id ) {

	if ( false === is_admin() ) {
		return;
	}

	global $wpdb;

	ws_ls_log_add( 'custom-field-group', sprintf( 'Deleting: %d', $id ) );

	do_action( 'wlt-custom-field-group-deleting', $id );

	$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_META_GROUPS, [ 'id' => $id ], [ '%d' ] );

	ws_ls_cache_user_delete( 'custom-fields-groups' );

	return ( 1 === $result );
}

/**
 * Fetch count of number of feilds in a group
 *
 * @param $id
 *
 * @return int
 */
function ws_ls_meta_fields_groups_count( $id ) {

	global $wpdb;

	if ( $cache = ws_ls_cache_user_get( 'custom-fields-groups', 'count-' . $id ) ) {
		return $cache;
	}

	$sql = $wpdb->prepare('Select count( id ) from ' . $wpdb->prefix . WE_LS_MYSQL_META_GROUPS . ' where group_id = %d', $id );

	$count = $wpdb->get_var( $sql );

	$count = (int) $count;

	ws_ls_cache_user_set( 'custom-fields-groups', 'count-' .$id , $count );

	return $count;
}

/**
 * Add a group
 *
 * @param $name
 *
 * @return bool|void
 */
function ws_ls_meta_fields_groups_add( $name ) {

	if ( false === is_admin() ) {
		return false;
	}

	if ( true === empty( $name ) ) {
		return false;
	}

	global $wpdb;

	ws_ls_log_add( 'custom-field-group', sprintf( 'Adding: %s', $name ) );

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_META_GROUPS , [ 'name' => $name, 'slug' => ws_ls_meta_fields_group_slug_generate( $name ) ], [ '%s', '%s' ] );

	ws_ls_cache_user_delete( 'custom-fields-groups' );

	return ( false === $result ) ? false : $wpdb->insert_id;
}
