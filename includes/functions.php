<?php
defined('ABSPATH') or die("Jog on!");

/**
 * Delete all targets and weight entries
 */
function ws_ls_delete_existing_data() {
    if( true === is_admin() )  {
	    ws_ls_db_entry_delete_all();

	    ws_ls_cache_delete_all();
    }
}

/**
 * Delete all data for given user
 * @param null $user_id
 */
function ws_ls_delete_data_for_user( $user_id = NULL ) {

    if( true === ws_ls_user_preferences_is_enabled() || is_admin())  {

	    $user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

        ws_ls_db_target_delete( $user_id );

	    ws_ls_db_entry_delete_all_for_user( $user_id );

		// Update User stats table
		ws_ls_stats_update_for_user( $user_id );

		// Let others know we cleared all user data
		do_action( 'wlt-hook-data-user-deleted', $user_id );
    }
}

/* Admin tool to check the relevant tables exist for this plugin */
function ws_ls_admin_check_mysql_tables_exist() {

    $error_text = '';
    global $wpdb;

    $tables_to_check = [    $wpdb->prefix . WE_LS_TARGETS_TABLENAME,
							$wpdb->prefix . WE_LS_TABLENAME,
							$wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME,
							$wpdb->prefix . WE_LS_MYSQL_META_FIELDS,
							$wpdb->prefix . WE_LS_MYSQL_META_ENTRY,
							$wpdb->prefix . WE_LS_MYSQL_AWARDS,
							$wpdb->prefix . WE_LS_MYSQL_AWARDS_GIVEN,
							$wpdb->prefix . WE_LS_MYSQL_GROUPS,
							$wpdb->prefix . WE_LS_MYSQL_GROUPS_USER,
							$wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT,
							$wpdb->prefix . WE_LS_MYSQL_EXPORT
                       ];

    // Check each table exists!
    foreach( $tables_to_check as $table_name ) {

        $rows = $wpdb->get_row('Show columns in ' . $table_name);

        if ( true === empty( $rows ) ) {
            $error_text .= sprintf( '<li>%s</li>', $table_name );
        }
    }

    // Return error message if tables missing
    if (!empty($error_text))  {
        return  __('The following MySQL tables are missing for this plugin', WE_LS_SLUG) . ':<ul>' . $error_text . '</ul>';
    }
    return false;
}


/**
 * Used to display a jQuery dialog box in the admin panel
 * @param $title
 * @param $message
 * @param $class_used_to_prompt_confirmation
 * @param bool $js_call
 */
function ws_ls_create_dialog_jquery_code( $title, $message, $class_used_to_prompt_confirmation, $js_call = false ) {

    global $wp_scripts;

    $queryui = $wp_scripts->query('jquery-ui-core');

    $url = sprintf( '//ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css', $queryui->ver );

    wp_enqueue_script( 'jquery-ui-dialog' );
    wp_enqueue_style('jquery-ui-smoothness', $url, false, null);

    $id_hash = md5($title . $message . $class_used_to_prompt_confirmation );

    printf('<div id="%1$s" title="%2$s">
                        <p>%3$s</p>
                    </div>
                    <script>
                        jQuery( function( $ ) {
                            let $info = $( "#%1$s" );
                            $info.dialog({
                                "dialogClass"   : "wp-dialog",
                                "modal"         : true,
                                "autoOpen"      : false
                            });

                            $( ".%4$s" ).click( function( event ) {
                                event.preventDefault();
                                target_url = $( this ).attr( "href" );
                                let  $info = $( "#%1$s" );
                                $info.dialog({
                                    "dialogClass"   : "wp-dialog",
                                    "modal"         : true,
                                    "autoOpen"      : false,
                                    "closeOnEscape" : true,
                                    "buttons"       : {
                                        "Yes": function() {
                                            %5$s
                                        },
                                        "No": function() {
                                            $(this).dialog( "close" );
                                        }
                                    }
                                });
                                $info.dialog("open");
                            });

                        });
                    </script>',
        $id_hash,
        esc_attr( $title ),
        wp_kses_post( $message ),
        esc_attr( $class_used_to_prompt_confirmation ),
        ( true === $js_call ) ? $js_call : 'window.location.href = target_url;'
    );

}

/**
 * Get today's in the correct format
 * @param null $user_id
 *
 * @return false|string
 */
function ws_ls_date_todays_date( $user_id = NULL ) {

	$user_id 	= ( NULL === $user_id ) ? get_current_user_id() : $user_id;
	$format		= ws_ls_get_date_format( $user_id );

	return date( $format );
}

/**
 * Return date format based on settings
 * @param null $user_id
 *
 * @return string
 */
function ws_ls_get_date_format( $user_id = NULL ) {

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	return ( true === ws_ls_setting('use-us-dates', $user_id ) ) ? 'm/d/Y': 'd/m/Y';
}

/**
 *
 * REFACTOR!!
 * CACHE!
 *
 * @param null $user_id
 *
 * @return array|bool
 * @throws Exception
 */
