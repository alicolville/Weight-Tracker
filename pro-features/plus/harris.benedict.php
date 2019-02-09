<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_harris_benedict_calculate_calories($user_id = false) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	// Data cached?
	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_HARRIS_BENEDICT;

	// Do we have BMR cached?
	if($cache = ws_ls_get_cache($cache_key)) {
		return $cache;
	}

	// First fetch the user's current BMR.
	$bmr = ws_ls_calculate_bmr($user_id, false);

	if(true === empty($bmr)) {
		return NULL;
	}

	// Fetch the user's activity level
	$activity_level = ws_ls_get_user_setting('activity_level', $user_id);

	if(true === empty($activity_level)) {
		return NULL;
	}

	// We have activity level and bmr, calculate daily calories.
	$calorie_intake['maintain'] = ['total' => round($activity_level * $bmr, 2), 'label' => __( 'Maintain', WE_LS_SLUG )];

	// Filter total
	$calorie_intake['maintain']['total'] = apply_filters( 'wlt-filter-calories-maintain', $calorie_intake['maintain']['total'], $bmr, $activity_level );

	$calories_to_lose = ($calorie_intake['maintain']['total'] > WS_LS_CAL_TO_SUBTRACT) ? $calorie_intake['maintain']['total'] - WS_LS_CAL_TO_SUBTRACT : $calorie_intake['maintain']['total'];

	$is_female = ws_ls_is_female($user_id);

	// Female
	if (true === $is_female && 0 !== WS_LS_CAL_CAP_FEMALE && $calories_to_lose > WS_LS_CAL_CAP_FEMALE) {
		$calories_to_lose = WS_LS_CAL_CAP_FEMALE;
	} elseif (false === $is_female && 0 !== WS_LS_CAL_CAP_MALE && $calories_to_lose > WS_LS_CAL_CAP_MALE) {
		$calories_to_lose = WS_LS_CAL_CAP_MALE;
	}

	$calorie_intake['lose'] = ['total' => $calories_to_lose, 'label' => __( 'Lose', WE_LS_SLUG ) ] ; // lose weight (1 to 2lbs per week)

	// Filter total
	$calorie_intake['lose']['total'] = apply_filters( 'wlt-filter-calories-lose', $calorie_intake['lose']['total'], $bmr, $activity_level, $calories_to_lose );

	// Allow all calorie totals to be replaced or add additional rows.
	$calorie_intake = apply_filters( 'wlt-filter-calories-pre', $calorie_intake, $bmr, $activity_level, $calories_to_lose );

	// Breakdown calories into meal types
	foreach ($calorie_intake as $key => $data) {

		$calc_figure = $calorie_intake[$key]['total'] / 100;

		$calorie_intake[$key]['breakfast'] = $calc_figure * 20;
		$calorie_intake[$key]['lunch'] = $calc_figure * 30;
		$calorie_intake[$key]['dinner'] = $calc_figure * 30;
		$calorie_intake[$key]['snacks'] = $calc_figure * 20;

		$calorie_intake[$key] = array_map('ws_ls_round_bmr_harris', $calorie_intake[$key]);
	}

	$calorie_intake = apply_filters(WE_LS_FILTER_HARRIS, $calorie_intake, $user_id);

	// Cache it!
	ws_ls_set_cache($cache_key, $calorie_intake);

	return $calorie_intake;
}

/**
 *
 * Render a HTML table of the person's maintain / lose calories
 *
 * @param $user_id
 * @param bool $missing_data_text
 * @return string
 */
function ws_ls_harris_benedict_render_table($user_id, $missing_data_text = false,  $additional_css_class = '', $email = false) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	$calories = ws_ls_harris_benedict_calculate_calories($user_id);

	$missing_data_text = (false === $missing_data_text) ? __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ) : $missing_data_text;

	if(false === empty($calories)) {

		// Table Header
		$html = sprintf('<table class="%s%s" %s >
                                <tr>
                                    <th class="ws-ls-empty-cell row-title"></th>
                                    <th>%s</th>
                                    <th data-breakpoints="xs sm">%s (20%%)</th>
                                    <th data-breakpoints="xs sm">%s (30%%)</th>
                                    <th data-breakpoints="xs sm">%s (30%%)</th>
                                    <th data-breakpoints="xs sm">%s (20%%)</th>
                                </tr>',
			(false === empty($additional_css_class)) ? esc_attr($additional_css_class) . ' ' : '',
			false === is_admin() ? 'ws-ls-harris-benedict' : 'widefat',
			true === $email ? 'cellpadding="10" border="1"' : '',
			__( 'Total', WE_LS_SLUG ),
			__( 'Breakfast', WE_LS_SLUG ),
			__( 'Lunch', WE_LS_SLUG ),
			__( 'Dinner', WE_LS_SLUG ),
			__( 'Snacks', WE_LS_SLUG )
		);

		$html .= apply_filters(WE_LS_FILTER_HARRIS_TOP_OF_TABLE, '', $calories);

		$rows_to_display = apply_filters( 'wlt-filter-harris-benedict-rows', ['maintain', 'lose'] );

		$css_class = '';

		foreach ( $rows_to_display as $row_name ) {

			$css_class = ( true === empty( $css_class ) ) ? 'alternate' : '';

			$html .= sprintf('<tr valign="top" class="%s">
										<td class="ws-ls-col-header">%s</td>
										<td>%s</td>
										<td>%s</td>
										<td>%s</td>
										<td>%s</td>
										<td>%s</td>
									</tr>',
				esc_attr( $css_class ),
				esc_html($calories[$row_name]['label']),
				number_format($calories[$row_name]['total']),
				esc_html($calories[$row_name]['breakfast']),
				esc_html($calories[$row_name]['lunch']),
				esc_html($calories[$row_name]['dinner']),
				esc_html($calories[$row_name]['snacks'])
			);


		}

		$html .= '</table>';

		if(true === is_admin() && false === $email) {
			$html .= sprintf('<p><small>%s</small></p>', ws_ls_display_calorie_cap($user_id));
		}

		return $html;

	} else {
		return '<p>' . esc_html($missing_data_text) . '</p>';
	}

}

