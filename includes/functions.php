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
                              $detect_and_convert_missing_values = false, $database_row_id = false, $user_nicename = '', $measurements = false)
{
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
    $weight['measurements'] = $measurements;

    // Build different date formats
    if($date != false && !empty($date)) {
        $time = strtotime($date);
        $weight['date'] = $date;
        $weight['date-uk'] = date('d/m/Y',$time);
        $weight['date-us'] = date('m/d/Y',$time);
		$weight['date-display'] = ws_ls_get_config('WE_LS_US_DATE') ? $weight['date-us'] : $weight['date-uk'];
        $weight['date-graph'] = date('d M',$time);
    }

    // If enabled, detect which weight fields need to be calculated and do it
    if ($detect_and_convert_missing_values)
    {
		switch (ws_ls_get_config('WE_LS_DATA_UNITS')) {
    		case 'pounds_only':
    			$weight['kg'] = ws_ls_pounds_to_kg($weight['only_pounds']);
            	$conversion = ws_ls_pounds_to_stone_pounds($weight['only_pounds']);
	            $weight['stones'] = $conversion['stones'];
            	$weight['pounds'] = $conversion['pounds'];
            	break;
    		case 'kg':
    			$weight['only_pounds'] = ws_ls_to_lb($weight['kg']);
            	$conversion = ws_ls_to_stone_pounds($weight['kg']);
            	$weight['stones'] = $conversion['stones'];
            	$weight['pounds'] = $conversion['pounds'];
    			break;
    		default:
    			$weight['kg'] = ws_ls_to_kg($weight['stones'], $weight['pounds']);
              	$weight['only_pounds'] = ws_ls_stones_pounds_to_pounds_only($weight['stones'], $weight['pounds']);
    			break;
    	}
    }

    // Generate display
    switch (ws_ls_get_config('WE_LS_DATA_UNITS')) {
      case 'pounds_only':
        $data = ws_ls_round_decimals($weight['only_pounds']);
        $weight['display'] = $data . __('lbs', WE_LS_SLUG);
        $weight['graph_value'] = $data;
        break;
      case 'kg':
        $weight['display'] = $weight['kg'] . __('Kg', WE_LS_SLUG);
        $weight['graph_value'] = $weight['kg'];
        break;
      default:
        $weight['display'] = $weight['stones'] . __('St', WE_LS_SLUG) . " " . $weight['pounds'] . __('lbs', WE_LS_SLUG);
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
	  $weight['display-admin'] = $weight['kg'] . __('Kg', WE_LS_SLUG);
	  break;
	default:
	  $weight['display-admin'] = $weight['stones'] . __('St', WE_LS_SLUG) . " " . $weight['pounds'] . __('lbs', WE_LS_SLUG);
	  break;
	}


  if ($weight['user_id']) {

    // Generate weight index
    $weight['user_id'] = $user_id;

    $weight['first_weight'] = ws_ls_get_start_weight($weight['user_id']);
    if(is_numeric($weight['first_weight']) && $weight['first_weight'] > 0 && $weight['first_weight'] <> $weight['kg']) {
		$weight['difference_from_start_kg'] = $weight['kg'] - $weight['first_weight'];
	  	$weight['difference_from_start'] = (($weight['difference_from_start_kg']) / $weight['first_weight']) * 100;
      	$weight['difference_from_start'] = round($weight['difference_from_start']);
		$weight['difference_from_start_display'] = ws_ls_convert_kg_into_relevant_weight_String($weight['difference_from_start_kg'], true);
    }

    // Get user display name
    $user_info = get_userdata($weight['user_id']);

    if($user_info) {
		$weight['user']['id'] = $weight['user_id'];
      	$weight['user']['display-name'] = $user_info->display_name;
	  	$weight['user']['email'] = $user_info->user_email;
    }
  }


  if (WE_LS_MEASUREMENTS_ENABLED && is_array($weight['measurements']) && !empty($weight['measurements'])) {

	  foreach ($weight['measurements'] as $key => $value) {

		  // Prep field!
		  $weight['measurements'][$key] = ws_ls_prep_measurement($weight['measurements'][$key]);

		  // Strip field prefix
		  if(strpos($key, 'ws-ls-') !== false) {
			  $new_key = str_replace('ws-ls-', '', $key);
			  $weight['measurements'][$new_key] = $weight['measurements'][$key];
			  unset($weight['measurements'][$key]);
		  }
	  }
  }

  return $weight;
}