function ws_ls_week_ranges_get( $user_id = NULL ) {

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $user_id, 'week-ranges' ) ) {
		return $cache;
	}

	$entered_date_ranges = ws_ls_db_dates_min_max_get( $user_id );

	if ( true === empty( $entered_date_ranges ) ) {
		return false;
	}

	// Get min and max dates for weight entries
	$start_date     = new DateTime( $entered_date_ranges[ 'min' ] );
	$end_date       = new DateTime( $entered_date_ranges[ 'max' ] );

	// Grab all the weekly intervals between those dates
	$interval       = new DateInterval( 'P1W' );
	$daterange      = new DatePeriod( $start_date, $interval ,$end_date );
	$date_ranges    = [ 0 => [ 'display' => __( 'View all weeks', WE_LS_SLUG ), 'start' => $start_date->format( 'Y-m-d' ), 'end' => $end_date->format( 'Y-m-d' ) ] ];
	$date_format    = ws_ls_get_date_format( $user_id );

	$i = 1;

	// Build an easy to use array
	foreach( $daterange as $date ){

		$end_of_week    = clone $date;
		$end_of_week    = date_modify( $end_of_week, '+1 week' );
		$display        = sprintf( '%s %d - %s %s %s', __( 'View Week', WE_LS_SLUG ), $i, $date->format( $date_format ), __('to', WE_LS_SLUG), $end_of_week->format( $date_format ) );

		$date_ranges[ $i ] =  [     'start'     => $date->format( 'Y-m-d' ),
		                            'end'       => $end_of_week->format( 'Y-m-d' ),
		                            'display'   => $display ];

		$i++;
	}

	ws_ls_cache_user_set( $user_id, 'week-ranges', $date_ranges );

	return $date_ranges;
}

/**
 * Get form / dropdown for weekly ranges
 * @param $week_ranges
 * @param $selected_week_number
 * @param null $user_id
 *
 * @return string|null
 * @throws Exception
 */
function ws_ls_week_ranges_display( $week_ranges, $selected_week_number ) {

	if ( true === empty( $week_ranges ) ) {
		return '';
	}

	$week_ranges    = wp_list_pluck( $week_ranges, 'display' );
    $output         = sprintf('<form action="%1$s#wlt-weight-history" method="post">', esc_url( get_permalink() ) );
	$output         .= ws_ls_form_field_select( [ 'key' => 'week-number', 'show-label' => false, 'values' => $week_ranges, 'selected' => $selected_week_number, 'css-class' => 'ws-ls-select', 'js-on-change' => 'this.form.submit()' ] );
	$output         .= '</form>';

	return $output;
}

/**
 * Fetch the user's target
 *
 * @param null $user_id
 * @param null $field
 *
 * @return void|null
 */
function ws_ls_target_get( $user_id = NULL, $field = NULL ) {

	$user_id 	= ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	$weight 	= NULL;
	$kg 		= ws_ls_db_target_get( $user_id );

	if ( false === empty( $kg ) ) {
		$weight = ws_ls_weight_display( $kg );
	}

	return ( false === empty( $weight[ $field ] ) ) ? $weight[ $field ] : $weight;
}

/**
 * Fetch an Entry from the database
 *
 * @param array $arguments
 *
 * @return string|null
 */
function ws_ls_entry_get( $arguments = [] ) {

	$arguments  = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'id' => NULL ] );
	$cache_key  = sprintf( 'entry-full-%d', $arguments[ 'id' ] );
	$entry      = NULL;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], $cache_key ) ) {

		$entry = $cache;

	} else {

		$entry = ws_ls_db_entry_get( $arguments );

		if ( true === empty( $entry ) ) {
			return null;
		}

		$entry['first_weight'] = ws_ls_db_weight_start_get( $arguments['user-id'] );

		$entry['difference_from_start_kg'] = ( false === empty( $entry['first_weight'] ) && $entry['first_weight'] <> $entry['kg'] ) ?
			$entry['kg'] - $entry['first_weight'] :
			0;

		if ( true === WS_LS_IS_PRO &&
		     true === ws_ls_meta_fields_is_enabled() ) {

			$entry['meta'] = ws_ls_meta( $arguments['id'] );

			// Pluck to meta_id => value
			if ( false === empty( $entry['meta'] ) ) {
				$entry['meta'] = wp_list_pluck( $entry['meta'], 'value', 'meta_field_id' );
			}
		}

		ws_ls_cache_user_set( $arguments['user-id'], $cache_key, $entry );

	}

	$entry = ws_ls_weight_prep( $entry );

	return $entry;
}

/**
 * Fetch Entries
 * @param $arguments
 *
 * @return array|object|null
 * @throws Exception
 */
function ws_ls_entries_get( $arguments ) {

	$arguments = wp_parse_args( $arguments, [   'custom-field-value-exists'     => '',
												'custom-field-restrict-rows'    => '',
												'user-id'   					=> get_current_user_id(),
	                                            'limit'     					=> ws_ls_option( 'ws-ls-max-points', '25', true ),
	                                            'week'      					=> NULL,
	                                            'sort'      					=> 'asc',
	                                            'prep'      					=> false,
												'reverse'  						=> false    // Handy when charting
	] );

	$entries = ws_ls_db_entries_get( $arguments );

	if ( true === $arguments[ 'prep' ] &&
	        false === empty( $entries ) ) {
		$entries = array_map( 'ws_ls_weight_prep', $entries );
	}

	if ( true === $arguments[ 'reverse' ] ) {
		$entries = array_reverse( $entries );
	}

	return $entries;
}

/**
 * Prep a weight result if further detail needed
 * @param $weight
 *
 * @return array
 */
