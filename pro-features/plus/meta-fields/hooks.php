<?php

defined('ABSPATH') or die("Jog on!");

/**
    AJAX: Fetch all meta fields for main list
 **/
function ws_ls_meta_fields_ajax_list() {


    check_ajax_referer( 'ws-ls-user-tables', 'security' );

    $columns = [
                    [ 'name' => 'id', 'title' => 'ID', 'visible'=> true, 'type' => 'number' ],
                    [ 'name' => 'field_name', 'title' => __('Field / Question', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                    [ 'name' => 'field_key', 'title' => __('Key', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
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
			$row[ 'id' ]        = $row[ 'id' ];
			$row[ 'slug' ]      = $row[ 'slug' ];
			$row[ 'name' ]      = $row[ 'name' ];
			$row[ 'count' ]     = ws_ls_meta_fields_groups_count( $row[ 'id' ] );
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

/**
 * Wrap around [wt-chart] but set weight/targets to false
 * @param $user_defined_arguments
 *
 * @return bool|string
 */
function ws_ls_meta_fields_shortcode_chart( $user_defined_arguments ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return false;
	}

	if ( false === is_array( $user_defined_arguments ) ) {
		$user_defined_arguments = [];
	}

	$user_defined_arguments[ 'show-weight' ] = false;
	$user_defined_arguments[ 'show-target' ] = false;
	$user_defined_arguments[ 'show-custom-fields' ] = true;

	return ws_ls_shortcode_chart( $user_defined_arguments );
}
add_shortcode( 'wt-custom-fields-chart', 'ws_ls_meta_fields_shortcode_chart' );
