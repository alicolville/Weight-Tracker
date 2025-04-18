<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Calculate calorie intake for given user
 * @param bool $user_id
 *
 * @return mixed|void|null
 */
function ws_ls_harris_benedict_calculate_calories( $user_id = false ) {

	if( false === WS_LS_IS_PREMIUM ) {
		return NULL;
	}

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $user_id, 'harris-benedict' ) ) {
		return $cache;
	}

	// First fetch the user's current BMR.
	$bmr = ws_ls_calculate_bmr($user_id, false);

	if( true === empty( $bmr ) ) {
		return NULL;
	}

	// Fetch the user's activity level
	$activity_level = ws_ls_user_preferences_get('activity_level', $user_id);

	if( true === empty( $activity_level ) ) {
		return NULL;
	}

	// Fetch the user's gender
	$gender = ws_ls_user_preferences_get('gender', $user_id );

	if( true === empty( $gender ) ) {
		return NULL;
	}

	$calorie_intake = ws_ls_harris_benedict_calculate_calories_raw( $bmr, $gender, $activity_level, $user_id );

	$calorie_intake = apply_filters( 'wlt-filter-harris-benedict', $calorie_intake, $user_id );

	ws_ls_cache_user_set( $user_id, 'harris-benedict', $calorie_intake );

	return $calorie_intake;

}

/**
 * Given arguments, calculate calorie intake
 * @param $bmr
 * @param $gender
 * @param $activity_level
 * @param bool $user_id
 *
 * @return mixed|void|null
 */
function ws_ls_harris_benedict_calculate_calories_raw( $bmr, $gender, $activity_level, $user_id = false ) {

	if( false === WS_LS_IS_PREMIUM ) {
		return NULL;
	}

	if( true === empty( $bmr ) ) {
		return NULL;
	}

	if( true === empty( $activity_level ) ) {
		return NULL;
	}

	// --------------------------------------------------
	// Total
	// --------------------------------------------------

	// We have activity level and bmr, calculate daily calories.
	$calorie_intake['maintain'] = ['total' => round($activity_level * $bmr, 2), 'label' => esc_html__( 'Maintain', WE_LS_SLUG )];

	// Filter total
	$calorie_intake['maintain']['total'] = apply_filters( 'wlt-filter-calories-maintain', $calorie_intake['maintain']['total'], $bmr, $activity_level, $user_id );

	// --------------------------------------------------
	// Lose
	// --------------------------------------------------

	$calories_to_lose   = ws_ls_harris_benedict_filter_calories_to_lose( $calorie_intake['maintain']['total'], $gender );
	$calories_to_lose   = ( $calorie_intake['maintain']['total'] > $calories_to_lose ) ? $calorie_intake['maintain']['total'] - $calories_to_lose : $calorie_intake['maintain']['total'];
	$is_female          = ws_ls_is_female_raw( $gender );

	$calorie_cap        = $is_female ?	ws_ls_harris_benedict_setting( 'ws-ls-female-cal-cap' ) : ws_ls_harris_benedict_setting( 'ws-ls-male-cal-cap' );

	// If suggested calorie intake to lose weight is above the cap, then set to cap.
	if ( $calories_to_lose > $calorie_cap ) {
		$calories_to_lose = $calorie_cap;
	}

	$calories_to_lose = ws_ls_harris_benedict_safety_cap_check_raw( $gender, $calories_to_lose );

	$calorie_intake['lose'] = ['total' => $calories_to_lose, 'label' => esc_html__( 'Lose', WE_LS_SLUG ) ] ; // lose weight (1 to 2lbs per week)

	// Filter lose total
	$calorie_intake['lose']['total'] = apply_filters( 'wlt-filter-calories-lose', $calorie_intake['lose']['total'], $bmr, $activity_level, $calories_to_lose, $user_id, $gender );

	// --------------------------------------------------
	// Gain
	// --------------------------------------------------

	$calories_to_gain = $calorie_intake['maintain']['total'] + ws_ls_harris_benedict_filter_calories_to_add( $calorie_intake['maintain']['total'], $gender );

	$calorie_intake['gain'] = [ 'total' => $calories_to_gain, 'label' => esc_html__( 'Gain', WE_LS_SLUG ) ] ;

	// Filter lose total
	$calorie_intake['gain']['total'] = apply_filters( 'wlt-filter-calories-gain', $calorie_intake['gain']['total'], $bmr, $activity_level, $calories_to_gain, $user_id, $gender );

	// --------------------------------------------------
	// Breakdown
	// --------------------------------------------------

	// Allow all calorie totals to be replaced or add additional rows.
	$calorie_intake = apply_filters( 'wlt-filter-calories-pre', $calorie_intake, $bmr, $activity_level, $calories_to_lose, $calories_to_gain, $user_id, $gender );

	$meal_ratios = ws_ls_harris_benedict_meal_ratio_defaults();

	// Breakdown calories into meal types
	foreach ($calorie_intake as $key => $data) {

		$calc_figure = $calorie_intake[ $key ][ 'total' ] / 100;

		foreach ( $meal_ratios as $meal => $default ) {
			$calorie_intake[ $key ][ $meal ]  = $calc_figure * ws_ls_harris_benedict_meal_ratio_get( $meal );
		}

		$calorie_intake[ $key ] = array_map('ws_ls_round_bmr_harris', $calorie_intake[$key]);
	}

	return apply_filters( 'wlt-filter-harris-benedict', $calorie_intake, $user_id, $bmr, $activity_level, $calories_to_lose, $calories_to_gain, $gender );
}

