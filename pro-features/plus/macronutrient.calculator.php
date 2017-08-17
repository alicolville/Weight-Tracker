<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_macro_calculate($user_id = false)
{

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    // Data cached?
    $cache_key = $user_id . '-' . WE_LS_CACHE_KEY_MACRO;

    if ($cache = ws_ls_get_cache($cache_key)) {
       // return $cache;
    }

    $macros = [];
    $calories = ws_ls_harris_benedict_calculate_calories($user_id);

    if (true === isset($calories['lose']['total'], $calories['maintain']['total'])) {

        foreach (['maintain', 'lose'] as $key) {

            $macros[$key]['calories'] = $calories[$key]['total'];

            // Total
            $macros[$key]['total']['protein'] = ($macros[$key]['calories'] * WS_LS_MACRO_PROTEINS) / 4;
            $macros[$key]['total']['carbs'] = ($macros[$key]['calories'] * WS_LS_MACRO_CARBS) / 4;
            $macros[$key]['total']['fats'] = ($macros[$key]['calories'] * WS_LS_MACRO_FATS) / 9;

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
        }

    } else {
        return NULL;
    }

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
function ws_ls_macro_render_table($user_id, $missing_data_text = false)
{

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    $macros = ws_ls_macro_calculate($user_id);

    $missing_data_text = (false === $missing_data_text) ? __('Please ensure all relevant data to calculate calorie intake has been entered i.e. Activity Level, Date of Birth, Current Weight, Gender and Height.', WE_LS_SLUG) : $missing_data_text;

    if (false === empty($macros)) {

        $html = sprintf('<table class="ws-ls-macro%s">', false === is_admin() ? '' : ' widefat');

        foreach (['lose', 'maintain'] as $key) {

            // Table Header
            $html .= sprintf('<thead>
                                <tr>
                                    <th class="row-title">%s (%s)</th>
                                    <th>%s</th>
                                    <th>%s</th>
                                    <th>%s</th>
                                    <th>%s</th>
                                    <th>%s</th>
                                </tr>
                             </thead>',
                ('maintain' == $key) ? __('Maintain', WE_LS_SLUG) : __('Lose', WE_LS_SLUG),
                number_format($macros[$key]['calories']),
                __('Total', WE_LS_SLUG),
                __('Breakfast', WE_LS_SLUG),
                __('Lunch', WE_LS_SLUG),
                __('Dinner', WE_LS_SLUG),
                __('Snacks', WE_LS_SLUG)
            );


            // Maintain
            $html .= sprintf('      <tr valign="top">
                                    <td><strong>%s</strong></td>
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

            // Maintain
            $html .= sprintf('     <tr valign="top" class="alternate">
                                    <td><strong>%s</strong></td>
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
            $html .= sprintf('     <tr valign="top">
                                    <td><strong>%s</strong></td>
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
    return (1 == (WS_LS_MACRO_PROTEINS + WS_LS_MACRO_CARBS + WS_LS_MACRO_FATS)) ? true : false;
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