/* Delete ALL existing user data */
function ws_ls_delete_existing_data() {
    if(is_admin())  {
        global $wpdb;
        $wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME);
        $wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix . WE_LS_TABLENAME);
    }
}

/* Delete all data for a user */
function ws_ls_delete_data_for_user($user_id = false) {

    if(WE_LS_ALLOW_USER_PREFERENCES || is_admin())  {

        if(false === $user_id) {
            $user_id = get_current_user_id();
        }

        global $wpdb;
        // Delete user targets
        ws_ls_delete_target($user_id);

        // Delete weight history
        $table_name =  $wpdb->prefix . WE_LS_TABLENAME;
        $sql = $wpdb->prepare("Delete from $table_name where weight_user_id = %d", $user_id);
        $wpdb->query($sql);

        ws_ls_delete_cache_for_given_user($user_id);

		// Update User stats table
		ws_ls_stats_update_for_user($user_id);

		// Let others know we cleared all user data
		do_action( WE_LS_HOOK_DATA_USER_DELETED, $user_id);
    }
}

/* Admin tool to check the relevant tables exist for this plugin */
function ws_ls_admin_check_mysql_tables_exist()
{
    $error_text = '';
    global $wpdb;

    $tables_to_check = array(
                            $wpdb->prefix . WE_LS_TARGETS_TABLENAME,
                            $wpdb->prefix . WE_LS_TABLENAME,
                            $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME
                        );

    // Check each table exists!
    foreach($tables_to_check as $table_name) {

        $rows = $wpdb->get_row('Show columns in ' . $table_name);
        if (0 == count($rows)) {
            $error_text .= '<li>' . $table_name . '</li>';
        }
    }

    // Return error message if tables missing
    if (!empty($error_text))  {
        return  __('The following MySQL tables are missing for this plugin', WE_LS_SLUG) . ':<ul>' . $error_text . '</ul>';
    }
    return false;
}

