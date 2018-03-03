<?php
	defined('ABSPATH') or die('Jog on!');

global $save_response;

function ws_ls_capture_and_handle_form_post()
{
		global $save_response;

		// Ignore non WLT posts
		if (!($_POST && isset($_POST['ws_ls_is_weight_form']) && 'true' == $_POST['ws_ls_is_weight_form'])) {
			return;
		}

		$error = false;
		$save_success = false;
		$html_output = '';

		// Capture and validate user id from form
    	$user_id = (isset($_POST['ws_ls_user_id']) && is_numeric($_POST['ws_ls_user_id'])) ? intval($_POST['ws_ls_user_id']) : false;

    	$form_number = (isset($_POST['ws_ls_form_number']) && is_numeric($_POST['ws_ls_form_number'])) ? intval($_POST['ws_ls_form_number']) : false;

		$save_response['form_number'] = $form_number;

		// Got an ID?
		if($user_id){
			$user_hash = (isset($_POST['ws_ls_security'])) ? $_POST['ws_ls_security'] : '';

			// If a valid hash, carry on
			if($user_hash == wp_hash($user_id)){

        		$save_success = ws_ls_capture_form_validate_and_save($user_id);

                // Do we have a redirect URL?
                if(isset($_POST['ws_redirect']) && wp_validate_redirect($_POST['ws_redirect'])) {
                    wp_safe_redirect($_POST['ws_redirect']);
                    exit;
                }  elseif ($save_success) {

                    // Allow others to override Saved message
                    $save_message = apply_filters( 'wlt-filter-form-saved-message', __('Saved!', WE_LS_SLUG));

					$html_output .= ws_ls_display_blockquote( $save_message, 'ws-ls-success');
				} else {
					$error = __('An error occurred while saving your data!', WE_LS_SLUG);
				}
			} else {
				$error = __('No user specified (hash)', WE_LS_SLUG);
			}
		} else {
			$error = __('No user specified', WE_LS_SLUG);
		}

		if($error) {
			$html_output .=  ws_ls_display_blockquote( $error, 'ws-ls-error-text');
		}

		$save_response['message'] = $html_output;

    return $html_output;

}
add_action('init', 'ws_ls_capture_and_handle_form_post');
add_action('admin_init', 'ws_ls_capture_and_handle_form_post');
