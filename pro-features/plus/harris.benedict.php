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
		$calorie_intake['maintain'] = ['total' => round($activity_level * $bmr, 2)];

		$calories_to_lose = $calorie_intake['maintain']['total'] - 500;
        $is_female = ws_ls_is_female($user_id);

        // Female
        if (true === $is_female && $calories_to_lose > WS_LS_CAL_CAP_FEMALE) {
            $calories_to_lose = WS_LS_CAL_CAP_FEMALE;
        } elseif (false === $is_female && $calories_to_lose > WS_LS_CAL_CAP_MALE) {
            $calories_to_lose = WS_LS_CAL_CAP_MALE;
        }

		$calorie_intake['lose'] = ['total' => $calories_to_lose] ; // lose weight (1 to 2lbs per week)

		// Breakdown calories into meal types
		foreach ($calorie_intake as $key => $data) {

			$calc_figure = $calorie_intake[$key]['total'] / 100;

			$calorie_intake[$key]['breakfast'] = $calc_figure * 20;
			$calorie_intake[$key]['lunch'] = $calc_figure * 30;
			$calorie_intake[$key]['dinner'] = $calc_figure * 30;
			$calorie_intake[$key]['snacks'] = $calc_figure * 20;

			$calorie_intake[$key] = array_map('ws_ls_round_bmr_harris', $calorie_intake[$key]);
		}

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
	function ws_ls_harris_benedict_render_table($user_id, $missing_data_text = false) {

		$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

		$calories = ws_ls_harris_benedict_calculate_calories($user_id);

		$missing_data_text = (false === $missing_data_text) ? __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ) : $missing_data_text;

		if(false === empty($calories)) {

		    // Table Header
            $html = sprintf('<table class="form-table">
                                <tr>
                                    <th></th>
                                    <th>%s</th>
                                    <th>%s (20%%)</th>
                                    <th>%s (30%%)</th>
                                    <th>%s (30%%)</th>
                                    <th>%s (20%%)</th>
                                </tr>',
                                __( 'Total', WE_LS_SLUG ),
                                __( 'Breakfast', WE_LS_SLUG ),
                                __( 'Lunch', WE_LS_SLUG ),
                                __( 'Dinner', WE_LS_SLUG ),
                                __( 'Snacks', WE_LS_SLUG )
                    );


                // Maintain
                $html .= sprintf('<tr valign="top">
                                    <td><strong>%s</strong></td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                </tr>',
                                __( 'Maintain', WE_LS_SLUG ),
                                number_format($calories['maintain']['total']),
                                esc_html($calories['maintain']['breakfast']),
                                esc_html($calories['maintain']['lunch']),
                                esc_html($calories['maintain']['dinner']),
                                esc_html($calories['maintain']['snacks'])
                    );

                // Lose
                $html .= sprintf('<tr valign="top" class="alternate">
                                                    <td><strong>%s</strong></td>
                                                    <td>%s</td>
                                                    <td>%s</td>
                                                    <td>%s</td>
                                                    <td>%s</td>
                                                    <td>%s</td>
                                                </tr>
                                    </table>',
                    __( 'Lose', WE_LS_SLUG ),
                    number_format($calories['lose']['total']),
                    esc_html($calories['lose']['breakfast']),
                    esc_html($calories['lose']['lunch']),
                    esc_html($calories['lose']['dinner']),
                    esc_html($calories['lose']['snacks'])
                );

            $html .= sprintf('<p><small>%s</small></p>', ws_ls_display_calorie_cap($user_id));

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
										'progress' => 'maintain',	// 'maintain', 'lose'
										'type' => 'lunch'			// 'breakfast', 'lunch', 'dinner', 'snack', 'total'
									], $user_defined_arguments );

		$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);
		$progress = (false === in_array($arguments['progress'], ['maintain', 'lose'])) ? 'maintain' : $arguments['progress'];
		$type = (false === in_array($arguments['type'], ['breakfast', 'lunch', 'dinner', 'snack', 'total'])) ? 'lunch' : $arguments['type'];

		$calorie_intake = ws_ls_harris_benedict_calculate_calories($arguments['user-id']);

		// No calorie data?
		if(true === empty($calorie_intake) && false === $arguments['suppress-errors']) {
			return esc_html('<p>' . $arguments['error-message'] . '</p>');
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

		$arguments = shortcode_atts([
										'error-message' => __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
										'user-id' => false
									], $user_defined_arguments );

		$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);

		return ws_ls_harris_benedict_render_table($arguments['user-id'], $arguments['error-message']);
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