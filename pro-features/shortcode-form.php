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

    $arguments = shortcode_atts( [     'user-id'           => get_current_user_id(),
                                       'target'            => false,
                                       'class'             => false,
								       'hide-titles'       => false,
								       'redirect-url'      => false,
								       'hide-measurements' => false,
								       'hide-meta'         => false
    ], $user_defined_arguments );

    // Port shortcode arguments to core function
	$arguments[ 'css-class-form' ]      = $arguments[ 'class' ];
	$arguments[ 'is-target-form' ]      = ws_ls_to_bool( $arguments[ 'target' ] );
	$arguments[ 'hide-titles' ]         = ws_ls_to_bool( $arguments[ 'hide-titles' ] );
	$arguments[ 'hide-fields-meta' ]    = ( true === ws_ls_to_bool( $arguments[ 'hide-meta' ] ) || true === ws_ls_to_bool( $arguments[ 'hide-measurements' ] ) );

	return ws_ls_form_weight( $arguments );

}
add_shortcode( 'wlt-form', 'ws_ls_shortcode_form' );
add_shortcode( 'wt-form', 'ws_ls_shortcode_form' );
