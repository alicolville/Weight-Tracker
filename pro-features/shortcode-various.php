<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_get_user_bmi($user_defined_arguments) {

	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$arguments = shortcode_atts(array(
						            'display' => 'index', // 'index' - Actual BMI value. 'label' - BMI label for given value. 'both' - Label and BMI value in brackets,
									'no-height-text' => __('Height needed', WE_LS_SLUG),
									'user-id' => get_current_user_id()
						           ), $user_defined_arguments );

	$kg = ws_ls_get_recent_weight_in_kg($arguments['user-id'], true);
	$cm = ws_ls_get_user_height($arguments['user-id']);

	// Do we have a height for user?
	if($cm) {
		$bmi = ws_ls_calculate_bmi($cm, $kg);

		switch ($arguments['display']) {
			case 'index':
				return $bmi;
				break;
			case 'label':
				return ws_ls_calculate_bmi_label($bmi);
				break;
			case 'both':
				return ws_ls_calculate_bmi_label($bmi) . ' (' . $bmi . ')';
				break;
			default:
				break;
		}
	} else {
		return esc_html($arguments['no-height-text']);
	}


	return '';
}

/**
 *
 * Shortcode to render the user's activity level
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_activity_level($user_defined_arguments) {

	$arguments = shortcode_atts(array(	'not-specified-text' => __('Not Specified', WE_LS_SLUG),
										'user-id' => get_current_user_id(),
									 	'shorten' => false),
								$user_defined_arguments );

	$arguments['shorten'] = ws_ls_force_bool_argument($arguments['shorten']);

	return ws_ls_display_user_setting($arguments['user-id'], 'activity_level', $arguments['not-specified-text'], $arguments['shorten']);
}

/**
 *
 * Shortcode to render the user's gender
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_gender($user_defined_arguments) {

	$arguments = shortcode_atts(array(	'not-specified-text' => __('Not Specified', WE_LS_SLUG),
										'user-id' => get_current_user_id() ),
								$user_defined_arguments );

	return ws_ls_display_user_setting($arguments['user-id'], 'gender', $arguments['not-specified-text']);
}

/**
 *
 * Shortcode to render the user's Date of Birth
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_dob($user_defined_arguments) {

	$arguments = shortcode_atts(array(	'not-specified-text' => __('Not Specified', WE_LS_SLUG),
										'user-id' => get_current_user_id() ),
								$user_defined_arguments );

	return ws_ls_get_dob_for_display($arguments['user-id'], $arguments['not-specified-text']);
}
