<?php
defined('ABSPATH') or die("Jog on!");

/*
Date Interval class doesn't exist before PHP 4.3.
*/
function ws_ls_is_date_intervals_enabled()	{
  return class_exists('DateInterval');
}

/* Get string representation of a weight  */
function ws_ls_weight_object($user_id, $kg, $pounds, $stones, $pounds_only, $notes = '', $date = false,
                              $detect_and_convert_missing_values = false, $database_row_id = false, $user_nicename = '', $measurements = false,
								$photo_id = false, $meta_fields = false )
{

	// TODO: Refactor this so arguments are an array!
	// TODO: Refactor this to support different types of weight object. Full, Graph, etc. or have arguments specifying what to include in object!
	// TODO: Cache object based on options

	/*
	 * Chart needs:
	 *
	 * 		graph_value, date-, db_row_id
	 *
	 */

    $weight['display'] = '';
    $weight['user_id'] = $user_id;
    $weight['user_nicename'] = $user_nicename;
    $weight['only_pounds'] = $pounds_only;
    $weight['kg'] = $kg;
    $weight['stones'] = $stones;
    $weight['pounds'] = $pounds;
    $weight['graph_value'] = 0;
    $weight['notes'] = esc_html($notes);
    $weight['first_weight'] = false;
    $weight['difference_from_unit'] = '';
    $weight['db_row_id'] = $database_row_id;
	$weight['photo_id'] = false === empty($photo_id) ? $photo_id : false;
    $weight['meta-fields'] = $meta_fields;

    // Build different date formats
    if($date != false && !empty($date)) {
        $time = strtotime($date);
        $weight['date'] = $date;
        $weight['date-uk'] = date('d/m/Y',$time);
        $weight['date-us'] = date('m/d/Y',$time);
		$weight['date-display'] = ws_ls_get_config('WE_LS_US_DATE') ? $weight['date-us'] : $weight['date-uk'];
        $weight['date-graph'] = date_i18n('d M',$time);
    }

    // If enabled, detect which weight fields need to be calculated and do it
    if ($detect_and_convert_missing_values)
    {
		switch (ws_ls_get_config('WE_LS_DATA_UNITS', $user_id )) {
    		case 'pounds_only':
    			$weight['kg'] = ws_ls_convert_pounds_to_kg($weight['only_pounds']);
            	$conversion = ws_ls_convert_pounds_to_stone_pounds($weight['only_pounds']);
	            $weight['stones'] = $conversion['stones'];
            	$weight['pounds'] = $conversion['pounds'];
            	break;
    		case 'kg':
    			$weight['only_pounds'] = ws_ls_convert_kg_to_lb($weight['kg']);
            	$conversion = ws_ls_convert_kg_to_stone_pounds($weight['kg']);
            	$weight['stones'] = $conversion['stones'];
            	$weight['pounds'] = $conversion['pounds'];
    			break;
    		default:
    			$weight['kg'] = ws_ls_convert_stones_pounds_to_kg($weight['stones'], $weight['pounds']);
              	$weight['only_pounds'] = ws_ls_convert_stones_pounds_to_pounds($weight['stones'], $weight['pounds']);
    			break;
    	}
    }

    // Generate display
    switch ( ws_ls_get_config('WE_LS_DATA_UNITS')) {
      case 'pounds_only':
        $data = ws_ls_round_decimals($weight['only_pounds']);
        $weight['display'] = $data . __('lbs', WE_LS_SLUG);
        $weight['graph_value'] = $data;
        break;
      case 'kg':
        $weight['display'] = $weight['kg'] . __('kg', WE_LS_SLUG);
        $weight['graph_value'] = $weight['kg'];
        break;
      default:

          // If pounds at 14, then round up stones!
          if(14 == $weight['pounds']) {
              $weight['pounds'] = 0;
              $weight['stones']++;
          }

          $weight['display'] = $weight['stones'] . __('st', WE_LS_SLUG) . " " . $weight['pounds'] . __('lbs', WE_LS_SLUG);
          $weight['graph_value'] = ($weight['stones'] * 14) + $weight['pounds'];
        break;
  }

  // Get Admin display value. Ignore what the user has selected. (email notifications etc)
  switch (WE_LS_DATA_UNITS) {
	case 'pounds_only':
	  $data = ws_ls_round_decimals($weight['only_pounds']);
	  $weight['display-admin'] = $data . __('lbs', WE_LS_SLUG);
	  break;
	case 'kg':
	  $weight['display-admin'] = $weight['kg'] . __('kg', WE_LS_SLUG);
	  break;
	default:
	  $weight['display-admin'] = $weight['stones'] . __('st', WE_LS_SLUG) . " " . $weight['pounds'] . __('lbs', WE_LS_SLUG);
	  break;
	}


  if ($weight['user_id']) {

    // Generate weight index
    $weight['user_id'] = $user_id;

    $weight['first_weight'] = ws_ls_db_weight_start_get($weight['user_id']);
    if(is_numeric($weight['first_weight']) && $weight['first_weight'] > 0 && $weight['first_weight'] <> $weight['kg']) {
		$weight['difference_from_start_kg'] = $weight['kg'] - $weight['first_weight'];
	  	$weight['difference_from_start'] = (($weight['difference_from_start_kg']) / $weight['first_weight']) * 100;
      	$weight['difference_from_start'] = round($weight['difference_from_start']);
		$weight['difference_from_start_display'] = ws_ls_convert_kg_into_relevant_weight_string($weight['difference_from_start_kg'], true);
    }

    // Get user display name
    $user_info = get_userdata($weight['user_id']);

    if($user_info) {
		$weight['user']['id']           = $weight[ 'user_id' ];
      	$weight['user']['display-name'] = ws_ls_user_display_name( $weight['user_id'] );
	  	$weight['user']['email']        = $user_info->user_email;
    }
  }

  return $weight;
}


