<?php

defined('ABSPATH') or die("Jog on!");



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

/**
 * Given height and weight, calculate a user's BMI
 * @param $cm
 * @param $kg
 * @return bool|float
 */
function ws_ls_calculate_bmi( $cm, $kg ) {

    if ( false === is_numeric( $cm ) || false === is_numeric( $kg ) ) {
        return false;
    }

    $bmi = $kg / ($cm * $cm);
    $bmi = $bmi * 10000;

	return round( $bmi, 1 );
}

// $bmi = ws_ls_calculate_bmi(150, 66);
// $label = ws_ls_calculate_bmi_label($bmi);
function ws_ls_calculate_bmi_label($bmi) {

	if( is_numeric($bmi) ) {

		if( $bmi < 18.5 ) {
			return __('Underweight', WE_LS_SLUG);
		} else if ( $bmi >= 18.5 && $bmi <= 24.9 ) {
			return __('Healthy', WE_LS_SLUG);
		}
		else if ( $bmi >= 25 && $bmi <= 29.9 ) {
			return __('Overweight', WE_LS_SLUG);
		} else if ( $bmi >= 30 ) {
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


/**
 * Return an array of all possible BMI labels
 *
 * @return array
 */
function ws_ls_bmi_all_labels() {
	return [
		0 => __('Underweight', WE_LS_SLUG),
		1 => __('Healthy', WE_LS_SLUG),
		2 => __('Overweight', WE_LS_SLUG),
		3 => __('Heavily Overweight', WE_LS_SLUG)
	];
}

/**
 * Render out tool tip
 * @param $text
 * @param $tooltip
 * @return string
 */
function ws_ls_tooltip( $text, $tooltip ) {

	if( true === empty( $text ) || true === empty( $tooltip ) ) {
		return '';
	}

	return sprintf('<div class="ws-ls-tooltip">%1$s<span>%s</span></div>', esc_html( $text ), esc_html( $tooltip ) );
}
/**
 * Return base URL for user data
 * @return string
 */
function ws_ls_get_link_to_user_data() {
	return admin_url( 'admin.php?page=ws-ls-data-home');
}

/**
 * Given a user ID, return a link to the user's profile
 * @param int $user_id User ID
 * @param null $display_text
 * @return string
 */
function ws_ls_get_link_to_user_profile( $user_id, $display_text = NULL ) {

	$profile_url = admin_url( 'admin.php?page=ws-ls-data-home&mode=user&user-id=' . (int) $user_id );

	$profile_url = esc_url( $profile_url );

	return ( NULL !== $display_text ) ?
			ws_ls_render_link( $profile_url, $display_text ) :
			$profile_url;
}

/**
 * @param $link
 * @param $label
 *
 * @return string
 */
function ws_ls_render_link( $link, $label ) {
	return sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $label ) );
}

/**
 * Given a user ID, return a link to delete a user's cache
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_delete_user_cache($id) {
    return ws_ls_get_link_to_user_profile($id) . '&amp;deletecache=y';
}

/**
 * Given a user ID, return a link to the user's settings page
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_user_settings($id) {
	return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-data-home&mode=user-settings&user-id=' . $id )) : '#';
}

/**
 * Given a user ID, return a link to edit a user's target
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_edit_target($id) {
	return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-data-home&mode=target&user-id=' . $id )) : '#';
}

/**
 * Given a user ID, return a link to view a user's photos
 * @param  int $id User ID
 * @return string
 */
function ws_ls_get_link_to_photos($id) {
    return is_numeric($id) ? esc_url(admin_url( 'admin.php?page=ws-ls-data-home&mode=photos&user-id=' . $id )) : '#';
}

/**
 * Get link to settings page
 * @return string
 */
function ws_ls_get_link_to_settings() {
    return admin_url( 'admin.php?page=ws-ls-settings' );
}


/**
 * Given a user and entry ID, return a link to the edit entrant page
 * @param $user_id
 * @param bool $entry_id Entry ID
 * @return string
 */
function ws_ls_get_link_to_edit_entry( $user_id, $entry_id = false ) {

	$base_url = admin_url( 'admin.php?page=ws-ls-data-home&mode=entry&user-id=' . $user_id );

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
function ws_ls_get_link_to_export( $type = 'csv', $user_id = false ) {

    $type = ( 'json' === $type ) ? 'application/json' : 'text/csv';

    $base_url = admin_url( 'admin-post.php?action=export_data&file-type=' . $type);

    if( true === is_numeric( $user_id ) ) {
        $base_url .= '&user-id=' . $user_id;
    }

    return esc_url( $base_url );
}

/**
 * Simple function to render a user's email address
 *
 * @param $user_id
 * @param bool $include_brackets
 * @return string
 */
function ws_ls_get_email_link( $user_id, $include_brackets = false ) {

    $user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

    $user_data = get_userdata( $user_id );

    if ( true === empty($user_data->user_email) ) {
        return '';
    }

    return sprintf('  %1$s<a href="mailto:%2$s">%2$s</a>%3$s',
        ( $include_brackets ) ? '( ' : '',
        esc_attr( $user_data->user_email ),
        ( $include_brackets ) ? ' )' : ''
    );
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
 * Return gender for given id
 * @param $id
 * @return mixed|string
 */
function ws_ls_genders_get( $id ) {

	$genders = ws_ls_genders();

	return ( false === empty( $genders[ $id ] ) ) ? $genders[ $id ] : '';
}

/**
 * Return an array of activity levels
 *
 * @return array
 */
function ws_ls_activity_levels() {

    $activity_levels = [
        '0' => '',
        '1.2' => __('Little / No Exercise', WE_LS_SLUG),
        '1.375' => __('Light Exercise', WE_LS_SLUG),
        '1.55' => __('Moderate Exercise (3-5 days a week)', WE_LS_SLUG),
        '1.725' => __('Very Active (6-7 days a week)', WE_LS_SLUG),
        '1.9' => __('Extra Active (very active & physical job)', WE_LS_SLUG)
    ];

	$activity_levels = apply_filters( 'wlt-filter-activity-levels', $activity_levels );

    return $activity_levels;
}

/**
 * Return an array of aims
 *
 * @return array
 */
function ws_ls_aims() {

    $aims = [
        0 => '',
        1 => __('Maintain current weight', WE_LS_SLUG),
        2 => __('Lose weight', WE_LS_SLUG),
        3 => __('Gain weight', WE_LS_SLUG)
    ];

    $aims = apply_filters( 'wlt-filter-aims', $aims );

    return $aims;
}



/**
 * Return an array of heights
 *
 * @return array
 */
function ws_ls_heights() {

    $all_heights = ws_ls_heights_metric_to_imperial();

    array_walk( $all_heights, 'ws_ls_heights_formatter' );

    return $all_heights;
}

/**
 * Return metric to imperial conversions for height.
 *
 * @param null $cm
 * @return array|mixed
 */
function ws_ls_heights_metric_to_imperial( $cm = NULL ) {

    $cm_to_metric = [
                        122 => [ 'feet' => 4, 'inches' => 0 ],
                        123 => [ 'feet' => 4, 'inches' => 0.4 ],
                        124 => [ 'feet' => 4, 'inches' => 0.8 ],
                        125 => [ 'feet' => 4, 'inches' => 1.2 ],
                        126 => [ 'feet' => 4, 'inches' => 1.6 ],
                        127 => [ 'feet' => 4, 'inches' => 2 ],
                        128 => [ 'feet' => 4, 'inches' => 2.4 ],
                        129 => [ 'feet' => 4, 'inches' => 2.8 ],
                        130 => [ 'feet' => 4, 'inches' => 3.2 ],
                        131 => [ 'feet' => 4, 'inches' => 3.6 ],
                        132 => [ 'feet' => 4, 'inches' => 4 ],
                        133 => [ 'feet' => 4, 'inches' => 4.4 ],
                        134 => [ 'feet' => 4, 'inches' => 4.8 ],
                        135 => [ 'feet' => 4, 'inches' => 5.1 ],
                        136 => [ 'feet' => 4, 'inches' => 5.5 ],
                        137 => [ 'feet' => 4, 'inches' => 5.9 ],
                        138 => [ 'feet' => 4, 'inches' => 6.3 ],
                        139 => [ 'feet' => 4, 'inches' => 6.7 ],
                        140 => [ 'feet' => 4, 'inches' => 7.1 ],
                        141 => [ 'feet' => 4, 'inches' => 7.5 ],
                        142 => [ 'feet' => 4, 'inches' => 7.9 ],
                        143 => [ 'feet' => 4, 'inches' => 8.3 ],
                        144 => [ 'feet' => 4, 'inches' => 8.7 ],
                        145 => [ 'feet' => 4, 'inches' => 9.1 ],
                        146 => [ 'feet' => 4, 'inches' => 9.5 ],
                        147 => [ 'feet' => 4, 'inches' => 9.9 ],
                        148 => [ 'feet' => 4, 'inches' => 10.3 ],
                        149 => [ 'feet' => 4, 'inches' => 10.7 ],
                        150 => [ 'feet' => 4, 'inches' => 11.1 ],
                        151 => [ 'feet' => 4, 'inches' => 11.4 ],
                        152 => [ 'feet' => 4, 'inches' => 11.8 ],
                        153 => [ 'feet' => 5, 'inches' => 0.2 ],
                        154 => [ 'feet' => 5, 'inches' => 0.6 ],
                        155 => [ 'feet' => 5, 'inches' => 1 ],
                        156 => [ 'feet' => 5, 'inches' => 1.4 ],
                        157 => [ 'feet' => 5, 'inches' => 1.8 ],
                        158 => [ 'feet' => 5, 'inches' => 2.2 ],
                        159 => [ 'feet' => 5, 'inches' => 2.6 ],
                        160 => [ 'feet' => 5, 'inches' => 3 ],
                        161 => [ 'feet' => 5, 'inches' => 3.4 ],
                        162 => [ 'feet' => 5, 'inches' => 3.8 ],
                        163 => [ 'feet' => 5, 'inches' => 4.2 ],
                        164 => [ 'feet' => 5, 'inches' => 4.6 ],
                        165 => [ 'feet' => 5, 'inches' => 5 ],
                        166 => [ 'feet' => 5, 'inches' => 5.4 ],
                        167 => [ 'feet' => 5, 'inches' => 5.7 ],
                        168 => [ 'feet' => 5, 'inches' => 6.1 ],
                        169 => [ 'feet' => 5, 'inches' => 6.5 ],
                        170 => [ 'feet' => 5, 'inches' => 6.9 ],
                        171 => [ 'feet' => 5, 'inches' => 7.3 ],
                        172 => [ 'feet' => 5, 'inches' => 7.7 ],
                        173 => [ 'feet' => 5, 'inches' => 8.1 ],
                        174 => [ 'feet' => 5, 'inches' => 8.5 ],
                        175 => [ 'feet' => 5, 'inches' => 8.9 ],
                        176 => [ 'feet' => 5, 'inches' => 9.3 ],
                        177 => [ 'feet' => 5, 'inches' => 9.7 ],
                        178 => [ 'feet' => 5, 'inches' => 10.1 ],
                        179 => [ 'feet' => 5, 'inches' => 10.5 ],
                        180 => [ 'feet' => 5, 'inches' => 10.9 ],
                        181 => [ 'feet' => 5, 'inches' => 11.3 ],
                        182 => [ 'feet' => 5, 'inches' => 11.7 ],
                        183 => [ 'feet' => 6, 'inches' => 0 ],
                        184 => [ 'feet' => 6, 'inches' => 0.4 ],
                        185 => [ 'feet' => 6, 'inches' => 0.8 ],
                        186 => [ 'feet' => 6, 'inches' => 1.2 ],
                        187 => [ 'feet' => 6, 'inches' => 1.6 ],
                        188 => [ 'feet' => 6, 'inches' => 2 ],
                        189 => [ 'feet' => 6, 'inches' => 2.4 ],
                        190 => [ 'feet' => 6, 'inches' => 2.8 ],
                        191 => [ 'feet' => 6, 'inches' => 3.2 ],
                        192 => [ 'feet' => 6, 'inches' => 3.6 ],
                        193 => [ 'feet' => 6, 'inches' => 4 ],
                        194 => [ 'feet' => 6, 'inches' => 4.4 ],
                        195 => [ 'feet' => 6, 'inches' => 4.8 ],
                        196 => [ 'feet' => 6, 'inches' => 5.2 ],
                        197 => [ 'feet' => 6, 'inches' => 5.6 ],
                        198 => [ 'feet' => 6, 'inches' => 6 ],
                        199 => [ 'feet' => 6, 'inches' => 6.3 ],
                        200 => [ 'feet' => 6, 'inches' => 6.7 ],
                        201 => [ 'feet' => 6, 'inches' => 7.1 ]
    ];

    return ( false === empty( $cm_to_metric[ $cm ] ) ) ? $cm_to_metric[ $cm ] : $cm_to_metric;
}

/**
 * Used by array_walk to format height options (for <select>)
 *
 * @param $height
 * @param $key
 */
function ws_ls_heights_formatter( &$height, $key ) {
    $height = sprintf( '%3$d%4$s - %1$d\' %2$s"', $height['feet'], $height['inches'],  $key, __('cm', WE_LS_SLUG) );
}

/**
 * Simple function display a given user preference field for the specified user
 *
 * @param $user_id - User ID
 * @param string $field - name of DB field
 * @param bool $not_specified_text
 * @param bool $shorten
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
        case 'aim':
            $field_data = ws_ls_aims();
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
function ws_ls_is_female( $user_id ) {

    $user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

    $gender = ws_ls_get_user_setting( 'gender', $user_id );

    return ( false === empty( $gender ) && 1 == (int) $gender ) ? true : false;
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
 * @param bool $user_id
 * @param string $not_specified_text
 * @param bool $include_age
 * @return bool|string
 * @throws Exception
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
 * @throws Exception
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

/**
 * Helper function to determine if the user exists in WP
 *
 * @param $user_id
 * @return bool
 */
function ws_ls_user_exist($user_id) {

    if(true === empty($user_id) || false === is_numeric($user_id)) {
        return false;
    }

    return (false === get_userdata( $user_id )) ? false : true;
}

/**
 * Helper function to check if user ID exists, if not throws wp_die()
 *
 * @param $user_id
 * @return bool
 */
function ws_ls_user_exist_check($user_id ) {

    if ( false === ws_ls_user_exist( $user_id ) ) {
        wp_die( __( 'Error: The user does not appear to exist' , WE_LS_SLUG ) );
    }

    return true;
}

/**
 * Used by the Calories and MacroN shortcodes to convert user's aim preference into a string for the progress attribute.
 *
 * @return string
 *
 * Note: Used by Meal Tracker
 */
function ws_ls_get_progress_attribute_from_aim() {

    $aim_int = (int) ws_ls_get_user_setting( 'aim' );

    switch ( $aim_int ) {
	    case 1:
		    $aim_string = 'maintain';
		    break;
	    case 3:
	    	$aim_string = 'gain';
	    	break;
	    default:
		    $aim_string = 'lose';
    }

    $aim_string = apply_filters('wlt-filter-aim-progress-attribute', $aim_string, $aim_int );

    return $aim_string;
}

/**
 * Helper function to ensure all fields have expected keys
 *
 * @param $data
 * @param $expected_fields
 * @return bool
 */
function ws_ls_array_check_fields($data, $expected_fields ) {

    foreach ( $expected_fields as $field ) {
        if ( false === isset( $data[ $field ] ) ) {
            return false;
        }
    }

    return true;
}
