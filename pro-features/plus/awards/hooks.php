<?php

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

			$awards = ws_ls_awards_to_give( $info['mode'] );    // Mode: update or add

			if ( false === empty( $awards ) ) {

				// Do we have any weight awards to consider?
				if ( false === empty( $awards['counts']['weight'] ) ) {

					// Get weight difference
					var_dump($weight_object['difference_from_start_kg']);
					var_dump('consider weight');
				}


				// Check this award hasn't already been issue to the user

			}

			var_dump( $awards );
			die;

		}

	}
	add_action( WE_LS_HOOK_DATA_ADDED_EDITED, 'ws_ls_awards_listen', 10, 2 );


	function ws_ls_awards_to_give( $change_type = NULL ) {

		//todo: move to own file
		//TODO: from DB and cache
		//todo: cache (although only cache the processed enabled stuff from DB) - do not return here as we further filter array below

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
												'max-awards' => 1,
												'enabled' => 1,
												'send-email' => 1,
												'apply-to-update' => 1,
												'apply-to-new' => 1
											],
											[
												'id' => 2,
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
												'apply-to-new' => 1
											]
										]
				];

		// Loop through each award in DB, count it's type and decide whether to consider issuing it.
		foreach ( $from_db as $category => $from_db_awards ) {

			foreach ( $from_db_awards as $award ) {

				if ( true === empty( $counts[ $category ] ) ) {
					$counts[ $category ] = 0;
				}

				// Only consider giving enabled awards
				if ( 1 === $award[ 'enabled' ] ) {
					$counts[ $category ] ++;
					$awards[] = $award;
				}

			}

		}

		// TODO: Cache the above.

		//todo: Add logic here to filter awards aray depending on update type $change_type

		return [
			'any' => ( count( $awards ) > 0 ) ? true : false,
			'counts' => $counts,
			'awards' => $awards
		];

	}


//	function test() {
//
//		$t= ws_ls_awards_to_give();
//		print_r($t);
//		die;
//
//	}
//	add_action('init' , 'test');