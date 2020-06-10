<?php

defined( 'ABSPATH' ) or die( "Jog on!" );

/**
 * Render the [wlt-chart] shortcode
 *
 * @param $user_defined_arguments
 *
 * @return bool|string
 */
function ws_ls_shortcode_chart( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	$chart_arguments = shortcode_atts( [
											'bezier'              => ws_ls_option_to_bool( 'ws-ls-bezier-curve' ),
											'height'              => 250,
											'ignore-login-status' => false,
											'max-data-points'     => ws_ls_option( 'ws-ls-max-points', '25', true ),
											'show-gridlines'      => ws_ls_option_to_bool( 'ws-ls-grid-lines' ),
											'show-meta-fields'    => true,
											'type'                => get_option( 'ws-ls-chart-type', 'line' ),
											'user-id'             => get_current_user_id(),
											'weight-fill-color'   => get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ),
											'weight-line-color'   => get_option( 'ws-ls-line-colour', '#aeaeae' ),
											'weight-target-color' => get_option( 'ws-ls-target-colour', '#76bada' )
	], $user_defined_arguments );

	// Make sure they are logged in
	if ( false === ws_ls_to_bool( $chart_arguments['ignore-login-status'] ) &&
	     false === is_user_logged_in() ) {
		return ws_ls_blockquote_login_prompt();
	}

	$chart_arguments['height'] = (int) $chart_arguments['height'];

	// Validate height and ensure height is not below 50
	if ( $chart_arguments['height'] < 50 ) {
		$chart_arguments['height'] = 250;
	}
	$chart_arguments['max-data-points'] = 0;

	// Ensure we have at least two data points
	$chart_arguments[ 'max-data-points' ] = (int) $chart_arguments[ 'max-data-points' ];

	if ( $chart_arguments['max-data-points'] < 2 ) {
		$chart_arguments['max-data-points'] = ws_ls_option( 'ws-ls-max-points', '25', true );
	}
	// Fetch data for chart
	$weight_data = ws_ls_db_weights_get( [ 'user-id' => $chart_arguments['user-id'], 'limit' => $chart_arguments['max-data-points'], 'prep' => true ] );

	// Reverse array so in cron order
	if ( true === empty( $weight_data ) ) {
		return ws_ls_display_blockquote( __( 'No data could be found for the user.', WE_LS_SLUG ) );
	}

	$weight_data = array_reverse( $weight_data );

	return ws_ls_display_chart( $weight_data, $chart_arguments );
}
add_shortcode( 'wlt-chart', 'ws_ls_shortcode_chart' );
