<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     * Fetch all awards for the given user
     *
     * @param $user_id
     * @return array
     */
    function ws_ls_awards_db_given_get( $user_id, $order_by = 'value' ) {

        $cache_key = 'awards-given-' . $order_by;

        $cache = ws_ls_cache_user_get( $user_id, $cache_key );

        if ( true === is_array( $cache ) ) {
            return $cache;
        }

        global $wpdb;

        $sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN . ' g INNER JOIN 
                                ' . $wpdb->prefix . WE_LS_MYSQL_AWARDS . ' a on g.award_id = a.id where user_id = %d', $user_id);

        $sql .= ( 'value' === $order_by ) ? ' order by a.category, CAST( a.value as DECIMAL( 10, 5 ) )' : ' order by g.timestamp desc' ;

        $results = $wpdb->get_results( $sql, ARRAY_A );

        ws_ls_cache_user_set( $user_id, $cache_key, $results );

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

	/**
	 * Delete awards to a user
	 *
	 * @param $user_id
	 * @return bool
	 */
	function ws_ls_awards_db_delete_awards_for_user( $user_id ) {

		global $wpdb;

		$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN , [ 'user_id' => $user_id ], [ '%d' ] );

		ws_ls_cache_user_delete( $user_id );

	}

    /**
     * Fetch all Awards
     *
     * @return array
     */
    function ws_ls_awards( $ignore_cache = false ) {

        global $wpdb;

        if ( false === $ignore_cache && $cache = ws_ls_cache_user_get( 'awards', 'all' ) ) {
          return $cache;
        }

        $sql = 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_AWARDS;


        $sql .= ' order by title asc';

        $data = $wpdb->get_results( $sql , ARRAY_A );

        ws_ls_cache_user_set( 'awards', 'all' , $data );

        return $data;
    }

    /**
     *
     * Add an award
     *
     * @param $award
     *
     * @return bool     true if success
     */
    function ws_ls_awards_add( $award ) {

        if ( false === is_admin() ) {
            return false;
        }

        // Ensure we have the expected fields.
        if ( false === ws_ls_array_check_fields( $award, [ 'title', 'category', 'value',
                                                            'badge', 'custom_message', 'max_awards', 'enabled', 'send_email', 'apply_to_update', 'apply_to_add' ] ) ) {
            return false;
        }

        unset( $award[ 'id' ] );

        global $wpdb;

        $formats = ws_ls_awards_formats( $award );

        $result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_AWARDS , $award, $formats );

        ws_ls_cache_user_delete( 'awards' );

        return ( false === $result ) ? false : $wpdb->insert_id;
    }

    /**
     *
     * Update an award
     *
     * @param $award
     *
     * @return bool     true if success
     */
    function ws_ls_awards_update( $award ) {

        if ( false === is_admin() ) {
            return false;
        }

        // Ensure we have the expected fields.
        if ( false === ws_ls_array_check_fields( $award, [ 'id' ] ) ) {
            return false;
        }

        // Extract ID
        $id = $award[ 'id' ];

        unset( $award[ 'id' ] );

        global $wpdb;

        $formats = ws_ls_awards_formats( $award );

        $result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_AWARDS, $award, [ 'id' => $id ], $formats, [ '%d' ] );

        ws_ls_cache_user_delete( 'awards' );

        ws_ls_log_add( 'awards', sprintf( 'Award updated: %s', $id ) );

        do_action( 'wlt-awards-updated', $id );

        return ( false !== $result );
    }

    /**
     * Delete an award
     *
     * @param $id       award ID to delete
     * @return bool     true if success
     */
    function ws_ls_awards_delete( $id ) {

        if ( false === is_admin() ) {
            return false;
        }

        global $wpdb;

        ws_ls_log_add( 'awards', sprintf( 'Deleting award: %d', $id ) );

        do_action( 'wlt-awards-deleting', $id );

        $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_AWARDS, [ 'id' => $id ], [ '%d' ] );

        ws_ls_awards_delete_all_given( $id );

        ws_ls_cache_user_delete( 'awards' );

        return ( 1 === $result );
    }

    /**
     * Delete all entries for award
     *
     * @param $meta_field_id
     * @return bool
     */
    function ws_ls_awards_delete_all_given( $award_id ) {

        if ( false === is_admin() ) {
            return false;
        }

        global $wpdb;

        ws_ls_log_add( 'awards', sprintf( 'Deleting awards given for: %d', $award_id ) );

        $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN, [ 'award_id' => $award_id ], [ '%d' ] );

        return ( 1 === $result );

    }
    do_action( 'wlt-awards-deleting', 'ws_ls_awards_delete_all_given' );

	/**
	 * Get details for an award
	 *
	 * @param $key
	 */
	function ws_ls_award_get( $id ) {

		global $wpdb;

		$sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_AWARDS . ' where id = %s limit 0, 1', $id );

		$award = $wpdb->get_row( $sql, ARRAY_A );

		return ( false === empty( $award ) ) ? $award : false;
	}

    /**
     * Delete all existing given awards
     *
     * @return bool     true if success
     */
    function ws_ls_awards_delete_all_previously_given( ) {

        if ( false === is_admin() ) {
            return false;
        }

        global $wpdb;

        $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN );

    }

    /**
     * Return data formats
     *
     * @param $data
     * @return array
     */
    function ws_ls_awards_formats( $data ) {

        $formats = [
            'id' => '%d',
            'title' => '%s',
            'category' => '%s',
            'gain_loss' => '%s',
            'badge' => '%d',
	        'bmi_equals' => '%d',
            'compare' => '%s',
            'custom_message' => '%s',
            'max_awards' => '%d',
            'enabled' => '%d',
            'send_email' => '%d',
            'apply_to_update' => '%d',
            'apply_to_add' => '%d',
            'value' => '%s'
        ];

        $return = [];

        foreach ( $data as $key => $value) {
            if ( false === empty( $formats[ $key ] ) ) {
                $return[] = $formats[ $key ];
            }
        }

        return $return;
    }