<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_message($user_defined_arguments, $content = null) {

	// If not logged in then return no value
	if(!is_user_logged_in()) {
		return '';
	}

	$default_consecutive = 1;

	$arguments = shortcode_atts(array(
						            'type' => 'gained', 					// Type of message:
																			// 		'gained' - Gained weight since previous chronlogical entry
																			// 		'lost' - Lost weight since previous chronlogical entry
									'consecutive' => $default_consecutive	// How many consecutive weight entries to consider when considering to display the message.
								), $user_defined_arguments );

	// Validate arguments
	$arguments['type'] = (in_array($arguments['type'], array('gained', 'lost'))) ? $arguments['type'] : 'gained';
	$arguments['consecutive'] = ws_ls_force_numeric_argument($arguments['consecutive'], $default_consecutive);

	// Only allow consecutive between 1 and 30.
	if(($arguments['consecutive'] < 1 || $arguments['consecutive'] > 30)) {
		$arguments['consecutive'] = $default_consecutive;
	}

	$checking_for_gains = ('gained' == $arguments['type']) ? true : false;

	$weight_data = ws_ls_db_weights_get( [ 'limit' => $arguments['consecutive'] + 1, 'prep' => true, 'sort' => 'desc' ] );

	// Fetch the user's weight history
	if( false === empty( $weight_data ) ) {

		// If we have data, ensure we have enough data to do our consecutive check
		if(count($weight_data) > $arguments['consecutive']) {

			for ($i=0; $i < $arguments['consecutive']; $i++) {

				$current_value = $weight_data[$i]['kg'];
				$previous_value = $weight_data[$i + 1]['kg'];

				// If we are checking for gains, ensure this entry less than the one before.
				if($checking_for_gains && $current_value <= $previous_value) {
					return '';
				// If we are checking for consecutive losses, ensure this entry is greater than the one before.
				} elseif (!$checking_for_gains && $current_value >= $previous_value) {
					return '';
				}
			}

			// If we haven't exited the loop here, then display the content!
			if (!empty($content)) {
				return $content;
			}

		}
	}

	return '';
}
