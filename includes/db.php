<?php
defined('ABSPATH') or die("Jog on!");

/* All DB related logic here! */

/* Load target data for logged in user */
function ws_ls_get_user_target($user_id) {

    global $wpdb;

    // Check if data exists in cache.
    $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_TARGET;

    // Return cache if found!
    if ($cache = ws_ls_get_cache($cache_key)) {
        return $cache;
    }
    // No cache? hit the DB
    else {

      $table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
      $sql = $wpdb->prepare('SELECT target_weight_weight, target_weight_stones, target_weight_pounds, target_weight_only_pounds FROM ' . $table_name . ' where weight_user_id = %d ', $user_id);
      $row = $wpdb->get_row( $sql );

      if (!is_null($row))
      {
            $target_weight = ws_ls_weight_object($user_id, $row->target_weight_weight, $row->target_weight_pounds, $row->target_weight_stones, $row->target_weight_only_pounds);

			// Clear all cache for this user
			ws_ls_delete_cache_for_given_user($user_id);

            // Store in cache
            ws_ls_set_cache($cache_key, $target_weight);
            return $target_weight;
      }
    }

  return false;
}

/* Fetch weight data for given user */
function ws_ls_get_weights($user_id, $limit = 100, $selected_week_number = -1, $sort_order = 'asc')
{    // Check if data exists in cache.
    $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_DATA;
    $cache_sub_key = $user_id . '-' . WE_LS_CACHE_KEY_DATA . '-' . $limit . '-' . $selected_week_number . '-' . $sort_order;

    $cache = ws_ls_get_cache($cache_key);

    // Return cache if found!
    if ($cache && !empty($cache[$cache_sub_key]))   {
		return $cache[$cache_sub_key];
    }
    // No cache? hit the DB

      global $wpdb;
      $additional_sql = '';

      if ($selected_week_number != -1){
        $week_ranges = ws_ls_get_week_ranges();

        if(!empty($week_ranges[$selected_week_number])) {
          $additional_sql =  $wpdb->prepare('and (weight_date BETWEEN %s AND %s)', $week_ranges[$selected_week_number]['start'], $week_ranges[$selected_week_number]['end']);
        }
      }

	  $measurement_columns_sql = '';
	  // Fetch measurement columns if enabled!
	  if (WE_LS_MEASUREMENTS_ENABLED) {
		  $measurement_columns = ws_ls_get_keys_for_active_measurement_fields('', true);
    	  $measurement_columns_sql = (!empty($measurement_columns)) ? ',' . implode(',', $measurement_columns) : '';
	  }

      $table_name = $wpdb->prefix . WE_LS_TABLENAME;
	  $sql =  $wpdb->prepare('SELECT id, weight_date, weight_weight, weight_stones, weight_pounds, weight_only_pounds, weight_notes' . $measurement_columns_sql . ' FROM ' . $table_name . ' where weight_user_id = %d ' . $additional_sql. ' order by weight_date ' . $sort_order . ' limit 0, %d', $user_id,  $limit);

	  $rows = $wpdb->get_results( $sql );

      // If data found in DB then save to cache and return
      if (is_array($rows) && count($rows) > 0) {

        $weight_data = array();

        foreach ($rows as $raw_weight_data) {

			$measurements = false;

			// Build weight array
			if(WE_LS_MEASUREMENTS_ENABLED && $measurement_columns && !empty($measurement_columns)) {
				$measurements = array();
				foreach ($measurement_columns as $key) {
					$measurements[$key] = $raw_weight_data->{$key};
				}
			}

          array_push($weight_data, ws_ls_weight_object($user_id,
                                                        $raw_weight_data->weight_weight,
                                                        $raw_weight_data->weight_pounds,
                                                        $raw_weight_data->weight_stones,
                                                        $raw_weight_data->weight_only_pounds,
                                                        $raw_weight_data->weight_notes,
                                                        $raw_weight_data->weight_date,
                                                        false,
                                                        $raw_weight_data->id,
														'',
														$measurements
                                                      ));
        }

	    // Fetch existing cached object for this user and store
        $cache[$cache_sub_key] = $weight_data;
        ws_ls_set_cache($cache_key, $cache);
        return $weight_data;
      }


    return false;
}
function ws_ls_get_weight($user_id, $row_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . WE_LS_TABLENAME;
    $sql =  $wpdb->prepare('SELECT * FROM ' . $table_name . ' where weight_user_id = %d and id = %d', $user_id, $row_id);
    $row = $wpdb->get_row( $sql );

    if (!is_null($row) ) {

		$measurement_columns = ws_ls_get_keys_for_active_measurement_fields('', true);
		$measurements = false;

		// Build weight array
		if(WE_LS_MEASUREMENTS_ENABLED && $measurement_columns && !empty($measurement_columns)) {
			$measurements = array();
			foreach ($measurement_columns as $key) {
				$measurements[$key] = ws_ls_prep_measurement_for_display($row->{$key}, $user_id);
			}
		}

        return ws_ls_weight_object($user_id,
                                    $row->weight_weight,
                                    $row->weight_pounds,
                                    $row->weight_stones,
                                    $row->weight_only_pounds,
                                    $row->weight_notes,
                                    $row->weight_date,
                                    false,
                                    $row->id,
									'',
									$measurements
                                  );
    }

    return false;
}
/* Fetch start weight data for given user */
function ws_ls_get_start_weight($user_id)
{
    // Check if data exists in cache.
    $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_START_WEIGHT;
    $cache_data = ws_ls_get_cache($cache_key);

    // Return cache if found!
    if ($cache_data && !empty($cache_data))   {
      return $cache_data;
    }
    // No cache? hit the DB
    else {

      global $wpdb;
      $table_name = $wpdb->prefix . WE_LS_TABLENAME;
      $sql =  $wpdb->prepare('SELECT weight_weight FROM ' . $table_name . ' where weight_user_id = %d order by weight_date asc limit 0, 1', $user_id);
      $cols = $wpdb->get_col($sql);

      // If data found in DB then save to cache and return
      if (is_array($cols) && count($cols) > 0) {

        $first_weight = $cols[0];

        // Fetch existing cached object for this user and store
        ws_ls_set_cache($cache_key, $first_weight);
        return $first_weight;
      }
    }

    return false;
}

