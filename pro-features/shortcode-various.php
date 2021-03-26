<?php

defined('ABSPATH') or die("Jog on!");

/**
 *
 * Render the shortcode for previous weight [wlt-weight-previous]
 *
 * @param null $user_id
 *
 * @return string
 */
function ws_ls_shortcode_previous_weight( $user_id = NULL ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-previous-weight' ) ) {
		return $cache;
	}

	$previous_entry = ws_ls_entry_get_previous( $arguments );

	$output = ( false === empty( $previous_entry[ 'display' ] ) ) ?
		$previous_entry[ 'display' ] :
		'';

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-previous-weight', $output );

	return $output;
}
add_shortcode('wlt-weight-previous', 'ws_ls_shortcode_previous_weight');
add_shortcode('wt-previous-weight', 'ws_ls_shortcode_previous_weight');

/**
 *
 * Render the shortcode for previous date [wt-previous-date]
 *
 * @param null $user_id
 *
 * @return string
 */
function ws_ls_shortcode_previous_date( $user_id = NULL ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-previous-date' ) ) {
		return $cache;
	}

	$previous_entry = ws_ls_entry_get_previous( $arguments );

	$output = ( false === empty( $previous_entry[ 'display-date' ] ) ) ?
				$previous_entry[ 'display-date' ] :
					'';

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-previous-date', $output );

	return $output;
}
add_shortcode( 'wt-previous-date', 'ws_ls_shortcode_previous_date' );

/**
 * Render shortcode [wt-start-date]
 * @param bool $user_id
 *
 * @return string
 */
function ws_ls_shortcode_start_date( $user_id = NULL ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-start-date' ) ) {
		return $cache;
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	$oldest_entry = ws_ls_entry_get_oldest( $arguments );

	if( true === empty( $oldest_entry ) ) {
		return '';
	}

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-start-date', $oldest_entry[ 'display-date' ] );

	return $oldest_entry[ 'display-date' ];
}
add_shortcode( 'wt-start-date', 'ws_ls_shortcode_start_date' );

/**
 * Shortcode for [wt-difference-from-previous] - render weight difference between latest and previous entry
 * @param bool $user_id
 *
 * @return string|null
 */
function ws_ls_shortcode_weight_difference_previous( $user_id = false ){

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	// If not logged in then return no value
	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-difference-previous' ) ) {
		return $cache;
	}

	$latest_entry = ws_ls_entry_get_latest( $arguments );

	if ( true === empty( $latest_entry[ 'kg' ] ) ) {
		return '';
	}

	$previous_entry = ws_ls_entry_get_previous( $arguments );

	if ( true === empty( $previous_entry[ 'kg' ] ) ) {
		return '';
	}

	$difference = $latest_entry[ 'kg' ] - $previous_entry[ 'kg' ];
	$sign       = ( $difference > 0 ) ? '+' : '';
	$difference = ws_ls_weight_display( $difference, $arguments[ 'user-id' ], false, false, true );
	$output     = sprintf ('%s%s', $sign, $difference[ 'display' ] );

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-difference-previous', $output );

	return $output;
}
add_shortcode('wlt-weight-difference-previous', 'ws_ls_shortcode_weight_difference_previous' );
add_shortcode('wt-difference-from-previous', 'ws_ls_shortcode_weight_difference_previous' );

/**
 * Render BMI shortcode [wt-bmi]
 *
 * @param array $arguments
 *
 * @return bool|mixed|string|null
 */
