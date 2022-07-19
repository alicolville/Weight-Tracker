<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render data table [wt-table]
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_shortcode_table( $user_defined_arguments ) {

    $arguments = shortcode_atts( [  'enable-add-edit'               => false,
	                                'weight-mandatory'              => true,
                                    'enable-bmi'                    => true,
                                    'enable-notes'                  => true,
                                    'enable-weight'                 => true,
						            'enable-meta-fields'            => false,
						            'enable-custom-fields'          => false,
						            'custom-field-restrict-rows'    => '',      // Only fetch entries that have either all custom fields completed (all), one or more (any) or leave blank if not concerned.
						            'custom-field-groups'           => '',      // If specified, only show custom fields that are within these groups
						            'custom-field-slugs'            => '',      // If specified, only show the custom fields that are specified
	                                'custom-field-col-size'         => '',
							        'bmi-format'                    => 'label',
							        'week'                          => NULL,
						            'user-id'                       => get_current_user_id(),
                                    'uikit'                         => false,
	                                'kiosk-mode'                    => false,
	                                'show-refresh-button'           => false
    ], $user_defined_arguments );

    $arguments[ 'user-id' ]                 = (int) $arguments[ 'user-id' ];
	$arguments[ 'enable-add-edit' ]         = ws_ls_to_bool( $arguments[ 'enable-add-edit' ] );
    $arguments[ 'enable-meta-fields' ]      = ws_ls_to_bool( $arguments[ 'enable-meta-fields' ] );  // Kept for redundancy (replaced in 8.0)
	$arguments[ 'enable-custom-fields' ]    = ws_ls_to_bool( $arguments[ 'enable-custom-fields' ] );

	// Check, that the person logged in is the person that is wanting to do the editing.
	if( false === $arguments[ 'kiosk-mode'] &&
	        true === $arguments['enable-add-edit'] && get_current_user_id() !== $arguments['user-id'] ) {
		$arguments[ 'enable-add-edit' ] = false;
	}

	$arguments[ 'enable-meta-fields' ] = $arguments[ 'enable-meta-fields' ] || $arguments[ 'enable-custom-fields' ];

	return ws_ls_data_table_render( $arguments );
}
add_shortcode( 'wlt-table', 'ws_ls_shortcode_table' );
add_shortcode( 'wt-table', 'ws_ls_shortcode_table' );