function ws_ls_weight_prep( $weight ) {

	if ( false === empty( $weight[ 'weight_date' ] ) ) {

		// Add dates to weight entry
		$dates  = ws_ls_convert_ISO_date_into_locale( $weight['weight_date'] );
		$weight = array_merge( $weight, $dates );
	}

	if ( false === empty( $weight[ 'kg' ] ) ) {

		// Add Weight display values
		$display_values = ws_ls_weight_display( $weight[ 'kg' ] );
		$weight = array_merge( $weight, $display_values );

	}

	return $weight;
}

/**
 * Fetch the oldest entry
 * @param array $arguments
 *
 * @return string|null
 */
function ws_ls_entry_get_oldest( $arguments = [] ) {

	$arguments              = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'meta' => true, 'kg-only' => false ] );
	$arguments[ 'which']    = 'oldest';
	$arguments[ 'id' ]      = ws_ls_db_entry_latest_or_oldest( $arguments );

	if ( true === empty( $arguments[ 'id' ] ) ) {
		return NULL;
	}

	$oldest_entry = ws_ls_entry_get( $arguments );

	return ( true === $arguments[ 'kg-only'] &&
			false === empty( $oldest_entry[ 'kg' ] ) ) ?
				$oldest_entry[ 'kg' ] :
					$oldest_entry;

}

/**
 * Return Kg for oldest weight
 * @param $user_id
 *
 * @return string|null
 */
function ws_ls_entry_get_oldest_kg( $user_id ) {

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	return ws_ls_entry_get_oldest( [ 'user-id' => $user_id, 'meta' => false, 'kg-only' => true ] );
}

/**
 * Fetch the start date for the given user
 *
 * @param $user_id
 *
 * @return array|mixed|string
 */
function ws_ls_entry_get_start_date( $user_id, $format = false ) {

	$user_id 		= ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $user_id, 'start-date' ) ) {
		return ( true === $format ) ?
				ws_ls_convert_ISO_date_into_locale( $cache ) :
					$cache;
	}

	$oldest_entry 	= ws_ls_entry_get_oldest( [ 'user-id' => $user_id, 'meta' => false, 'kg-only' => false ] );

	ws_ls_cache_user_set( $user_id, 'start-date', $oldest_entry[ 'raw' ] );

	return ( true === $format ) ?
				ws_ls_convert_ISO_date_into_locale( $oldest_entry[ 'raw' ] ) :
					$oldest_entry[ 'raw' ];
}

/**
 * Fetch the latest entry
 * @param array $arguments
 *
 * @return string|null
 */
function ws_ls_entry_get_latest( $arguments = [] ) {

	$arguments              = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'meta' => true, 'kg-only' => false ] );
	$arguments[ 'which']    = 'latest';
	$arguments[ 'id' ]      = ws_ls_db_entry_latest_or_oldest( $arguments );

	if ( true === empty( $arguments[ 'id' ] ) ) {
		return NULL;
	}

	$latest_entry = ws_ls_entry_get( $arguments );

	return ( true === $arguments[ 'kg-only'] &&
	         false === empty( $latest_entry[ 'kg' ] ) ) ?
				$latest_entry[ 'kg' ] :
					$latest_entry;
}

/**
 * Return Kg for latest weight
 * @param $user_id
 *
 * @return string|null
 */
function ws_ls_entry_get_latest_kg( $user_id = NULL ) {

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	return ws_ls_entry_get_latest( [ 'user-id' => $user_id, 'meta' => false, 'kg-only' => true ] );
}

/**
 * Get previous entry
 * @param array $arguments
 *
 * @return string|null
 */
function ws_ls_entry_get_previous( $arguments = [] ) {

	$arguments              = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'meta' => true ] );
	$arguments[ 'id' ]      = ws_ls_db_entry_previous( $arguments );

	if ( true === empty( $arguments[ 'id' ] ) ) {
		return NULL;
	}

	return ws_ls_entry_get( $arguments );
}

/**
 *  DEPRECATED: replace with ws_ls_to_bool()
 *
 * Force a string to boolean
 * @param $value
 * @return bool
 */
function ws_ls_force_bool_argument( $value ) {

    return ( 'true' === strtolower( $value ) ||
            ( true === is_bool( $value ) && true === $value ) );
}

/**
 * Convert string to bool
 * @param $string
 * @return mixed
 */
