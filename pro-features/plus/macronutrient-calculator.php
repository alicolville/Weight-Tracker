<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Calculate MacroN values for the given user
 * @param bool $user_id
 *
 * @return mixed|void|null
 */
function ws_ls_macro_calculate($user_id = false) {

	if( false === WS_LS_IS_PRO_PLUS ) {
		return NULL;
	}

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $user_id, 'macros' ) ) {
		return $cache;
	}

    $calories = ws_ls_harris_benedict_calculate_calories( $user_id );

    $macros = apply_filters( 'wlt-filter-macros-custom', [], $calories );

    // If a 3rd party plugin has specified macros then no point carrying on below!
    if ( false === empty( $macros ) ) {
        return $macros;
    }

    if ( true === isset( $calories['maintain']['total'] ) ) {

        $macros_to_calculate = apply_filters( 'wlt-filter-macros-calculate', ['maintain', 'lose', 'gain' ], $calories, $user_id);

        foreach ( $macros_to_calculate as $key ) {

        	// If the data doesn't exist then skip over!
        	if ( false === isset( $calories[ $key ] ) ) {
        		continue;
	        }

            $macros[$key]['calories'] = $calories[$key]['total'];

	        $protein_calc   = ws_ls_harris_benedict_setting( 'ws-ls-macro-proteins' );
	        $carbs_calc     = ws_ls_harris_benedict_setting( 'ws-ls-macro-carbs' );
	        $fats_calc      = ws_ls_harris_benedict_setting( 'ws-ls-macro-fats' );

	        $protein_calc   = $protein_calc / 100;
			$carbs_calc     = $carbs_calc / 100;
			$fats_calc      = $fats_calc / 100;

            // Total
            $macros[$key]['total']['protein'] = ($macros[$key]['calories'] * $protein_calc) / 4;
            $macros[$key]['total']['carbs'] = ($macros[$key]['calories'] * $carbs_calc) / 4;
            $macros[$key]['total']['fats'] = ($macros[$key]['calories'] * $fats_calc) / 9;

            // Allow 3rd parties to filter macro nutrient totals
            $macros[$key]['total'] = apply_filters( 'wlt-filter-macros-' . $key . '-total', $macros[$key]['total'], $key, $calories, $user_id );

            // Breakfast
            $macros[$key]['breakfast']['protein'] = $macros[$key]['total']['protein'] * 0.2;
            $macros[$key]['breakfast']['carbs'] = $macros[$key]['total']['carbs'] * 0.2;
            $macros[$key]['breakfast']['fats'] = $macros[$key]['total']['fats'] * 0.2;

            // Lunch
            $macros[$key]['lunch']['protein'] = $macros[$key]['total']['protein'] * 0.3;
            $macros[$key]['lunch']['carbs'] = $macros[$key]['total']['carbs'] * 0.3;
            $macros[$key]['lunch']['fats'] = $macros[$key]['total']['fats'] * 0.3;

            // Dinner
            $macros[$key]['dinner']['protein'] = $macros[$key]['total']['protein'] * 0.3;
            $macros[$key]['dinner']['carbs'] = $macros[$key]['total']['carbs'] * 0.3;
            $macros[$key]['dinner']['fats'] = $macros[$key]['total']['fats'] * 0.3;

            // Snacks
            $macros[$key]['snacks']['protein'] = $macros[$key]['total']['protein'] * 0.2;
            $macros[$key]['snacks']['carbs'] = $macros[$key]['total']['carbs'] * 0.2;
            $macros[$key]['snacks']['fats'] = $macros[$key]['total']['fats'] * 0.2;

            $macros[$key] = apply_filters( 'wlt-filter-macros-' . $key, $macros[$key], $calories, $user_id );
        }

    } else {
        return NULL;
    }

	$macros = apply_filters( 'wlt-filter-macros', $macros, $calories, $user_id );

	ws_ls_cache_user_set( $user_id, 'macros', $macros );

    return $macros;
}

/**
 * Render MacroN table
 *
 * @param $user_id
 * @param bool $missing_data_text
 * @param string $additional_css_class
 * @return string
 */
