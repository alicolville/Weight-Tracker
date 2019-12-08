<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_user_data($filters = false)
{
    global $wpdb;

	 // Fetch measurement columns if enabled!
  	$measurement_columns = ws_ls_get_keys_for_active_measurement_fields('', true);
  	$measurement_columns_sql = (!empty($measurement_columns)) ? ',' . implode(',', $measurement_columns) : '';

    $table_name = $wpdb->prefix . WE_LS_TABLENAME;
    $user_table_name = $wpdb->prefix . 'users';
    $sql = 'SELECT ' . $table_name . '.id, weight_date, weight_weight, weight_stones, weight_pounds, weight_only_pounds, weight_notes, weight_user_id, display_name as user_nicename, photo_id' . $measurement_columns_sql . ' FROM ' . $table_name
                            . ' INNER JOIN ' . $user_table_name . ' on ' . $user_table_name . '.id = ' . $table_name . '.weight_user_id';

	// Limit to a certain user?
    if(isset($filters['user-id']) && is_numeric($filters['user-id'])) {
		$sql .=  $wpdb->prepare(' WHERE weight_user_id = %d', $filters['user-id']);
    }

    // Sorting?
    if(isset($filters['sort-column']) && in_array($filters['sort-column'], ws_ls_allowed_sort_columns()) && in_array($filters['sort-order'], ws_ls_allowed_sort_orders())) {
        $sql .= ' ORDER BY ' . $filters['sort-column'] . ' ' . $filters['sort-order'];
    } else {
        $sql .= ' ORDER BY weight_date desc';
    }

    $number_of_results = ws_ls_sql_count($sql);

	if(isset($filters['start']) && isset($filters['limit'])) {
		$sql .=  $wpdb->prepare(' LIMIT %d, %d', $filters['start'], $filters['limit']);
	}

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

		$meta_field_data = ( true === ws_ls_meta_fields_is_enabled() ) ? ws_ls_meta_fields_for_entry_display( $raw_weight_data->id ) : false;

        array_push($weight_data, ws_ls_weight_object(	$raw_weight_data->weight_user_id,
                                                      	$raw_weight_data->weight_weight,
                                                      	$raw_weight_data->weight_pounds,
                                                      	$raw_weight_data->weight_stones,
                                                      	$raw_weight_data->weight_only_pounds,
                                                      	$raw_weight_data->weight_notes,
                                                      	$raw_weight_data->weight_date,
                                                      	false,
                                                      	$raw_weight_data->id,
                                                      	$raw_weight_data->user_nicename,
													  	$measurements,
														$raw_weight_data->photo_id,
                                                        $meta_field_data
                                                    ));
      }

      $results['weight_data'] = $weight_data;
      $results['count'] = $number_of_results;

      return $results;
    }

    return false;
}
function ws_ls_user_data_count()
{
    global $wpdb;

    $table_name = $wpdb->prefix . WE_LS_TABLENAME;
    $result =  $wpdb->get_col('SELECT count(*) from ' . $table_name);

    if(!empty($result)) {
      return $result;
    }

    return 0;
}
function ws_ls_sql_count($sql)
{
    global $wpdb;

    if(!empty($sql)) {
        $table_name = $wpdb->prefix . WE_LS_TABLENAME;

        // Find FROM in SQL and remove everything before it. Replace with count.
        $sql = substr($sql, stripos($sql, 'FROM'), strlen($sql));
        $sql = 'SELECT count(0) as count ' . $sql;

        $result =  $wpdb->get_row($sql);

        if(!is_null($result)) {
          return $result->count;
        }
    }
    return 0;
}
function ws_ls_allowed_sort_columns()
{
  return array('id', 'weight_date', 'weight_weight', 'user_nicename');
}
function ws_ls_allowed_sort_orders()
{
  return array('asc', 'desc');
}
// -----------------------------------------------------------------
// Stats
// -----------------------------------------------------------------
/*
	Fetch records that haven't been updated in the last hour
*/
function ws_ls_stats_fetch_those_that_need_update() {

	global $wpdb;
	$table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$sql = 'SELECT * FROM ' . $table_name . ' where last_update < DATE_SUB(NOW(),INTERVAL 6 HOUR) or last_update is null ORDER BY RAND() ';
	$rows = $wpdb->get_results( $sql, ARRAY_A );

	if (is_array($rows) && count($rows) > 0) {
		return $rows;
	}

	return false;
}

