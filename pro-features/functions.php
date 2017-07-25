<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_admin_measurment_unit() {
	return ('inches' == WE_LS_MEASUREMENTS_UNIT) ? __('Inches', WE_LS_SLUG) : __('Cm', WE_LS_SLUG);
}

function ws_ls_get_bmi_for_table($cm, $kg) {

	if ($cm) {
		$bmi = ws_ls_calculate_bmi($cm, $kg);

		if($bmi) {
			return ws_ls_calculate_bmi_label($bmi);
		}
	} else {
		return __('Add Height for BMI', WE_LS_SLUG);
	}
	return '';
}

function ws_ls_calculate_bmi($cm, $kg) {

	$bmi = false;

	if(is_numeric($cm) && is_numeric($kg)) {

		$bmi = $kg / ($cm * $cm);
		$bmi = $bmi * 10000;
		$bmi = round($bmi, 1);
	}

	return $bmi;
}

// $bmi = ws_ls_calculate_bmi(150, 66);
// $label = ws_ls_calculate_bmi_label($bmi);
function ws_ls_calculate_bmi_label($bmi) {

	if(is_numeric($bmi)) {

		if($bmi < 18.5) {
			return __('Underweight', WE_LS_SLUG);
		} else if ($bmi >= 18.5 && $bmi <= 24.9) {
			return __('Healthy', WE_LS_SLUG);
		}
		else if ($bmi >= 25 && $bmi <= 29.9) {
			return __('Overweight', WE_LS_SLUG);
		} else if ($bmi >= 30) {
			return __('Heavily Overweight', WE_LS_SLUG);
		}
		// } else if ($bmi >= 30 && $bmi <= 39.9) {
		// 	return __('Obese', WE_LS_SLUG);
		// } else if ($bmi >= 39.9) {
		// 	return __('Severely / Morbidly obese', WE_LS_SLUG);
		// }

	}

	return 'Err';
}

function ws_ls_tooltip($text, $tooltip) {

	if(empty($text) || empty($tooltip)) {
		return;
	}

	return sprintf(
		'<div class="ws-tooltip">%s
		  <span class="tooltiptext">%s</span>
		</div>',
		esc_html($text),
		esc_html($tooltip)
	);
}
/**
 * Return base URL for user data
 * @return string
 */
function ws_ls_get_link_to_user_data() {
	return admin_url( 'admin.php?page=ws-ls-wlt-data-home');
}

/**
 * Given a user ID, return a link to the user's profile
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_user_profile($id) {
	return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-wlt-data-home&mode=user&user-id=' . $id )) : '#';
}

/**
 * Given a user and entry ID, return a link to the edit entrant page
 * @param  int $id User ID
 * @param  int $entry_id Entry ID
 * @return string
 */
function ws_ls_get_link_to_edit_entry($user_id, $entry_id = false, $redirect = true) {

	$base_url = admin_url( 'admin.php?page=ws-ls-wlt-data-home&mode=entry&user-id=' . $user_id );

	if(is_numeric($entry_id)) {
		$base_url .= '&entry-id=' . $entry_id;
	}

	$base_url .= '&redirect=' . ws_ls_get_url(true);

	return esc_url($base_url);
}

function ws_ls_get_email_link($user_id, $include_brackets = false) {

	if(true === is_numeric($user_id)) {

		$user_data = get_userdata( $user_id );

		if($user_data && false === empty($user_data->user_email)) {

			$html = ($include_brackets) ? '(' : '';
			$html .= sprintf('<a href="mailto:%s">%s</a>', esc_attr($user_data->user_email), esc_html($user_data->user_email));
			$html .= ($include_brackets) ? ')' : '';
			return $html;
		}
	}

	return '';
}