/**
 * Render the shortcode [wlt-calories]
 *
 * @param $user_defined_arguments
 */
function ws_ls_shortcode_harris_benedict($user_defined_arguments) {

	if(false === WS_LS_IS_PRO_PLUS) {
		return;
	}

	$arguments = shortcode_atts([
		'error-message' => __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
		'user-id' => false,
		'progress' => 'maintain',	// 'maintain', 'lose', 'auto'
		'type' => 'total'			// 'breakfast', 'lunch', 'dinner', 'snack', 'total'
	], $user_defined_arguments );

	$allowed_progress = apply_filters( WE_LS_FILTER_HARRIS_ALLOWED_PROGRESS, ['auto', 'maintain', 'lose']);

	// If "progress" set as "auto", then determine from the user's aim which progress type to display
    if ( 'auto' === $arguments['progress'] ) {
        $arguments['progress'] = ws_ls_get_progress_attribute_from_aim();
    }

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);
	$progress = (false === in_array($arguments['progress'], $allowed_progress)) ? 'maintain' : $arguments['progress'];
	$type = (false === in_array($arguments['type'], ['breakfast', 'lunch', 'dinner', 'snacks', 'total'])) ? 'lunch' : $arguments['type'];

	$calorie_intake = ws_ls_harris_benedict_calculate_calories($arguments['user-id']);

	// No calorie data?
	if(true === empty($calorie_intake) && false === empty($arguments['error-message'])) {
		return '<p>' . esc_html( $arguments['error-message'] ) . '</p>';
	}

	$display_value = (false === empty($calorie_intake[$progress][$type])) ? number_format ($calorie_intake[$progress][$type]) : '' ;

	return esc_html($display_value);
}
add_shortcode( 'wlt-calories', 'ws_ls_shortcode_harris_benedict' );

/**
 * Renders the shortcode [wlt-calories-table]
 *
 * Basically displays the maintain / lose calorie table as shown on a user's record in admin
 *
 * @param $user_defined_arguments
 */
function ws_ls_shortcode_harris_benedict_table($user_defined_arguments) {

	if(false === WS_LS_IS_PRO_PLUS) {
		return;
	}

	$arguments = shortcode_atts([	'css-class' => '',
		'error-message' => __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
		'user-id' => false,
		'disable-jquery' => false
	], $user_defined_arguments );

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);
	$arguments['disable-jquery'] = ws_ls_force_bool_argument($arguments['disable-jquery']);

	// Include footable jQuery?
	if ( false === $arguments['disable-jquery'] ) {
		ws_ls_data_table_enqueue_scripts();
		$arguments['css-class'] .= ' ws-ls-footable';
	}

	return ws_ls_harris_benedict_render_table($arguments['user-id'], $arguments['error-message'], $arguments['css-class']);
}
add_shortcode( 'wlt-calories-table', 'ws_ls_shortcode_harris_benedict_table' );

/**
 *
 * Depending on the user's gender, display the calorie cap information
 *
 * @param bool $user_id
 */
function ws_ls_display_calorie_cap($user_id = false) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	$is_female = ws_ls_is_female($user_id);

	return sprintf('%s %s %s. %s <a href="%s">%s</a>.',
		($is_female) ? __('Female', WE_LS_SLUG ) : __('Male', WE_LS_SLUG ),
		__('calories for weight loss are capped at ', WE_LS_SLUG ),
		($is_female) ? number_format(WS_LS_CAL_CAP_FEMALE) : number_format(WS_LS_CAL_CAP_MALE),
		__('This can be modified within ', WE_LS_SLUG ),
		ws_ls_get_link_to_settings(),
		__('settings', WE_LS_SLUG )
	);
}
