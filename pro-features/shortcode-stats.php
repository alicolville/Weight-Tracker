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
		'show_percentage' => true,		// Show / hide percentage
		'ignore_cache' => false			// If we have a cached data set, then use that.
	 ), $user_defined_arguments );

	$data = ws_ls_stats_league_table_fetch($arguments['ignore_cache'], $arguments['number_to_show'],
	 										$arguments['losers_only'], $arguments['order']);

	$arguments['show_percentage'] = ws_ls_force_bool_argument($arguments['show_percentage']);

	if($data) {

		$html = '<table class="ws-ls-stats-table' . (is_admin() ? ' footable table' : '') . '">
					<thead>
						<tr>
							<th class="ws-col-rank-th"></th>
							<th class="ws-col-name-th">' . __('Name', WE_LS_SLUG) . '</th>
							<th class="ws-weight-diff-th">' . __('Weight Difference', WE_LS_SLUG) . '</th>';

							if(true == $arguments['show_percentage']) {
								$html .= '<th class="ws-weight-diff-th">+/-</th>';
							}

							$html .= '<th>' . __('No of entries', WE_LS_SLUG) . '</th>
						</tr>
					</thead>
					<tbody>
		';
		$rank = 1;
		foreach ($data as $row) {

			// Allow others to manipulate this data
			$row = apply_filters(WE_LS_FILTER_STATS_ROW, $row);

			// Display name from WP
			$user_info = get_userdata($row['user_id']);
			$display_name = isset($user_info->display_name) ? $user_info->display_name : '';

            // If used in admin, wrap display name in link
            if(is_admin()) {
                $display_name = '<a href="' . ws_ls_get_link_to_user_profile($row['user_id']) . '">' . $display_name . '</a>';
            }

			// Get the display value for weight
			$stats = ws_ls_shortcode_stats_display_value(
															array('kg' => $row['weight_difference'], 'display-unit' => ws_ls_get_config('WE_LS_DATA_UNITS'), 'display-value' => ''),
															$arguments
														);

			$percentage = '';

			// Calculate %
			if(true == $arguments['show_percentage'] && 0 !== intval($row['start_weight'])) {
				$percentage = (($row['recent_weight'] - $row['start_weight']) / $row['start_weight']) * 100;
				$percentage = (false === ws_ls_force_bool_argument($arguments['invert'])) ? $percentage : -$percentage ;
		        $percentage = round($percentage) . '%';
			}

            $table_cell = (is_admin()) ? ' style="display: table-cell;"' : '';

			// Add HTML!
			$html .= sprintf(
				'<tr class="ws-rank-%s%s">
					<td class="ws-col-rank" ' . $table_cell . '>%s</td>
					<td ' . $table_cell . '>%s</td>
					<td ' . $table_cell . '>%s</td>
					%s
					<td ' . $table_cell . '>%s</td>
				</tr>',
				$rank,
                (('asc' == $arguments['order'] && $row['weight_difference'] < 0) || 'desc' == $arguments['order'] && $row['weight_difference'] > 0) ? ' ws-ls-good' : ' ws-ls-bad',
				$rank,
				$display_name,
				$stats['display-value'],
                (true == $arguments['show_percentage']) ? '<td ' . $table_cell . '>' . $percentage . '</td>' : '',
                $row['no_entries']
			);

			$rank++;
		}

		$html .= '	</tbody>
				</table>';

		// Allow others to manipulate this html
		return apply_filters(WE_LS_FILTER_STATS_TABLE_HTML, $html);
	}

	return '<p>' . __('The league table has not been generated yet. This is a scheduled task so please check back in 15 minutes or try pressing the button below.', WE_LS_SLUG) .'</p>';
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
				$stats['display-value'] .= ws_ls_format_stones_pound_for_comparison_display($weight);
				break;
			default:
				$stats['display-value'] .= ws_ls_round_decimals($difference)  . __('kg', WE_LS_SLUG);
		}

		// Allow theme developer to override stats message
		$stats = apply_filters(WE_LS_FILTER_STATS_SHORTCODE, $stats);

		return $stats;
	}

	return '';
}