/**
 * Fetch a meal ratio from site options. If it doesn't exist, apply the default.
 * @param $meal
 * @return int
 */
function ws_ls_harris_benedict_meal_ratio_get( $meal ) {

    $meal_ratios = ws_ls_harris_benedict_meal_ratio_defaults();

    // Does this meal type exist?
    if ( true === array_key_exists( $meal, $meal_ratios ) ) {

        // See if we have a saved value, if not, apply default
        $default = (int) $meal_ratios[ $meal ];

        return get_option( 'ws-ls-meal-ratio-' . $meal, $default );
    }

    return 0;   // This is ERROR.
}

/**
 * Return default rations for meals
 * @return mixed
 */
function ws_ls_harris_benedict_meal_ratio_defaults() {

    $defaults = [
                    'breakfast' => 20,
                    'lunch'     => 30,
                    'dinner'    => 30,
                    'snacks'    => 20
    ];

    return apply_filters( 'wlt-filter-harris-benedict-meal-ratio-defaults', $defaults );
}

/**
 *
 * Render a HTML table of the person's maintain / lose calories
 *
 * @param $user_id
 * @param bool $missing_data_text
 * @param string $additional_css_class
 * @param bool $email
 * @param bool $include_range
 * @param null $calories
 *
 * @return string
 */
