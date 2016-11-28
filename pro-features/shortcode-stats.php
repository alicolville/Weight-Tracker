<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_stats_total_lost($user_defined_arguments)
{
    if(!WS_LS_IS_PRO || WE_LS_DISABLE_USER_STATS) {
       return  __('Stats disabled', WE_LS_SLUG);
    }

	$arguments = shortcode_atts(
	array(
		'display' => 'number/text',
		'invert' => false,
		'force-to-kg' => 'false'
	 ), $user_defined_arguments );

	$summary_stats = ws_ls_stats_get_summary_stats();

	$difference = $summary_stats['difference'];

	$stats = ['kg' => $difference, 'display-unit' => ws_ls_get_config('WE_LS_DATA_UNITS'), 'display-value' => ''];

	// If display number text, remove sign and use text to represent gain / loss
	if('number/text' == $arguments['display']) {

		$stats['display-value'] = ($difference <= 0) ? __('Lost', WE_LS_SLUG) : __('Gained', WE_LS_SLUG);
		$stats['display-value'] .= ': ';
		$difference = abs($difference);

	} else {
		// Invert positive / negative numbers
		$difference = (false === ws_ls_force_bool_argument($arguments['invert'])) ? $difference : -$difference ;
	}

	// Ignore global and user settings and force display to Kg?
	if(true == ws_ls_force_bool_argument($arguments['force-to-kg'])) {
		$stats['display-unit'] = 'kg';
	}

	switch ($stats['display-unit']) {
		case 'pounds_only':
			$stats['display-value'] .= ws_ls_to_lb($difference) .  __('lbs', WE_LS_SLUG);
			break;
		case 'stones_pounds':
			$weight = ws_ls_to_stone_pounds($difference);
			$stats['display-value'] .= $weight['stones'] . __('St', WE_LS_SLUG) . " " . $weight['pounds'] . __('lbs', WE_LS_SLUG);
			break;
		default:
			$stats['display-value'] .= $difference  . __('Kg', WE_LS_SLUG);
	}

	// Allow theme developer to override stats message
	$stats = apply_filters('ws-ls-stats-shortcode', $stats);

	return $stats['display-value'];
}
