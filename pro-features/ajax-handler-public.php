<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_save_preferences_callback()
{
  	$ajax_response = 0;

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$in_admin_area = ws_ls_post_value('we-ls-in-admin');
    $in_admin_area = (false === empty($in_admin_area)) ? true : false;

  	// List of form fields / globals we want to store for the user
  	$keys_to_save = array('WE_LS_DATA_UNITS', 'WE_LS_US_DATE');

  	$user_preferences = array();

  	foreach ($keys_to_save as $key) {

      	$value = ws_ls_post_value($key);
      	if(!is_null($value)) {
        	$user_preferences[$key] = ws_ls_string_to_bool(ws_ls_post_value($key));
      	}
  	}

  	if ('stones_pounds' == $user_preferences['WE_LS_DATA_UNITS'] || 'pounds_only' == $user_preferences['WE_LS_DATA_UNITS']) {
    	$user_preferences['WE_LS_IMPERIAL_WEIGHTS'] = true;
  	} else {
    	$user_preferences['WE_LS_IMPERIAL_WEIGHTS'] = false;
  	}

  	$fields = [];

    $fields['settings'] = $user_preferences;

	// Save Height?
    $fields['height'] = false;
	if(!is_null(ws_ls_post_value('we-ls-height'))) {
        $fields['height'] = (int) ws_ls_post_value('we-ls-height');
	}

    // Save Activity Level, DoB, Aim and Gender
    $fields['gender'] = (!is_null(ws_ls_post_value('ws-ls-gender'))) ? (int) ws_ls_post_value('ws-ls-gender') : 0;
    $fields['aim'] = (!is_null(ws_ls_post_value('ws-ls-aim'))) ? (int) ws_ls_post_value('ws-ls-aim') : 0;
    $fields['activity_level'] = (!is_null(ws_ls_post_value('ws-ls-activity-level'))) ? (float) ws_ls_post_value('ws-ls-activity-level') : 0;
    $fields['dob'] = (!is_null(ws_ls_post_value('ws-ls-dob'))) ? ws_ls_post_value('ws-ls-dob') : false;
    $fields['user_id'] = ws_ls_post_value('user-id');

    // Add additional fields to be saved.
    $fields = apply_filters( 'wlt-filter-user-settings-save-fields', $fields );

    do_action( 'ws-ls-hook-user-preference-save', (int) $fields['user_id'], $in_admin_area, $fields );

  	if( true == ws_ls_set_user_preferences( $in_admin_area, $fields ) ){
    	$ajax_response = 1;

    	do_action( 'ws-ls-hook-user-preference-saved' );
  	}
  	echo $ajax_response;
	wp_die();
}
add_action( 'wp_ajax_ws_ls_save_preferences', 'ws_ls_save_preferences_callback' );