function ws_ls_save_data($user_id, $weight_object, $is_target_form = fals, $existing_row_id = false)
{
	global $wpdb;

	$db_prefix = ($is_target_form) ? 'target_' : '';
	$db_is_update = false;
	$table_name = $wpdb->prefix . WE_LS_TABLENAME;

	// Ensure each weight field has been populated!
	if(!ws_ls_validate_weight_data($weight_object)) {
		return false;
	}

	// Build array of fields to pass to DB
	$db_fields['weight_user_id'] = $user_id;
	$db_fields[$db_prefix . 'weight_stones'] = $weight_object['stones'];
	$db_fields[$db_prefix . 'weight_pounds'] = $weight_object['pounds'];
	$db_fields[$db_prefix . 'weight_only_pounds'] = $weight_object['only_pounds'];
	$db_fields[$db_prefix . 'weight_weight'] = $weight_object['kg'];

	// Set data types
	$db_field_types = array('%d','%f', '%f', '%f', '%f');

	// If Measurements enabled then save measurements too.
	if (WE_LS_MEASUREMENTS_ENABLED) {
		$weight_keys = ws_ls_get_keys_for_active_measurement_fields();
		foreach ($weight_keys as $key) {
			if(isset($weight_object['measurements'][$key])) {

				// If empty or zero then NULL field before storing
				$db_fields[$key] = (empty($weight_object['measurements'][$key]) || 0 == $weight_object['measurements'][$key]) ? NULL : $weight_object['measurements'][$key];
				$db_field_types[] = '%f';
			}
		}
	}

	// Customise depending on whether an update or not
	if($is_target_form) {
		$db_is_update = ws_does_target_weight_exist($user_id);
	    $table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
	} else {

		$db_is_update = false;

		if($existing_row_id && is_numeric($existing_row_id)) {
			$db_is_update = $existing_row_id;
		} else {
			$db_is_update = ws_does_weight_exist_for_this_date($user_id, $weight_object['date']);
		}

		$db_fields['weight_notes'] = $weight_object['notes'];
	    array_push($db_field_types, '%s');
	    $db_fields['weight_date'] = $weight_object['date'];
	    array_push($db_field_types, '%s');
	}
	$result = false;

	// Update or insert
	if($db_is_update != false) {
		$result = $wpdb->update(
	    	$table_name,
	    	$db_fields,
	    	array( 'id' => $db_is_update ),
	    	$db_field_types,
	    	array( '%d' )
		);
	}
	else {
	    $result = $wpdb->insert(
	    	$table_name,
	      $db_fields,
	    	$db_field_types
	    );
	 }

	$result = ($result === false) ? false : true;

	// Tidy up cache
	ws_ls_delete_cache_for_given_user($user_id);

	// Update User stats table
	if (WS_LS_IS_PRO) {
		ws_ls_stats_update_for_user($user_id);

		// Throw data out in case anyone else wants to process it (also used within plugin for sending emails etc)
		$type = array (
			'user-id' => $user_id,
			'type' => ($is_target_form) ? 'target' : 'weight-measurements',
			'mode' => ($db_is_update) ? 'update' : 'add'
		);

		do_action(WE_LS_HOOK_DATA_ADDED_EDITED, $type, $weight_object);
	}

	return $result;
}

