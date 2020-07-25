<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Progress Bar shortcode
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_shortcode_progress_bar( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments = shortcode_atts ( [     'type'                  => 'line', 		// Type of progress bar: 'circle' / 'line'
										'display-errors'        => true,
										'stroke-width'          => 3,
										'stroke-colour'         => '#FFEA82',
										'trail-width'           => 1,
										'trail-colour'          => '#eee',
										'text-colour'           => '#000',
										'animation-duration'    => 1400,	    // Animation time in ms. Defaults to 1400
										'width'                 => '100%',		// % or pixels
										'height'                => '100%',		// % or pixels
										'percentage-text'       => __( 'towards your target of {t}.', WE_LS_SLUG ),
										'user-id'               => get_current_user_id()
								], $user_defined_arguments );

	$display_errors = ws_ls_to_bool( $arguments[ 'display-errors' ] );

	// Are targets enabled? If not, no point carrying on!
	if( false === ws_ls_targets_enabled() ) {
		return ws_ls_shortcode_progress_bar_display_error( __( 'This shortcode can not be used as Target weights have been disabled in the plugin\'s settings.', WE_LS_SLUG ), $display_errors );
	}

	$arguments[ 'target-weight' ] = ws_ls_target_get( $arguments[ 'user-id' ], 'kg' );

	if ( true === empty( $arguments[ 'target-weight' ] ) ) {
		return ws_ls_shortcode_progress_bar_display_error( __( 'Please enter a target weight to see your progress.', WE_LS_SLUG ), $display_errors );
	}

	$arguments[ 'weight' ] = ws_ls_entry_get_latest_kg();

	if ( true === empty( $arguments[ 'weight' ] ) ) {
		return ws_ls_shortcode_progress_bar_display_error( __( 'Please add a weight entry to see your progress.', WE_LS_SLUG ), $display_errors );
	}

	$arguments[ 'target-weight-display' ] = ws_ls_weight_display( $arguments[ 'target-weight' ], $arguments[ 'user-id' ], 'display' );

	// Width / Height specified for circle?
	$arguments[ 'width-height-specified' ]  = ( true === isset( $user_defined_arguments['width'] ) || true === isset( $user_defined_arguments['height'] ) );
	$arguments[ 'type' ]                    = ( true === in_array( $arguments[ 'type' ], [ 'circle', 'line' ] ) ) ? $arguments[ 'type' ] : 'line';
	$arguments['stroke-width']              = ws_ls_force_numeric_argument( $arguments[ 'stroke-width' ], 3 );
	$arguments['trail-width']               = ws_ls_force_numeric_argument( $arguments[ 'trail-width' ], 1 );
	$arguments['animation-duration']        = ws_ls_force_numeric_argument( $arguments[ 'animation-duration' ], 1400 );

	// If no width or height specified by user, then set circle to a better default size.
	if('circle' == $arguments['type'] && false == $arguments['width-height-specified']) {
		$arguments['width'] = '150px';
		$arguments['height'] = '150px';
	}

	$oldest_entry = ws_ls_entry_get_oldest_kg( $arguments[ 'user-id' ] );

	$arguments['start-weight'] = ( false === empty( $oldest_entry ) ) ? $oldest_entry : NULL;

	// -----------------------------------------------------
	// Aim to Gain weight?
	// -----------------------------------------------------
	if ( $arguments[ 'start-weight' ] < $arguments[ 'target-weight' ] ) {
		// Have we met or exceeded the target?
		if ( $arguments[ 'weight' ] >= $arguments[ 'target-weight' ] ) {

			$arguments[ 'progress' ] = 1.0;

			// Is recent weight less than target? If so, calulate %.
		} else if ( $arguments[ 'target-weight' ] >= $arguments[ 'weight' ] ) {

			$arguments[ 'weight-to-be-gained' ]     = abs($arguments[ 'target-weight' ] - $arguments[ 'start-weight' ] );
			$arguments[ 'weight-gained-so-far' ]    = $arguments[ 'weight' ] - $arguments[ 'start-weight' ];
			$arguments[ 'progress' ]                = ( $arguments[ 'weight-gained-so-far' ] > 0 ) ?
														( $arguments[ 'weight-gained-so-far' ] / $arguments[ 'weight-to-be-gained' ] ) * 100 :
															0;
		} else {
			$arguments[ 'progress' ] = 0;	// Error
		}
	} else {

		// -----------------------------------------------------
	// Aim to Lose weight?
	// -----------------------------------------------------
		// Have we met or exceeded the target?
		if ( $arguments[ 'weight' ] <= $arguments[ 'target-weight' ] ) {

				$arguments[ 'progress' ] = 1.0;

		// Is recent weight greater than target? If so, calulate %.
		} else if ( $arguments[ 'target-weight' ] <= $arguments[ 'weight' ] ) {

			$arguments[ 'weight-to-be-lost' ]   = abs( $arguments[ 'target-weight' ] - $arguments[ 'start-weight' ] );
			$arguments[ 'weight-lost-so-far' ]  = $arguments[ 'start-weight' ] - $arguments[ 'weight' ];
			$arguments[ 'progress' ]            = ( $arguments[ 'weight-lost-so-far' ] > 0 ) ?
														( $arguments[ 'weight-lost-so-far' ] / $arguments[ 'weight-to-be-lost' ] ) * 100 :
															0;
		} else {
			$arguments[ 'progress' ] = 0;	// Error
		}
	}

	// -----------------------------------------------------
	// Set Progress figure for chart library
	// -----------------------------------------------------
	if ( 1 === $arguments[ 'progress' ] ) {
		$arguments[ 'progress-chart' ] = 1;
	} else if ( $arguments[ 'progress' ] >= 100 ) {
		$arguments[ 'progress-chart' ] = 1;
	} else if ( $arguments[ 'progress' ] > 0) {
		$arguments[ 'progress-chart' ] = round($arguments['progress'] / 100, 2);
	} else {
		$arguments[ 'progress-chart' ] = 0;
	}

	// Render bar!
	return ws_ls_shortcode_progress_bar_render($arguments);

}
add_shortcode( 'wlt-progress-bar', 'ws_ls_shortcode_progress_bar' );
add_shortcode( 'wt-progress-bar', 'ws_ls_shortcode_progress_bar' );