function ws_ls_harris_benedict_render_table( $user_id, $missing_data_text = false,  $additional_css_class = '', $email = false, $include_range = true, $calories = NULL ) {

	if( false === WS_LS_IS_PREMIUM ) {
		return '';
	}

	$user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( NULL === $calories ) {
		$calories = ws_ls_harris_benedict_calculate_calories( $user_id );
	}

	$missing_data_text = ( false === $missing_data_text ) ? esc_html__('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ) : $missing_data_text;

	if(false === empty($calories)) {

		// Table Header
		$html = sprintf('<table class="%1$s%2$s" %3$s >
                                <tr>
                                    <th class="ws-ls-empty-cell row-title"></th>
                                    <th>%4$s</th>
                                    <th data-breakpoints="xs sm">%5$s (%9$s%%)</th>
                                    <th data-breakpoints="xs sm">%6$s (%10$s%%)</th>
                                    <th data-breakpoints="xs sm">%7$s (%11$s%%)</th>
                                    <th data-breakpoints="xs sm">%8$s (%12$s%%)</th>
                                </tr>',
			(false === empty($additional_css_class)) ? esc_attr($additional_css_class) . ' ' : '',
			false === is_admin() ? 'ws-ls-harris-benedict' : 'widefat',
			true === $email ? 'cellpadding="10" border="1"' : '',
			esc_html__( 'Total', WE_LS_SLUG ),
			esc_html__( 'Breakfast', WE_LS_SLUG ),
			esc_html__( 'Lunch', WE_LS_SLUG ),
			esc_html__( 'Dinner', WE_LS_SLUG ),
			esc_html__( 'Snacks', WE_LS_SLUG ),
            ws_ls_harris_benedict_meal_ratio_get( 'breakfast' ),
            ws_ls_harris_benedict_meal_ratio_get( 'lunch' ),
            ws_ls_harris_benedict_meal_ratio_get( 'dinner' ),
            ws_ls_harris_benedict_meal_ratio_get( 'snacks' )
		);

		$html .= apply_filters( 'wlt-filter-harris-benedict-top-of-table', '', $calories);

		$rows_to_display = apply_filters( 'wlt-filter-harris-benedict-rows', [ 'maintain', 'lose', 'gain' ], $user_id );

		$css_class = '';

		foreach ( $rows_to_display as $row_name ) {

			if ( false === isset( $calories[ $row_name ] ) ) {
				continue;
			}

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
				esc_html( $calories[$row_name]['label'] ),
				ws_ls_round_number( $calories[$row_name]['total'] ),
				esc_html( $calories[$row_name]['breakfast'] ),
				esc_html( $calories[$row_name]['lunch'] ),
				esc_html( $calories[$row_name]['dinner'] ),
				esc_html( $calories[$row_name]['snacks'] )
			);
		}

		$html .= '</table>';

		if(true === is_admin() && false === $email) {

			// Fetch the user's gender
			$user_gender = ws_ls_user_preferences_get('gender', $user_id );

			// Do we wish to include the range used to determine calorie intake for loss / gain?
			if( true === $include_range && false === empty( $calories[ 'maintain' ][ 'total' ] ) ) {

				if ( true === ws_ls_harris_benedict_show_lose_figures() ) {

					if( true === empty( $user_gender ) ) {
						return NULL;
					}

					$range = ws_ls_harris_benedict_filter_calories_to_lose( $calories[ 'maintain' ][ 'total' ], $user_gender, true );

					if ( false === empty( $range ) ) {

						$gender = ws_ls_genders_get( $range[ 'gender' ] );

						$html .= sprintf('
						<p><strong>%8$s</strong>: %9$s - %1$d%3$s to %2$d%3$s - %5$s %6$s%7$s.</p>',
							$range[ 'from' ],
							$range[ 'to' ],
							esc_html__( 'kcal', WE_LS_SLUG ),
							$calories['maintain']['total'],
							esc_html__( 'Subtract', WE_LS_SLUG ),
							esc_html( $range[ 'amount' ] ),
							'fixed' === $range[ 'unit' ] ? esc_html__( 'kcals of total calories to maintain weight', WE_LS_SLUG ) : esc_html__( '% of total calories required to maintain weight', WE_LS_SLUG ),
							esc_html__( 'Rule applied for suggested weight loss', WE_LS_SLUG ),
							( false === empty( $gender ) ) ? $gender : esc_html__( 'Everyone', WE_LS_SLUG )
						);
					}
				}

				if ( true === ws_ls_harris_benedict_show_gain_figures() ) {

					$range = ws_ls_harris_benedict_filter_calories_to_add( $calories[ 'maintain' ][ 'total' ], $user_gender, true );

					if ( false === empty( $range ) ) {

						$gender = ws_ls_genders_get( $range[ 'gender' ] );

						$html .= sprintf('
						<p><strong>%8$s</strong>: %9$s - %1$d%3$s to %2$d%3$s - %5$s %6$s%7$s.</p>',
							$range[ 'from' ],
							$range[ 'to' ],
							esc_html__( 'kcal', WE_LS_SLUG ),
							$calories['maintain']['total'],
							esc_html__( 'Add', WE_LS_SLUG ),
							esc_html( $range[ 'amount' ] ),
							'fixed' === $range[ 'unit' ] ? esc_html__( 'kcals of total calories to maintain weight', WE_LS_SLUG ) : esc_html__( '% of total calories required to maintain weight', WE_LS_SLUG ),
							esc_html__( 'Rule applied for suggested weight gain', WE_LS_SLUG ),
							( false === empty( $gender ) ) ? $gender : esc_html__( 'Everyone', WE_LS_SLUG )
						);
					}
				}
			}

			$html .= sprintf( '<p><small>%s</small></p>', ws_ls_display_calorie_cap_raw( $user_gender ) );
		}

		return $html;

	} else {
		return '<p>' . esc_html( $missing_data_text ) . '</p>';
	}

}