function ws_ls_delete_entry($user_id, $row_id)
{
  $result = false;
  global $wpdb;

  if (is_numeric($user_id) && is_numeric($row_id)) {
    $result = $wpdb->delete($wpdb->prefix . WE_LS_TABLENAME, array( 'id' => $row_id, 'weight_user_id' => $user_id));

    if ($result !== false) {
      $result = true;
      // Tidy up cache
      ws_ls_delete_cache_for_given_user($user_id);

	  // Update User stats table
	  if (WS_LS_IS_PRO) {
		  ws_ls_stats_update_for_user($user_id);
	  }
    }
  }
  return $result;
}

function ws_ls_delete_target($user_id)
{
  $result = false;
  global $wpdb;

  if (is_numeric($user_id)) {

    $result = $wpdb->delete($wpdb->prefix . WE_LS_TARGETS_TABLENAME, array( 'weight_user_id' => $user_id));

    if ($result !== false) {
      $result = true;
      // Tidy up cache
      ws_ls_delete_cache_for_given_user($user_id);
    }
  }
  return $result;
}

function ws_ls_get_min_max_dates($user_id)
{
  global $wpdb;

  $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_MIN_MAX_DATES;
  $cache = ws_ls_get_cache($cache_key);

  // Return cache if found!
  if ($cache)   {
      return $cache;
  }

  $table_name = $wpdb->prefix . WE_LS_TABLENAME;
  $sql =  $wpdb->prepare('SELECT min(weight_date) as min_date, max(weight_date) as max_date FROM ' . $table_name . ' WHERE weight_user_id = %d', $user_id);
  $row = $wpdb->get_row($sql);
  if (!is_null($row)) {
    ws_ls_set_cache($cache_key, $row);
    return $row;
  }
  return false;
}
function ws_does_weight_exist_for_this_date($user_id, $date)
{
  	global $wpdb;

	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_FOR_DAY;

	// Return cache if found!
    if ($cache = ws_ls_get_cache($cache_key)) {
        return $cache;
    }

	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql =  $wpdb->prepare('SELECT id FROM ' . $table_name . ' WHERE weight_date = %s and weight_user_id = %d', $date, $user_id);
	$row = $wpdb->get_row($sql);

	if (!is_null($row)) {
		ws_ls_set_cache($cache_key, true);
		return $row->id;
	}

	ws_ls_set_cache($cache_key, false);
  	return false;
}
function ws_does_target_weight_exist($user_id)
{
  global $wpdb;
  $table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
  $sql =  $wpdb->prepare('SELECT id FROM ' . $table_name . ' WHERE weight_user_id = %d', $user_id);
  $row = $wpdb->get_row($sql);

  if (!is_null($row)) {
    return $row->id;
  }

  return false;
}
function ws_ls_set_user_preferences($settings, $user_id = false, $height = false)
{
  global $wpdb;

	  if(false == $user_id){
	    $user_id = get_current_user_id();
	  }

	  // If not an array passed in blank Settings
	  if(!is_array($settings)) {
	    $settings = array();
	  }

	$table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

	// Build array of fields to pass to DB
	$db_fields['user_id'] = $user_id;
	$db_fields['settings'] = json_encode($settings);

	// Save Height, if not specified look up.
	if(WE_LS_DISPLAY_BMI_IN_TABLES) {
		if (false !== $height) {
			$height = ws_ls_validate_height($height);
		} else {
			$height = ws_ls_get_user_height($user_id, false);
		}
	}


  $db_fields['height'] = $height;

  // Set data types
  $db_field_types = array('%d','%s','%d');

  // Update or insert
  $result = $wpdb->replace(
                            $table_name,
                            $db_fields,
                            $db_field_types
                          );

  $result = ($result === false) ? false : true;

  // Tidy up cache
  ws_ls_delete_cache_for_given_user($user_id);
  return $result;
}

