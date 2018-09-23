<?php

    defined('ABSPATH') or die("Jog on!");

	/**
	 * Listen for weight entries / updates and determine whether it should be considered for an award
	 *
	 * @param $type
	 * @param $weight_object
	 */
	function ws_ls_awards_listen( $info, $weight_object ) {

	    if ( false === WS_LS_IS_PRO_PLUS ) {
	        return;
        }

		if ( false === empty( $info['type'] ) && $info['type'] === 'weight-measurements' ) {

            // Is user gaining or losing weight?
            $losing_weight = ( $weight_object['difference_from_start_kg'] < 0 );

			$awards = ws_ls_awards_to_give( NULL, $info['mode'], $losing_weight );    // Mode: update or add

			if ( false === empty( $awards ) ) {
               //print_r($awards);
				// Do we have any weight awards to consider?
				if ( false === empty( $awards['counts']['weight'] ) ) {

                    $weight_difference_from_start = absint( $weight_object['difference_from_start_kg'] );

                    foreach ( $awards['awards']['weight'] as $weight_award ) {

                        if ( (int) $weight_award['value'] > $weight_difference_from_start ) {
                            continue;
                        }

                        //var_dump(( true === $losing_weight && 'loss' === $weight_award['gain_loss'] ), ( false === $losing_weight && 'gain' === $weight_award['gain_loss'] ));
                        if ( ( true === $losing_weight && 'loss' === $weight_award['gain_loss'] ) || ( false === $losing_weight && 'gain' === $weight_award['gain_loss'] )  ) {

                            ws_ls_awards_db_given_add( $info['user-id'], $weight_award['id'] );

                            // Throw hook out so other's can process award e.g. send emails. Log etc.
                            do_action( 'wlt-award-given', $weight_object, $weight_award, $info );

                        }
				    }
				}
			}
		}

	}
	add_action( WE_LS_HOOK_DATA_ADDED_EDITED, 'ws_ls_awards_listen', 10, 2 );

    /**
     * Log award
     *
     * @param $weight_object
     * @param $weight_award
     * @param $info
     */
    function ws_ls_awards_log_award(  $weight_object, $weight_award, $info ) {

        if ( false === empty( $info['user-id'] ) && false === empty( $weight_award['title'] ) ) {
            ws_ls_log_add('awards-added', sprintf('User: %s / %s', $info['user-id'], $weight_award['title'] ) );
        }

    }
	add_action( 'wlt-award-given', 'ws_ls_awards_log_award', 10, 3 );



//	function test() {
//
//    $a = ws_ls_awards_to_give(1, 'add');
//        print_r($a);
//////		$t= ws_ls_awards_user_times_awarded(1,33);
//////		print_r($t);
//		die;
//
//
//        //ws_ls_awards_db_given_add(1,222);
//
//        $t = ws_ls_awards_previous_awards_get_ids();
//        print_r($t);
//
//        die;
//
//	}
//	add_action('init' , 'test');