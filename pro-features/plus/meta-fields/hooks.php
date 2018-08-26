<?php

	defined('ABSPATH') or die("Jog on!");

    /**
        AJAX: Fetch all meta fields for main list
     **/
    function ws_ls_meta_fields_ajax_list() {


        check_ajax_referer( 'ws-ls-user-tables', 'security' );

        $columns = [
                        [ 'name' => 'id', 'title' => 'ID', 'visible'=> false, 'type' => 'number' ],
                        [ 'name' => 'field_name', 'title' => __('Name', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                        [ 'name' => 'field_key', 'title' => __('Key', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                        [ 'name' => 'field_type', 'title' => __('Type', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                        [ 'name' => 'sort', 'title' => __('Display Order', WE_LS_SLUG), 'visible'=> true, 'type' => 'number' ],
                        [ 'name' => 'mandatory', 'title' => __('Mandatory', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
                        [ 'name' => 'enabled', 'title' => __('Enabled', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ]
        ];

        $rows = [
            [ 'id' => 12, 'field_name' => 'NAME'],
            [ 'id' => 113, 'field_name' => 'ALI']

        ];

        $meta_fields = ws_ls_meta_fields();

        // Format Row data
        for ( $i = 0 ; $i < count( $meta_fields ) ; $i++ ) {

            $meta_fields[ $i ][ 'field_type' ] = ws_ls_meta_fields_types_get_string( $meta_fields[ $i ][ 'field_type' ] );
            $meta_fields[ $i ][ 'enabled' ] = ws_ls_meta_fields_enabled_get_string( $meta_fields[ $i ][ 'enabled' ] );
            $meta_fields[ $i ][ 'mandatory' ] = ws_ls_meta_fields_enabled_get_string( $meta_fields[ $i ][ 'mandatory' ] );
        }

        $data = [
                    'columns' => $columns,
                    'rows' => $meta_fields
        ];

        wp_send_json($data);

    }
    add_action( 'wp_ajax_meta_fields_full_list', 'ws_ls_meta_fields_ajax_list' );

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

    //todo: hook on to delete hooks in meta fields DB - clear user cache for current logged in uer

    //todo:
// Hook onto do_action( 'wlt-meta-fields-deleted', $meta_field_id ); and delete all attachements if a photo field


    /**
     * Listens to the delete entry hook and deletes any photos / meta entries
     *
     * @param $entry_id
     */
    function ws_ls_meta_fields_tidy_entries_and_attachments( $entry ) {

        // Fetch all meta entries for entry ID ($entry['db_row_id']_

        // Get all Photo fields (enabled or disabled)




        // Delete all attachment ids for photo vidoes

        // Delete all meta entries for this field

    }
   // add_action();


    //TODO:
    /*
     * Hook onto do_action(WE_LS_HOOK_DATA_ENTRY_DELETED, $weight_entry);
     *
     * 1) Delete all photos from media library for given attachment /ID stored in Photo meta fields!
     * 2) Delete all meta entries for this ID
     */


// ------------------------------------------------------------------
// Hooks
// ------------------------------------------------------------------

///**
// * If an entry is deleted, check for a photo ID. If it exists, delete attachment from media library
// */
//function ws_ls_photos_tidy_up_after_entry_deleted( $entry ) {
//
//    // TODO: Search for meta data
//
//    if ( false === empty( $entry['photo_id'] ) && true === is_numeric( $entry['photo_id'] ) ) {
//        wp_delete_attachment( intval( $entry['photo_id']) , true );
//        ws_ls_delete_cache_for_given_user( $entry['user_id'] );
//    }
//}
//add_action(WE_LS_HOOK_DATA_ENTRY_DELETED, 'ws_ls_photos_tidy_up_after_entry_deleted');

/**
 * If admin deletes a user's photo from the media library, ensure there is no foreign key to it in DB
 * @param $attachment_id
 */
function ws_ls_photos_tidy_up_after_attachment_deleted($attachment_id) {

    //todo: Get all photo fields and delete entries where the ID matches entry value

    if ( false === empty($attachment_id) && true === is_numeric($attachment_id)) {
        global $wpdb;
        $sql = $wpdb->prepare('Update ' . $wpdb->prefix . WE_LS_TABLENAME . ' SET photo_id = null where photo_id = %d', $attachment_id);
        $wpdb->query($sql);
    }
}
add_action('delete_attachment', 'ws_ls_photos_tidy_up_after_attachment_deleted');