function ws_ls_to_bool( $string ) {
	return filter_var( $string, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Fetch the given key from options
 * @param $key
 * @param $default
 * @param bool $has_to_be_pro
 * @return bool|mixed|void
 */
function ws_ls_option( $key, $default, $has_to_be_pro = false ) {

	// If they need to be a Pro user and not, the apply default
	if ( true === $has_to_be_pro && false === WS_LS_IS_PRO ) {
		return $default;
	}

	return get_option( $key, $default);
}

/**
 * Fetch the given key from options and force to bool
 * @param $key
 * @param string $default
 * @param bool $has_to_be_pro
 * @return mixed
 */
function ws_ls_option_to_bool( $key, $default = 'yes', $has_to_be_pro = false ) {

	$value = ws_ls_option( $key, $default, $has_to_be_pro );

	return ws_ls_to_bool( $value );
}

/**
 * Fetch the given key from options and force to int
 * @param $key
 * @param int $default
 * @param bool $has_to_be_pro
 * @return int
 */
function ws_ls_option_to_int( $key, $default = 0, $has_to_be_pro = false ) {
	return (int) ws_ls_option( $key, $default, $has_to_be_pro );
}


/**
 * Force a value to an int
 * @param $value
 * @param bool $default
 * @return int
 */
function ws_ls_force_numeric_argument( $value, $default = false ) {

    if ( is_numeric( $value ) ) {
		return (int) $value;
	}

    return $default ?: 0;
}

/**
 * Used to validate a dimension - eg. except a number or a %
 *
 * @param $value
 * @param bool $default
 * @return bool|int
 */
function ws_ls_force_dimension_argument($value, $default = false) {

	if ( false === empty($value) ) {

		// Is this a percentage?
		$is_percentage = (false !== stripos($value, '%') ) ? true: false;

		// Strip % sign out if needed
		$value = ( $is_percentage ) ? ws_ls_remove_non_numeric($value) : $value;

		// If not numeric or below 0, apply default
		if ( false === is_numeric($value) || $value < (int) $value ) {
			$value = ( false === empty($default) ) ? $default : 0;
		}

		// Add % sign back on if needed
		return ( $is_percentage ) ? $value . '%' : $value;
	}

	return ( false === empty($default) ) ? $default : 0;
}

/**
 * Remove non numeric characters
 * @param $text
 *
 * @return string|string[]|null
 */
function ws_ls_remove_non_numeric( $text ) {
	if( true === empty( $text ) ) {
		return '';
	}

	return preg_replace("/[^0-9]/", '', $text );
}

/**
 * Fetch a value from the $_GET
 *
 * @param $key
 * @param bool $force_to_int
 * @param bool $default
 * @param bool $force_empty_to_null
 *
 * @return bool|int|mixed|null
 */
function ws_ls_querystring_value( $key, $force_to_int = false, $default = false, $force_empty_to_null = true ) {

    $return_value = NULL;

    if ( true === isset( $_GET[$key] ) ) {

		if ( true === $force_empty_to_null && '' === $_GET[ $key ] ) {
			return NULL;
		}

        return ( true === $force_to_int ) ? (int) $_GET[$key] : $_GET[$key];
    }

    // Use default if available
    return ( false !== $default && true === is_null( $return_value ) ) ? $default : $return_value;
}

/**
 * Fetch an item from the $_POST object
 *
 * @param $key
 * @param null $default
 * @param bool $force_empty_to_null
 * @param bool $json_decode
 *
 * @param null $cast
 *
 * @return mixed|null
 */
function ws_ls_post_value( $key, $default = NULL, $force_empty_to_null = false, $json_decode = false, $cast = NULL ) {

    if( false === isset( $_POST[ $key ] ) ) {
        return $default;
    }

    if ( true === $force_empty_to_null && '' === $_POST[ $key ] ) {
		return NULL;
    }

	$value = $_POST[ $key ];

    switch ( $cast ) {

	    case 'int':
	    	$value = (int) $value;
	        break;
	    case 'float':
		    $value = (float) $value;
		    break;
	    case 'bool':
		    $value = ws_ls_to_bool( $value );
		    break;
	    default:
	    	//already in the right format
    }

    return ( true === $json_decode ) ? json_decode( $value ) : $value;
}

/**
 * Check the value of $_POST and convert to bool
 * @param $key
 *
 * @return mixed
 */
function ws_ls_post_value_to_bool( $key ) {

	$value = ws_ls_post_value( $key );
	return ws_ls_to_bool( $value );
}

/**
 * Check for POST value and whether numeric or not
 * @param $key
 * @param null $default
 *
 * @return mixed|null
 */
function ws_ls_post_value_numeric( $key, $default = NULL ) {

	$value = ws_ls_post_value( $key, $default );

	if ( false === is_numeric( $value ) ) {
		return $default;
	}

	return $value;
}

/**
 * Deprecated!! Replace with  ws_ls_post_value_numeric
 *
 * @param $key
 * @param bool $default
 *
 * @return bool|mixed
 */
function ws_ls_get_numeric_post_value($key, $default = false) {
	return (isset($_POST[$key]) && is_numeric($_POST[$key])) ? $_POST[$key] : $default;
}

/**
 * Do we have a validate height
 * @param $height
 *
 * @return int
 */
function ws_ls_height_validate( $height ) {

	$height = (int) $height;

	return ( $height < 122 || $height > 201 ) ? 0 : $height;
}

/**
 * Either fetch data from the $_POST object for the given object keys
 *
 * TODO: Refactor to use ws_ls_post_value()
 *
 * @param $keys
 * @return array
 */
function ws_ls_get_values_from_post( $keys ) {

	$data = [];

	foreach ( $keys as $key ) {

		if ( true === isset( $_POST[ $key ] ) ) {
			$data[ $key ] = $_POST[ $key ];
		} else {
			$data[ $key ] = '';
		}

	}

	return $data;

}

/**
 * Get the current page URL
 * @param bool $base_64_encode
 * @return mixed|string#
 */
function ws_ls_get_url( $base_64_encode = false ) {

    $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	// Wee hack, replace removedata querystring value
	$current_url = str_replace('removedata', 'removed', $current_url);

	return ( true === $base_64_encode ) ? base64_encode( $current_url ) : $current_url;
}

/**
 * Validate an ISO date
 * @param $iso_date
 * @return bool
 */
function ws_ls_iso_date_valid( $iso_date ) {
	$dt = DateTime::createFromFormat("Y-m-d", $iso_date);
	return $dt !== false && !array_sum($dt::getLastErrors());
}

/**
 * Helper function to convert an ISO date into the relevant date format
 *
 * @param $date
 * @param null $user_id
 *
 * @return false|string
 */
function ws_ls_iso_date_into_correct_format( $date, $user_id = NULL ) {

	$user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

    // Build different date formats
    if( false === empty( $date ) ) {

    	$time 	= strtotime( $date );
    	$format = ws_ls_setting('use-us-dates', $user_id ) ? 'm/d/Y' : 'd/m/Y';

    	return date( $format, $time );
    }

    return NULL;
}

/**
 * Helper function to convert an ISO date into the relevant date/time format
 *
 * @param $date
 * @param null $user_id
 *
 * @return false|string
 */
function ws_ls_iso_datetime_into_correct_format( $datetime, $user_id = NULL ) {

	$user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	// Build different date formats
	if( false === empty( $datetime ) ) {

		$time 	= strtotime( $datetime );
		$format = ws_ls_setting('use-us-dates', $user_id ) ? 'm/d/Y H:m' : 'd/m/Y H:m';

		return date( $format, $time );
	}

	return NULL;
}
/**
 * Return the link for upgrade page
 * @return string
 */
function ws_ls_upgrade_link() {
    return admin_url( 'admin.php?page=ws-ls-license');
}

/**
 * Return the link for managing Groups page
 * @return string
 */
function ws_ls_groups_link() {
	return admin_url( 'admin.php?page=ws-ls-settings&mode=groups');
}

/**
 * Return the link for calculations page
 * @param bool $link_only
 * @return string
 */
function ws_ls_calculations_link( $link_only = false ) {

    $url = 'https://docs.yeken.uk/calculations.html';

    return ( false === $link_only ) ? sprintf('<a href="%s" target="blank">%s</a>', $url, __( 'Read more about calculations', WE_LS_SLUG ) ) : $url;
}

/**
* Helper function to get URL for further info on license types.
**/
function ws_ls_url_license_types() {
	return sprintf( 'For further information regarding the types of licenses available, <a href="%s" rel="noopener noreferrer" target="_blank">please visit our site, https://docs.yeken.uk</a>', esc_url( WE_LS_LICENSE_TYPES_URL ) );
}

/**
 * Helper function to display a WP notice (in Admin)
 * @param $text
 * @param string $type
 */
function ws_ls_display_notice( $text, $type = 'success' ) {

	if( true === empty( $text ) ) {
		return;
	}

	$type = ( false === empty( $type )
	            && false === in_array( $type, [ 'success', 'error', 'warning', 'info' ] ) ) ? 'success' :
					$type;

	echo sprintf('	<div class="notice notice-%s">
								<p>%s</p>
							</div>',
                            wp_kses_post( $type ),
                            wp_kses_post( $text )
	);
}
/**
 * If QS value detected, display data saved message
 */
function ws_ls_display_data_saved_message() {

	if( 'n' !== ws_ls_querystring_value( 'ws-edit-saved', false, 'n' ) ) {
		return ws_ls_display_blockquote( __('Your modifications have been saved', WE_LS_SLUG ), 'ws-ls-success' );
	}

	return '';
}

/**
 * Helper function to use Blockquote
 *
 * @class ws-ls-success
 * @text
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @param bool $include_log_link
 * @return string
 */
function ws_ls_display_blockquote( $text, $class = '', $just_echo = false, $include_log_link = false ) {

	$login_link = ( true === $include_log_link ) ?
					sprintf( ' <a class="ws-ls-login-link" href="%1$s">%2$s</a>.', esc_url( wp_login_url( get_permalink() ) ), __( 'Login' , WE_LS_SLUG ) ) :
					'';

	$html_output = sprintf('	<blockquote class="ws-ls-blockquote%s">
										<p>%s%s</p>
									</blockquote>',
									(false === empty( $class ) ) ? ' ' . esc_attr( $class ) : '',
									wp_kses_post( $text ),
									$login_link
						);

	if ( true === $just_echo ) {
		echo $html_output;
		return;
	}

	return $html_output;
}

/**
 * Display a success block quote
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @param bool $include_log_link
 *
 * @return string
 */
function ws_ls_blockquote_success( $text, $class = 'ws-ls-success' , $just_echo = false, $include_log_link = false ) {
	return ws_ls_display_blockquote( $text, $class, $just_echo, $include_log_link );
}

/**
 * Display Error Block quote for an error
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @param bool $include_log_link
 * @return string
 */
function ws_ls_blockquote_error( $text, $class = 'ws-ls-error-text', $just_echo = false, $include_log_link = false ) {
	return ws_ls_display_blockquote( $text, $class, $just_echo, $include_log_link );
}

/**
 * Display Error Block quote for an error
 * @param $text
 * @param string $class
 * @param bool $just_echo
 * @param bool $include_log_link
 * @return string
 */
function ws_ls_blockquote_login_prompt( ) {
	return ws_ls_display_blockquote( __( 'You must be logged in to view or edit your data.' , WE_LS_SLUG ) , '', false, true );
}

/**
 * Calculate max upload size (taken from Drupal)
 * @return float|int
 */
function ws_ls_file_upload_max_size() {

	static $max_size = -1;

    if ( $max_size < 0 ) {
        // Start with post_max_size.
        $post_max_size = ws_ls_parse_size( ini_get( 'post_max_size' ) );
        if ( $post_max_size > 0) {
            $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = ws_ls_parse_size( ini_get( 'upload_max_filesize' ) );
        if ( $upload_max > 0 && $upload_max < $max_size ) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

/**
 * Parse size from PHP ini into bytes (taken from Drupal)
 *
 * @param $size
 *
 * @return float
 */
function ws_ls_parse_size( $size ) {

    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size ); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size ); // Remove the non-numeric characters from the size.

	if ( $unit ) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0] ) ) );
    }

	return round( $size );
}

