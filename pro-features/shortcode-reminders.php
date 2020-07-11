<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Reminder shortcode
 * @param $user_defined_arguments
 * @param null $content
 *
 * @return mixed|string|void|null
 */
function ws_ls_shortcode_reminder($user_defined_arguments, $content = null) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	// If not logged in then return no value
	if( false === is_user_logged_in() ) {
		return '';
	}

	$message = '';

	$arguments = shortcode_atts([   'type'              => 'weight',    // Type of message:
																		// 		'weight' - check they have entered a weight for today.
																		// 		'target' - check they have entered a target weight
																		//		'both' - check if they have entered both
									'message'           => '',			// Custom message
									'additional_css'    => '',		    // Additional class for containing element
									'link'              => ''			// Wrap the message in a link
								], $user_defined_arguments );

	$target_required = ( true === in_array( $arguments[ 'type' ], [ 'target', 'both' ] ) && true === ws_ls_targets_enabled() && NULL == ws_ls_target_get( get_current_user_id() ) );
	$weight_required = ( true === in_array( $arguments[ 'type' ], [ 'weight', 'both' ] ) && !ws_ls_db_entry_for_date( get_current_user_id(), date('Y-m-d') ) );

	// Missing both?
	if ( 'both' == $arguments[ 'type' ] && $target_required && $weight_required ) {
		$message = __( 'Please remember to enter your weight for today as well as your target weight.', WE_LS_SLUG ) ;
	// Do they have a target weight?
	} else if ( 'target' == $arguments[ 'type' ]  && $target_required ) {
		$message = __( 'Please remember to enter your target weight.', WE_LS_SLUG );
	// Do they have a weight entry for today?
	} else if ( 'weight' == $arguments[ 'type' ] && $weight_required) {
		$message = __( 'Please remember to enter your weight for today.', WE_LS_SLUG ) ;
	}

	// Do we have a message to display?
	if( false === empty( $content ) && false === empty( $message ) ) {
		return $content;
	} else if( false === empty( $message ) ) {

		// Has a custom message been specified?
		$message = ( false === empty( $user_defined_arguments[ 'message' ] ) ) ? $user_defined_arguments[ 'message' ] : $message;

		// Escape
		$message = esc_html( $message );

		// Encase in a link?
		$message = ( false === empty( $arguments[ 'link' ] ) ) ? sprintf('<a href="%s">%s</a>', esc_url( $arguments[ 'link' ]) , $message ) : $message;

		$message = sprintf(
						'<div class="ws-ls-reminder ws-ls-alert-box ws-ls-info%s">%s</span></div>',
						! empty( $arguments[ 'additional_css' ] ) ? ' ' . esc_html( $arguments[ 'additional_css' ] ) : '',
						$message
					);
	}

	return $message;
}
add_shortcode( 'wlt-reminder', 'ws_ls_shortcode_reminder' );
add_shortcode( 'wt-reminder', 'ws_ls_shortcode_reminder' );
