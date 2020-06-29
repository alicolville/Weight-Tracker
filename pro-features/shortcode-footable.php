<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_shortcode_table($user_defined_arguments) {

    if(!WS_LS_IS_PRO) {
        return false;
    }

    $arguments = shortcode_atts(
        array(
            'user-id' => get_current_user_id(),
			'enable-add-edit' => false,
            'enable-meta-fields' => false,
	        'week' => NULL
        ), $user_defined_arguments);

    $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
	$arguments['enable-add-edit'] = ws_ls_force_bool_argument($arguments['enable-add-edit']);
    $arguments['enable-meta-fields'] = ws_ls_to_bool($arguments['enable-meta-fields']);

	// Check, that the person logged in is the person that is wanting to do the editing.
	if( get_current_user_id() !== (int) $arguments['user-id'] && true === $arguments['enable-add-edit']) {
		$arguments['enable-add-edit'] = false;
	}

	return ws_ls_data_table_render( [ 'user-id' => $arguments['user-id'], 'enable-add-edit' => $arguments['enable-add-edit'], 'enable-meta-fields' => $arguments['enable-meta-fields'], 'week' => $arguments['week'] ] );
}
add_shortcode( 'wlt-table', 'ws_ls_shortcode_table' );
add_shortcode( 'wt-table', 'ws_ls_shortcode_table' );
