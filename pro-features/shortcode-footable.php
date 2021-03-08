<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render data table [wt-table]
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_shortcode_table( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

    $arguments = shortcode_atts( [  'enable-add-edit'       => false,
						            'enable-meta-fields'    => false,
						            'enable-custom-fields'  => false,
						            'custom-field-groups'   => '',      // If specified, only show custom fields that are within these groups
						            'custom-field-slugs'    => '',      // If specified, only show the custom fields that are specified
							        'bmi-format'            => 'label',
							        'week'                  => NULL,
						            'user-id'               => get_current_user_id() ], $user_defined_arguments );

    $arguments[ 'user-id' ]                 = (int) $arguments[ 'user-id' ];
	$arguments[ 'enable-add-edit' ]         = ws_ls_to_bool( $arguments[ 'enable-add-edit' ] );
    $arguments[ 'enable-meta-fields' ]      = ws_ls_to_bool( $arguments[ 'enable-meta-fields' ] );  // Kept for redundancy (replaced in 8.0)
	$arguments[ 'enable-custom-fields' ]    = ws_ls_to_bool( $arguments[ 'enable-custom-fields' ] );

	// Check, that the person logged in is the person that is wanting to do the editing.
	if( true === $arguments['enable-add-edit'] && get_current_user_id() !== $arguments['user-id'] ) {
		$arguments[ 'enable-add-edit' ] = false;
	}

	return ws_ls_data_table_render( [   'user-id'               => $arguments[ 'user-id' ],
	                                    'enable-add-edit'       => $arguments[ 'enable-add-edit' ],
	                                    'enable-meta-fields'    => $arguments[ 'enable-meta-fields' ] || $arguments[ 'enable-custom-fields' ],
	                                    'week'                  => $arguments[ 'week' ],
										'bmi-format'            => $arguments[ 'bmi-format' ],
										'custom-field-groups'   => $arguments[ 'custom-field-groups' ],
										'custom-field-slugs'    => $arguments[ 'custom-field-slugs' ],
	] );
}
add_shortcode( 'wlt-table', 'ws_ls_shortcode_table' );
add_shortcode( 'wt-table', 'ws_ls_shortcode_table' );
