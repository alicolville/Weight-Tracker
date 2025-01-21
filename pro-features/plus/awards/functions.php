<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     * Returns true if Awards enabled
     *
     * @return bool
     */
    function ws_ls_awards_is_enabled() {
        return WS_LS_IS_PREMIUM;
    }
    /*
    * Return base URL for meta fields
    * @return string
    */
    function ws_ls_awards_base_url() {
        return admin_url( 'admin.php?page=ws-ls-awards');
    }

/**
 * Return an array of Award Types
 *
 * @param bool $simple
 * @return array
 */
    function ws_ls_awards_categories( $simple = false) {

    	if ( true === $simple ) {

		    return [
			    'weight'            => esc_html__('Weight', WE_LS_SLUG ),
			    'weight-percentage' => esc_html__('Weight %', WE_LS_SLUG ),
			    'weight-target'     => esc_html__('Target met', WE_LS_SLUG ),
			    'bmi'               => esc_html__('BMI: Change', WE_LS_SLUG ),
			    'bmi-equals'        => esc_html__('BMI: Equals', WE_LS_SLUG ),
		    ];

	    }

        return [
	        'bmi'               => esc_html__('BMI: Change', WE_LS_SLUG ),
	        'bmi-equals'        => esc_html__('BMI: Equals', WE_LS_SLUG ),
	        'weight-target'     => esc_html__('Weight: Target met (based on user aim)', WE_LS_SLUG ),
            'weight'            => esc_html__('Weight: Change in units', WE_LS_SLUG ),
            'weight-percentage' => esc_html__('Weight: Change as a percentage', WE_LS_SLUG )

        ];

    }

	/**
	 * Format Gain / Loss string
	 *
	 * @param $gain_loss
	 *
	 * @return string
	 */
    function ws_ls_awards_gain_loss_display( $gain_loss ) {

    	switch ( $gain_loss ) {
    		case 'gain';
    		    return esc_html__('Gain', WE_LS_SLUG);
		    case 'loss':
			    return esc_html__('Loss', WE_LS_SLUG);
		    default:
		    	return '';
	    }
    }

/**
 * Return the text value of a custom field
 *
 * @param $slug
 * @return mixed|string
 */
    function ws_ls_custom_fields_get_string( $slug ) {

        $types = ws_ls_awards_categories();

        return ( false === empty( $types[ $slug ] ) ) ? $types[ $slug ] : '';
    }

    /**
     * Has the user previously been given this award?
     *
     * @param null $user_id
     * @param $award_id
     * @return int
     */
    function ws_ls_awards_user_times_awarded( $user_id, $award_id ) {

        $user_id = $user_id ?: get_current_user_id();

        $previous_awards = ws_ls_awards_previous_awards_get_ids( $user_id );

        $counts = array_count_values( $previous_awards );

        return false === empty( $counts[ $award_id ] ) ? (int) $counts[ $award_id ] : 0;
    }

	/**
	 * Return a count of awards for given user
	 * @param $user_id
	 *
	 * @return int
	 */
    function ws_ls_awards_count( $user_id ) {

	    $awards = ws_ls_awards_db_given_get( $user_id );

	    return count( $awards );
    }

	/**
	 * Return all enabled Awards
	 *
	 * @return array|bool
	 */
    function ws_ls_awards_get_enabled() {

    	$awards = ws_ls_awards();

    	if ( false === empty( $awards ) ) {

    		$enabled_awards = [];

    		foreach ( $awards as $award ) {
    			if ( 2 === (int) $award['enabled'] ) {

    				if ( false === isset( $enabled_awards[ $award['category'] ] ) ) {
					    $enabled_awards[ $award['category'] ] = [];
				    }

				    $enabled_awards[ $award['category'] ][] = $award;
			    }
		    }

		    return ( false === empty( $enabled_awards ) ) ? $enabled_awards : false;

	    }

	    return false;
    }

/**
 * Determine what awards can be given to this user and the given change type
 *
 * @param null $user_id
 * @param null $change_type
 * @param null $losing_weight_only
 * @return array
 */
    function ws_ls_awards_to_give( $user_id = NULL, $change_type = NULL, $losing_weight_only = NULL ) {

        $user_id = $user_id ?: get_current_user_id();

        $counts = [];
        $awards = [];

        // Fetch all enabled awards from db
        $from_db = ws_ls_awards_get_enabled();

        if ( false === empty( $from_db ) ) {

        	foreach ( $from_db as $category => $from_db_awards ) {

		        foreach ( $from_db_awards as $award ) {

			        if ( true === empty( $counts[ $category ] ) ) {
				        $counts[ $category ] = 0;
			        }

			        // Only consider giving enabled awards and ones that haven't been already issued to this user.
			        if ( 2 === (int) $award[ 'enabled' ] ) {

				        // Consider whether gaining or losing weight
				        if ( false === in_array( $category, [ 'bmi-equals', 'weight-target' ] ) ) {

					        // If specified, strip out the gain or loss awards. For example, if the user has gained since start weight then we can assume they will not be winning
					        // any "loss" awards.
					        if ( true === $losing_weight_only && 'loss' !== $award['gain_loss'] ) {
						        continue;
					        }

					        if ( false === $losing_weight_only && 'gain' !== $award['gain_loss'] ) {
						        continue;
					        }
				        }

				        // Is this award available for the type of update i.e. update / add
				        if ( true === isset( $award['apply_to_' . $change_type ] ) && 0 === (int) $award['apply_to_' . $change_type ] ) {
					        continue;
				        }

				        // Has this award already been awarded more that is allowed for this user?
				        $previous_no_awards = ws_ls_awards_user_times_awarded( $user_id, $award['id'] );

				        if ( $previous_no_awards >= $award['max_awards'] ) {
				        	continue;
				        }

				        $counts[ $category ] ++;
				        $awards[ $category ][ $award['id'] ] = $award;
			        }
		        }
	        }
        }

        // Loop through each award in DB, count it's type and decide whether to consider issuing it.

        return [
            'any' => count( $awards ) > 0,
            'counts' => $counts,
            'awards' => $awards
        ];

    }

