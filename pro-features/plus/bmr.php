<?php

defined('ABSPATH') or die("Jog on!");

/**
 *
 * Calculate a user's BMR for their given attributes (entered by user in preferences)
 *
 * Based upon documentation at:
 * http://www.diabetes.co.uk/bmr-calculator.html
 * http://www.bmi-calculator.net/bmr-calculator/metric-bmr-calculator.php#result
 *
 * @param bool $user_id
 * @param bool $return_error
 * @return float|null
 */
function ws_ls_calculate_bmr( $user_id = false, $return_error = true ) {

    if( false === WS_LS_IS_PRO_PLUS ) {
	    return '';
    }

    $user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	// Do we have BMR cached?
    if ( $cache = ws_ls_cache_user_get( $user_id, 'bmr' ) ) {
        return ws_ls_round_bmr_harris( $cache );
    }

    // First, we need to ensure the person has a gender.
    $gender = ws_ls_user_preferences_get('gender', $user_id );

    if( true === empty( $gender ) ) {
        return ( $return_error ) ? __('No Gender specified', WE_LS_SLUG) : NULL;
    }

    // Check if user has DOB - calculate age
    $age = ws_ls_user_get_age_from_dob( $user_id );

    if( true === empty( $age ) ) {
        return ( $return_error ) ? __('No Date of Birth specified or too young', WE_LS_SLUG ) : NULL;
    }

    //Get height
    $height = ws_ls_user_preferences_get('height', $user_id);

    if( true === empty( $height ) ) {
        return ( $return_error ) ? __( 'No Height specified', WE_LS_SLUG ) : NULL;
    }

    // Recent weight?
    $weight = ws_ls_entry_get_latest_kg($user_id);

	$weight = apply_filters( 'wlt_filters_bmr_weight_raw', $weight, $user_id );

    if( true === empty( $weight ) ) {
        return ( $return_error ) ? __('No Weight entered', WE_LS_SLUG) : NULL;
    }

    $bmr = ws_ls_calculate_bmr_raw( $gender, $weight, $height, $age, $user_id );

	ws_ls_cache_user_set( $user_id, 'bmr', $bmr );

    return $bmr;
}

/**
 * Calculate BMR
 *
 * @param $gender
 * @param $weight
 * @param $height
 * @param $age
 * @param bool $user_id
 *
 * @return float
 */
function ws_ls_calculate_bmr_raw( $gender, $weight, $height, $age, $user_id = false ) {

    $bmr = NULL;

    // Calculate BMR based on gender
    if ( 1 === (int) $gender ) {
	    // Female:  655.1 + (9.6 * weight [kg]) + (1.8 * size [cm]) − (4.7 * age [years])
	    $bmr = 655.1 + ( 9.6 * $weight ) + ( 1.8 * $height ) - ( 4.7 * $age );
    } else {
	    // Male:    66.47 + (13.7 * weight [kg]) + (5 * size [cm]) − (6.8 * age [years])
	    $bmr = 66 + ( 13.7 * $weight ) + ( 5 * $height ) - ( 6.8 * $age );
    }

    $bmr = apply_filters( 'wlt-filter-bmr-calculation', $bmr, $gender, $weight, $height, $age, $user_id );

    return ws_ls_round_bmr_harris( $bmr );
}

/**
 * [wlt-bmr] - Shortcode to render a user's BMR
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_shortcode_bmr( $user_defined_arguments ) {

    if( false === WS_LS_IS_PRO_PLUS ) {
        return '';
    }

    $arguments = shortcode_atts([
                                    'suppress-errors' => false,      // If true, don't display errors from ws_ls_calculate_bmr()
                                    'user-id' => false
                                ], $user_defined_arguments );

    $arguments['suppress-errors'] = ws_ls_force_bool_argument($arguments['suppress-errors']);
    $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id']);

    $bmr = ws_ls_calculate_bmr($arguments['user-id']);

    return (false === is_numeric($bmr) && $arguments['suppress-errors']) ? '' : esc_html($bmr);
}
add_shortcode( 'wlt-bmr', 'ws_ls_shortcode_bmr' );
add_shortcode( 'wt-bmr', 'ws_ls_shortcode_bmr' );

/**
 * Round BMR / Harris Benedict
 *
 * @param $value
 * @return float $value
 */
function ws_ls_round_bmr_harris( $value ) {
	return ( true === is_numeric( $value ) ) ? round( $value ) : $value;
}