/**
 * Render the shortcode [wlt-calories]
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_shortcode_harris_benedict( $user_defined_arguments ) {

	if( false === WS_LS_IS_PREMIUM ) {
		return '';
	}

	$arguments = shortcode_atts( [
									'error-message' 	=> esc_html__('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
									'user-id' 			=> false,
									'add-unit'          => false,
									'progress' 			=> 'maintain',		// 'maintain', 'lose', 'gain', 'auto'
									'type' 				=> 'total',			// 'breakfast', 'lunch', 'dinner', 'snack', 'total'
									'percentage'		=> '100',			// Percentage of the figure to display
									],
									$user_defined_arguments
	);

	$allowed_progress = apply_filters( 'wlt-filter-harris-benedict-allowed-progresses', [ 'auto', 'maintain', 'lose', 'gain' ] );

	// If "progress" set as "auto", then determine from the user's aim which progress type to display
    if ( 'auto' === $arguments['progress'] ) {
        $arguments['progress'] = ws_ls_get_progress_attribute_from_aim();
    }

	$arguments['user-id'] = ws_ls_force_numeric_argument( $arguments['user-id'] );
	$progress = ( false === in_array( $arguments['progress'], $allowed_progress ) ) ? 'maintain' : $arguments['progress'];
	$type = ( false === in_array( $arguments['type'], ['breakfast', 'lunch', 'dinner', 'snacks', 'total'] ) ) ? 'lunch' : $arguments['type'];

	$calorie_intake = ws_ls_harris_benedict_calculate_calories( $arguments['user-id'] );

	// No calorie data?
	if( true === empty( $calorie_intake ) && false === empty( $arguments['error-message'] ) ) {
		return sprintf( '<p>%s</p>',  esc_html( $arguments['error-message'] ) );
	}

	if ( true === empty( $calorie_intake[ $progress ][ $type ] ) ) {
		return '';
	}

	$display_value = ws_ls_calculate_percentage_of_number( $calorie_intake[ $progress ][ $type ], $arguments[ 'percentage' ] );

	if ( true === $arguments[ 'add-unit' ] ) {
		$display_value .= esc_html__( 'kcal', WE_LS_SLUG );
	}

	return esc_html( $display_value );
}
add_shortcode( 'wlt-calories', 'ws_ls_shortcode_harris_benedict' );
add_shortcode( 'wt-calories', 'ws_ls_shortcode_harris_benedict' );

/**
 * Renders the shortcode [wlt-calories-table]
 *
 * Basically displays the maintain / lose calorie table as shown on a user's record in admin
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_shortcode_harris_benedict_table( $user_defined_arguments ) {

	if( false === WS_LS_IS_PREMIUM ) {
		return '';
	}

	$arguments = shortcode_atts( [	'css-class' => '',
									'error-message' => esc_html__('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
									'user-id' => false,
									'disable-jquery' => false
								],
								$user_defined_arguments
	);

	$arguments[ 'user-id' ] 		= (int) $arguments['user-id'];
	$arguments[ 'disable-jquery' ] 	= ws_ls_force_bool_argument( $arguments['disable-jquery'] );

	// Include footable jQuery?
	if ( false === $arguments['disable-jquery'] ) {

		ws_ls_data_table_enqueue_scripts();

		$arguments['css-class'] .= ' ws-ls-footable';
	}

	return ws_ls_harris_benedict_render_table( $arguments['user-id'], $arguments['error-message'], $arguments['css-class'] );
}
add_shortcode( 'wlt-calories-table', 'ws_ls_shortcode_harris_benedict_table' );
add_shortcode( 'wt-calories-table', 'ws_ls_shortcode_harris_benedict_table' );

/**
 *
 * Depending on the user's gender, display the calorie cap information
 *
 * @param bool $user_id
 * @return string
 */
function ws_ls_display_calorie_cap( $user_id = false ) {

	$gender = ws_ls_user_preferences_get('gender', $user_id );

	return ws_ls_display_calorie_cap_raw( $gender );
}

/**
 * Display salary capped for given gender
 * @param $gender
 *
 * @return string
 */
function ws_ls_display_calorie_cap_raw( $gender ) {

	$is_female          = ws_ls_is_female_raw( $gender );
	$calorie_cap        = $is_female ? ws_ls_harris_benedict_setting( 'ws-ls-female-cal-cap' ) : ws_ls_harris_benedict_setting( 'ws-ls-male-cal-cap' );

	return sprintf('%s %s %s. %s <a href="%s">%s</a>.',
		($is_female) ? esc_html__('Female', WE_LS_SLUG ) : esc_html__('Male', WE_LS_SLUG ),
		esc_html__('calories for weight loss are capped at ', WE_LS_SLUG ),
		ws_ls_round_number( $calorie_cap ),
		esc_html__('This can be modified within ', WE_LS_SLUG ),
		ws_ls_get_link_to_settings(),
		esc_html__('settings', WE_LS_SLUG )
	);
}

