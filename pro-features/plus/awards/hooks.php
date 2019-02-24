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

        // Ensure the user has more than one weight entry! No point doing any comparisons!
        $user_stats = ws_ls_get_entry_counts(  $info['user-id'] );

	    if ( (int) $user_stats[ 'number-of-entries' ] <= 1 ) {
	    	return;
	    }

		if ( false === empty( $info['type'] ) && $info['type'] === 'weight-measurements' ) {

            // Is user gaining or losing weight?
            $losing_weight = ( $weight_object['difference_from_start_kg'] < 0 );

			$awards = ws_ls_awards_to_give( $info['user-id'], $info['mode'], $losing_weight );    // Mode: update or add

			if ( false === empty( $awards ) ) {

			    $start_weight = ws_ls_get_weight_extreme( $info['user-id'] );

			    // ---------------------------------------------------------------
			    // Weight Awards
                // ---------------------------------------------------------------

                if ( false === empty( $awards['counts']['weight'] ) ) {

                    $weight_difference_from_start = absint( $weight_object['difference_from_start_kg'] );

	                foreach ( $awards['awards']['weight'] as $weight_award ) {

						if ( (float) $weight_award['value'] > $weight_difference_from_start ) {
	                        continue;
                        }

                        if ( ( true === $losing_weight && 'loss' === $weight_award['gain_loss'] ) || ( false === $losing_weight && 'gain' === $weight_award['gain_loss'] )  ) {

                            ws_ls_awards_db_given_add( $info['user-id'], $weight_award['id'] );

                            // Throw hook out so other's can process award e.g. send emails. Log etc.
                            do_action( 'wlt-award-given', $weight_object, $weight_award, $info );

                        }
				    }
				}

				// ---------------------------------------------------------------
				// BMI Equals
				// ---------------------------------------------------------------

				if ( false === empty( $awards['counts']['bmi-equals'] ) && false === empty( $start_weight ) ) {

					$user_height = ws_ls_get_user_height( $info['user-id'] );

					if ( false === empty( $user_height ) ) {

						$starting_bmi =  ws_ls_calculate_bmi( $user_height, $start_weight );
						$current_bmi = ws_ls_calculate_bmi( $user_height, $weight_object['kg'] );

						// We're only interested in changes of BMI
						if ( $starting_bmi !== $current_bmi ) {

							$starting_label = ws_ls_calculate_bmi_label( $starting_bmi );
							$current_label = ws_ls_calculate_bmi_label( $current_bmi );

							// Do we actually have a change in BMI label? If not, no need to process any awards
							if ( $starting_label !== $current_label ) {

								foreach ( $awards['awards']['bmi-equals'] as $bmi_award ) {

									$bmi_labels = ws_ls_bmi_all_labels();

									$award_label = $bmi_labels[ (int) $bmi_award['bmi_equals'] ];

									if ( $current_label === $award_label ) {

										ws_ls_awards_db_given_add( $info['user-id'], $bmi_award['id'] );

										// Throw hook out so other's can process award e.g. send emails. Log etc.
										do_action( 'wlt-award-given', $weight_object, $bmi_award, $info );
									}
								}
							}
						}
					}
				}

                // ---------------------------------------------------------------
                // BMI Change
                // ---------------------------------------------------------------

                if ( false === empty( $awards['counts']['bmi'] ) && false === empty( $start_weight ) ) {

                    $user_height = ws_ls_get_user_height( $info['user-id'] );

                    if ( false === empty( $user_height ) ) {

                        $starting_bmi =  ws_ls_calculate_bmi( $user_height, $start_weight );
                        $current_bmi = ws_ls_calculate_bmi( $user_height, $weight_object['kg'] );

                        // We're only interested in changes of BMI
                        if ( $starting_bmi !== $current_bmi ) {

                            $starting_label = ws_ls_calculate_bmi_label( $starting_bmi );
                            $current_label = ws_ls_calculate_bmi_label( $current_bmi );

                            // Do we actually have a change in BMI label? If not, no need to process any awards
                            if ( $starting_label !== $current_label ) {

                                $increase_in_bmi = ( $current_bmi > $starting_bmi );

                                foreach ( $awards['awards']['bmi'] as $bmi_award ) {

                                    if ( ( false === $increase_in_bmi && 'loss' === $bmi_award['gain_loss'] ) || ( true === $increase_in_bmi && 'gain' === $bmi_award['gain_loss'] ) ) {

                                        ws_ls_awards_db_given_add( $info['user-id'], $bmi_award['id'] );

                                        // Throw hook out so other's can process award e.g. send emails. Log etc.
                                        do_action( 'wlt-award-given', $weight_object, $bmi_award, $info );

                                    }
                                }
                            }
                        }
                    }
                }

                // ---------------------------------------------------------------
                // Percentage of body weight lost
                // ---------------------------------------------------------------

                if ( false === empty( $awards['counts']['weight-percentage'] ) && false === empty( $weight_object['difference_from_start_kg'] ) ) {

                    $percentage_difference = ws_ls_calculate_percentage_difference( $start_weight, $weight_object['kg'] );

                    if ( false === is_null( $percentage_difference ) ) {

                        $percentage_difference['percentage'] = round( $percentage_difference['percentage'], 2 );

                        foreach ( $awards['awards']['weight-percentage'] as $percentage_award ) {

                            if ( 'gain' === $percentage_award['gain_loss'] && true === $percentage_difference['increase'] &&
                                    (int) $percentage_award['value'] > $percentage_difference['percentage'] ) {
                               continue;
                            }

                            if ( 'loss' === $percentage_award['gain_loss'] && false === $percentage_difference['increase'] &&
                                (int) $percentage_award['value'] > $percentage_difference['percentage'] ) {
                                continue;
                            }

                            ws_ls_awards_db_given_add( $info['user-id'], $percentage_award['id'] );

                            do_action( 'wlt-award-given', $weight_object, $percentage_award, $info );

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
    function ws_ls_awards_log_award( $weight_object, $weight_award, $info ) {

	    if ( false === WS_LS_IS_PRO_PLUS ) {
		    return;
	    }

        if ( false === empty( $info['user-id'] ) && false === empty( $weight_award['title'] ) ) {

	        $user_info = get_userdata(  $info['user-id'] );

	        $details = sprintf('%s (%s)', $user_info->user_nicename, $user_info->user_email );

            ws_ls_log_add('awards-added', sprintf('User: %s / %s', $details, $weight_award['title'] ) );
        }

    }
	add_action( 'wlt-award-given', 'ws_ls_awards_log_award', 10, 3 );

	/**
	 * If applicable, send an email for the award!
	 *
	 * @param $weight_object
	 * @param $award
	 * @param $info
	 */
    function ws_ls_awards_send_email( $weight_object, $award, $info ) {

	    if ( false === WS_LS_IS_PRO_PLUS ) {
		    return;
	    }

    	// Email notifications enabled for awards?
	    if ( false === ws_ls_awards_email_notifications_enabled() ) {
	    	return;
	    }

        // Email not to be sent!
        if ( 2 !== (int) $award['send_email'] ) {
            return;
        }

   	    $email_template = ws_ls_emailer_get( 'award ');

        if ( false === empty( $email_template ) ) {

	        $badge = ( false === empty( $award['badge'] ) ) ? ws_ls_photo_get( $award['badge'], 300 ) : NULL;

	        if ( false === empty( $badge['thumb'] ) ) {

		        $award['badge'] = '<table border="0" cellpadding="0" cellspacing="0" width="100%">
						                  <tbody>
						                    <tr>
						                      <td align="left">
						                        <table border="0" cellpadding="0" cellspacing="0">
						                          <tbody>
						                            <tr>
						                              <td align="center">' . $badge['thumb'] . '</td>
						                            </tr>
						                          </tbody>
						                        </table>
						                      </td>
						                    </tr>
						                  </tbody>
						                </table>';
	        } else {
		        $award['badge'] = '';
	        }

	        $current_user = get_userdata( $info['user-id'] );

	        if ( false === empty( $current_user->user_email ) ) {
		        ws_ls_emailer_send( $current_user->user_email,  $email_template['subject'],  $email_template['email'], $award );
	        }
        }
    }
    add_action( 'wlt-award-given', 'ws_ls_awards_send_email', 10, 3 );

	/**
		AJAX: Fetch all awards for main list
	 **/
	function ws_ls_awards_ajax_list() {


		check_ajax_referer( 'ws-ls-user-tables', 'security' );

		$columns = [
			[ 'name' => 'id', 'title' => 'ID', 'visible'=> false, 'type' => 'number' ],
			[ 'name' => 'title', 'title' => __('Title', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			[ 'name' => 'category', 'title' => __('Category', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			[ 'name' => 'gain_loss', 'title' => __('Gain / Loss', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			[ 'name' => 'value', 'title' => __('Value', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			//[ 'name' => 'max_awards', 'title' => __('Max. Awards', WE_LS_SLUG), 'visible'=> true, 'type' => 'number' ],
			[ 'name' => 'apply_to_add', 'title' => __('Add', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			[ 'name' => 'apply_to_update', 'title' => __('Update', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			[ 'name' => 'send_email', 'title' => __('Email', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
			[ 'name' => 'enabled', 'title' => __('Enabled', WE_LS_SLUG), 'visible'=> true, 'type' => 'text' ],
		];

		$awards = ws_ls_awards();

		if ( false === empty( $awards ) ) {

			$categories = ws_ls_awards_categories( true );

			// Format Row data
			for ( $i = 0 ; $i < count( $awards ) ; $i++ ) {

				if ( 'weight' === $awards[ $i ][ 'category' ] ) {
					$awards[ $i ][ 'value' ] = ws_ls_convert_kg_into_relevant_weight_String( $awards[ $i ][ 'value' ] );
				} else if ( 'weight-percentage' === $awards[ $i ][ 'category' ]  ) {
					$awards[ $i ][ 'value' ] = $awards[ $i ][ 'value' ] . '%';
				} else if ( 'bmi-equals' === $awards[ $i ][ 'category' ]  ) {

					$labels = ws_ls_bmi_all_labels();

					$awards[ $i ][ 'value' ] = $labels [ (int) $awards[ $i ][ 'bmi_equals' ] ];
				}

				$awards[ $i ][ 'category' ] = ( false === empty( $categories[ $awards[ $i ][ 'category' ] ] ) ) ? $categories[ $awards[ $i ][ 'category' ] ] : '';
				$awards[ $i ][ 'gain_loss' ] = ws_ls_awards_gain_loss_display( $awards[ $i ][ 'gain_loss' ] );
				$awards[ $i ][ 'apply_to_add' ] = ws_ls_boolean_as_yes_no_string( $awards[ $i ][ 'apply_to_add' ], 1 );
				$awards[ $i ][ 'apply_to_update' ] = ws_ls_boolean_as_yes_no_string( $awards[ $i ][ 'apply_to_update' ], 1 );
				$awards[ $i ][ 'send_email' ] = ws_ls_boolean_as_yes_no_string( $awards[ $i ][ 'send_email' ] );
				$awards[ $i ][ 'enabled' ] = ws_ls_boolean_as_yes_no_string( $awards[ $i ][ 'enabled' ] );

			}

		}

		$data = [
			'columns' => $columns,
			'rows' => $awards
		];

		wp_send_json($data);

	}
	add_action( 'wp_ajax_awards_full_list', 'ws_ls_awards_ajax_list' );

	/**
	 * AJAX: Delete given award ID
	 */
	function ws_ls_award_ajax_delete() {

		if ( false === ws_ls_awards_is_enabled() ) {
			return;
		}

		check_ajax_referer( 'ws-ls-user-tables', 'security' );

		$id = ws_ls_get_numeric_post_value('id');

		if ( false === empty( $id ) ) {

			$result = ws_ls_awards_delete( $id );

			if ( true === $result ) {
				wp_send_json( 1 );
			}
		}

		wp_send_json( 0 );

	}
	add_action( 'wp_ajax_awards_delete', 'ws_ls_award_ajax_delete' );

	/**
	 * Render a photo gallery of all awards
	 *
	 * @param $user_defined_arguments
	 *
	 * @return string
	 */
	function ws_ls_awards_shortcode_gallery( $user_defined_arguments ) {

		if( false === WS_LS_IS_PRO_PLUS ) {
			return '';
		}

		if ( false === is_array( $user_defined_arguments ) ) {
			$user_defined_arguments = [];
		}

		$user_defined_arguments[ 'source' ] = 'awards';
		$user_defined_arguments[ 'mode' ] = 'tilesgrid';

		return ws_ls_photos_shortcode_gallery( $user_defined_arguments );
	}
	add_shortcode('wlt-awards', 'ws_ls_awards_shortcode_gallery');

	/**
	 * Render a grid of all awards
	 *
	 * @param $user_defined_arguments
	 *
	 * @return string
	 */
	function ws_ls_awards_shortcode_grid( $user_defined_arguments ) {

		if( false === WS_LS_IS_PRO_PLUS ) {
			return '';
		}

		$arguments = shortcode_atts([
			'message' => __('No awards', WE_LS_SLUG),
			'user-id' => get_current_user_id(),
			'thumb-width' => 150,
			'thumb-height' => 150
		], $user_defined_arguments );

		$arguments['thumb-width'] = ws_ls_force_dimension_argument( $arguments['thumb-width'] , 150 );
        $arguments['thumb-height'] = ws_ls_force_dimension_argument( $arguments['thumb-width'] , 150 );
		$arguments['user-id'] = ws_ls_force_numeric_argument( $arguments['user-id'], get_current_user_id() );

		$awards = ws_ls_awards_previous_awards( $arguments['user-id'], $arguments['thumb-width'], $arguments['thumb-height'] );

		if  ( false === empty( $awards ) ) {

			$html = '<div class="ws-ls-grid">';

			foreach ( $awards as $award ) {
				if ( false === empty( $award['badge'] ) ) {
				    $html .= sprintf( '<div class="ws-ls-module">%1$s</div>', $award['thumb'] );
                }
			}

			$html .= '</div>';

			return $html;
		}

		return esc_html( $arguments['message'] );
	}
	add_shortcode('wlt-awards-grid', 'ws_ls_awards_shortcode_grid');

	/**
	 * Display latest award image
	 *
	 * @param $user_defined_arguments
	 *
	 * @return string
	 */
	function ws_ls_awards_shortcode_recent( $user_defined_arguments ) {

		if( false === WS_LS_IS_PRO_PLUS ) {
			return '';
		}

		$arguments = shortcode_atts([
			'message' => '',
			'user-id' => get_current_user_id(),
			'height' => 200,
			'width' => 200,
		], $user_defined_arguments );

		$awards = ws_ls_awards_previous_awards( $arguments['user-id'], $arguments['width'], $arguments['height'], 'timestamp' );

		if ( false === empty( $awards[0]['thumb'] ) ) {
			return sprintf('<div class="ws-ls-award-latest-img">%s</div>', $awards[0]['thumb'] ) ;
		} elseif ( false === empty( $awards[0]['title'] ) ) {
			return sprintf('<div class="ws-ls-award-latest-text">%s</div>', esc_html( $awards[0]['title'] ) ) ;
		}

		return ( false === empty( $arguments['message'] ) ) ? esc_html( $arguments['message'] ) : '';

	}
	add_shortcode('wlt-awards-recent', 'ws_ls_awards_shortcode_recent');