/**
 * Show an error?
 * @param $text
 * @param $show
 *
 * @return string
 */
function ws_ls_shortcode_progress_bar_display_error( $text, $show ) {
	return ( true === $show ) ? $text : '';
}

/**
 * Render HTML for progress bar
 * @param $arguments
 *
 * @return string
 */
function ws_ls_shortcode_progress_bar_render( $arguments ) {

	if( true === empty( $arguments ) ) {
		return '';
	}

	ws_ls_enqueue_files();

	// Enqueue Progress library
	wp_enqueue_script('ws-ls-progress-bar', plugins_url( '../assets/js/libraries/progress-bar.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION );

	$arguments[ 'percentage-text' ] = str_replace('{t}', $arguments['target-weight-display'], $arguments[ 'percentage-text' ] );

	return sprintf('<div id="%s" class="ws-ls-progress" data-stroke-width="%s" data-stroke-colour="%s"
								data-trail-width="%s" data-trail-colour="%s" data-percentage-text="%s" data-text-colour="%s"
								data-animation-duration="%s" data-width="%s" data-height="%s" data-type="%s" data-progress="%s"></div>',
								ws_ls_component_id(),
								esc_attr( $arguments[ 'stroke-width' ] ),
								esc_attr( $arguments[ 'stroke-colour' ] ),
								esc_attr( $arguments[ 'trail-width' ] ),
								esc_attr( $arguments[ 'trail-colour' ] ),
								esc_attr( $arguments[ 'percentage-text' ] ),
								esc_attr( $arguments[ 'text-colour' ] ),
								esc_attr( $arguments[ 'animation-duration' ] ),
								esc_attr( $arguments[ 'width' ] ),
								esc_attr( $arguments[ 'height' ] ),
								esc_attr( $arguments[ 'type' ] ),
								esc_attr( $arguments[ 'progress-chart' ] )
	);
}
