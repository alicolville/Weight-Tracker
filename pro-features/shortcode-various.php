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
