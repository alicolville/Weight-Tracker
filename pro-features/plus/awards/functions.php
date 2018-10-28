<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     * Returns true if Awards enabled
     *
     * @return bool
     */
    function ws_ls_awards_is_enabled() {
        return WS_LS_IS_PRO_PLUS;
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
     * @return array
     */
    function ws_ls_awards_categories( $simple = false) {

    	if ( true === $simple ) {

		    return [
			    'weight' => __('Weight', WE_LS_SLUG),
			    'weight-percentage' => __('Weight %', WE_LS_SLUG),
			    'bmi' => __('BMI: Change', WE_LS_SLUG),
			    'bmi-equals' => __('BMI: Equals', WE_LS_SLUG),
		    ];

	    }

        $fields = [
	        'bmi' => __('BMI: Change', WE_LS_SLUG),
	        'bmi-equals' => __('BMI: Equals', WE_LS_SLUG),
            'weight' => __('Weight change in units', WE_LS_SLUG),
            'weight-percentage' => __('Weight change as a percentage', WE_LS_SLUG)
        ];

        return $fields;
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
    		    return __('Gain', WE_LS_SLUG);
    		    break;
		    case 'loss':
			    return __('Loss', WE_LS_SLUG);
				break;
		    default:
		    	return '';
	    }
    }

    /**
     * Return the text value of a custom field
     *
     * @param $id
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
    function ws_ls_awards_user_times_awarded( $user_id = NULL, $award_id ) {

        $user_id = $user_id ?: get_current_user_id();

        $previous_awards = ws_ls_awards_previous_awards_get_ids( $user_id );

        $counts = array_count_values( $previous_awards );

        return false === empty( $counts[ $award_id ] ) ? (int) $counts[ $award_id ] : 0;
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
     * @return array
     */
    function ws_ls_awards_to_give( $user_id = NULL, $change_type = NULL, $losing_weight_only = NULL ) {

        $user_id = $user_id ?: get_current_user_id();

        $counts = [];
        $awards = [];

        // Fetch all enabled awards from db
        $from_db = ws_ls_awards_get_enabled();

        // Loop through each award in DB, count it's type and decide whether to consider issuing it.
        foreach ( $from_db as $category => $from_db_awards ) {

            foreach ( $from_db_awards as $award ) {

                if ( true === empty( $counts[ $category ] ) ) {
                    $counts[ $category ] = 0;
                }

                // Only consider giving enabled awards and ones that haven't been already issued to this user.
                if ( 2 === (int) $award[ 'enabled' ] ) {

                	// Consider whether gaining or losing weight
                	if ( false === in_array( $category, [ 'bmi-equals' ] ) ) {

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

        return [
            'any' => ( count( $awards ) > 0 ) ? true : false,
            'counts' => $counts,
            'awards' => $awards
        ];

    }

    /**
     * Fetch all the award IDs previous given to this user.
     * @param null $user_id
     *
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
	 */
	function ws_ls_awards_previous_awards( $user_id = NULL ) {

		$user_id = $user_id ?: get_current_user_id();

		$cache = ws_ls_cache_user_get( $user_id, 'awards-given-formatted' );

		if ( true === is_array( $cache ) ) {
			return $cache;
		}

		$awards = ws_ls_awards_db_given_get( $user_id );

		ws_ls_cache_user_set( $user_id, 'awards-given-formatted', $awards );

		return $awards;

	}

	function ws_ls_awards_render_badges( $user_defined_arguments ) {

		if( false === WS_LS_IS_PRO_PLUS ) {
			return '';
		}

		$html = '';

		$arguments = shortcode_atts([
			'error-message' => ( is_admin() ) ? __('The user has no awards', WE_LS_SLUG ) : __('You have no awards yet', WE_LS_SLUG ),
			'user-id' => get_current_user_id(),
		], $user_defined_arguments );

		$awards = ws_ls_awards_previous_awards( $arguments[ 'user-id' ] );

		if ( false === empty( $awards ) ) {

		    $html .= '<div class="ws-ls-badge-collection">';

	//echo print_r($awards, true);
			foreach ( $awards as $award ) {

			    $image = ws_ls_photo_get( $award['badge'], 100 );

				$html .= sprintf('<div>
                                    %s
                                    <span>%s</span>
                                  </div>',
                                    ( false === empty( $image['thumb'] ) ) ? $image['thumb'] : '',
                                    $award['title']
                );
			}

            $html .= '</div>';

		} else {
			$html = esc_html( $arguments[ 'error-message' ] . '.' );
		}

		return $html;

	}


//
//
//    function t() {
//
//    	if ( is_admin() ) {
//    	//	return;
//	    }
//
//print_r( ws_ls_awards_to_give( 1, 'add') );
//
//        die;
//    }
//    add_action('init' , 't');