/**
 * Display bytes in readable format
 * (Snippet from PHP Share: http://www.phpshare.org)
 * @param $bytes
 *
 * @return string
 */
function ws_ls_format_bytes_into_readable( $bytes ) {
    if ( $bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2 ) . 'Gb';
    }
    elseif ( $bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2 ) . 'Mb';
    } elseif ( $bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2 ) . ' Kb';
    } elseif ( $bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ( $bytes == 1) {
        $bytes = $bytes . ' byte';
    }  else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

/**
 * Get Photos sizes in Mb
 *
 * @param bool $key
 * @return array|mixed
 */
function ws_ls_photo_get_sizes($key = false) {

	$sizes = [
				1000000 => '1Mb',
				2000000 => '2Mb',
				3000000 => '3Mb',
				4000000 => '4Mb',
				5000000 => '5Mb',
				6000000 => '6Mb',
				7000000 => '7Mb',
				8000000 => '8Mb',
				9000000 => '9Mb',
				10000000 => '10Mb'
	];

	return ( false === empty( $key ) && array_key_exists( $key, $sizes ) ) ? $sizes[ $key ] : $sizes;
}

/**
 * Return the max photo size allowed.
 *
 * @return float|int
 */

function ws_ls_photo_max_upload_size() {

	$file_size = get_option( 'ws-ls-photos-max-size', 2000000 );	// Default to 2Mb
	$max_size = ws_ls_file_upload_max_size();

	return ( $file_size > $max_size ) ? (int) $max_size : (int) $file_size;
}


/**
 * Simple function to render max upload size selected by user
 */
function ws_ls_photo_display_max_upload_size() {

    $max_size = ws_ls_photo_max_upload_size();

    $upload_size = ws_ls_photo_get_sizes($max_size);

    return ( true == is_array($upload_size) ) ? ws_ls_display_max_server_upload_size() : $upload_size;
}

/**
 * @return string Return server file upload limit
 */
function ws_ls_display_max_server_upload_size() {

	$max_size = ws_ls_file_upload_max_size();

	return ws_ls_format_bytes_into_readable($max_size);
}

/**
 * Display upgrade notice
 *
 * @param string $prompt_level
 */
function ws_ls_display_pro_upgrade_notice( $prompt_level = '' ) {


	// Is there a certain Pro level we're prompting for?
	if ( 'pro-plus' === $prompt_level ) {
		$title 		= __( 'Upgrade to Pro Plus and get more features!', WE_LS_SLUG );
		$message 	= __( 'Upgrade to Pro Plus version of this plugin to get additional features like Challenges, Harris Benedict, BMR, Macronutrients and much more!', WE_LS_SLUG );
	} else {
		$title 		= __( 'Upgrade Weight Tracker and get more features!', WE_LS_SLUG );
		$message 	= __( 'Upgrade to the latest Pro or Pro Plus version of this plugin to manipulate your user\'s data, add custom fields, BMR, Macronutrients and much more!', WE_LS_SLUG );
	}

	?>
    <div class="postbox ws-ls-advertise">
        <h3 class="hndle"><span><?php echo $title; ?> </span></h3>
        <div style="padding: 0px 15px 0px 15px">
            <p><?php echo $message; ?></p>
            <p><a href="<?php echo esc_url( admin_url('admin.php?page=ws-ls-license') ); ?>" class="button-primary"><?php echo __( 'Read more and upgrade', WE_LS_SLUG); ?></a></p>
        </div>
    </div>

<?php
}

/**
 * Upgrade notice for shortcode
 * @return string
 */
function ws_ls_display_pro_upgrade_notice_for_shortcode () {

	return sprintf( '<p>%s <a href="%s">%s</a></p>',
							__( 'To view this data, you need to upgrade to the Pro or Pro Plus version.', WE_LS_SLUG ),
							esc_url( admin_url('admin.php?page=ws-ls-license') ),
							__( 'Upgrade now', WE_LS_SLUG )
	);
}

/**
 * Return a Blur CSS class if not valid license
 *
 * @param bool $pro_plus
 * @param bool $space_before
 * @return string
 */
function ws_ls_blur( $pro_plus = false, $space_before = true ) {

    $class = 'ws-ls-blur';

    if ( true === $space_before ) {
        $class = ' ' . $class;
    }

    if ( false === $pro_plus && false === WS_LS_IS_PRO ) {
        return $class;
    } elseif ( true === $pro_plus && false === WS_LS_IS_PRO_PLUS ) {
        return $class;
    }

    return '';
}

/**
 * Blur string if incorrect license
 *
 * @param $text
 * @param bool $pro_plus
 * @return string
 */
function ws_ls_blur_text( $text, $pro_plus = false ) {

    if ( false === empty( $text ) ) {

        $blur = false;

        if ( false === $pro_plus && false === WS_LS_IS_PRO ) {
            $blur = true;
        } elseif ( true === $pro_plus && false === WS_LS_IS_PRO_PLUS ) {
            $blur = true;
        }

        if ( true === $blur ) {
            $text = str_repeat( '0', strlen( $text ) + 1 );
        }

    }

    return $text;
}

/**
 * Calculate the percentage difference between two numbers
 *
 * @param $previous_weight
 * @param $current_weight
 * @return array|null
 */
function ws_ls_calculate_percentage_difference( $previous_weight, $current_weight ) {

    if ( false === isset( $previous_weight ) || false === isset( $current_weight ) || $previous_weight === $current_weight ) {
        return NULL;
    }

    $difference = [ 'current' => $current_weight, 'increase' => ( $current_weight > $previous_weight ), 'previous' => $previous_weight ];

    if ( true === $difference['increase'] ) {

        $increase = $current_weight - $previous_weight;
        $difference['percentage'] = $increase / $previous_weight * 100;

    } else {

        $decrease = $previous_weight - $current_weight;
        $difference['percentage'] = $decrease /$previous_weight * 100;

    }

    return $difference;
}

/**
 * Wrapper of ws_ls_calculate_percentage_difference to return the value as a formatted number
 *
 * @param $previous_weight
 * @param $current_weight
 * @return string|null
 */
function ws_ls_calculate_percentage_difference_as_number( $previous_weight, $current_weight  ) {

    $difference = ws_ls_calculate_percentage_difference( $previous_weight, $current_weight );

    if ( true === empty( $difference ) ) {
        return NULL;
    }

    $return = $difference[ 'percentage' ];

    // Invert number if a decrease
    if ( false === $difference[ 'increase' ] ) {
        $return = 0 - $return;
    }

    return ws_ls_round_number( $return, 2 );
}

/**
 * Return the text value of enabled value
 *
 * @param $value
 * @param int $true_value
 * @return mixed|string
 */
function ws_ls_boolean_as_yes_no_string( $value, $true_value = 2 ) {

	return ( (int) $true_value == (int) $value ) ? __('Yes', WE_LS_SLUG) : __('No', WE_LS_SLUG);
}

/**
 * Fetch a user's First name / Last name from WP. IF not available, use display_name.
 * @param $user_id
 * @return string
 */
function ws_ls_user_display_name( $user_id ) {

    if ( true === empty( $user_id ) ) {
        return '-';
    }

	if ( $cache = ws_ls_cache_user_get( $user_id, 'display-name' ) ) {
		return $cache;
	}

    $name           = sprintf( '%s %s', get_user_meta( $user_id, 'first_name' , true ), get_user_meta( $user_id, 'last_name' , true ) );

    $display_name   = ( true === empty( $name ) || ' ' === $name ) ?
                            get_user_meta( $user_id, 'nickname' , true ) :
                                $name;

	ws_ls_cache_user_set( $user_id, 'display-name', $display_name );

	return $display_name;
}

/**
 * Challenges enabled?
 * @return bool
 */
function ws_ls_challenges_is_enabled() {
    return ( WS_LS_IS_PRO_PLUS &&
				true === ws_ls_settings_challenges_enabled() );
}

/**
 * Wrapper to number_format() so we can be consistent throughout plugin for number_format() options.
 * @param $number
 * @param int $decimal_places
 * @return string
 */
function ws_ls_round_number( $number, $decimal_places = 0 ) {

	$seperator = ( 'yes' === get_option( 'ws-ls-number-formatting-separator', 'yes' ) ) ? ',' : '';

	return number_format( $number, $decimal_places, '.', $seperator );
}

/**
 * Return a randomised ID for WT user controls
 * @return string
 */
function ws_ls_component_id() {
	return sprintf( 'ws_ls_%1$s_%2$s', mt_rand(), mt_rand() );
}

/**
 * User preferences enabled?
 * @return mixed
 */
function ws_ls_user_preferences_is_enabled() {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	return ws_ls_option_to_bool( 'ws-ls-allow-user-preferences', 'no', true );
}

/**
 * Hass CSS been disabled?
 * @return bool
 */
function ws_ls_css_is_disabled() {
	return ( 'yes' === get_option('ws-ls-disable-css', 'no' ) );
}

/**
 * Are target forms enabled?
 * @return bool
 */
function ws_ls_targets_enabled() {
	return ( 'yes' === get_option( 'ws-ls-allow-targets', 'yes' ) );
}

/**
 * Challenges enabled?
 * @return bool
 */
function ws_ls_settings_challenges_enabled() {
	return ( 'yes' === get_option( 'ws-ls-challenges-enabled', 'yes' ) );
}

/**
 * Fetch weight unit as readable text
 * @param $key
 *
 * @return string|void
 */
function ws_ls_weight_unit_label( $key ) {

	$units = ws_ls_weight_units();

	return ( false === empty( $units[ $key ] ) ) ?  $units[ $key ] : '';
}

/**
 * Return an array of weight units
 * @return array
 */
function ws_ls_weight_units() {
	return [ 'kg' => __( 'Kg', WE_LS_SLUG ), 'pounds_only' => __( 'Pounds', WE_LS_SLUG ), 'stones_pounds' => __( 'Stones & Pounds', WE_LS_SLUG ) ];
}

/**
 * Get the default weight unit for the site (specified in admin settings)
 * @return mixed|void
 */
function ws_ls_settings_weight_unit() {
	return get_option( 'ws-ls-units', 'kg' );
}

/**
 * Fetch admin weight unit setting as a readable string
 * @return string|void
 */
function ws_ls_settings_weight_unit_readable() {
	$unit = get_option( 'ws-ls-units', 'kg' );

	return ws_ls_weight_unit_label( $unit );
}

/**
 * Is the site default (specified in admin settings) to use imperial measurements?
 * @return bool
 */
function ws_ls_settings_weight_is_imperial() {
	return ( 'kg' !== ws_ls_settings_weight_unit() );
}

/**
 * Is the site default (specified in admin settings) to use US date formats?
 * @return bool
 */
function ws_ls_settings_date_is_us() {
	return ( 'us' == get_option( 'ws-ls-use-us-dates', 'uk' ) );
}

/**
 * Fetch a user setting, OR, if not applicable load the default site setting
 *
 * Works with:
 *
 *      Weight Unit         ( weight-unit )
 *      Use Imperial Unit   ( use-imperial ) - historical
 *      Use US Date format? ( use-us-dates )
 *
 * @param string $key
 * @param null $user_id
 * @param bool $force_admin
 *
 * @return bool|mixed|void|null
 */
function ws_ls_setting( $key = 'weight-unit', $user_id = NULL, $force_admin = false ) {

	$mappings = ws_ls_setting_mappings();

	// Valid key?
	if ( true === empty( $mappings[ $key ] ) ) {
		return NULL;
	}

	// Only consider ourselves in admin when not handling AJAX requests. Otherwise, force admin required.
	$in_admin = ( true === $force_admin ||
				  	( true === is_admin() && false === wp_doing_ajax() ) );

	// Are we considering the user preferences?
	if ( false === empty( $user_id )
	            && true === ws_ls_user_preferences_is_enabled() &&
	                false === $force_admin &&
		 				false === $in_admin ) {

		$user_preference    = NULL;
		$legacy_key         = $mappings[ $key ];

		// Do a direct lookup?
		if ( true === in_array( $legacy_key, [ 'WE_LS_US_DATE', 'WE_LS_DATA_UNITS' ] ) ) {

			$user_preference = ws_ls_user_preferences_settings_get( $legacy_key, $user_id );

			// We don't need to store this any longer, it can be determined by the selected user weight unit
		} elseif ( 'WE_LS_IMPERIAL_WEIGHTS' === $legacy_key ) {

			$user_weight_unit = ws_ls_user_preferences_settings_get( 'WE_LS_DATA_UNITS', $user_id );

			if ( false === empty( $user_weight_unit ) ) {
				$user_preference = ( 'kg' !== $user_weight_unit );
			}
		}

		// If we were able to find a user setting, then return that!
		if ( false === $user_preference ||
		        false === empty( $user_preference ) ) {
			return $user_preference;
		}
	}

	// Use the defaults specified in admin settings
	switch ( $key ) {

		case 'weight-unit':
			return ws_ls_settings_weight_unit();
			break;
		case 'use-imperial':
			return ws_ls_settings_weight_is_imperial();
			break;
		case 'use-us-dates':
			return ws_ls_settings_date_is_us();
			break;
	}

	return NULL;
}

/**
 * Return of setting keys against their legacy names (stored in user preferences table as part of a JSON object)
 * @return array
 */
function ws_ls_setting_mappings() {
	return [
				'use-us-dates'  => 'WE_LS_US_DATE',
				'weight-unit'   => 'WE_LS_DATA_UNITS',
				'use-imperial'  => 'WE_LS_IMPERIAL_WEIGHTS'     // This is likely to be dropped as can be determined by WE_LS_DATA_UNITS
	];
}

/**
 * Is the notes field to be shown?
 * @return bool
 */
function ws_ls_setting_hide_notes() {
	return ( 'no' === get_option( 'ws-ls-allow-user-notes', 'yes' ) );
}

/**
 * Should we add previous entry values as placeholders to form?
 * @return bool
 */
function ws_ls_setting_populate_placeholders_with_previous_values() {
	return ( 'yes' === get_option( 'ws-ls-populate-placeholders-with-previous-values', 'yes' ) );
}

/**
 * Compare the given weight against a user's start weight and return difference
 * @param $user_id
 * @param $kg
 * @param bool $display
 *
 * @return string|null
 */
function ws_ls_weight_difference_from_start( $user_id, $kg ) {

	$start_kg = ws_ls_db_weight_start_get( $user_id );

	if ( true === empty( $start_kg ) ) {
		return NULL;
	}

	return $kg - $start_kg;
}


/**
 * REFACTOR: replace with ws_ls_weight_unit_label
 *
 * @return string|void
 */
function ws_ls_get_unit() {

	switch ( ws_ls_setting( 'weight-unit' )) {
		case 'pounds_only':
			$unit = __("lbs", WE_LS_SLUG);
			break;
		case 'kg':
			$unit = __("Kg", WE_LS_SLUG);
			break;
		default:
			$unit = __("St", WE_LS_SLUG) . " " . __("lbs", WE_LS_SLUG);
			break;
	}

	return $unit;
}

