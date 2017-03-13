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
									'display-errors' => true,
									'stroke-width' => 3,
									'stroke-colour' => '#FFEA82',
									'trail-width' => 1,
									'trail-colour' => '#eee',
									'text-colour' => '#000',
									'animation-duration' => 1400,	// Animation time in ms. Defaults to 1400
									'width' => '100%',				// % or pixels
									'height' => '100%',				// % or pixels
									'percentage-text' => __('towards your target of {t}.', WE_LS_SLUG)
								), $user_defined_arguments );

	$display_errors = ws_ls_force_bool_argument($arguments['display-errors']);

	// Are targets enabled? If not, no point carrying on!
	if(WE_LS_ALLOW_TARGET_WEIGHTS) {

		// Width / Height specified for circle?
		$arguments['width-height-specified'] = (isset($user_defined_arguments['width']) || isset($user_defined_arguments['height'])) ?
															true :
															false;

		// Validate / apply defaults
		$arguments['type'] = (in_array($arguments['type'], array('circle', 'line'))) ? $arguments['type'] : 'line';
		$arguments['stroke-width'] = ws_ls_force_numeric_argument($arguments['stroke-width'], 3);
		$arguments['trail-width'] = ws_ls_force_numeric_argument($arguments['trail-width'], 1);
		$arguments['animation-duration'] = ws_ls_force_numeric_argument($arguments['animation-duration'], 1400);

		// If no width or height specified by user, then set circle to a better default size.
		if('circle' == $arguments['type'] && false == $arguments['width-height-specified']) {
			$arguments['width'] = '150px';
			$arguments['height'] = '150px';
		}

		// Got a target weight?
		if($arguments['target-weight'] = ws_ls_get_target_weight_in_kg()) {

			//$arguments['target-weight-display'] = we_ls_format_weight_into_correct_string_format($arguments['target-weight']);
			$arguments['target-weight-display'] = ws_ls_convert_kg_into_relevant_weight_String($arguments['target-weight']);

			// Latest weight
			if($arguments['weight'] = ws_ls_get_recent_weight_in_kg()) {

				$arguments['start-weight'] = ws_ls_get_start_weight_in_kg();

				// -----------------------------------------------------
				// Aim to Gain weight?
				// -----------------------------------------------------
				if ($arguments['start-weight'] < $arguments['target-weight']) {
					// Have we met or exceeded the target?
					if ($arguments['weight'] >= $arguments['target-weight']) {
						$arguments['progress'] = 1.0;
					// Is recent weight less than target? If so, calulate %.
				} else if ($arguments['target-weight'] >= $arguments['weight']) {

						$arguments['weight-to-be-gained'] = abs($arguments['target-weight'] - $arguments['start-weight']);
						$arguments['weight-gained-so-far'] = $arguments['weight'] - $arguments['start-weight'];

						$arguments['progress'] = ($arguments['weight-gained-so-far'] > 0) ?
													($arguments['weight-gained-so-far'] / $arguments['weight-to-be-gained']) * 100 :
													0;
					} else {
						$arguments['progress'] = 0;	// Error
					}
				} else {
				// -----------------------------------------------------
				// Aim to Lose weight?
				// -----------------------------------------------------
					// Have we met or exceeded the target?
					if ($arguments['weight'] <= $arguments['target-weight']) {
						$arguments['progress'] = 1.0;
					// Is recent weight greater than target? If so, calulate %.
					} else if ($arguments['target-weight'] <= $arguments['weight']) {

						$arguments['weight-to-be-lost'] = abs($arguments['target-weight'] - $arguments['start-weight']);
						$arguments['weight-lost-so-far'] = $arguments['start-weight'] - $arguments['weight'];

						$arguments['progress'] = ($arguments['weight-lost-so-far'] > 0) ?
													($arguments['weight-lost-so-far'] / $arguments['weight-to-be-lost']) * 100 :
													0;
					} else {
						$arguments['progress'] = 0;	// Error
					}
				}

				// -----------------------------------------------------
				// Set Progress figure for chart library
				// -----------------------------------------------------
				if ($arguments['progress'] == 1) {
					$arguments['progress-chart'] = 1;
				} else if ($arguments['progress'] >= 100) {
					$arguments['progress-chart'] = 1;
				} else if ($arguments['progress'] > 0) {
					$arguments['progress-chart'] = round($arguments['progress'] / 100, 2);
				} else {
					$arguments['progress-chart'] = 0;
				}

				// Render bar!
				return ws_ls_shortcode_progress_bar_render($arguments);

			} else if ($display_errors) {
				return __('Please enter add a weight entry to see your progress.', WE_LS_SLUG);
			}

		} else if ($display_errors) {
			return __('Please enter a target weight to see your progress.', WE_LS_SLUG);
		}

	} else if($display_errors) {
		return __('This shortcode can not be used as Target weights have been disabled in the plugin\'s settings.', WE_LS_SLUG);
	}

}

function ws_ls_shortcode_progress_bar_render($arguments = array()) {

	if($arguments) {

		ws_ls_enqueue_files();

		// Enqueue Progress library
		wp_enqueue_script('ws-ls-progress-bar',plugins_url( '../js/progress-bar.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);

		// Create a unique ID
		$shortcode_id = 'ws_ls_progress_bar_' . rand(10,1000) . '_' . rand(10,1000);

		$arguments['percentage-text'] = str_replace('{t}', $arguments['target-weight-display'], $arguments['percentage-text']);

		return sprintf('<div id="%s" class="ws-ls-progress"
						data-stroke-width="%s" data-stroke-colour="%s"
						data-trail-width="%s" data-trail-colour="%s"
						data-precentage-text="%s" data-text-colour="%s"
						data-animation-duration="%s"
						data-width="%s" data-height="%s"
						data-type="%s" data-progress="%s"></div>',
					$shortcode_id,
					esc_html($arguments['stroke-width']),
					esc_html($arguments['stroke-colour']),
					esc_html($arguments['trail-width']),
					esc_html($arguments['trail-colour']),
					esc_html($arguments['percentage-text']),
					esc_html($arguments['text-colour']),
					esc_html($arguments['animation-duration']),
					esc_html($arguments['width']),
					esc_html($arguments['height']),
					esc_html($arguments['type']),
					esc_html($arguments['progress-chart'])
		);
	}


}
