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
		$calorie_intake['lose'] = ['total' => $calorie_intake['maintain']['total'] - 500] ; // lose weight (1 to 2lbs per week)

		// Breakdown calories into meal types
		foreach ($calorie_intake as $key => $data) {

			$calc_figure = $calorie_intake[$key]['total'] / 100;

			$calorie_intake[$key]['breakfast'] = $calc_figure * 20;
			$calorie_intake[$key]['lunch'] = $calc_figure * 30;
			$calorie_intake[$key]['dinner'] = $calc_figure * 30;
			$calorie_intake[$key]['snacks'] = $calc_figure * 20;

			$calorie_intake[$key] = array_map('ws_ls_round_decimals', $calorie_intake[$key]);
		}

		// Cache it!
		ws_ls_set_cache($cache_key, $calorie_intake);

		return $calorie_intake;
	}

	function ws_ls_shortcode_harris_benedict($user_defined_arguments) {

		if(false === WS_LS_IS_PRO_PLUS) {
			return;
		}

		$arguments = shortcode_atts([
										'suppress-errors' => false,      // If true, don't display errors from ws_ls_calculate_bmr()
										'user-id' => false
									], $user_defined_arguments );

		$arguments['suppress-errors'] = ws_ls_force_bool_argument($arguments['suppress-errors']);
		$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);

		$calorie_intake = ws_ls_harris_benedict_calculate_calories($arguments['user-id']);
var_dump($calorie_intake);
		//return (false === is_numeric($bmr) && $arguments['suppress-errors']) ? '' : esc_html($bmr);
	}
	add_shortcode( 'wlt-calories', 'ws_ls_harris_benedict_calculate_calories' );
