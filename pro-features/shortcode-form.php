<?php

defined('ABSPATH') or die("Jog on");

global $form_number;

function ws_ls_shortcode_form($user_defined_arguments)
{

    global $form_number;

  	ws_ls_enqueue_files();

    if(is_null($form_number)){
      $form_number = 1;
    } else {
      $form_number++;
    }

    if(!WS_LS_IS_PRO) {
       return false;
    }

    $form_arguments = shortcode_atts(
        array(
            'user-id' => get_current_user_id(),
            'target' => false,
            'class' => false,
            'hide-titles' => false,
            'redirect-url' => false,
			'hide-measurements' => false
           ), $user_defined_arguments );

    // Argument validation
    if (!is_numeric($form_arguments['user-id'])) {
        $form_arguments['user-id'] = get_current_user_id();
    }

	// Ensure certain arguments are booleans
	foreach (['hide-measurements', 'hide-titles', 'target'] as $key) {
		$form_arguments[$key] = ws_ls_force_bool_argument($form_arguments[$key]);
	}

    return ws_ls_display_weight_form($form_arguments['target'], $form_arguments['class'], $form_arguments['user-id'], $form_arguments['hide-titles'],
                                        $form_number, false, true, $form_arguments['hide-measurements'], $form_arguments['redirect-url']);

}
