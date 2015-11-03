<?php

defined('ABSPATH') or die("Jog on"); 

global $shortcode_number;

function ws_ls_shortcode_form($user_defined_arguments)
{
    global $shortcode_number;
    if(is_null($shortcode_number)) {
       $shortcode_number = 0; 
    }
    $shortcode_number++;
        
    if(!WS_LS_IS_PRO) {
       return false;
    }
    
    $form_arguments = shortcode_atts( 
        array(
            'user-id' => get_current_user_id(),
            'target' => false,
            'class' => false,
            'hide-titles' => false  
           ), $user_defined_arguments );
    
    // Argument validation
    if (!is_numeric($form_arguments['user-id'])) {
        $form_arguments['user-id'] = get_current_user_id();
    }

    $form_arguments['target-form'] = ws_ls_force_bool_argument($form_arguments['target-form']);
    $form_arguments['hide-titles'] = ws_ls_force_bool_argument($form_arguments['hide-titles']);

    $html_output .= ws_ls_display_weight_form($form_arguments['target'], $form_arguments['class'], $form_arguments['user-id'], $form_arguments['hide-titles'], $shortcode_number);
    
    return $html_output;

}
