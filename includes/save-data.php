<?php
	defined('ABSPATH') or die('Jog on!');

global $form_save_perfomed_already;

function ws_ls_capture_and_handle_form_post($user_id = false, $form_number = false)
{ 
    global $form_save_perfomed_already;
  
    if(false == $user_id){
        $user_id = get_current_user_id();
    }
    
    $html_output = '';
    
    // Has a form save already been performed? If so, exit.
//    if ($form_save_perfomed_already === true) {
//        return '';
//    } 
    
    // If specified, only save this if same form number
    if ($form_number != false && (isset($_POST['ws_ls_form_number']) && intval($_POST['ws_ls_form_number']) != $form_number)) {
        return '';
    }
    
    // Capture and save HTML post?
    if ($_POST && isset($_POST['ws_ls_is_weight_form']) && 'true' == $_POST['ws_ls_is_weight_form']) {
      
            $target_form_post = (isset($_POST['ws_ls_is_target']) && 'true' == $_POST['ws_ls_is_target']) ? true : false;
            
            // If Target and Weight form exist on same page then we don't want to do two saves!
           // $save_data = ($target_form == $target_form_post || !$target_form == !$target_form_post) ? true : false ;
          var_dump($_POST);
           // if ($save_data) {
                
                $save_success = ws_ls_capture_form_validate_and_save($user_id);
                if ($save_success) {
                    $html_output .= '<blockquote class="ws-ls-blockquote ws-ls-success"><p>' . __('Saved!', WE_LS_SLUG) . '</p></blockquote>';
                } else {
                    $html_output .= '<blockquote class="ws-ls-blockquote ws-ls-error-text"><p>' . __('An error occurred while saving your data!', WE_LS_SLUG) . '</p></blockquote>';
                } 
           // }    
        
            $form_save_perfomed_already = true;
          
    }
    
    return $html_output;

}
// add_action('init', 'ws_ls_capture_and_handle_form_post');