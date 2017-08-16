<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_admin_measurment_unit() {
	return ('inches' == WE_LS_MEASUREMENTS_UNIT) ? __('Inches', WE_LS_SLUG) : __('Cm', WE_LS_SLUG);
}

function ws_ls_get_bmi_for_table($cm, $kg, $no_height_text = false) {

	if ($cm) {
		$bmi = ws_ls_calculate_bmi($cm, $kg);

		if($bmi) {
			return ws_ls_calculate_bmi_label($bmi);
		}
	} else {

        $no_height_text = (empty($no_height_text)) ? __('Add Height for BMI', WE_LS_SLUG) : $no_height_text;

		return esc_html($no_height_text);
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
		'<div class="ws-tooltip">%s<span class="tooltiptext">%s</span></div>',
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
 * Given a user ID, return a link to the user's settings page
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_user_settings($id) {
	return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-wlt-data-home&mode=user-settings&user-id=' . $id )) : '#';
}

/**
 * Given a user ID, return a link to edit a user's target
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_edit_target($id) {
	return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-wlt-data-home&mode=target&user-id=' . $id )) : '#';
}

/**
 * Get link to settings page
 * @return string
 */
function ws_ls_get_link_to_settings() {
    return admin_url( 'admin.php?page=ws-ls-weight-loss-tracker-main-menu' );
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

/**
 *
 * Returns the link for CSV / JSON export
 *
 * @param string $type - json / csv
 * @param bool $user_id - WP user ID
 * @return mixed
 */
function ws_ls_get_link_to_export($type = 'csv', $user_id = false) {

    $type = ('json' == $type) ? 'application/json' : 'text/csv';

    $base_url = admin_url( 'admin-post.php?action=export_data&file-type=' . $type);

    if(is_numeric($user_id)) {
        $base_url .= '&user-id=' . $user_id;
    }

    return esc_url($base_url);
}

/**
 * Simple function to render a user's email address
 *
 * @param $user_id
 * @param bool $include_brackets
 * @return string
 */
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

/**
 * Return an array of supported genders
 *
 * @return array
 */
function ws_ls_genders() {

    return [
        0 => '',
        1 => __('Female', WE_LS_SLUG),
        2 => __('Male', WE_LS_SLUG)
    ];
}

/**
 * Return an array of activity levels
 *
 * @return array
 */
function ws_ls_activity_levels() {

    return [
        '0' => '',
        '1.2' => __('Little / No Exercise', WE_LS_SLUG),
        '1.375' => __('Light Exercise', WE_LS_SLUG),
        '1.55' => __('Moderate Exercise (3-5 days a week)', WE_LS_SLUG),
        '1.725' => __('Very Active (6-7 days a week)', WE_LS_SLUG),
        '1.9' => __('Extra Active (very active & physical job)', WE_LS_SLUG)
    ];
}

/**
 * Return an array of heights
 *
 * @return array
 */
function ws_ls_heights() {
	return array(
		    0 => '',
		    142 => '4\'8" - 142cm',
		    145 => '4\'8" - 145cm',
		    147 => '4\'9" - 147cm',
		    150 => '4\'11" - 150cm',
		  	152 => '5\'0" - 152cm',
		  	155 => '5\'1" - 155cm',
		    157 => '5\'2" - 157cm',
		  	160 => '5\'3" - 160cm',
		  	163 => '5\'4" - 163cm',
		    165 => '5\'5" - 165cm',
		  	168 => '5\'6" - 168cm',
		  	170 => '5\'7" - 170cm',
		    173 => '5\'8" - 173cm',
		    175 => '5\'9" - 175cm',
		    178 => '5\'10" - 178cm',
		    180 => '5\'11" - 180cm',
		    183 => '6\'0" - 183cm',
		    185 => '6\'1" - 185cm',
		    188 => '6\'2" - 188cm',
		    191 => '6\'3" - 191cm',
		    193 => '6\'4" - 193cm',
		    195 => '6\'5" - 195cm',
		    198 => '6\'6" - 198cm',
		    201 => '6\'7" - 201cm'
		);
}

/**
 * Simple function display a given user preference field for the specified user
 *
 * @param $user_id - User ID
 * @param $field - name of DB field
 * @return bool|string
 */
function ws_ls_display_user_setting($user_id, $field = 'dob', $not_specified_text = false, $shorten = false) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	$not_specified_text = (false === $not_specified_text) ? __('Not Specified', WE_LS_SLUG) : esc_html($not_specified_text);

	$user_data = ws_ls_get_user_setting($field, $user_id);

	switch ($field) {
		case 'activity_level':
			$field_data = ws_ls_activity_levels();
			break;
		case 'height':
			$field_data = ws_ls_heights();
			break;
		default:
			$field_data = ws_ls_genders();
			break;
	}

	if (false === empty($user_data) && isset($field_data[$user_data])) {

		// If a height setting and we want to shorten, look for a bracket and remove everything from there onwards
		if($shorten && 'activity_level' == $field) {

			$bracket_location = strpos($field_data[$user_data], '(');

			if(false !== $bracket_location) {
				$field_data[$user_data] = substr($field_data[$user_data], 0, $bracket_location);
			}

		}

		return esc_html($field_data[$user_data]);
	}

	return $not_specified_text;
}

/**
 * Determine if user is a female
 *
 * @param $user_id
 * @return bool
 */
function ws_ls_is_female($user_id) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    $gender = ws_ls_get_user_setting('gender', $user_id);

    return (false === empty($gender) && 1 == intval($gender)) ? true : false;
}

/**
 * Fetch user's ISO DOB
 *
 * @param $user_id
 * @return bool|string
 */
function ws_ls_get_dob($user_id) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    return ws_ls_get_user_setting('dob', $user_id);
}

/**
 * Simple function to convert a user's ISO DOB into pretty format
 *
 * @param $user_id
 * @return bool|string
 */
function ws_ls_get_dob_for_display($user_id = false, $not_specified_text = '', $include_age = false) {

	$dob = ws_ls_get_dob($user_id);

	$not_specified_text = (false === $not_specified_text) ? __('Not Specified', WE_LS_SLUG) : esc_html($not_specified_text);

    if (false === empty($dob) && '0000-00-00 00:00:00' !== $dob) {
		$html = ws_ls_iso_date_into_correct_format($dob);

		// Include age?
		if(true === $include_age) {
			$html .= ' ('. ws_ls_get_age_from_dob($user_id) . ')';
		}

		return $html;
	}

	return $not_specified_text;
}

/**
 * Used to calculate agre from the person's DOB
 *
 * @param bool $user_id
 * @return bool|int
 */
function ws_ls_get_age_from_dob($user_id = false){

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    $dob = ws_ls_get_dob($user_id);

    if(false === empty($dob) && '0000-00-00 00:00:00' !== $dob) {

        $dob = new DateTime($dob);
        $today   = new DateTime('today');
        $age = $dob->diff($today)->y;

        return $age;
    }

    return NULL;
}

/**
 * Helper function to disable admin page if the user doesn't have the correct user role.
 */
function ws_ls_user_data_permission_check() {
    if ( !current_user_can( WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
    }
}
