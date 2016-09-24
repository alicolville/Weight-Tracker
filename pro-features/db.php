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
    $sql = 'SELECT ' . $table_name . '.id, weight_date, weight_weight, weight_stones, weight_pounds, weight_only_pounds, weight_notes, weight_user_id, user_nicename' . $measurement_columns_sql . ' FROM ' . $table_name
                            . ' INNER JOIN ' . $user_table_name . ' on ' . $user_table_name . '.id = ' . $table_name . '.weight_user_id';

    // Searching column for something specific?
    if (isset($filters['search']) && !empty($filters['search'])) {
      $sql .=  $wpdb->prepare(' WHERE user_nicename like %s OR weight_notes like %s', '%' . $filters['search'] . '%', '%' . $filters['search'] . '%');
    }

    // Sorting?
    if(isset($filters['sort-column']) && in_array($filters['sort-column'], ws_ls_allowed_sort_columns()) && in_array($filters['sort-order'], ws_ls_allowed_sort_orders())) {
        $sql .= ' ORDER BY ' . $filters['sort-column'] . ' ' . $filters['sort-order'];
    } else {
        $sql .= ' ORDER BY weight_date desc';
    }

    $number_of_results = ws_ls_sql_count($sql);
    $sql .=  $wpdb->prepare(' LIMIT %d, %d', $filters['start'], $filters['limit']);

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

        array_push($weight_data, ws_ls_weight_object($raw_weight_data->weight_user_id,
                                                      $raw_weight_data->weight_weight,
                                                      $raw_weight_data->weight_pounds,
                                                      $raw_weight_data->weight_stones,
                                                      $raw_weight_data->weight_only_pounds,
                                                      $raw_weight_data->weight_notes,
                                                      $raw_weight_data->weight_date,
                                                      false,
                                                      $raw_weight_data->id,
                                                      $raw_weight_data->user_nicename,
													  $measurements
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
        $sql = strtoupper($sql);

        // Find FROM in SQL and remove everything before it. Replace with count.
        $sql = substr($sql, strpos($sql, 'FROM'), strlen($sql));
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
