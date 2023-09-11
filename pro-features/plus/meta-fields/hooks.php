<?php

defined('ABSPATH') or die("Jog on!");

/**
 * AJAX handler for clearing Target Weight
 */
function ws_ls_meta_fields_ajax_accumulator() {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		wp_send_json( [ 'error' => true, 'text' => 'Meta fields are not enabled' ] );
	}

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$increment = ws_ls_post_value( 'increment' );

	if( true === empty( $increment ) ) {
		wp_send_json( [ 'error' => true, 'text' => 'Missing increment' ] );
	}

	$meta_field_id = ws_ls_post_value( 'meta-field-id' );

	if( true === empty( $meta_field_id ) ) {
		wp_send_json( [ 'error' => true, 'text' => 'Missing meta field ID' ] );
	}

	$meta_field = ws_ls_meta_fields_get_by_id( $meta_field_id );

	if ( false === $meta_field ||
	        0 !== (int) $meta_field[ 'field_type' ] ) {
		wp_send_json( [ 'error' => true, 'text' => 'Meta field does not exist or not numeric' ] );
	}

	$return = [ 'error' => false, 'value' => '', 'previous' => '', 'new-entry' => false ];

	// Do we have an entry for today's date?
	$return[ 'entry_id' ]       = ws_ls_db_entry_for_date( get_current_user_id(), date('Y-m-d' ) );
	$increment                  = (int) $increment;

	// If no entry for today, then we need to add one!
	if ( true === empty( $return[ 'entry_id' ] ) ) {
		$return[ 'new-entry' ]  = true;
		$return[ 'entry_id' ]   = ws_ls_db_entry_set( [ 'weight_date' => date('Y-m-d' ) ], get_current_user_id() );
		$return[ 'value' ]      = ( $increment > 0 ) ? $increment : 0;
	} else {
		$return[ 'previous' ]   = (int) ws_ls_meta_fields_get_value_for_entry( $return[ 'entry_id' ], $meta_field_id );
		$return[ 'value' ]      = $return[ 'previous' ] + $increment;

		// Don't allow minus values // TODO: Review this - should we allow them?
		if ( $return[ 'value' ] < 0 ) {
			$return[ 'value' ] = 0;
		}
	}

	// Final check - do we have an entry ID?
	if( true === empty( $return[ 'entry_id' ] ) ) {

		$return[ 'error' ] = true;
		$return[ 'text' ] = 'Error loading entry ID';

		wp_send_json( $return );
	}

	$result = ws_ls_meta_add_to_entry([
			'entry_id'      => $return[ 'entry_id' ],
			'key'           => $meta_field_id,
			'value'         => $return[ 'value' ]
		]
	);

	if ( false === $result ) {
		wp_send_json( [ 'error' => true, 'text' => 'Error incrementing meta value' ] );
	}

	wp_send_json( $return );
}
add_action( 'wp_ajax_ws_ls_meta_field_accumulator', 'ws_ls_meta_fields_ajax_accumulator' );

/**
    AJAX: Fetch all meta fields for main list
 **/