function ws_ls_shortcode_bmi( $arguments = [] ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	// If not logged in then return no value
	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments = shortcode_atts( [      'display'           => 'index',                             // 'index' - Actual BMI value. 'label' - BMI label for given value. 'both' - Label and BMI value in brackets,
										'no-height-text'    => __( 'Height needed', WE_LS_SLUG ),
										'user-id'           => get_current_user_id()
						           ], $arguments );

	$cache_key = ws_ls_cache_generate_key_from_array( 'shortcode-bmi', $arguments );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], $cache_key ) ) {
		return $cache;
	}

	$kg = ws_ls_entry_get_latest_kg( $arguments['user-id'] );
	$cm = ws_ls_user_preferences_get( 'height', $arguments['user-id']);

	// Don't attempt to calculate BMI if no weight entries!
	if( true === empty( $kg ) ) {
		return ws_ls_cache_user_set_and_return( $arguments['user-id'], $cache_key, __( 'Weight needed', WE_LS_SLUG ) );
	}

	// Do we have a height for user?
	if( true === empty( $cm ) ) {
		return ws_ls_cache_user_set_and_return( $arguments['user-id'], $cache_key, esc_html( $arguments['no-height-text'] ) );
	}

	$bmi    = ws_ls_calculate_bmi( $cm, $kg );
	$output = ws_ls_bmi_display( $bmi, $arguments['display'] );

	return ws_ls_cache_user_set_and_return( $arguments['user-id'], $cache_key, $output );
}
add_shortcode( 'wlt-bmi', 'ws_ls_shortcode_bmi' );
add_shortcode( 'wt-bmi', 'ws_ls_shortcode_bmi' );

/**
 *
 * Shortcode to render the user's activity level
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_activity_level( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'not-specified-text'    => __( 'Not Specified', WE_LS_SLUG ),
									'user-id'               => get_current_user_id(),
									'shorten'               => false
								], $user_defined_arguments );

	$arguments[ 'shorten' ] = ws_ls_to_bool( $arguments[ 'shorten' ] );
	$arguments[ 'field' ]   = 'activity_level';

	return ws_ls_user_preferences_display( $arguments );
}
add_shortcode( 'wlt-activity-level', 'ws_ls_shortcode_activity_level' );
add_shortcode( 'wt-activity-level', 'ws_ls_shortcode_activity_level' );

/**
 *
 * Shortcode to render the user's gender
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_gender( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'not-specified-text'    => __( 'Not Specified', WE_LS_SLUG ),
	                                'user-id'               => get_current_user_id(),
	                                'shorten'               => false
	], $user_defined_arguments );

	$arguments[ 'shorten' ] = ws_ls_to_bool( $arguments[ 'shorten' ] );
	$arguments[ 'field' ]   = 'gender';

	return ws_ls_user_preferences_display( $arguments );
}
add_shortcode( 'wlt-gender', 'ws_ls_shortcode_gender' );
add_shortcode( 'wt-gender', 'ws_ls_shortcode_gender' );

/**
 *
 * Shortcode to render the user's Date of Birth
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_dob( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [ 'not-specified-text' => __( 'Not Specified', WE_LS_SLUG ), 'user-id' => get_current_user_id() ], $user_defined_arguments );

	$cache_key = ws_ls_cache_generate_key_from_array( 'shortcode-dob', $arguments );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], $cache_key ) ) {
		return $cache;
	}

	$output = ws_ls_get_dob_for_display( $arguments['user-id'], $arguments['not-specified-text'] );

	return ws_ls_cache_user_set_and_return(  $arguments['user-id'], $cache_key, $output );
}
add_shortcode( 'wlt-dob', 'ws_ls_shortcode_dob' );
add_shortcode( 'wt-dob', 'ws_ls_shortcode_dob' );

/**
 *
 * Shortcode to render the user's Height
 *
 * @param $user_defined_arguments an array of arguments passed in via shortcode
 * @return string - HTML to be sent to browser
 */
function ws_ls_shortcode_height( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'not-specified-text'    => __( 'Not Specified', WE_LS_SLUG ),
	                                'user-id'               => get_current_user_id()
	], $user_defined_arguments );

	$arguments[ 'field' ]   = 'height';

	$cache_key = ws_ls_cache_generate_key_from_array( 'shortcode-height', $arguments );

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], $cache_key ) ) {
		return $cache;
	}

	$output = ws_ls_user_preferences_display( $arguments );

	return ws_ls_cache_user_set_and_return(  $arguments['user-id'], $cache_key, $output );
}
add_shortcode( 'wlt-height', 'ws_ls_shortcode_height' );
add_shortcode( 'wt-height', 'ws_ls_shortcode_height' );

