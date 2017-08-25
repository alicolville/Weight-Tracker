<?php
defined('ABSPATH') or die("Jog on!");

/* All caching related logic here! */

function ws_ls_get_cache($key) {

    if(WE_LS_CACHE_ENABLED) {
      $key = ws_ls_generate_cache_key($key);
      return get_transient($key);
    }

    return false;

}

function ws_ls_set_cache($key, $data, $time_to_expire = WE_LS_CACHE_TIME) {

    if(WE_LS_CACHE_ENABLED) {
      $key = ws_ls_generate_cache_key($key);
      set_transient($key, $data, $time_to_expire);
    }

    return false;
}

function ws_ls_delete_cache($key){

    if(WE_LS_CACHE_ENABLED) {
      $key = ws_ls_generate_cache_key($key);
      return delete_transient($key);
    }
    return false;
}
function ws_ls_delete_cache_for_given_user($user_id = false)
{
  global $wpdb;

  if (WE_LS_CACHE_ENABLED){
    if (false == $user_id)  {
      $user_id = get_current_user_id();
    }

    if(false === is_numeric($user_id)) {
        return;
    }

    $sql = "Delete FROM  $wpdb->options
            WHERE option_name LIKE '%transient_" . WE_LS_SLUG . $user_id ."%'";

    $wpdb->query($sql);

    $sql = "Delete FROM  $wpdb->options
            WHERE option_name LIKE '%transient_timeout_" . WE_LS_SLUG . $user_id ."%'";

	$wpdb->query($sql);

	$keys_to_clear = array(
							$user_id . '-' . WE_LS_CACHE_KEY_START_WEIGHT,
							$user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_FOR_DAY,
							$user_id . '-' . WE_LS_CACHE_KEY_TARGET_WEIGHT . 'target_weight_weight',
							$user_id . '-' . WE_LS_CACHE_KEY_TARGET_WEIGHT . 'target_weight_only_pounds',
							$user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_EXTREME . '-asc-weight_only_pounds',
	 						$user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_EXTREME . '-desc-weight_only_pounds',
							$user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_EXTREME . '-asc-weight_weight',
							$user_id . '-' . WE_LS_CACHE_KEY_WEIGHT_EXTREME . '-desc-weight_weight',
                            $user_id . '-' . WE_LS_CACHE_KEY_USER_PREFERENCE . '-gender',
                            $user_id . '-' . WE_LS_CACHE_KEY_USER_PREFERENCE . '-activity_level',
                            $user_id . '-' . WE_LS_CACHE_KEY_USER_PREFERENCE . '-dob',
							$user_id . '-' . WE_LS_CACHE_KEY_BMR,
							$user_id . '-' . WE_LS_CACHE_KEY_HARRIS_BENEDICT,
                            $user_id . '-' . WE_LS_CACHE_KEY_MACRO
						);

	foreach ($keys_to_clear as $key) {
		ws_ls_delete_cache($key);
	}

  }
}
function ws_ls_delete_all_cache()
{
  global $wpdb;

  if (WE_LS_CACHE_ENABLED){

    $sql = "Delete FROM  $wpdb->options
            WHERE option_name LIKE '%transient_" . WE_LS_SLUG ."%'";

    $wpdb->query($sql);

    $sql = "Delete FROM  $wpdb->options
            WHERE option_name LIKE '%transient_timeout_" . WE_LS_SLUG ."%'";

    $wpdb->query($sql);
  }
}
function ws_ls_generate_cache_key($key){
    return WE_LS_SLUG . $key;
}