/**
 * Show loss figures to users?
 *
 * @return bool
 */
function ws_ls_harris_benedict_show_lose_figures() {

	if ( false === WS_LS_IS_PREMIUM ) {
		return false;
	}

	return ! ( 'no' === get_option( 'ws-ls-cal-show-loss', true ) );
}

/**
 * Show gain figures to users?
 *
 * @return bool
 */
function ws_ls_harris_benedict_show_gain_figures() {

	if ( false === WS_LS_IS_PREMIUM ) {
		return false;
	}

	return 'yes' === get_option('ws-ls-cal-show-gain', false );
}

/**
 *
 * Show / Hide gain or loss figures for MacroN or Harris Benedict tables
 *
 * @param $calorie_intake
 *
 * @return mixed
 */
function ws_ls_harris_benedict_filter_show_hide_gains_loss( $calorie_intake ) {

	if( false === WS_LS_IS_PREMIUM) {
		return $calorie_intake;
	}

	// Hide gains?
	if ( true !== ws_ls_harris_benedict_show_gain_figures() ) {
		unset( $calorie_intake['gain'] );
	}

	// Hide loss?
	if ( true !== ws_ls_harris_benedict_show_lose_figures() ) {
		unset( $calorie_intake['lose'] );
	}

	return $calorie_intake;
}
add_filter( 'wlt-filter-calories-pre', 'ws_ls_harris_benedict_filter_show_hide_gains_loss' );

/**
 * Return the setting for calories to add weight
 *
 * @param null $calories_to_maintain
 * @param null $gender
 * @param bool $return_range
 * @return int
 */
function ws_ls_harris_benedict_filter_calories_to_add( $calories_to_maintain = NULL, $gender = NULL, $return_range = false ) {

	if( false === WS_LS_IS_PREMIUM) {
		return 0;
	}

	// See if we have any matching ranges for maintain calories
	$ranges = ws_ls_harris_benedict_calorie_add_ranges();

	if ( true === empty( $ranges ) ) {
		return 0;	// We don't want to subtract anything
	}

	$calories_to_maintain 	= (int) $calories_to_maintain;
	$cal_to_add		 		= 0;

	foreach ( $ranges as $range ) {

		// Disabled? Skip on to next range
		if ( 1 !== (int) $range[ 'enabled' ] ) {
			continue;
		}

		$user_gender 		= (int) $gender;
		$gender_match_rule	= ( (int) $range[ 'gender' ] === $user_gender  || 0 === (int) $range[ 'gender' ] ) ;

		// Does the calorie intake fall into this range?
		if ( $gender_match_rule &&
			$calories_to_maintain >= (int) $range[ 'from' ] &&
			$calories_to_maintain <= (int) $range[ 'to' ] ) {

			// Are we just interesting in returning the range that matched? e.g. for debugging purpose?
			if ( true === $return_range ) {
				return $range;
			}

			$cal_to_add = (float) $range[ 'amount' ];

			// Percentage of calories to subtract?
			if ( 'percentage' ===  $range[ 'unit' ] &&
				false === empty( $calories_to_maintain ) ) {

				$cal_to_add = ( ( $calories_to_maintain / 100 ) * $cal_to_add );
			}

			// Do no further processing. We only consider the first range we come across.
			break;
		}
	}

	$cal_to_subtract = apply_filters( 'wlt-filter-calories-add-raw', $cal_to_add );

	return (int) $cal_to_subtract;
}

/**
 * Return the setting for calories to lose weight
 *
 * @param null $calories_to_maintain
 * @param null $gender
 * @param bool $return_range
 *
 * @return int
 */
