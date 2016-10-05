<?php

defined('ABSPATH') or die('Jog on!');

/*
	This file is used to send stats to YeKen.uk and fetch read only data

	No personal or identifiable data is sent to YeKen. Nor is any passwords or technical details.

*/

// ---------------------------------------------------------------------------------
// Fetch read only data from YeKen
// ---------------------------------------------------------------------------------

function ws_ls_get_data_from_yeken()
{
  // Look up date from Yeken.uk
  $cache = ws_ls_get_cache(WE_LS_CACHE_KEY_YEKEN_JSON);

  // Return cache if found!
  if ($cache)   {
      return $cache;
  }
  $response = wp_remote_get(WE_LS_DATA_URL);

  if( is_array($response) ) {
    if (200 == $response['response']['code'] && !empty($response['body'])) {
      $data = json_decode($response['body']);
      ws_ls_set_cache(WE_LS_CACHE_KEY_YEKEN_JSON, $data, 3 * HOUR_IN_SECONDS);
      return $data;
    }
  }

  return false;
}

// ---------------------------------------------------------------------------------
// Post Stats to YeKen
// ---------------------------------------------------------------------------------

// If cache key not found time to send communication to YeKen
if (WE_LS_ALLOW_STATS && is_admin() && false == ws_ls_get_cache(WE_LS_CACHE_COMMS_KEY)) {

	// Build payload to send to Yeken
	$data = array();
	$data['url'] = get_site_url();
	$data['is-pro'] = WS_LS_IS_PRO;
	$data['valid-license'] = ws_ls_has_a_valid_license();
	$data['site-hash'] = get_option(WS_LS_LICENSE_SITE_HASH);
	$data['hash'] = WS_LS_IS_PRO;
	$data['no-wp-users'] = ws_ls_do_counts();
	$data['no-wl-users'] = ws_ls_do_counts('wlt');
	$data['no-entries'] = ws_ls_do_counts('entries');
	$data['no-targets'] = ws_ls_do_counts('targets');
	$data['no-preferences'] = ws_ls_do_counts('preferences');
	$data['preferences'] = json_encode($globals);

	$result = wp_remote_post(WE_LS_STATS_URL, array('body' => $data));

	ws_ls_set_cache(WE_LS_CACHE_COMMS_KEY, 1, WE_LS_CACHE_COMMS_KEY_TIME);
}


function ws_ls_do_counts($type = '') {

	global $wpdb;
	$sql = 'Select count(ID) from ' . $wpdb->prefix . 'users';

	switch ($type) {
		case 'wlt':
			$sql = 'Select count(*) from (SELECT distinct weight_user_id FROM ' . $wpdb->prefix . WE_LS_TABLENAME . ') as T';
			break;
		case 'entries':
			$sql = 'Select count(*) from ' . $wpdb->prefix . WE_LS_TABLENAME;
			break;
		case 'targets':
			$sql = 'Select count(*) from ' . $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
			break;
		case 'preferences':
			$sql = 'Select count(*) from ' . $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;
			break;
		default:
			# code...
			break;
	}

	$count = $wpdb->get_var($sql);
	return (is_null($count)) ? false : $count;
}