/**
 * Delete all targets and weight entries
 */
function ws_ls_delete_existing_data() {
    if( true === is_admin() )  {
	    ws_ls_db_entry_delete_all();
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
							$wpdb->prefix . WE_LS_MYSQL_GROUPS_USER
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

function ws_ls_render_date($weight_object, $use_admin_setting = false)
{
	$config_setting = ($use_admin_setting) ? WE_LS_US_DATE : ws_ls_get_config('WE_LS_US_DATE');

  	// Return US date if enabled otherwise return UK date
	if($config_setting) {
		return $weight_object['date-us'];
	}
	else {
		return $weight_object['date-uk'];
	}
}
function ws_ls_get_unit()
{

  switch (ws_ls_get_config('WE_LS_DATA_UNITS')) {
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

/**
 *
 * REFACTOR!!
 * CACHE!
 *
 * @return array|bool
 * @throws Exception
 */
function ws_ls_get_week_ranges()
{
  $entered_date_ranges = ws_ls_db_dates_min_max_get(get_current_user_id());

  if ($entered_date_ranges != false)  {

    // Get min and max dates for weight entries
    $start_date = new DateTime($entered_date_ranges[ 'min' ]);
    $end_date = new DateTime($entered_date_ranges[ 'max' ]);

    // Grab all the weekly intervals between those dates
    $interval = new DateInterval('P1W');
    $daterange = new DatePeriod($start_date, $interval ,$end_date);

    $date_ranges = array();

    $i = 1;

    // Build an easy to use array
    foreach($daterange as $date){

      $end_of_week = clone $date;
      $end_of_week = date_modify($end_of_week, '+1 week' );

        $date_ranges[$i] =  array("start" => $date->format("Y-m-d"), "end" => $end_of_week->format("Y-m-d") );

        $i++;
    }

    return $date_ranges;
  }

  return false;
}
function ws_ls_get_date_format()
{
  if(ws_ls_get_config('WE_LS_US_DATE')){
    return 'm/d/Y';
  }

  return 'd/m/Y';
}
function ws_ls_display_week_filters($week_ranges, $selected_week_number)
{
  $output = '';

  // If we have valid options for week dropdown, start building it
  if ( false === empty( $week_ranges ) && true === is_array( $week_ranges ) )	{

    $output .= '<form action="' .  get_permalink() . '#wlt-weight-history" method="post">
                  <input type="hidden" value="true" name="week_filter">
                    <div class="ws_ls_week_controls">
                      <select name="week_number" onchange="this.form.submit()" class="ws-ls-select">
                        <option value="-1" ' . (($selected_week_number == -1) ?  ' selected="selected"' : '') . '>'. __('View all weeks', WE_LS_SLUG) . '</option>';

    // Loop through each weekly option and build drop down
    foreach ($week_ranges as $key => $week) {

      $date_format = ws_ls_get_date_format();

      $start_date = new DateTime($week['start']);
      $start_date = $start_date->format($date_format);

      $end_date = new DateTime($week['end']);
      $end_date = $end_date->format($date_format);

      $output .= '<option value="' . $key . '" ' . (($selected_week_number == $key) ? ' selected="selected"' : '') . '>'
                  . __('View Week', WE_LS_SLUG) . ' ' . $key . ' - ' . $start_date . ' ' . __('to', WE_LS_SLUG) . ' ' . $end_date . '</option>';

    }

    $output .= '</select>
              </div>
            </form>';
  }

  return $output;

}

// TODO: This will need to be changed when globals are tweaked
// TODO: Heabily refactor this function
function ws_ls_get_config( $key, $user_id = false, $force_admin = false )
{
	// If in an admin screen, then use admin settings. We're not interested in the user's preference.
	if ( true === is_admin() || true === $force_admin ) {
		return constant( $key );
	}

  // If user preferences are enabled, then see if they specified
  if ( true === ws_ls_user_preferences_is_enabled() && (!is_admin() || $user_id != false))  {

    // Look to see if the user had a preference, if not, default to admin choice
    $user_preference = ws_ls_get_user_preference($key, $user_id);

    if(is_null($user_preference)) {
      return constant($key);
    } else {
      return $user_preference;
    }

  }

   return constant($key);
}

function ws_ls_get_user_preference( $key, $user_id = false )
{
  if(false == $user_id){
    $user_id = get_current_user_id();
  }

  $user_preferences = ws_ls_user_preferences_settings( $user_id );

  if(array_key_exists($key, $user_preferences)){
    return $user_preferences[$key];
  }

  return NULL;
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

	$arguments = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'id' => NULL, 'meta' => true ] );

	$cache_key  = sprintf( 'entry-full-%d', $arguments[ 'id' ] );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], $cache_key ) ) {
		return $cache;
	}

	$entry = ws_ls_db_entry_get( $arguments );

	if ( true === empty( $entry ) ) {
		return NULL;
	}

	$entry[ 'first_weight' ] = ws_ls_db_weight_start_get( $arguments[ 'user-id' ] );

	$entry[ 'difference_from_start_kg' ] = ( false === empty( $entry[ 'first_weight' ] ) && $entry[ 'first_weight' ] <> $entry[ 'kg' ] ) ?
												$entry[ 'kg' ] - $entry[ 'first_weight' ] :
													0;

	if ( true === WS_LS_IS_PRO &&
	        true === $arguments[ 'meta' ] &&
	            true === ws_ls_meta_fields_is_enabled() ) {

		$entry[ 'meta' ] = ws_ls_meta( $arguments[ 'id' ] );

		// Pluck to meta_id => value
		if ( false === empty( $entry[ 'meta'] ) ) {
			$entry[ 'meta'] = wp_list_pluck( $entry[ 'meta'], 'value', 'meta_field_id' );
		}
	}

	ws_ls_cache_user_set( $arguments[ 'user-id' ], $cache_key, $entry );

	return $entry;
}