/*
	Refresh total lost count
*/
function ws_ls_stats_sum_weight_difference() {

	global $wpdb;
	$result = $wpdb->get_var( 'SELECT sum(weight_difference) FROM ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME );

	if (!is_null($result)) {
		return floatval($result);
	}

	return false;
}
/*
	Refresh total sum count
*/
function ws_ls_stats_sum_all_weights() {

	global $wpdb;
	$result = $wpdb->get_var( 'SELECT sum(sum_of_weights) FROM ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME );

	if (!is_null($result)) {
		return floatval($result);
	}

	return false;
}
/*
	Copy user IDs of those that have entered weights into stats table (assuming they aren't they're already)
*/
function ws_ls_stats_insert_missing_user_ids_into_stats() {

	global $wpdb;
	$stats_table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$data_table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql = "INSERT INTO $stats_table_name (user_id, start_weight, recent_weight, weight_difference, last_update)
			Select distinct weight_user_id, NULL, NULL, NULL, NULL from $data_table_name where weight_user_id not in (Select user_id from $stats_table_name)";
	$wpdb->query($sql);
	return;
}

/*
	Copy user IDs of those that have entered weights into stats table (assuming they aren't they're already)
*/
function ws_ls_stats_remove_deleted_user_ids_from_stats() {

	global $wpdb;

	$stats_table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
	$data_table_name = $wpdb->prefix . 'users';

	$sql = "Delete from $stats_table_name Where user_id not in ( Select ID from $data_table_name )";

	$wpdb->query( $sql );

	return;
}

/*
	Select league table
*/
function ws_ls_stats_league_table_fetch($ignore_cache = false, $limit = 10, $losers_only = false, $order = 'asc') {

	$cache_key = WE_LS_CACHE_STATS_TABLE . md5($ignore_cache . $limit . $losers_only . $order);

	// Return cache if found!
    if (!$ignore_cache && $cache = ws_ls_get_cache($cache_key)) {

		return $cache;
    }

	global $wpdb;

	$sql = 'SELECT * FROM ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;

	// -------------------------------------------------
	// Build where clause
	// -------------------------------------------------
	$where = array();

	// Select only users that have lost weight?
	if(true == ws_ls_force_bool_argument($losers_only)) {
		$where[] = 'weight_difference <= 0';
	}

	// Add where
	if (!empty($where)) {
		$sql .= ' where ' . implode(' and ', $where);
	}
	// -------------------------------------------------
	// Order
	// -------------------------------------------------
	$sql .= ' order by weight_difference ' . ((empty($order) || !in_array($order, array('desc', 'asc'))) ? 'desc' : $order);
	// -------------------------------------------------
	// Limit
	// -------------------------------------------------
	$sql .= $wpdb->prepare(
							' limit 0, %d',
							(empty($limit) || !is_numeric($limit)) ? 10 : (int) $limit
						);

	$results = $wpdb->get_results( $sql, ARRAY_A );

	if(!empty($results)) {
		ws_ls_set_cache($cache_key, $results, 5 * MINUTE_IN_SECONDS);
		return $results;
	}

	return false;
}

// -----------------------------------------------------------------
// Get Users
// -----------------------------------------------------------------

function ws_ls_user_get($id) {

    if(false === empty($id) && true === is_numeric($id) ) {

        global $wpdb;

        // Return cache if found!
        $cache = ws_ls_cache_user_get($id, 'user-object');
        if (false === empty($cache)) {
            return $cache;
        }

        $stats_table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;
        $data_table_name = $wpdb->prefix . WE_LS_TABLENAME;

        $sql = "SELECT distinct {$wpdb->prefix}users.*, us.* FROM {$wpdb->prefix}users
				LEFT JOIN {$data_table_name} as wd ON ( {$wpdb->prefix}users.ID = wd.weight_user_id )
				LEFT JOIN {$stats_table_name} as us ON ( {$wpdb->prefix}users.ID = us.user_id )
				LEFT JOIN {$wpdb->prefix}usermeta um ON ( {$wpdb->prefix}users.ID = um.user_id )
				WHERE 1=1 AND {$wpdb->prefix}users.ID = %d";

        $id = (int) $id;

        $sql = $wpdb->prepare($sql, $id);

        $user = $wpdb->get_row($sql, ARRAY_A);

        ws_ls_cache_user_set($id, 'user-object', $user);

        return $user;
    }

    return false;

}