function ws_ls_meta_fields_ajax_list() {


    check_ajax_referer( 'ws-ls-user-tables', 'security' );

    $columns = [
                    [ 'name' => 'id', 'title' => 'ID', 'visible'=> true, 'type' => 'number' ],
                    [ 'name' => 'field_name', 'title' => __('Field / Question', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                    [ 'name' => 'field_key', 'title' => __('Key / Slug', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                    [ 'name' => 'field_type', 'title' => __('Type', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                    [ 'name' => 'sort', 'title' => __('Display Order', WE_LS_SLUG), 'visible'=> true, 'type' => 'number' ],
                    [ 'name' => 'group', 'title' => __('Group', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                    [ 'name' => 'mandatory', 'title' => __('Mandatory', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                    [ 'name' => 'enabled', 'title' => __('Enabled', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ]
    ];

    $meta_fields = ws_ls_meta_fields();

    // Format Row data
    for ( $i = 0 ; $i < count( $meta_fields ) ; $i++ ) {

        $meta_fields[ $i ][ 'group' ]       = ws_ls_meta_fields_groups_get_field( $meta_fields[ $i ][ 'group_id' ] );
        $meta_fields[ $i ][ 'field_type' ]  = ws_ls_meta_fields_types_get_string( $meta_fields[ $i ][ 'field_type' ] );
        $meta_fields[ $i ][ 'enabled' ]     = ws_ls_boolean_as_yes_no_string( $meta_fields[ $i ][ 'enabled' ] );
        $meta_fields[ $i ][ 'mandatory' ]   = ws_ls_boolean_as_yes_no_string( $meta_fields[ $i ][ 'mandatory' ] );
	    $meta_fields[ $i ][ 'field_name' ]  = stripslashes( $meta_fields[ $i ][ 'field_name' ] );
    }

    $data = [
                'columns' => $columns,
                'rows' => $meta_fields
    ];

    wp_send_json($data);

}
add_action( 'wp_ajax_meta_fields_full_list', 'ws_ls_meta_fields_ajax_list' );

/**
Get Groups
 **/
function ws_ls_meta_fields_ajax_custom_field_groups_get(){

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$table_id = ws_ls_post_value('table_id' );

	if ( $cache = ws_ls_cache_user_get( 'custom-fields-groups', $table_id ) ) {
		wp_send_json( $cache );
	}

	$columns = [
		[ 'name' => 'id', 'title' => __('Group ID', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'number', 'visible' => false ],
		[ 'name' => 'slug', 'title' => __( 'Slug', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ],
		[ 'name' => 'name', 'title' => __( 'Name', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ],
		[ 'name' => 'count', 'title' => __( 'No. Fields', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'number' ],
	];

	$rows = [];

	if ( true === WS_LS_IS_PRO ) {

		$rows = ws_ls_meta_fields_groups( false );

		foreach ( $rows as &$row ) {
			$row[ 'count' ] = ws_ls_meta_fields_groups_count( $row[ 'id' ] );
		}

	}

	$data = [
		'columns' => $columns,
		'rows' => $rows,
		'table_id' => $table_id
	];

	ws_ls_cache_user_set( 'custom-fields-groups', $table_id, $data );

	wp_send_json( $data );

}
add_action( 'wp_ajax_get_custom_field_groups', 'ws_ls_meta_fields_ajax_custom_field_groups_get' );

/**
 * AJAX: Delete given group
 */
function ws_ls_ajax_meta_fields_groups_delete() {

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$id = ws_ls_get_numeric_post_value('id');

	if ( false === empty( $id ) ) {

		$result = ws_ls_meta_fields_groups_delete( $id );

		if ( true === $result ) {
			wp_send_json( 1 );
		}
	}

	wp_send_json( 0 );

}
add_action( 'wp_ajax_custom_field_groups_delete', 'ws_ls_ajax_meta_fields_groups_delete' );

/**
 * AJAX: Delete given meta field ID
 */
function ws_ls_meta_fields_ajax_delete() {

    if ( false === ws_ls_meta_fields_is_enabled() ) {
        return;
    }

    check_ajax_referer( 'ws-ls-user-tables', 'security' );

    $id = ws_ls_get_numeric_post_value('id');

    if ( false === empty( $id ) ) {

        $result = ws_ls_meta_fields_delete( $id );

        if ( true === $result ) {
            wp_send_json( 1 );
        }
    }

    wp_send_json( 0 );

}
add_action( 'wp_ajax_meta_fields_delete', 'ws_ls_meta_fields_ajax_delete' );

/**
 * Check if a the deleted meta field was a photo field. If so, delete all attachments.
 *
 * @param $meta_field_id
 */
function ws_ls_meta_fields_hook_delete_photos_for_deleted_meta_field( $meta_field_id ) {

    if ( false === ws_ls_meta_fields_is_enabled() ) {
	    return;
    }

    // Check we actually have a meta field!
    if ( false === ws_ls_meta_fields_photos_is_photo_field( $meta_field_id ) ) {
        return;
    }

    ws_ls_meta_fields_photos_delete_all_photos_for_meta_field( $meta_field_id );

    // Clear cache for all users that have entry for this meta field
    $user_ids = ws_ls_meta_fields_get_user_ids_for_this_meta_field( $meta_field_id );

    ws_ls_delete_cache_for_given_users( $user_ids );

}
add_action( 'wlt-meta-fields-deleting-meta-field', 'ws_ls_meta_fields_hook_delete_photos_for_deleted_meta_field' );


/**
 * Listens to the delete entry hook and deletes any photos / meta entries
 *
 * @param $entry
 */
function ws_ls_meta_fields_tidy_entries_and_attachments( $entry ) {

    if ( false === ws_ls_meta_fields_is_enabled() ) {
	    return;
    }

    $photos = ws_ls_meta_fields_photos_for_given_entry_id( $entry[ 'id' ] );

    foreach ( $photos as $photo ) {
	    if ( false === empty( $photo['value'] ) ) {
		    wp_delete_attachment( (int) $photo['value'] , true );
	    }
    }

    ws_ls_meta_delete_for_entry( $entry[ 'id' ] );

    ws_ls_cache_user_delete( $entry[ 'user-id' ] );

}
add_action( 'wlt-hook-data-entry-deleted', 'ws_ls_meta_fields_tidy_entries_and_attachments' );


/**
 * Delete all meta entries for a deleted attachment id
 *
 * @param $attachment_id
 */
function ws_ls_photos_tidy_up_after_attachment_deleted( $attachment_id ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return;
	}

	ws_ls_meta_fields_photos_delete_entry( $attachment_id );

}
add_action('delete_attachment', 'ws_ls_photos_tidy_up_after_attachment_deleted');

/**
 * Clear cache for users that use the meta field that is being updated or deleted.
 *
 * @param $meta_field_id
 */
function ws_ls_meta_fields_clear_cache_for_users_using_this_field( $meta_field_id ) {

	$user_ids = ws_ls_meta_fields_get_user_ids_for_this_meta_field( $meta_field_id );

	ws_ls_delete_cache_for_given_users( $user_ids );

}
add_action('wlt-meta-fields-deleting-meta-field', 'ws_ls_meta_fields_clear_cache_for_users_using_this_field', 50);
add_action('wlt-meta-fields-updating-meta-field', 'ws_ls_meta_fields_clear_cache_for_users_using_this_field', 50);

/**
 * Delete awards for given user
 * @param $user_id
 */
function ws_ls_meta_fields_delete_for_given_user( $user_id ) {
	ws_ls_awards_db_delete_awards_for_user( $user_id );
}
add_action( 'wlt-hook-data-user-deleted', 'ws_ls_meta_fields_delete_for_given_user' );
