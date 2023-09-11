<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Shortcode for stats
 * @param $user_defined_arguments
 *
 * @return mixed|string|void
 */
function ws_ls_shortcode_stats_league_total( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [
									'display'           => 'number',
									'force-to-kg'       => 'false',
									'invert'            => false,
									'order'             => 'asc',		// asc: Lost the most first. desc: lost the least first
									'number_to_show'    => 10,			// Number of users to display in table
									'losers_only'       => false,		// Only show people that have lost weight
									'show_percentage'   => true,		// Show / hide percentage
									'ignore_cache'      => false		// If we have a cached data set, then use that.
	 ], $user_defined_arguments );

	$data = ws_ls_db_stats_league_table_fetch( $arguments[ 'ignore_cache' ], $arguments[ 'number_to_show' ], $arguments[ 'losers_only' ], $arguments[ 'order' ] );

	$arguments[ 'show_percentage' ] = ws_ls_force_bool_argument( $arguments[ 'show_percentage' ] );

	if( false === empty( $data ) ) {

		$html = '<table class="ws-ls-stats-table' . (is_admin() ? ' footable table' : '') . '">
					<thead>
						<tr>
							<th class="ws-col-rank-th"></th>
							<th class="ws-col-name-th">' . __('Name', WE_LS_SLUG) . '</th>
							<th class="ws-weight-diff-th">' . __('Weight Difference', WE_LS_SLUG) . '</th>';

							if( $arguments['show_percentage'] ) {
								$html .= '<th class="ws-weight-diff-th">+/-</th>';
							}

							$html .= '<th>' . __('No of entries', WE_LS_SLUG) . '</th>
						</tr>
					</thead>
					<tbody>
		';
		$rank = 1;

		foreach ( $data as $row ) {

			// Allow others to manipulate this data
			$row = apply_filters( 'wlt-filter-stats-table-row', $row );

            $display_name = ws_ls_user_display_name( $row[ 'user_id' ] );

            // If used in admin, wrap display name in link
            if( true === is_admin() ) {
                $display_name = sprintf( '<a href="%s">%s</a>', ws_ls_get_link_to_user_profile( $row['user_id'] ), $display_name );
            }

            // Get the display value for weight
            $stats = ws_ls_shortcode_stats_display_value([ 'kg' => $row['weight_difference'], 'display-unit' => ws_ls_setting(), 'display-value' => '' ], $arguments );

            $percentage = '';

            // Calculate %
            if( $arguments['show_percentage'] && 0 !== (int) $row[ 'start_weight' ] ) {
                $percentage = ( ( $row[ 'recent_weight' ] - $row[ 'start_weight' ]) / $row[ 'start_weight' ] ) * 100;
                $percentage = ( false === ws_ls_to_bool( $arguments[ 'invert' ] ) ) ? $percentage : -$percentage ;
                $percentage = round( $percentage ) . '%';
            }

            $table_cell = (is_admin()) ? ' style="display: table-cell;"' : '';

            // Add HTML!
            $html .= sprintf(
                '<tr class="ws-rank-%1$s%2$s">
                <td class="ws-col-rank" ' . $table_cell . '>%1$s</td>
                <td ' . $table_cell . '>%4$s</td>
                <td ' . $table_cell . ' class="%3$s">%5$s</td>
                %6$s
                <td ' . $table_cell . ' class="%3$s">%7$s</td>
            </tr>',
                $rank,
                ( ('asc' == $arguments[ 'order' ] && $row[ 'weight_difference' ] < 0 ) || ( 'desc' == $arguments[ 'order' ] && $row[ 'weight_difference' ] > 0 ) ) ? ' ws-ls-good' : ' ws-ls-bad',
                ws_ls_blur(),
                $display_name,
                ws_ls_blur_text( $stats[ 'display-value' ] ),
                $arguments['show_percentage'] ? '<td ' . $table_cell . ' class="' . ws_ls_blur() . '">' . ws_ls_blur_text( $percentage ) . '</td>' : '',
                ws_ls_blur_text( $row[ 'no_entries' ] )
            );

            $rank++;
		}

		$html .= '	</tbody>
				</table>';

		// Allow others to manipulate this html
		return apply_filters( 'wlt-filter-stats-table-html', $html);
	}

	return sprintf( '<p>%s</p>', __( 'The league table has not been generated yet. This is a scheduled task so please check back in 15 minutes or try pressing the button below.', WE_LS_SLUG ) );
}
add_shortcode( 'wlt-league-table', 'ws_ls_shortcode_stats_league_total' );
add_shortcode( 'wt-league-table', 'ws_ls_shortcode_stats_league_total' );

/**
 * Shortcode for stats / total lost
 * @param $user_defined_arguments
 *
 * @return mixed|string|void
 */
function ws_ls_shortcode_stats_total_lost( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments      = shortcode_atts( [	'display' => 'number/text', 'force-to-kg' => 'false', 'invert' => false ], $user_defined_arguments );
	$summary_stats  = ws_ls_stats_get_summary_stats();
	$stats = ws_ls_shortcode_stats_display_value( [ 'kg' => $summary_stats['difference'], 'display-unit' => ws_ls_setting(), 'display-value' => '' ], $arguments );

	return $stats['display-value'];
}
add_shortcode( 'wlt-total-lost', 'ws_ls_shortcode_stats_total_lost' );
add_shortcode( 'wt-total-lost', 'ws_ls_shortcode_stats_total_lost' );
add_shortcode( 'wt-total-weight-loss-by-community', 'ws_ls_shortcode_stats_total_lost' );

/**
 * Display a Stat value
 * @param $stats
 * @param $arguments
 *
 * @return array|string
 */
function ws_ls_shortcode_stats_display_value( $stats, $arguments ) {

	if( true === is_array( $stats ) && false === empty( $stats ) &&
			true === is_array( $arguments ) && false === empty( $arguments ) ) {

		$difference = $stats['kg'];

		// If display number text, remove sign and use text to represent gain / loss
		if( 'number/text' == $arguments[ 'display' ] ) {

			$stats['display-value'] = ($difference <= 0) ? __( 'Lost', WE_LS_SLUG ) : __( 'Gained', WE_LS_SLUG );
			$stats['display-value'] .= ': ';
			$difference             = abs( $difference );

		} else {
			// Invert positive / negative numbers
			$difference = ( false === ws_ls_to_bool( $arguments[ 'invert' ] ) ) ? $difference : -$difference ;
		}

		// Ignore global and user settings and force display to Kg?
		if( ws_ls_to_bool( $arguments['force-to-kg'] ) ) {
			$stats[ 'display-unit' ] = 'kg';
		}

		switch ( $stats[ 'display-unit' ] ) {
			case 'pounds_only':
				$stats[ 'display-value' ] .= ws_ls_convert_kg_to_lb( $difference ) . __( 'lbs', WE_LS_SLUG );
				break;
			case 'stones_pounds':
				$weight = ws_ls_convert_kg_to_stone_pounds( $difference );
				$stats[ 'display-value' ] .= ws_ls_format_stones_pound_for_comparison_display( $weight );
				break;
			default:
				$stats['display-value'] .= ws_ls_round_decimals( $difference )  . __('kg', WE_LS_SLUG);
		}

		// Allow theme developer to override stats message
		return apply_filters( 'wlt-filter-stats-shortcode', $stats );
	}

	return '';
}
