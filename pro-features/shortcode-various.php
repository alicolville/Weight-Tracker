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

	// Don't attempt to calculate BMI if no weight entries!
	if(true === empty($kg)) {
		return __('Weight needed', WE_LS_SLUG);
	}

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

/**
 *
 * Shortcode to render the user's Height
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_height($user_defined_arguments) {

	$arguments = shortcode_atts(array(	'not-specified-text' => __('Not Specified', WE_LS_SLUG),
										'user-id' => get_current_user_id() ),
								$user_defined_arguments );

	return ws_ls_display_user_setting($arguments['user-id'], 'height', $arguments['not-specified-text']);
}

/**
 * Shortcode to render the number of WordPress users in last x days
 *
 * Args:    "days" (number of days to look back)
 *          "count-all-roles" - by default false and only count user's with a role of Subscriber. Set to true to count everyone.
 *
 * @param $user_defined_arguments
 * @return mixed
 */
function ws_ls_shortcode_new_users( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return '';
	}

    $arguments = shortcode_atts(['days' => 7, 'count-all-roles' => false], $user_defined_arguments );

    $arguments['days'] = ws_ls_force_numeric_argument($arguments['days'], 7);
    $arguments['count-all-roles'] = ws_ls_force_bool_argument($arguments['count-all-roles']);

    // Ensure no. days greater than or equal to 1
    $arguments['days'] = ($arguments['days'] < 1) ? 1 : $arguments['days'];

    // Build from date
    $from_date = strtotime ( "-{$arguments['days']} day" ) ;
    $from_date = date ( 'Y-m-d H:i:s' , $from_date );

    $wp_search_query = [
					        'date_query'    => [
										            [
										                'after'     => $from_date,
										                'inclusive' => true,
										            ],
						    ],
					        'fields' => 'ID'
    ];

    // Do we want to count all user roles within WordPress or subscribers (most likely) only
    if (false === $arguments['count-all-roles']) {
        $wp_search_query['role'] = 'subscriber';
    }

    $user_query = new WP_User_Query( $wp_search_query );

    return esc_html( $user_query->total_users );
}
add_shortcode( 'wlt-new-users', 'ws_ls_shortcode_new_users' );