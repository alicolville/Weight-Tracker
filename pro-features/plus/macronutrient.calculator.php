<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_macro_calculate($user_id = false) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    // Data cached?
    $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_MACRO;

    if ($cache = ws_ls_get_cache($cache_key)) {
       return $cache;
    }

    $calories = ws_ls_harris_benedict_calculate_calories($user_id);

    $macros = apply_filters( 'wlt-filter-macros-custom', [], $calories);

    // If a 3rd party plugin has specified macros then no point carrying on below!
    if ( false === empty( $macros ) ) {
        return $macros;
    }

    if (true === isset($calories['lose']['total'], $calories['maintain']['total'])) {

        foreach (['maintain', 'lose'] as $key) {

            $macros[$key]['calories'] = $calories[$key]['total'];

			$protein_calc = WS_LS_MACRO_PROTEINS / 100;
			$carbs_calc = WS_LS_MACRO_CARBS / 100;
			$fats_calc = WS_LS_MACRO_FATS / 100;

            // Total
            $macros[$key]['total']['protein'] = ($macros[$key]['calories'] * $protein_calc) / 4;
            $macros[$key]['total']['carbs'] = ($macros[$key]['calories'] * $carbs_calc) / 4;
            $macros[$key]['total']['fats'] = ($macros[$key]['calories'] * $fats_calc) / 9;

            // Allow 3rd parties to filter macro nutrient totals
            $macros[$key]['total'] = apply_filters( 'wlt-filter-macros-' . $key . '-total', $macros[$key]['total'], $key, $calories );

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

            $macros[$key] = apply_filters( 'wlt-filter-macros-' . $key, $macros[$key], $calories );
        }

    } else {
        return NULL;
    }

	$macros = apply_filters( 'wlt-filter-macros', $macros, $calories );

    // Cache it!
    ws_ls_set_cache($cache_key, $macros);

    return $macros;
}

/**
 * Render MacroN table
 *
 * @param $user_id
 * @param bool $missing_data_text
 * @return string
 */
function ws_ls_macro_render_table($user_id, $missing_data_text = false, $additional_css_class = '')
{

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    $macros = ws_ls_macro_calculate($user_id);

    $missing_data_text = (false === $missing_data_text) ? __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG) : $missing_data_text;

    if (false === empty($macros)) {

        $html = sprintf('<table class="%sws-ls-macro%s"  >',
			(false === empty($additional_css_class)) ? esc_attr($additional_css_class) . ' ' : '',
				false === is_admin() ? '' : ' widefat');

        foreach (['lose', 'maintain'] as $key) {

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
				('maintain' == $key) ? __('Maintain', WE_LS_SLUG) : __('Lose', WE_LS_SLUG),
                number_format($macros[$key]['calories']),
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
 */
function ws_ls_shortcode_macro($user_defined_arguments) {

	if(false === WS_LS_IS_PRO_PLUS) {
		return;
	}

	$arguments = shortcode_atts([
									'error-message' => __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG ),
									'user-id' => false,
									'progress' => 'maintain',	// 'maintain', 'lose'
									'nutrient' => 'fats', 		// 'fats', 'protein', 'carbs'
									'type' => 'lunch'			// 'breakfast', 'lunch', 'dinner', 'snack', 'total'
								], $user_defined_arguments );

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);
	$progress = (false === in_array($arguments['progress'], ['maintain', 'lose'])) ? 'maintain' : $arguments['progress'];
	$type = (false === in_array($arguments['type'], ['breakfast', 'lunch', 'dinner', 'snacks', 'total'])) ? 'lunch' : $arguments['type'];
	$nutrient = (false === in_array($arguments['nutrient'], ['fats', 'protein', 'carbs'])) ? 'fats' : $arguments['nutrient'];

	$macros = ws_ls_macro_calculate($arguments['user-id']);

	// No macro data?
    if(true === empty($macros) && false === empty($arguments['error-message'])) {
        return '<p>' . esc_html( $arguments['error-message'] ) . '</p>';
    }

	$display_value = (false === empty($macros[$progress][$type][$nutrient])) ? number_format($macros[$progress][$type][$nutrient], 2) : '' ;

	return esc_html($display_value);
}
add_shortcode( 'wlt-macronutrients', 'ws_ls_shortcode_macro' );

/**
 * Renders the shortcode [wlt-macronutrients-table]
 *
 * Basically displays the maintain / lose macronutrients table as shown on a user's record in admin
 *
 * @param $user_defined_arguments
 */
function ws_ls_shortcode_macro_table($user_defined_arguments) {

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

	return ws_ls_macro_render_table($arguments['user-id'], $arguments['error-message'], $arguments['css-class']);
}
add_shortcode( 'wlt-macronutrients-table', 'ws_ls_shortcode_macro_table' );

/**
 *
 * Validate the macro percentages
 *
 * @return bool
 */
function ws_ls_macro_validate_percentages()
{

    // All numeric?
    if (false === is_numeric(WS_LS_MACRO_PROTEINS) ||
        false === is_numeric(WS_LS_MACRO_CARBS) ||
        false === is_numeric(WS_LS_MACRO_FATS)
    ) {
        return false;
    }

    // Is their sum 100 (i.e. 100%)
    return (100 == (WS_LS_MACRO_PROTEINS + WS_LS_MACRO_CARBS + WS_LS_MACRO_FATS)) ? true : false;
}

/**
 *
 * Round a MacroN number
 *
 * @param $value
 * @return string
 */
function ws_ls_macro_round($value) {
    return number_format($value, 2);
}
