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

