<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_stats_total_lost($user_defined_arguments)
{
    if(!WS_LS_IS_PRO || WE_LS_DISABLE_USER_STATS) {
       return false;
    }

	$difference = ws_ls_stats_get_summary_stats();
	$stats = ['kg' => $difference, 'display-unit' => ws_ls_get_config('WE_LS_DATA_UNITS')];

	switch (ws_ls_get_config('WE_LS_DATA_UNITS')) {
		case 'pounds_only':
			$stats['display-value'] = ws_ls_to_lb($difference) .  __('lbs', WE_LS_SLUG);
			break;
		case 'stones_pounds':
			$weight = ws_ls_to_stone_pounds($difference);
			$stats['display-value'] = $weight['stones'] . __('St', WE_LS_SLUG) . " " . $weight['pounds'] . __('lbs', WE_LS_SLUG);
			break;
		default:
			$stats['display-value'] = $difference  . __('Kg', WE_LS_SLUG);
	}

	return $stats['display-value'];
}