function ws_ls_macro_render_table($user_id, $missing_data_text = false, $additional_css_class = '') {

	if( false === WS_LS_IS_PRO_PLUS ) {
		return '';
	}

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    $macros = ws_ls_macro_calculate( $user_id );

    $missing_data_text = ( false === $missing_data_text ) ? __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG) : $missing_data_text;

    if ( false === empty( $macros ) ) {

        $html = sprintf('<table class="%sws-ls-macro%s"  >',
			(false === empty($additional_css_class)) ? esc_attr($additional_css_class) . ' ' : '',
				false === is_admin() ? '' : ' widefat');

        $macros_to_display = apply_filters( 'wlt-filter-macros-display', [ 'maintain', 'lose', 'gain' ], $macros, $user_id);

        foreach ( $macros_to_display as $key ) {

        	if ( false === isset( $macros[$key] ) ) {
        		continue;
        	}

            // Table Header
            $html .= sprintf('
                                <tr>
                                    <th class="row-title">%s (%skcal)</th>
                                    <th>%s</th>
                                    <th data-breakpoints="xs sm">%s</th>
                                    <th data-breakpoints="xs sm">%s</th>
                                    <th data-breakpoints="xs sm">%s</th>
                                    <th data-breakpoints="xs sm">%s</th>
                                </tr>
                            ',
				ws_ls_get_macro_name( $key ),
				ws_ls_round_number( $macros[$key]['calories'], 0 ),
                __('Total', WE_LS_SLUG),
                __('Breakfast', WE_LS_SLUG),
                __('Lunch', WE_LS_SLUG),
                __('Dinner', WE_LS_SLUG),
                __('Snacks', WE_LS_SLUG)
            );

            // Protein
            $html .= sprintf('  <tr valign="top" class="alternate">
                                    <td class="ws-ls-col-header">%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                </tr>',
                __('Proteins', WE_LS_SLUG),
                ws_ls_macro_round($macros[$key]['total']['protein']),
                ws_ls_macro_round($macros[$key]['breakfast']['protein']),
                ws_ls_macro_round($macros[$key]['lunch']['protein']),
                ws_ls_macro_round($macros[$key]['dinner']['protein']),
                ws_ls_macro_round($macros[$key]['snacks']['protein'])
            );

            // Carbs
            $html .= sprintf('  <tr valign="top" >
                                    <td class="ws-ls-col-header">%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                </tr>',
                __('Carbs', WE_LS_SLUG),
                ws_ls_macro_round($macros[$key]['total']['carbs']),
                ws_ls_macro_round($macros[$key]['breakfast']['carbs']),
                ws_ls_macro_round($macros[$key]['lunch']['carbs']),
                ws_ls_macro_round($macros[$key]['dinner']['carbs']),
                ws_ls_macro_round($macros[$key]['snacks']['carbs'])
            );

            // Fats
            $html .= sprintf('  <tr valign="top" class="alternate">
                                    <td class="ws-ls-col-header">%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td>%s</td>
                                </tr>',
                __('Fats', WE_LS_SLUG),
                ws_ls_macro_round($macros[$key]['total']['fats']),
                ws_ls_macro_round($macros[$key]['breakfast']['fats']),
                ws_ls_macro_round($macros[$key]['lunch']['fats']),
                ws_ls_macro_round($macros[$key]['dinner']['fats']),
                ws_ls_macro_round($macros[$key]['snacks']['fats'])
            );

        }
        $html .= '</table>';
        return $html;

    } else {
        return '<p>' . esc_html($missing_data_text) . '</p>';
    }

}