function ws_ls_get_user_preferences($user_id = false, $use_cache = true)
{
  global $wpdb;

  if(false == $user_id){
    $user_id = get_current_user_id();
  }

  $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

  $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_USER_PREFERENCE;
  $cache = ws_ls_get_cache($cache_key);

  // Return cache if found!
  if (is_array($cache) && true == $use_cache)   {
      return $cache;
  }

  $sql =  $wpdb->prepare('SELECT settings FROM ' . $table_name . ' WHERE user_id = %d', $user_id);
  $row = $wpdb->get_row($sql);

  $settings = false;

  if (!is_null($row)) {
    $settings = json_decode($row->settings, true);
  }

  if (!is_array($settings))  {
    $settings = array();
  }

  ws_ls_set_cache($cache_key, $settings);

  return $settings;
}

function ws_ls_get_user_height($user_id = false, $use_cache = true)
{
  global $wpdb;

  if(false == $user_id){
    $user_id = get_current_user_id();
  }

  $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

  $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_USER_HEIGHT;
  $cache = ws_ls_get_cache($cache_key);

  // Return cache if found!
  if ($cache && true == $use_cache)   {
      return $cache;
  }

  $sql =  $wpdb->prepare('SELECT height FROM ' . $table_name . ' WHERE user_id = %d', $user_id);
  $row = $wpdb->get_row($sql);

  $height = false;

  if (!is_null($row) && $row->height != 0 && !is_null($row->height)) {
    $height = $row->height;
	ws_ls_set_cache($cache_key, $height);
  }

  return $height;
}
function ws_ls_validate_height($height) {
	 // If not a valid height clear value
	 if(!is_numeric($height) || $height < 142 || $height > 201) {
	   $height = 0;
	 }
	 return $height;
}

function ws_ls_set_user_height($height, $user_id = false)
{
  global $wpdb;

  if(false == $user_id){
    $user_id = get_current_user_id();
  }

  // If not a valid height clear value
  $height = ws_ls_validate_height($height);

  $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

  // Build array of fields to pass to DB
  $db_fields['user_id'] = $user_id;
  $db_fields['height'] = $height;
  $db_fields['settings'] = json_encode(ws_ls_get_user_preferences($user_id, false));

  // Set data types
  $db_field_types = array('%d','%d','%s');

  // Update or insert
  $result = $wpdb->replace(
                            $table_name,
                            $db_fields,
                            $db_field_types
                          );

  $result = ($result === false) ? false : true;

  // Tidy up cache
  ws_ls_delete_cache_for_given_user($user_id);
  return $result;
}
