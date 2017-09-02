<?php

defined('ABSPATH') or die("Jog on");

function ws_ls_shortcode_if($user_defined_arguments, $content = null) {

	// Check if we have content between opening and closing [wlt-if] tags, if we don't then nothing to render so why bother proceeding?
	if(true === empty($content)) {
		return '<p>' . __('To use this shortcode, you must specify content between opening and closing tags<br /><br />e.g. [wlt-if]something to show if IF is true[/wlt-if]', WE_LS_SLUG) . '</p>';
	}

	$arguments = shortcode_atts(array(
		'user-id' => get_current_user_id(),
		'operator' => 'exists',				// exists, not-exists
		'field' => 'weight'					// weight, target, bmr, height, gender, activity-level, dob, is-logged-in
	), $user_defined_arguments );

	// Validate arguments
	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
	$arguments['operator'] = (true === in_array($arguments['operator'], ['exists', 'not-exists'])) ? $arguments['operator'] : 'exists';
	$arguments['field'] = (true === in_array($arguments['field'], ['weight', 'target', 'bmr', 'height', 'gender', 'activity_level', 'dob', 'is-logged-in'])) ? $arguments['field'] : 'exists';

	// Remove Pro Plus fields if they don't have a license
	if( false === WS_LS_IS_PRO_PLUS && true === in_array($arguments['field'], ['bmr'])) {
		return '<p>' . __('Unfortunately the field you specified is for Pro Plus licenses only.', WE_LS_SLUG) . '</p>';
	}

	$else_content = '';

	// Is there an [else] within the content? If so, split the content into true condition and else.
	$else_location = stripos($content, '[else]');

	if(false !== $else_location) {

		$else_content = substr($content, $else_location + 6);

		// Strip out [else] content from true condition
		$content = substr($content, 0, $else_location);
	}

	$value = '';

	// Ok, now we have content let's fetch the field we're interested in.
	switch ($arguments['field']) {
        case 'is-logged-in':
            $value = is_user_logged_in();
            break;
		case 'weight':
			$value = ws_ls_get_recent_weight_in_kg($arguments['user-id']);
			break;
		case 'target':
			$value = ws_ls_get_target_weight_in_kg($arguments['user-id']);
			break;
		case 'bmr':
			$value = ws_ls_calculate_bmr($arguments['user-id']);
			$value = (false === is_numeric($value)) ? '' : $value;
			break;
		case 'height':
		case 'gender':
		case 'activity_level':
		case 'dob':
			$value = ws_ls_get_user_setting($arguments['field'], $arguments['user-id']);
			break;
	}

	$does_value_exist = (false === empty($value)) ? true : false;

	$display_true_condition = 	(
									(true === $does_value_exist && 'exists' === $arguments['operator']) ||		// True if field exists
									(false === $does_value_exist && 'not-exists' === $arguments['operator'])	// True if field does not exist
								) ? true : false;

	// If we should display true content, then do so. IF not, and it was specified, display [else]
	if($display_true_condition) {
		return do_shortcode($content);
	} else if (false === $display_true_condition && false === empty($else_content)) {
		return do_shortcode($else_content);
	}

	return '';
}