<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     * Fetch all the award IDs previous given to this user.
     * @param null $user_id
     *
     */
    function ws_ls_awards_db_previous_awards_get( $user_id = NULL ) {

        $user_id = $user_id ?: get_current_user_id();

        // TODO: Fetch all previous award IDs issued to the current user ID.
        return [
//            1,
//            1,
//            10,
//            2,
//            2,
//            2,
//            5,
//            33
        ];

    }