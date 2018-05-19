<?php
	defined('ABSPATH') or die('Jog on!');

function ws_ls_weight_target_weight($user_id = false, $admin_display = false) {

	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	$target_weight = ws_ls_get_user_target($user_id);

	if ($target_weight) {
	    return (true === $admin_display) ? $target_weight['display-admin'] : $target_weight['display'];
	}

	return '';
}

function ws_ls_weight_start($user_id = false)
{
	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	if (ws_ls_get_config('WE_LS_DATA_UNITS') == "pounds_only") {
		$weight = ws_ls_get_start_weight_in_pounds($user_id);
	}
	else {
		$weight = ws_ls_get_weight_extreme($user_id);
	}
	return we_ls_format_weight_into_correct_string_format($weight);
}
function ws_ls_weight_recent($user_id = false)
{
	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	if (ws_ls_get_config('WE_LS_DATA_UNITS') == "pounds_only") {
		$weight =  ws_ls_get_recent_weight_in_pounds($user_id);
	}
	else {
		$weight =  ws_ls_get_weight_extreme($user_id, true);
	}

	return we_ls_format_weight_into_correct_string_format($weight);
}
function ws_ls_weight_difference($user_id = false)
{
	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	if (ws_ls_get_config('WE_LS_DATA_UNITS') == "pounds_only"){
		$start_weight = ws_ls_get_start_weight_in_pounds($user_id);
		$recent_weight = ws_ls_get_recent_weight_in_pounds($user_id);
	}
	else	{
		$start_weight = ws_ls_get_start_weight_in_kg($user_id);
		$recent_weight = ws_ls_get_weight_extreme($user_id, true);
	}

	// If no data, return empty string
    if ( false === $recent_weight && false === $start_weight ) {
	    return '';
    }

	$difference = $recent_weight - $start_weight;

	$display_string = ($difference > 0) ? "+" : "";

	$display_string .= we_ls_format_weight_into_correct_string_format($difference, true);

	return $display_string;
}
function ws_ls_weight_difference_target($user_id = false){
	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	if (ws_ls_get_config('WE_LS_DATA_UNITS') == "pounds_only") {
		$target_weight = ws_ls_get_target_weight_in_pounds($user_id);
		$recent_weight = ws_ls_get_recent_weight_in_pounds($user_id);
	}
	else {
		$target_weight = ws_ls_get_target_weight_in_kg($user_id);
		$recent_weight = ws_ls_get_weight_extreme($user_id, true);
	}

	if(empty($target_weight)) {
		return __('No target set', WE_LS_SLUG);
	}

	$difference = $recent_weight - $target_weight;

	$display_string = ($difference > 0) ? "+" : "";

	$display_string .= we_ls_format_weight_into_correct_string_format($difference, true);

	return $display_string;
}
function ws_ls_weight_difference_previous($user_id = false){
	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	$previous_weight = ws_ls_get_weight_previous( $user_id );
	$recent_weight = ws_ls_get_recent_weight_in_kg( $user_id );

	$difference = $recent_weight - $previous_weight;

	$display_string = ($difference > 0) ? "+" : "";

	$display_string .= we_ls_format_weight_into_correct_string_format($difference, true);

	return $display_string;
}

function ws_ls_get_start_weight_in_kg($user_id = false){

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_extreme($user_id);
}
function ws_ls_get_recent_weight_in_kg($user_id = false){

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_extreme($user_id, true);
}
function ws_ls_get_start_weight_in_pounds($user_id = false) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_extreme($user_id, false, "weight_only_pounds");
}
function ws_ls_get_recent_weight_in_pounds($user_id){

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_extreme($user_id, true, "weight_only_pounds");
}

function ws_ls_get_weight_extreme($user_id, $recent = false, $unit = "weight_weight")
{
	global $wpdb;

	$direction = "asc";

	if ($recent)
		$direction = "desc";

	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_EXTREME . '-' . $direction . '-' . $unit;

	// Return cache if found!
	if ($cache = ws_ls_get_cache($cache_key))   {
		return $cache;
	}

	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql =  $wpdb->prepare("SELECT " . $unit . " as weight_value FROM $table_name where weight_user_id = %d order by weight_date " . $direction . " limit 0, %d", $user_id, 1);
	$rows = $wpdb->get_row($sql);

	if ( false === empty( $rows->weight_value ) ) {
		ws_ls_set_cache($cache_key, $rows->weight_value );

		return $rows->weight_value;
	}
	else
		return false;

}
function ws_ls_get_weight_previous($user_id) {
	global $wpdb;

	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_PREVIOUS;

	// Return cache if found!
	if ($cache = ws_ls_get_cache($cache_key))   {
		return $cache;
	}

	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql = $wpdb->prepare("SELECT weight_weight FROM $table_name where weight_user_id = %d order by weight_date desc limit 1, 1", $user_id);

	$result = $wpdb->get_var($sql);

	if ( false === empty( $result ) ) {

		$result = floatval($result);

		ws_ls_set_cache($cache_key, $result);
		return $result;
	}

	return NULL;
}
function ws_ls_get_target_weight_in_kg($user_id = false){

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_target($user_id);
}
function ws_ls_get_target_weight_in_pounds($user_id = false){

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_target($user_id, "target_weight_only_pounds");
}
function ws_ls_get_weight_target($user_id, $unit = "target_weight_weight")
{
	global $wpdb;

	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_TARGET_WEIGHT . $unit;
  	$cache = ws_ls_get_cache($cache_key);

	  // Return cache if found!
	  if ($cache)   {
		 return  $cache;
	  }

	$table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
	$sql =  $wpdb->prepare("SELECT " . $unit . " as weight_value FROM $table_name where weight_user_id = %d", $user_id);
	$rows = $wpdb->get_row($sql);

	if (count($rows) > 0) {
		ws_ls_set_cache($cache_key, $rows->weight_value);
		return $rows->weight_value;
	}

	return false;

}
function we_ls_format_weight_into_correct_string_format($weight, $comparison = false) {

    // Don't bother converting the value if there isn't one!
    if ( false === $weight) {
        return '';
    }

	if(ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS'))
	{
		if (ws_ls_get_config('WE_LS_DATA_UNITS') == "pounds_only")
			return $weight . __("lbs", WE_LS_SLUG);
		else
		{
			$weight_data = ws_ls_to_stone_pounds($weight);

			if ($comparison) {
				return ws_ls_format_stones_pound_for_comparison_display($weight_data);
			} else {

				if($weight_data["pounds"] < 0) {
					$weight_data["pounds"] = abs($weight_data["pounds"]);
				}

				// If Lbs is 14, then set to 0 and increment stones!
				if(14 == $weight_data["pounds"]) {
					$weight_data["pounds"] = 0;
					$weight_data["stones"]++;
				}

				return $weight_data["stones"] . __("st", WE_LS_SLUG) . " " . $weight_data["pounds"] . __("lbs", WE_LS_SLUG);
			}
		}
	}
	else {
		return $weight . __("Kg", WE_LS_SLUG);
	}

	return '';

}