/**
 * Shortcode to render the number of WordPress users in last x days
 *
 * Args:    "days" (number of days to look back)
 *          "count-all-roles" - by default false and only count user's with a role of Subscriber. Set to true to count everyone.
 *
 * @param $user_defined_arguments
 * @return mixed
 */
function ws_ls_shortcode_new_users( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

    $arguments = shortcode_atts( [ 'days' => 7, 'count-all-roles' => false ], $user_defined_arguments );

    $arguments[ 'days' ]                = ws_ls_force_numeric_argument( $arguments[ 'days' ], 7 );
    $arguments[ 'count-all-roles' ]     = ws_ls_to_bool( $arguments[ 'count-all-roles' ] );

    // Ensure no. days greater than or equal to 1
    $arguments[ 'days' ] = ( $arguments[ 'days' ] < 1 ) ? 1 : $arguments[ 'days' ];

	$cache_key = ws_ls_cache_generate_key_from_array( 'shortcode-new-users', $arguments );

	if ( $cache = ws_ls_cache_user_get( 'new-users', $cache_key ) ) {
		return $cache;
	}

    // Build from date
    $from_date = strtotime ( "-{$arguments['days']} day" ) ;
    $from_date = date ( 'Y-m-d H:i:s' , $from_date );

    $wp_search_query = [
					        'date_query'    => [
										            [
										                'after'     => $from_date,
										                'inclusive' => true,
										            ],
						    ],
					        'fields' => 'ID'
    ];

    // Do we want to count all user roles within WordPress or subscribers (most likely) only
    if ( false === $arguments[ 'count-all-roles' ] ) {
        $wp_search_query[ 'role' ] = 'subscriber';
    }

    $user_query = new WP_User_Query( $wp_search_query );
    $output     = esc_html( $user_query->get_total() );

    return ws_ls_cache_user_set_and_return( 'new-users', $cache_key, $output );
}
add_shortcode( 'wlt-new-users', 'ws_ls_shortcode_new_users' );
add_shortcode( 'wt-new-users', 'ws_ls_shortcode_new_users' );

/**
 * Render shortcode [wt-latest-date]
 * @param bool $user_id
 *
 * @return string
 */
function ws_ls_shortcode_recent_date( $user_id = NULL ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-latest-date' ) ) {
		return $cache;
	}

	$latest_entry = ws_ls_entry_get_latest( $arguments );

	if( true === empty( $latest_entry ) ) {
		return '';
	}

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-latest-date', $latest_entry[ 'display-date' ] );

	return $latest_entry[ 'display-date' ];
}
add_shortcode( 'wt-latest-date', 'ws_ls_shortcode_recent_date' );

/**
 * Render shortcode [wt-days-between-start-and-latest]
 *
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_shortcode_days_between_start_and_latest( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [ 'user-id' => get_current_user_id(), 'include-brackets' => false, 'include-days' => false ], $user_defined_arguments );

	if( false === is_user_logged_in() ) {
		return '';
	}

	$latest_entry = ws_ls_entry_get_latest( $arguments );

	if ( true === empty( $latest_entry[ 'raw' ] ) ) {
		return '';
	}

	$oldest_entry = ws_ls_entry_get_oldest( $arguments );

	if ( true === empty( $oldest_entry[ 'raw' ] ) ) {
		return '';
	}


	$latest_entry   = date_create( $latest_entry[ 'raw' ] );
	$oldest_entry   = date_create( $oldest_entry[ 'raw' ] );
	$difference     = date_diff( $latest_entry, $oldest_entry, true);

	if ( true === empty( $difference->days ) ) {
		return '';
	}

	$text = ( true === ws_ls_to_bool( $arguments[ 'include-days' ] ) ) ?
				sprintf( '%d %s', $difference->days, __( 'days', WE_LS_SLUG ) ) :
					$difference->days;

	return ( true === ws_ls_to_bool( $arguments[ 'include-brackets' ] ) ) ?
				sprintf( '(%s)', $text ) :
					$text;
}
add_shortcode( 'wt-days-between-start-and-latest', 'ws_ls_shortcode_days_between_start_and_latest' );