/**
 * Fetch all the award IDs previous given to this user.
 * @param null $user_id
 *
 * @return array
 */
    function ws_ls_awards_previous_awards_get_ids( $user_id = NULL ) {

        $user_id = $user_id ?: get_current_user_id();

        $awards = ws_ls_awards_db_given_get( $user_id );

        if ( false === empty( $awards ) ) {
            $awards = array_column( $awards, 'award_id' );
        }

        return $awards;

    }

/**
 * Fetch all the awards given to this user (add relevant image data too)
 * @param null $user_id
 *
 * @param int $width
 * @param int $height
 * @param string $order_by
 * @return array|null
 */
	function ws_ls_awards_previous_awards( $user_id = NULL, $width = 200, $height = 200, $order_by = 'value' ) {

		$user_id = $user_id ?: get_current_user_id();

		$cache_key = 'awards-given-formatted-' . md5( $width . $height . $order_by );

		$cache = ws_ls_cache_user_get( $user_id, $cache_key );

		if ( true === is_array( $cache ) ) {
			return $cache;
		}

		$awards = ws_ls_awards_db_given_get( $user_id, $order_by );

		foreach ( $awards as &$award ) {

			$photo_src = ws_ls_photo_get( $award['badge'], $width, $height);

			$award['no-badge'] = false;

			if ( false === empty( $photo_src ) ) {
				$award = array_merge( $photo_src, $award);
			} else {

				// If no badge, use a dummy placeholder
				$placeholder = plugins_url( '../../assets/img/badge-placeholder-transparent.png', __DIR__ );
				$award['thumb'] = sprintf( '<img src="%s" width="%d" height="%d" />',
                    esc_url( $placeholder ),
                    (int) $width,
                    (int) $height
                );
				$award['full'] = $placeholder;
				$award['no-badge'] = true;
			}

			$award['display-text'] = $award['title'];

			// Do we have a URL?
			$award['thumb-with-url'] = ( false === empty( $award['url']  ) ) ? sprintf( '<a href="%s" target="_blank" rel="noopener" >%s</a>', esc_url(  $award['url'] ), $award['thumb'] ) : NULL;

		}

		ws_ls_cache_user_set( $user_id, $cache_key, $awards );

		return $awards;

	}

	/**
	 * Render badges for awards issued to the user
	 *
	 * @param $user_defined_arguments
	 *
	 * @return string
	 */
	function ws_ls_awards_render_badges( $user_defined_arguments ) {

		if( false === WS_LS_IS_PREMIUM ) {
			return '';
		}

		$html = '';

		$arguments = shortcode_atts([
			'error-message' => ( is_admin() ) ? esc_html__('The user has no awards', WE_LS_SLUG ) : esc_html__('You have no awards yet', WE_LS_SLUG ),
			'user-id' => get_current_user_id(),
		], $user_defined_arguments );

		$awards = ws_ls_awards_previous_awards( $arguments[ 'user-id' ], 100, 100 );

		if ( false === empty( $awards ) ) {

		    $html .= '<div class="ws-ls-badge-collection">';

			$placeholder = plugins_url( '../../assets/img/badge-placeholder-transparent.png', __DIR__ );

			foreach ( $awards as $award ) {

			    if ( false === empty( $award['thumb-with-url'] ) ) {
				    $thumbnail = $award['thumb-with-url'];
			    } else if ( false === empty( $award['thumb'] ) ) {
				    $thumbnail = $award['thumb'];
			    } else {
				    $thumbnail = '<img src="' . esc_url( $placeholder ) . '" width="100" height="100" />';
			    }

			    if ( false === empty( $award['url'] ) ) {
				    $award['title'] = sprintf( '<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url( $award['url'] ), esc_html( $award['title'] ) );
			    } else {
				    $award['title'] = esc_html( $award['title'] );
			    }

				$html .= sprintf('<div>
									<p>%s</p>
                                    %s
                                  </div>',
									$award['title'],
                                    $thumbnail

                );
			}

            $html .= '</div>';

		} else {
			$html = esc_html( $arguments[ 'error-message' ] . '.' );
		}

		return $html;

	}

    /**
	 * Are email notifications enabled for rewards?
	 *
	 * @return bool
	 */
	function ws_ls_awards_email_notifications_enabled() {
		return 'y' === get_option('ws-ls-awards-email-notifications', false );
	}
