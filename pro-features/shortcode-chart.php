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

	if ( false === WS_LS_IS_PREMIUM ) {
		return false;
	}

	$chart_arguments = shortcode_atts( [
											'bezier'              	        => ws_ls_option_to_bool( 'ws-ls-bezier-curve' ),
											'height'              	        => 250,
											'ignore-login-status' 	        => false,
											'message-no-data'               => esc_html__( 'Currently there is no data to display on the chart.', WE_LS_SLUG ),
											'max-data-points'     	        => ws_ls_option( 'ws-ls-max-points', '25', true ),
											'show-gridlines'      	        => ws_ls_option_to_bool( 'ws-ls-grid-lines' ),
											'show-custom-fields'  	        => true,
											'show-weight'                   => true,
											'show-target'                   => true,
											'type'                	        => get_option( 'ws-ls-chart-type', 'line' ),
											'user-id'            	        => get_current_user_id(),
											'weight-fill-color'   	        => get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ),
											'weight-line-color'   	        => get_option( 'ws-ls-line-colour', '#aeaeae' ),
											'weight-target-color' 	        => get_option( 'ws-ls-target-colour', '#76bada' ),
											'reverse'				        => true,
											'custom-field-restrict-rows'    => '',      // Only fetch entries that have either all custom fields completed (all), one or more (any) or leave blank if not concerned.
											'custom-field-groups'           => '',      // If specified, only show custom fields that are within these groups
											'custom-field-slugs'            => '',      // If specified, only show the custom fields that are specified
	], $user_defined_arguments );

	// Make sure they are logged in
	if ( false === ws_ls_to_bool( $chart_arguments['ignore-login-status'] ) &&
	     false === is_user_logged_in() ) {
		return ws_ls_blockquote_login_prompt();
	}

	$chart_arguments['height']                      = (int) $chart_arguments['height'];
	$chart_arguments[ 'custom-field-value-exists' ] = [];


	// Validate height and ensure height is not below 50
	if ( $chart_arguments['height'] < 50 ) {
		$chart_arguments['height'] = 250;
	}

	// Ensure we have at least two data points
	$chart_arguments[ 'max-data-points' ] = (int) $chart_arguments[ 'max-data-points' ];

	if ( $chart_arguments['max-data-points'] < 2 ) {
		$chart_arguments['max-data-points'] = ws_ls_option( 'ws-ls-max-points', '25', true );
	}

	// Do we need to restrict which database rows we fetch from the database?
	if ( false === empty( $chart_arguments[ 'custom-field-restrict-rows' ] ) ) {
		$chart_arguments[ 'custom-field-value-exists' ] = ws_ls_meta_fields_slugs_and_groups_to_id( $chart_arguments ) ;
	}

	// Fetch data for chart
	$weight_data = ws_ls_entries_get( [	    'user-id'                       => $chart_arguments['user-id'],
	                                        'limit'                         => $chart_arguments['max-data-points'],
	                                        'prep'                          => true,
	                                        'sort'                          => 'desc',
	                                        'custom-field-value-exists'     => $chart_arguments[ 'custom-field-value-exists' ],
											'custom-field-restrict-rows'    => $chart_arguments[ 'custom-field-restrict-rows' ] ]);

	// Reverse array so in cron order
	if ( true === empty( $weight_data ) ) {
		return ws_ls_display_blockquote( $chart_arguments[ 'message-no-data' ] );
	}

	$chart_arguments[ 'show-meta-fields' ] = $chart_arguments[ 'show-custom-fields' ];

	return ws_ls_display_chart( $weight_data, $chart_arguments );
}
add_shortcode( 'wlt-chart', 'ws_ls_shortcode_chart' );
add_shortcode( 'wt-chart', 'ws_ls_shortcode_chart' );