/**
 * Render the shortcode [wlt-macronutrients]
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_shortcode_macro( $user_defined_arguments ) {

	if( false === WS_LS_IS_PRO_PLUS ) {
		return '';
	}

	$arguments = shortcode_atts([
									'error-message' 	=> __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
									'user-id' 			=> false,
									'progress' 			=> 'maintain',	// 'maintain', 'lose', 'gain', 'auto'
									'nutrient' 			=> 'fats', 		// 'fats', 'protein', 'carbs'
									'type' 				=> 'lunch'		// 'breakfast', 'lunch', 'dinner', 'snack', 'total'
								], $user_defined_arguments );

    $allowed_progress = apply_filters( 'wlt-filter-macro-nutrients-allowed-progresses', [ 'maintain', 'lose', 'gain', 'auto' ] );

    // If "progress" set as "auto", then determine from the user's aim which progress type to display
    if ( 'auto' === $arguments['progress'] ) {
        $arguments['progress'] = ws_ls_get_progress_attribute_from_aim();
    }

	$arguments['user-id'] 	= (int) $arguments[ 'user-id' ];
    $progress 				= ( false === in_array( $arguments[ 'progress' ], $allowed_progress ) ) ? 'maintain' : $arguments['progress'];
	$type 					= ( false === in_array( $arguments[ 'type' ], [ 'breakfast', 'lunch', 'dinner', 'snacks', 'total' ] ) ) ? 'lunch' : $arguments['type'];
	$nutrient 				= ( false === in_array( $arguments[ 'nutrient' ], [ 'fats', 'protein', 'carbs'] ) ) ? 'fats' : $arguments[ 'nutrient' ];
	$macros 				= ws_ls_macro_calculate( $arguments['user-id'] );

	// No macro data?
    if( true === empty($macros) && false === empty( $arguments[ 'error-message' ] ) ) {
        return sprintf( '<p>%s</p>', esc_html( $arguments[ 'error-message' ] ) );
    }

	$display_value = ( false === empty( $macros[ $progress ][ $type ][ $nutrient ] ) ) ?
						ws_ls_macro_round( $macros[ $progress ][ $type ][ $nutrient ] ) : '' ;

	return esc_html( $display_value );
}
add_shortcode( 'wlt-macronutrients', 'ws_ls_shortcode_macro' );
add_shortcode( 'wt-macronutrients', 'ws_ls_shortcode_macro' );

/**
 * Renders the shortcode [wlt-macronutrients-table]
 *
 * Basically displays the maintain / lose macronutrients table as shown on a user's record in admin
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_shortcode_macro_table($user_defined_arguments) {

	if( false === WS_LS_IS_PRO_PLUS ) {
		return '';
	}

	$arguments = shortcode_atts([	'css-class' 		=> '',
									'error-message' 	=> __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
									'user-id' 			=> false,
                                    'disable-jquery' 	=> false
								], $user_defined_arguments );

	$arguments[ 'user-id' ] 		= (int) $arguments['user-id'];
    $arguments[ 'disable-jquery' ] 	= ws_ls_force_bool_argument( $arguments[ 'disable-jquery' ] );

    // Include footable jQuery?
    if ( false === $arguments[ 'disable-jquery' ] ) {
        ws_ls_data_table_enqueue_scripts();
        $arguments[ 'css-class' ] .= ' ws-ls-footable';
    }

	return ws_ls_macro_render_table( $arguments[ 'user-id' ], $arguments[ 'error-message' ], $arguments[ 'css-class' ] );
}
add_shortcode( 'wlt-macronutrients-table', 'ws_ls_shortcode_macro_table' );
add_shortcode( 'wt-macronutrients-table', 'ws_ls_shortcode_macro_table' );

/**
 *
 * Validate the macro percentages
 *
 * @return bool
 */
function ws_ls_macro_validate_percentages() {

	$proteins   = ws_ls_harris_benedict_setting( 'ws-ls-macro-proteins' );
	$fats       = ws_ls_harris_benedict_setting( 'ws-ls-macro-fats' );
	$carbs      = ws_ls_harris_benedict_setting( 'ws-ls-macro-carbs' );

    // Is their sum 100 (i.e. 100%)
    return ( 100 === ( $proteins + $fats + $carbs ) );
}

/**
 *
 * Round a MacroN number
 *
 * @param $value
 * @return string
 */
function ws_ls_macro_round($value) {

	$macro_rounding = (int) apply_filters( 'wlt-filters-macros-rounding', 2 );

    return ws_ls_round_number( $value, $macro_rounding );
}

/**
 * Return Label for a macro key
 *
 * @param $key
 * @return mixed
 */
function ws_ls_get_macro_name( $key ) {

    $lookup = [ 'maintain' => __('Maintain', WE_LS_SLUG), 'lose' => __('Lose', WE_LS_SLUG), 'gain' => __('Gain', WE_LS_SLUG) ];

    $lookup = apply_filters( 'wlt-filter-macros-labels', $lookup );

    if ( true === array_key_exists( $key, $lookup ) ) {
        return $lookup[ $key ];
    }

    return $key;
}
