<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_reminder($user_defined_arguments, $content = null) {

	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$message = '';

	$arguments = shortcode_atts(array(
						            'type' => 'weight', 		// Type of message:
																// 		'weight' - check they have entered a weight for today.
																// 		'target' - check they have entered a target weight
									'message' => '',			// Custom message
									'additional_css' => '',		// Additional class for containing element
									'link' => ''				// Wrap the message in a link
								), $user_defined_arguments );

	// Do they have a target weight?
	if ('target' == $arguments['type'] && WE_LS_ALLOW_TARGET_WEIGHTS && false == ws_ls_get_user_target(get_current_user_id())) {
		$message = __('Please remember to enter your target weight.', WE_LS_SLUG);
	// Do they have a weight entry for today?
	} else if('weight' == $arguments['type'] && !ws_does_weight_exist_for_this_date(get_current_user_id(), date('Y-m-d'))) {
		$message = (WE_LS_MEASUREMENTS_ENABLED) ? __('Please remember to enter your weight and measurements for today.', WE_LS_SLUG) :  __('Please remember to enter your weight for today', WE_LS_SLUG) ;
	}

	// Do we have a message to display?
	if(!empty($content)) {
		return $content;
	} else if(!empty($message)) {

		// Has a custom message been specified?
		$message = (!empty($user_defined_arguments['message'])) ? $user_defined_arguments['message'] : $message;

		// Escape
		$message = esc_html($message);

		// Encase in a link?
		$message = (!empty($arguments['link'])) ? sprintf('<a href="%s">%s</a>', esc_url($arguments['link']), $message) : $message;

		$message = sprintf(
						'<div class="ws-ls-reminder ws-ls-alert-box ws-ls-info%s">%s</span></div>',
						!empty($arguments['additional_css']) ? ' ' . esc_html($arguments['additional_css']) : '',
						$message
					);
	}

	return $message;
}
