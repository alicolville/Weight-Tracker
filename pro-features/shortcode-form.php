<?php

defined('ABSPATH') or die("Jog on");

global $form_number;

/**
 * Render [wt-form] form
 * @param $user_defined_arguments
 *
 * @return bool|mixed|string
 */
function ws_ls_shortcode_form( $user_defined_arguments ) {

    if( false === WS_LS_IS_PRO ) {
       return false;
    }

    $arguments = shortcode_atts( [     'user-id'            => get_current_user_id(),
                                       'target'             => false,
                                       'class'              => false,
								       'hide-titles'        => false,
								       'hide-notes'         => ws_ls_setting_hide_notes(),
								       'redirect-url'       => false,
								       'hide-measurements'  => false,
								       'hide-custom-fields' => false
    ], $user_defined_arguments );

    // Port shortcode arguments to core function
	$arguments[ 'css-class-form' ]      = $arguments[ 'class' ];
	$arguments[ 'is-target-form' ]      = ws_ls_to_bool( $arguments[ 'target' ] );
	$arguments[ 'hide-titles' ]         = ws_ls_to_bool( $arguments[ 'hide-titles' ] );
	$arguments[ 'hide-notes' ]          = ws_ls_to_bool( $arguments[ 'hide-notes' ] );
	$arguments[ 'hide-fields-meta' ]    = ( true === ws_ls_to_bool( $arguments[ 'hide-custom-fields' ] ) || true === ws_ls_to_bool( $arguments[ 'hide-measurements' ] ) );

	return ws_ls_form_weight( $arguments );

}
add_shortcode( 'wlt-form', 'ws_ls_shortcode_form' );
add_shortcode( 'wt-form', 'ws_ls_shortcode_form' );

/**
 * Render [wt-form-target] form
 * @param $user_defined_arguments
 *
 * @return bool|mixed|string
 */
function ws_ls_shortcode_target_form( $user_defined_arguments ) {

	if( false === WS_LS_IS_PRO ) {
		return false;
	}

	$arguments = shortcode_atts( [     	'user-id'            => get_current_user_id(),
										'class'              => false,
										'hide-titles'        => false,
										'hide-notes'         => ws_ls_setting_hide_notes(),
										'redirect-url'       => false,
										'hide-measurements'  => false,
										'hide-custom-fields' => false
	], $user_defined_arguments );

	$arguments[ 'target' ] = true;

	return ws_ls_shortcode_form( $arguments );

}
add_shortcode( 'wt-form-target', 'ws_ls_shortcode_target_form' );