function ws_ls_harris_benedict_filter_calories_to_lose( $calories_to_maintain = NULL, $gender = NULL, $return_range = false ) {

	if( false === WS_LS_IS_PREMIUM) {
		return 0;
	}

	// See if we have any matching ranges for maintain calories
	$ranges = ws_ls_harris_benedict_calorie_subtract_ranges();

	if ( true === empty( $ranges ) ) {
		return 0;	// We don't want to subtract anything
	}

	$calories_to_maintain 	= (int) $calories_to_maintain;
	$cal_to_subtract 		= 0;
	$user_gender 		    = (int) $gender;

	foreach ( $ranges as $range ) {

		// Disabled? Skip on to next range
		if ( 1 !== (int) $range[ 'enabled' ] ) {
			continue;
		}

		$gender_match_rule	= ( (int) $range[ 'gender' ] === $user_gender  || 0 === (int) $range[ 'gender' ] ) ;

		// Does the calorie intake fall into this range?
		if ( $gender_match_rule &&
				$calories_to_maintain >= (int) $range[ 'from' ] &&
					$calories_to_maintain <= (int) $range[ 'to' ] ) {

			// Are we just interesting in returning the range that matched? e.g. for debugging purpose?
			if ( true === $return_range ) {
				return $range;
			}

			$cal_to_subtract = (float) $range[ 'amount' ];

			// Percentage of calories to subtract?
			if ( 'percentage' ===  $range[ 'unit' ] &&
				false === empty( $calories_to_maintain ) ) {

				$cal_to_subtract = ( ( $calories_to_maintain / 100 ) * $cal_to_subtract );
			}

			// Do no further processing. We only consider the first range we come across.
			break;
		}
	}

	$cal_to_subtract = apply_filters( 'wlt-filter-calories-lose-raw', $cal_to_subtract );

	return (int) $cal_to_subtract;
}

/**
 * Ensure the calorie intake value for this user respects the safety cap
 * @param $user_id
 * @param $value
 */
function ws_ls_harris_benedict_safety_cap_check( $user_id, $value ) {

	$gender = (int) ws_ls_user_preferences_get('gender', $user_id );

	return ws_ls_harris_benedict_safety_cap_check_raw( $gender, $value );
}

/**
 * Given gender, should we be capping this calorie intake
 * @param $gender
 * @param $value
 *
 * @return int|int[]|null
 */
function ws_ls_harris_benedict_safety_cap_check_raw( $gender, $value ) {

	$cap = ws_ls_harris_benedict_safety_cap( $gender );

	// Return the value if no safety cap!
	if ( true === empty( $cap ) ) {
		$value;
	}

	return ( $value < $cap ) ? $cap : $value;
}

/**
 * For the given gender, load the safety cap to ensure we don't calculate a value below a given level.
 *
 * @param $gender
 *
 * @return int|int[]|null
 */
function ws_ls_harris_benedict_safety_cap( $gender ) {

	$gender = (int) $gender;

	if ( 1 === $gender ) {
		return (int) ws_ls_harris_benedict_setting( 'ws-ls-female-min-cal-cap' );
	} elseif ( 2 === $gender ) {
		return (int) ws_ls_harris_benedict_setting( 'ws-ls-male-min-cal-cap' );
	}

	return NULL;
}

/**
 * Return array of subtract ranges for calories
 * @return mixed|void
 */
function ws_ls_harris_benedict_calorie_subtract_ranges() {

	$ranges = [

		[
			'name'		=> 'ws-ls-cal-subtract',
			'from'		=> (int) get_option( 'ws-ls-cal-subtract-from', 0 ),
			'to'		=> (int) get_option( 'ws-ls-cal-subtract-to', 9999 ),
			'amount'	=> (int) get_option( 'ws-ls-cal-subtract', 600 ),
			'unit'		=> get_option( 'ws-ls-cal-subtract-unit', 'fixed' ),
			'gender'	=> (int) get_option( 'ws-ls-cal-subtract-gender', 0 ),
			'enabled'	=> (int) get_option( 'ws-ls-cal-subtract-enabled', 1 ),
			'default'	=> 1
		]

	];

	for ( $i = 1; $i < 9; $i++ ) {

		$name = sprintf( 'ws-ls-cal-subtract-%d', $i );

		$ranges[] = [
						'name'		=> $name,
						'from'		=> (int) get_option( $name . '-from', 0 ),
						'to'		=> (int) get_option( $name . '-to', 0 ),
						'amount'	=> (int) get_option( $name, 0 ),
						'unit'		=> get_option( $name . '-unit', 'fixed' ),
						'gender' 	=> (int) get_option( $name . '-gender', 0 ),
						'enabled'	=> (int) get_option(  $name . '-enabled', 0 )
					];
	}

	return apply_filters( 'wlt-filter-calories-subtract-ranges', $ranges );
}

