<?php

    defined('ABSPATH') or die('Jog on!');

    function ws_ls_shortcode_table($user_defined_arguments) {

        if(!WS_LS_IS_PRO) {
            return false;
        }

        $arguments = shortcode_atts(
            array(
                'user-id' => get_current_user_id()
            ), $user_defined_arguments);

        $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());

        ws_ls_data_table_placeholder($arguments['user-id']);

    }