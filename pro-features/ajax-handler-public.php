<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_save_preferences_callback()
{
  $ajax_response = 0;

//  check_ajax_referer( 'ws_ls_save_preferences', 'security' ); //TODO: Add back in!

  // List of form fields / globals we want to store for the user
  $keys_to_save = array('WE_LS_DATA_UNITS', 'WE_LS_US_DATE');

  $user_preferences = array();

  foreach ($keys_to_save as $key) {

      $value = ws_ls_ajax_post_value($key);
      if(!is_null($value)) {
        $user_preferences[$key] = ws_ls_string_to_bool(ws_ls_ajax_post_value($key));
      }
  }

  if ('stones_pounds' == $user_preferences['WE_LS_DATA_UNITS'] || 'pounds_only' == $user_preferences['WE_LS_DATA_UNITS']) {
    $user_preferences['WE_LS_IMPERIAL_WEIGHTS'] = true;
  } else {
    $user_preferences['WE_LS_IMPERIAL_WEIGHTS'] = false;
  }

  if(true == ws_ls_set_user_preferences($user_preferences, ws_ls_ajax_post_value('user-id'))){
    $ajax_response = 1;
  }
  echo $ajax_response;
	wp_die();
}
add_action( 'wp_ajax_ws_ls_save_preferences', 'ws_ls_save_preferences_callback' );
add_action( 'wp_ajax_nopriv_ws_ls_save_preferences', 'ws_ls_save_preferences_callback' ); //TODO: REmove to non-logged in users

function ws_ls_ajax_post_value($key, $json_decode = false)
{
    if(isset($_POST[$key]) && $json_decode) {
        return json_decode($_POST[$key]);
    }
    elseif(isset($_POST[$key])) {
    	return $_POST[$key];
    }

    return NULL;
}