/**
 * Return an array of keys for registering when saving settings
 * @return array|mixed|void
 */
function ws_ls_harris_benedict_calorie_subtract_ranges_keys() {

	$range_subtract = ws_ls_harris_benedict_calorie_subtract_ranges();
	$ranges_add 	= ws_ls_harris_benedict_calorie_add_ranges();

	$ranges = array_merge( $range_subtract, $ranges_add );

	$ranges = wp_list_pluck( $ranges, 'name' );

	foreach ( $ranges as $name ) {

		$ranges[] = $name . '-from';
		$ranges[] = $name . '-to';
		$ranges[] = $name . '-unit';
		$ranges[] = $name . '-gender';
		$ranges[] = $name . '-enabled';
	}

	return $ranges;
}

/**
 * Return array of add ranges for calories
 * @return mixed|void
 */
function ws_ls_harris_benedict_calorie_add_ranges() {

	$ranges = [

		[
			'name'		=> 'ws-ls-cal-add',
			'from'		=> (int) get_option( 'ws-ls-cal-add-from', 0 ),
			'to'		=> (int) get_option( 'ws-ls-cal-add-to', 9999 ),
			'amount'	=> (int) get_option( 'ws-ls-cal-add', 600 ),
			'unit'		=> get_option( 'ws-ls-cal-add-unit', 'fixed' ),
			'gender'	=> (int) get_option( 'ws-ls-cal-add-gender', 0 ),
			'enabled'	=> (int) get_option( 'ws-ls-cal-add-enabled', 1 ),
			'default'	=> 1
		]

	];

	for ( $i = 1; $i < 9; $i++ ) {

		$name = sprintf( 'ws-ls-cal-add-%d', $i );

		$ranges[] = [
			'name'		=> $name,
			'from'		=> (int) get_option( $name . '-from', 0 ),
			'to'		=> (int) get_option( $name . '-to', 0 ),
			'amount'	=> (int) get_option( $name, 0 ),
			'unit'		=> get_option( $name . '-unit', 'fixed' ),
			'gender' 	=> get_option( $name . '-gender', 0 ),
			'enabled'	=> (int) get_option(  $name . '-enabled', 0 )
		];
	}

	return apply_filters( 'wlt-filter-calories-add-ranges', $ranges );
}

/**
 * Look up Harris Benedict setting
 * @param $key
 *
 * @return int|int[]|null
 */
function ws_ls_harris_benedict_setting( $key ) {

	$default_value = ws_ls_harris_benedict_defaults( $key );

	// apply default if no Pro Plus license
	if ( false === WS_LS_IS_PREMIUM ) {
		return $default_value;
	}

	$value = (int) get_option( $key, $default_value );

	// Do we want to ensure this value within 0 and 100.
	if ( true === ws_ls_string_contains( $key, 'ws-ls-macro' ) && ( $value < 0 || $value > 100 ) ) {
		$value = $default_value;
	}

	return $value;
}

/**
 * Return an array of default values for Harris Benedict calculations
 * @param $key
 *
 * @return int[]
 */
function ws_ls_harris_benedict_defaults( $key = NULL ) {

		$defaults = [
					'ws-ls-male-cal-cap'        			=> 1900,
					'ws-ls-male-min-cal-cap'    			=> NULL,
					'ws-ls-female-cal-cap'      			=> 1400,
					'ws-ls-female-min-cal-cap'  			=> NULL,
					'ws-ls-macro-proteins'      			=> 25,	// Deprecated as of 9.3.7
					'ws-ls-macro-carbs'         			=> 50,	// Deprecated as of 9.3.7
					'ws-ls-macro-fats'          			=> 25,	// Deprecated as of 9.3.7
					'ws-ls-macro-proteins-maintain'      	=> 25,
					'ws-ls-macro-carbs-maintain'   			=> 50,
					'ws-ls-macro-fats-maintain'        		=> 25,
					'ws-ls-macro-proteins-lose'      		=> 25,
					'ws-ls-macro-carbs-lose'   				=> 50,
					'ws-ls-macro-fats-lose'        			=> 25,
					'ws-ls-macro-proteins-gain'      		=> 25,
					'ws-ls-macro-carbs-gain'   				=> 50,
					'ws-ls-macro-fats-gain'        			=> 25
		];

		return ( NULL !== $key && false === empty( $defaults[ $key ] ) ) ?
					 $defaults[ $key ] :
						NULL;
}
