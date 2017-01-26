<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_progress_bar($user_defined_arguments) {

	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$arguments = shortcode_atts(array(
						            'type' => 'line', 			// Type of progress bar:
																// 		'circle'
																// 		'line'
									'display-errors' => true
								), $user_defined_arguments );

	$display_errors = ws_ls_force_bool_argument($arguments['display-errors']);

	// Are targets enabled? If not, no point carrying on!
	if(WE_LS_ALLOW_TARGET_WEIGHTS) {

		// Validate / apply defaults
		$arguments['type'] = (in_array($arguments['type'], ['circle', 'line'])) ? $arguments['type'] : 'line';

		// Got a target weight?
		if($arguments['target-weight'] = ws_ls_get_target_weight_in_kg()) {

			// Latest weight
			if($arguments['weight'] = ws_ls_get_recent_weight_in_kg()) {

				// Have we met or exceeded the target?
				if ($arguments['weight'] <= $arguments['target-weight']) {
					$arguments['progress'] = 1.0;
				// Is recent weight greater than target? If so, calulate %.
				} else if ($arguments['target-weight'] <= $arguments['weight']) {

					$arguments['start-weight'] = ws_ls_get_start_weight_in_kg();
					$arguments['weight-to-be-lost'] = abs($arguments['target-weight'] - $arguments['start-weight']);
					$arguments['weight-lost-so-far'] = $arguments['start-weight'] - $arguments['weight'];

					$arguments['progress'] = ($arguments['weight-lost-so-far'] > 0) ?
												($arguments['weight-lost-so-far'] / $arguments['weight-to-be-lost']) * 100 :
												0;
				} else {
					$arguments['progress'] = 0;	// Error
				}


				// TODO : greater or equal than 1 then don't round either.
				

				$arguments['progress-chart'] = ($arguments['progress'] > 0) ? round($arguments['progress'] / 100, 2) : 0;

			} else if ($display_errors) {
				return __('Please enter add a weight entry to see your progress.', WE_LS_SLUG);
			}

		} else if ($display_errors) {
			return __('Please enter a target weight to see your progress.', WE_LS_SLUG);
		}

	} else if($display_errors) {
		return __('This shortcode can not be used as Target weights have been disabled in the plugin\'s settings.', WE_LS_SLUG);
	}

	var_dump($arguments);

}
