<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     * Has the user previously been given this award?
     *
     * @param null $user_id
     * @param $award_id
     * @return int
     */
    function ws_ls_awards_user_times_awarded( $user_id = NULL, $award_id ) {

        $user_id = $user_id ?: get_current_user_id();

        $previous_awards = ws_ls_awards_db_previous_awards_get( $user_id );

        $counts = array_count_values( $previous_awards );

        return false === empty( $counts[ $award_id ] ) ? (int) $counts[ $award_id ] : 0;
    }

    /**
     * Determine what awards can be given to this user and the given change type
     *
     * @param null $user_id
     * @param null $change_type
     * @return array
     */
    function ws_ls_awards_to_give( $user_id = NULL, $change_type = NULL, $losing_weight_only = NULL ) {

        $user_id = $user_id ?: get_current_user_id();

       //TODO: from DB and cache


        $counts = [];
        $awards = [];

        $from_db = [
            'weight' =>     [
                [
                    'id' => 1,
                    'title' => 'Lost 10 Kg!',
                    'category' => 'weight',
                    'gain-loss' => 'loss',
                    'value' => '10',
                    'badge' => NULL,        // media Library ID
                    'custom-message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                    'max-awards' => 3,
                    'enabled' => 1,
                    'send-email' => 1,
                    'apply-to-update' => 1,
                    'apply-to-add' => 1
                ],
                [
                    'id' => 22,
                    'title' => 'Lost 20 Kg!',
                    'category' => 'weight',
                    'gain-loss' => 'loss',
                    'value' => '20',
                    'badge' => NULL,        // media Library ID
                    'custom-message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                    'max-awards' => 1,
                    'enabled' => 1,
                    'send-email' => 1,
                    'apply-to-update' => 1,
                    'apply-to-add' => 1
                ],
                [
                    'id' => 333,
                    'title' => 'Gained 20 Kg!',
                    'category' => 'weight',
                    'gain-loss' => 'gain',
                    'value' => '20',
                    'badge' => NULL,        // media Library ID
                    'custom-message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
                    'max-awards' => 1,
                    'enabled' => 1,
                    'send-email' => 1,
                    'apply-to-update' => 1,
                    'apply-to-add' => 1
                ]
            ]
        ];

        // Loop through each award in DB, count it's type and decide whether to consider issuing it.
        foreach ( $from_db as $category => $from_db_awards ) {

            foreach ( $from_db_awards as $award ) {

                if ( true === empty( $counts[ $category ] ) ) {
                    $counts[ $category ] = 0;
                }

                // Only consider giving enabled awards and ones that haven't been already issued to this user.
                if ( 1 === $award[ 'enabled' ] ) {

                    // If specified, strip out the gain or loss awards. For example, if the user has gained since start weight then we can assume they will not be winning
                    // any "loss" awards.
                    if ( true === $losing_weight_only && 'loss' !== $award['gain-loss'] ) {
                        continue;
                    }

                    if ( false === $losing_weight_only && 'gain' !== $award['gain-loss'] ) {
                       continue;
                    }

                    // Is this award available for the type of update i.e. update / add
                    if ( true === isset( $award['apply-to-' . $change_type ] ) && 0 === $award['apply-to-' . $change_type ] ) {
                        continue;
                    }

                    // Has this award already been awarded more that is allowed for this user?
                    $previous_no_awards = ws_ls_awards_user_times_awarded( $user_id, $award['id'] );

                    if ( $previous_no_awards >= $award['max-awards'] ) {
                        continue;
                    }

                    $counts[ $category ] ++;
                    $awards[ $category ][ $award['id'] ] = $award;
                }

            }

        }

        // TODO: Cache the above for the user... yep.

        return [
            'any' => ( count( $awards ) > 0 ) ? true : false,
            'counts' => $counts,
            'awards' => $awards
        ];

    }