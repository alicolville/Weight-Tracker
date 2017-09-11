<?php

    defined('ABSPATH') or die('Jog on!');

    function ws_ls_shortcode_table($user_defined_arguments) {

        if(!WS_LS_IS_PRO) {
            return false;
        }

        $arguments = shortcode_atts(
            array(
                'user-id' => get_current_user_id(),
				'enable-add-edit' => false
            ), $user_defined_arguments);

        $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
		$arguments['enable-add-edit'] = ws_ls_force_bool_argument($arguments['enable-add-edit']);

		// Check, that the person logged in is the person that is wanting to do the editing.
		if( get_current_user_id() !== intval($arguments['user-id']) && true === $arguments['enable-add-edit']) {
			$arguments['enable-add-edit'] = false;
		}

		return ws_ls_data_table_placeholder($arguments['user-id'], false, false, $arguments['enable-add-edit']);
    }