/*
  Used to display a jQuery dialog box in the admin panel
*/
function ws_ls_create_dialog_jquery_code($title, $message, $class_used_to_prompt_confirmation, $js_call = false)
{
  	global $wp_scripts;
  	$queryui = $wp_scripts->query('jquery-ui-core');
  	$url = "//ajax.googleapis.com/ajax/libs/jqueryui/".$queryui->ver."/themes/smoothness/jquery-ui.css";
  	wp_enqueue_script( 'jquery-ui-dialog' );
  	wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
    $id_hash = md5($title . $message . $class_used_to_prompt_confirmation);

    ?>
    <div id='<?php echo $id_hash; ?>' title='<?php echo $title; ?>'>
      <p><?php echo $message; ?></p>
    </div>
     <script>
          jQuery(function($) {
            var $info = $('#<?php echo $id_hash; ?>');
            $info.dialog({
                'dialogClass'   : 'wp-dialog',
                'modal'         : true,
                'autoOpen'      : false
            });
            $('.<?php echo $class_used_to_prompt_confirmation; ?>').click(function(event) {
                event.preventDefault();
                target_url = $(this).attr('href');
                var $info = $('#<?php echo $id_hash; ?>');
                $info.dialog({
                    'dialogClass'   : 'wp-dialog',
                    'modal'         : true,
                    'autoOpen'      : false,
                    'closeOnEscape' : true,
                    'buttons'       : {
                        'Yes': function() {

                            <?php if ($js_call != false): ?>
                                <?php echo $js_call; ?>

                                $(this).dialog('close');
                            <?php else: ?>
                                window.location.href = target_url;
                            <?php endif; ?>
                        },
                         'No': function() {
                            $(this).dialog('close');
                        }
                    }
                });
                $info.dialog('open');
            });

        });




      </script>

  <?php
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
function ws_ls_get_week_ranges()
{
  $entered_date_ranges = ws_ls_get_min_max_dates(get_current_user_id());

  if ($entered_date_ranges != false)  {

    // Get min and max dates for weight entries
    $start_date = new DateTime($entered_date_ranges->min_date);
    $end_date = new DateTime($entered_date_ranges->max_date);

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
  if ($week_ranges != false && count($week_ranges > 1))	{

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

function ws_ls_round_weights($weight)
{
  $weight['only_pounds'] = round($weight['only_pounds']);
  $weight['kg'] = round($weight['kg']);
  $weight['stones'] = round($weight['stones']);
  $weight['pounds'] = round($weight['pounds']);

  if (!empty($weight['difference_from_start']) && is_numeric($weight['difference_from_start'])) {
    $weight['difference_from_start'] = round($weight['difference_from_start']);
  }

  return $weight;
}

function ws_ls_get_config($key, $user_id = false)
{

  // If user preferences are enabled, then see if they specified
  if (WE_LS_ALLOW_USER_PREFERENCES && (!is_admin() || $user_id != false))  {

    // Look to see if the user had a preference, if not, default to admin choice
    $user_preference = ws_ls_get_user_preference($key, $user_id);

    if(is_null($user_preference)) {
      return constant($key);
    } else {
      return $user_preference;
    }

  }
  else {

    // Use admin default
    return constant($key);
  }
}
function ws_ls_get_user_preference($key, $user_id = false)
{
  if(false == $user_id){
    $user_id = get_current_user_id();
  }

  $user_preferences = ws_ls_get_user_preferences($user_id);

  if(array_key_exists($key, $user_preferences)){
    return $user_preferences[$key];
  }

  return NULL;
}
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
function ws_ls_force_bool_argument($value)
{

    if (strtolower($value) == 'true' || (is_bool($value) === true && $value == true)) {
        return true;
    }

    return false;
}
function ws_ls_force_numeric_argument($value, $default = false)
{
	if (is_numeric($value)) {
		return intval($value);
	}

    return ($default) ? $default : 0;
}
function ws_ls_remove_non_numeric($text)
{
  if(!empty($text)){
    return preg_replace("/[^0-9]/", "", $text);
  }
  return $text;
}
function ws_ls_fetch_elements_from_end_of_array($data, $number_to_grab)
{
    if (is_array($data) && count($data) > $number_to_grab) {
        $start = count($data) - $number_to_grab;
        $data = array_slice($data, $start, $number_to_grab);
    }

    return $data;
}

function ws_ls_display_default_measurements() {

	if(defined('WE_LS_MEASUREMENTS')) {
		$supported_measurements = json_decode(WE_LS_MEASUREMENTS, true);
		echo '
			<p>' . __('The plugin supports the following measurements', WE_LS_SLUG) . ':</p>
			<ul>';
		foreach ($supported_measurements as $key => $measurement) {
			echo '<li>' . $measurement['title'] . '</li>';
		}
		echo '</ul>';
	}

}

function ws_ls_format_stones_pound_for_comparison_display($weight) {

	if(isset($weight['stones']) && isset($weight['pounds'])) {

		$text = array();

		$show_stones = true;

		// Is stones equal to zero?
		if(-0 == $weight['stones'] || 0 == $weight['stones']) {
			$show_stones = false;
		}

		if ($show_stones) {
			$text[] = $weight['stones'] . __('St', WE_LS_SLUG);
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

function ws_ls_querystring_value($key, $force_to_int = false, $default = false) {

		$return_value = NULL;

	    if(isset($_GET[$key]) && $force_to_int) {
	        return intval($_GET[$key]);
	    }
	    elseif(isset($_GET[$key])) {
	    	return $_GET[$key];
	    }

		// Use default if aval
		if ($default && is_null($return_value)) {
			$return_value = $default;
		}
    return $return_value;
}

function ws_ls_get_url($base_64_encode = false) {
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	return (true === $base_64_encode) ? base64_encode($current_url) : $current_url;
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
 * @param bool $return_formatted_date_only
 * @return false|string
 */
function ws_ls_iso_date_into_correct_format($date, $return_formatted_date_only = true) {

    // Build different date formats
    if($date != false && !empty($date)) {
        $time = strtotime($date);
        $weight['date'] = $date;
        $weight['date-uk'] = date('d/m/Y',$time);
        $weight['date-us'] = date('m/d/Y',$time);
        $weight['date-display'] = ws_ls_get_config('WE_LS_US_DATE') ? $weight['date-us'] : $weight['date-uk'];
        $weight['date-graph'] = date('d M',$time);
        return ($return_formatted_date_only) ? $weight['date-display'] : $weight;
    }

    return $date;
}


?>