/**
 * Fetch the oldest entry
 * @param array $arguments
 *
 * @return string|null
 */
function ws_ls_entry_get_oldest( $arguments = [] ) {

	$arguments              = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'meta' => true ] );
	$arguments[ 'which']    = 'oldest';
	$arguments[ 'id' ]      = ws_ls_db_entry_latest_or_oldest( $arguments );

	if ( true === empty( $arguments[ 'id' ] ) ) {
		return NULL;
	}

	return ws_ls_entry_get( $arguments );
}

/**
 * Fetch the latest entry
 * @param array $arguments
 *
 * @return string|null
 */
function ws_ls_entry_get_latest( $arguments = [] ) {

	$arguments              = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'meta' => true ] );
	$arguments[ 'which']    = 'latest';
	$arguments[ 'id' ]      = ws_ls_db_entry_latest_or_oldest( $arguments );

	if ( true === empty( $arguments[ 'id' ] ) ) {
		return NULL;
	}

	return ws_ls_entry_get( $arguments );
}

/**
 *
 * DEPRECATED: replace with ws_ls_to_bool()
 *
 * @param $value
 * @return bool
 *
 */
function ws_ls_string_to_bool($value)
{
  if('false' == $value) {
    return false;
  }
  elseif('true' == $value) {
    return true;
  }

  return $value;
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
function ws_ls_remove_non_numeric($text) {
  if( false === empty($text) ){
    return preg_replace("/[^0-9]/", "", $text);
  }
  return $text;
}

function ws_ls_format_stones_pound_for_comparison_display($weight) {

	if(isset($weight['stones']) && isset($weight['pounds'])) {

		$text = array();

		$show_stones = true;

		// Round up figures that hit 14lb
		if(14 == $weight["pounds"]) {
			$weight["pounds"] = 0;
			$weight["stones"]++;
		} else if (-14 == $weight["pounds"]) {
			$weight["pounds"] = 0;
			$weight["stones"]--;
		}

		// Is stones equal to zero?
		if(-0 == $weight['stones'] || 0 == $weight['stones']) {
			$show_stones = false;
		}

		if ($show_stones) {
			$text[] = $weight['stones'] . __('st', WE_LS_SLUG);
		}

		if (is_numeric($weight['pounds'])) {

			// If both stones and pounds negative then invert pounds.
			// e.g.
			// -1 stone -10 pounds will get displayed as -1 stone 10 pounds
			if ($show_stones && (-0 == $weight['stones'] || $weight['stones'] < 0) && $weight['pounds'] < 0) {
				$weight['pounds'] = abs($weight['pounds']);
			}

			$text[] = $weight['pounds'] . __('lbs', WE_LS_SLUG);
		}

		return implode(' ', $text);
	}

	return '';
}

/**
 * Fetch a value from the $_GET
 * @param $key
 * @param bool $force_to_int
 * @param bool $default
 * @return bool|int|mixed|null
 */
function ws_ls_querystring_value( $key, $force_to_int = false, $default = false ) {

    $return_value = NULL;

    if ( true === isset( $_GET[$key] ) ) {
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

    if ( true === $force_empty_to_null && "" === $_POST[ $key ] ) {
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
 * Render a <select> for the given key / value array
 * @param $key
 * @param $name
 * @param $values
 * @param string $key_prefix
 * @return string
 */
function ws_ls_select( $key, $name, $values, $key_prefix = 'filter' ) {

    $key = sprintf( '%s-%s', $key_prefix, $key );

    $html = sprintf( '<label for="%1$s">%2$s:</label>
                                    <select id="%1$s" name="%1$s" >',
        esc_attr( $key ),
        esc_attr( $name )
    );

    $selected = ws_ls_querystring_value( $key, true );

    foreach ( $values as $id => $value ) {
        $html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
                    esc_attr( $id ),
                    selected( $selected, $id, false ),
                    esc_html( $value )
        );
    }

    $html .= '</select>';

    return $html;
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

function ws_ls_stats_clear_last_updated_date(){
    global $wpdb;
    $wpdb->query('Update ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME . ' set last_update = NULL');
    return;
}

/**
 * Helper function to convert an ISO date into the relevant date format
 *
 * @param $date
 * @return false|string
 */
function ws_ls_iso_date_into_correct_format( $date ) {

    // Build different date formats
    if( false === empty( $date ) ) {

    	$time 	= strtotime( $date );
    	$format = ws_ls_get_config('WE_LS_US_DATE') ? 'm/d/Y' : 'd/m/Y';

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
function ws_ls_calculations_link($link_only = false) {

    $url = 'https://weight.yeken.uk/calculations/';

    return (false === $link_only) ? sprintf('<a href="%s" target="blank">%s</a>', $url, __('Read more about calculations', WE_LS_SLUG)) : $url;
}

/**
* Helper function to get URL for further info on license types.
**/
function ws_ls_url_license_types() {
	return sprintf('For further information regarding the types of licenses available, <a href="%s" rel="noopener noreferrer" target="_blank">please visit our site, weight.yeken.uk</a>', esc_url(WE_LS_LICENSE_TYPES_URL));
}

/**
 * Helper function to display a WP notice (in Admin)
 * @param $text
 * @param string $type
 */
function ws_ls_display_notice($text, $type = 'success') {

	if(true === empty($text)) {
		return;
	}

	$type = (false === empty($type) && false === in_array($type, ['success', 'error', 'warning', 'info'])) ? 'success' : $type;

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
					sprintf( ' <a href="ws-ls-login-link" href="%1$s">%2$s</a>.', esc_url( wp_login_url( get_permalink() ) ), __( 'Login' , WE_LS_SLUG ) ) :
					'';

	$html_output = sprintf('	<blockquote class="ws-ls-blockquote%s">
										<p>%s%s</p>
									</blockquote>',
									(false === empty( $class ) ) ? ' ' . esc_attr( $class ) : '',
									esc_html( $text ),
									$login_link
						);

	if ( true === $just_echo ) {
		echo $html_output;
		return;
	}

	return $html_output;
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

//todo: review this
// Calculate max upload size (taken from Drupal)
function ws_ls_file_upload_max_size() {
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = ws_ls_parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = ws_ls_parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

// Parse size from PHP ini into bytes (taken from Drupal)
function ws_ls_parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    else {
        return round($size);
    }
}

// Snippet from PHP Share: http://www.phpshare.org
function ws_ls_format_bytes_into_readable($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . 'Gb';
    }
    elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . 'Mb';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' Kb';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
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

	return ( false === empty($key) && array_key_exists($key, $sizes) ) ? $sizes[$key] : $sizes;
}

/**
 * Return the max photo size allowed.
 *
 * @return float|int
 */

function ws_ls_photo_max_upload_size() {

	$file_size = WE_LS_PHOTOS_MAX_SIZE;
	$max_size = ws_ls_file_upload_max_size();

	// If no photo size specified, default to 2Mb
	if ( false === defined('WE_LS_PHOTOS_MAX_SIZE') || 0 === $file_size ) {
		return 2000000;
	}

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
 */
function ws_ls_display_pro_upgrade_notice( ) {
?>

    <div class="postbox ws-ls-advertise">
        <h3 class="hndle"><span><?php echo __( 'Upgrade Weight Tracker and get more features!', WE_LS_SLUG); ?> </span></h3>
        <div style="padding: 0px 15px 0px 15px">
            <p><?php echo __( 'Upgrade to the latest Pro or Pro Plus version of this plugin to manipulate your user\'s data, add custom fields, BMR, Macro Nutrients and much more!', WE_LS_SLUG); ?></p>
            <p><a href="<?php echo esc_url( admin_url('admin.php?page=ws-ls-license') ); ?>" class="button-primary"><?php echo __( 'Read more and Upgrade to Pro / Pro Plus Version', WE_LS_SLUG); ?></a></p>
        </div>
    </div>

<?php
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

    $name = sprintf( '%s %s', get_user_meta( $user_id, 'first_name' , true ), get_user_meta( $user_id, 'last_name' , true ) );

    return ( true === empty( $name ) || ' ' === $name ) ?
        get_user_meta( $user_id, 'nickname' , true ) :
        $name;
}

/**
 * Challenges enabled?
 * @return bool
 */
function ws_ls_challenges_is_enabled() {
    return ( true === WS_LS_IS_PRO_PLUS &&
        'yes' === get_option( 'ws-ls-challenges-enabled', 'no' ) );
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
	return ws_ls_option_to_bool( 'ws-ls-allow-user-preferences', 'no', true );
}
