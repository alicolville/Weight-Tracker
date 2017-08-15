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

		//TODO: Add cap here to calorie level

		$calorie_intake['lose'] = ['total' => $calorie_intake['maintain']['total'] - 500] ; // lose weight (1 to 2lbs per week)

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

	function ws_ls_harris_benedict_render_table($user_id, $missing_data_text = false) {

		$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

		$calories = ws_ls_harris_benedict_calculate_calories($user_id);

		$missing_data_text = (false === $missing_data_text) ? __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ) : $missing_data_text;

		if(false === empty($calories)) {
			?>
			<table class="form-table">
				<tr>
					<th></th>
					<th><?php echo __( 'Total', WE_LS_SLUG ); ?></th>
					<th><?php echo __( 'Breakfast', WE_LS_SLUG ); ?> (20%)</th>
					<th><?php echo __( 'Lunch', WE_LS_SLUG ); ?> (30%)</th>
					<th><?php echo __( 'Dinner', WE_LS_SLUG ); ?> (30%)</th>
					<th><?php echo __( 'Snacks', WE_LS_SLUG ); ?> (20%)</th>
				</tr>
				<tr valign="top">
					<td><strong><?php echo __( 'Maintain', WE_LS_SLUG ); ?></strong></td>
					<td><?php echo number_format($calories['maintain']['total']); ?></td>
					<td><?php esc_html_e($calories['maintain']['breakfast']); ?></td>
					<td><?php esc_html_e($calories['maintain']['lunch']); ?></td>
					<td><?php esc_html_e($calories['maintain']['dinner']); ?></td>
					<td><?php esc_html_e($calories['maintain']['snacks']); ?></td>
				</tr>
				<tr valign="top" class="alternate">
					<td><strong><?php echo __( 'Lose', WE_LS_SLUG ); ?></strong></td>
					<td><?php echo number_format($calories['lose']['total']); ?></td>
					<td><?php esc_html_e($calories['lose']['breakfast']); ?></td>
					<td><?php esc_html_e($calories['lose']['lunch']); ?></td>
					<td><?php esc_html_e($calories['lose']['dinner']); ?></td>
					<td><?php esc_html_e($calories['lose']['snacks']); ?></td>
				</tr>
			</table>
			<?php

		} else {
			echo '<p>' . esc_html($missing_data_text) . '</p>';
		}

	}

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
