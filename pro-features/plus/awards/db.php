<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     * Fetch all awards for the given user
     *
     * @param $user_id
     * @return array
     */
    function ws_ls_awards_db_given_get( $user_id ) {

        $cache = ws_ls_cache_user_get( $user_id, 'awards-given' );

        if ( true === is_array( $cache ) ) {
            return $cache;
        }

        global $wpdb;

        $sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN . ' where user_id = %d', $user_id);

        $results = $wpdb->get_results( $sql, ARRAY_A );

        ws_ls_cache_user_set( $user_id, 'awards-given', $results );

        return $results;
    }

    /**
     * Add an award to a user
     *
     * @param $user_id
     * @param $award_id
     * @return bool
     */
    function ws_ls_awards_db_given_add( $user_id, $award_id ) {

        global $wpdb;

        $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN , [ 'user_id' => $user_id , 'award_id' => $award_id ], [ '%d', '%d' ] );

        ws_ls_cache_user_delete( $user_id );

        return ( false === $result ) ? false : $wpdb->insert_id;
    }

