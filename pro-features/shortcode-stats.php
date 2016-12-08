<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_stats_league_total($user_defined_arguments)
{
    if(!WS_LS_IS_PRO || WE_LS_DISABLE_USER_STATS) {
       return  __('Stats disabled', WE_LS_SLUG);
    }

	$arguments = shortcode_atts(
	array(
		'display' => 'number',
		'force-to-kg' => 'false',
		'invert' => false,
		'order' => 'asc',				// asc: Lost the most first. desc: lost the least first
		'number_to_show' => 10,			// Number of users to display in table
		'losers_only' => false,			// Only show people that have lost weight
		'ignore_cache' => false			// If we have a cached data set, then use that.
	 ), $user_defined_arguments );

	$data = ws_ls_stats_league_table_fetch($arguments['ignore_cache'], $arguments['number_to_show'],
	 										$arguments['losers_only'], $arguments['order']);

	if($data) {

		$html = '<table class="ws-ls-stats-table">
					<thead>
						<tr>
							<th class="ws-col-rank-th"></th>
							<th class="ws-col-name-th">' . __('Name', WE_LS_SLUG) . '</th>
							<th class="ws-weight-diff-th">' . __('Weight Difference', WE_LS_SLUG) . '</th>
						</tr>
					</thead>
					<tbody>
		';
		$rank = 1;
		foreach ($data as $row) {

			// Allow others to manipulate this data
			$row = apply_filters('weight-loss-stats-table-row', $row);

			// Display name from WP
			$user_info = get_userdata($row['user_id']);
			$display_name = isset($user_info->display_name) ? $user_info->display_name : '';

			// Get the display value for weight
			$stats = ws_ls_shortcode_stats_display_value(
															array('kg' => $row['weight_difference'], 'display-unit' => ws_ls_get_config('WE_LS_DATA_UNITS'), 'display-value' => ''),
															$arguments
														);

			// Add HTML!
			$html .= sprintf(
				'<tr class="ws-rank-%s">
					<td class="ws-col-rank">%s</td>
					<td>%s</td>
					<td>%s</td>
				</tr>',
				$rank,
				$rank,
				$display_name,
				$stats['display-value']
			);

			$rank++;
		}

		$html .= '	</tbody>
				</table>';

		// Allow others to manipulate this html
		return apply_filters('weight-loss-stats-table-html', $html);
	}

	return '[Issue loading Weight Loss table]';
}


function ws_ls_shortcode_stats_total_lost($user_defined_arguments)
{
    if(!WS_LS_IS_PRO || WE_LS_DISABLE_USER_STATS) {
       return  __('Stats disabled', WE_LS_SLUG);
    }

	$arguments = shortcode_atts(
	array(
		'display' => 'number/text',
		'force-to-kg' => 'false',
		'invert' => false
	 ), $user_defined_arguments );

	$summary_stats = ws_ls_stats_get_summary_stats();
	$stats = array('kg' => $summary_stats['difference'], 'display-unit' => ws_ls_get_config('WE_LS_DATA_UNITS'), 'display-value' => '');
	$stats = ws_ls_shortcode_stats_display_value($stats, $arguments);

	return $stats['display-value'];
}

function ws_ls_shortcode_stats_display_value($stats, $arguments) {

	if(is_array($stats) && !empty($stats) &&
		is_array($arguments) && !empty($arguments)) {

		$difference = $stats['kg'];

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

		return $stats;
	}

	return '';
}
