<?php

defined('ABSPATH') or die('Jog on!');

global $save_response;

/**
 * Look for WT form submissions. If one is found, validate the form and attempt to save the data.
 *
 * @return nothing
 */
function ws_ls_capture_and_handle_form_post(){

		// Ignore non WLT posts
		if ( true === empty( $_POST[ 'ws_ls_is_weight_form' ] ) ) {
			return false;
		}

		global $save_response;

		$form_number = ws_ls_post_value( 'ws_ls_form_number', false );

		$save_response = [ 'form_number' => (int) $form_number, 'message' => '' ];

		// Do we have a security hash?
		$user_hash = ws_ls_post_value( 'ws_ls_security' );

		if ( true === empty( $user_hash ) ) {
			return ws_ls_save_form_error_prep( $save_response, __( 'No user hash could be found', WE_LS_SLUG ) );
		}

		// Got a user ID?
		$user_id = ws_ls_post_value( 'ws_ls_user_id' );

		if ( true === empty( $user_id ) ) {
			return ws_ls_save_form_error_prep( $save_response, __( 'No user ID has been found', WE_LS_SLUG ) );
		}

		// Does the hash work for the given user ID?
		if( $user_hash !== wp_hash( $user_id ) ) {
			return ws_ls_save_form_error_prep( $save_response, __( 'The given user hash did not match the logged in user', WE_LS_SLUG ) );
		}

		// Process posted form and save!
		$save_success = ws_ls_capture_form_validate_and_save( $user_id );

		if ( false === $save_success ) {
			return ws_ls_save_form_error_prep( $save_response, __( 'An error occurred while saving your data', WE_LS_SLUG ) );
		}

		// Redirect?
		$redirect_url = ws_ls_post_value( 'ws_redirect' );

		if ( false === empty( $redirect_url ) ) {
			wp_safe_redirect( $_POST['ws_redirect'] );
			exit;
		}

		$message = apply_filters( 'wlt-filter-form-saved-message', __( 'Your entry has been saved.', WE_LS_SLUG ) );

		$save_response[ 'message' ] = ws_ls_display_blockquote( $message, 'ws-ls-success' );
}
add_action( 'init', 'ws_ls_capture_and_handle_form_post' );
add_action( 'admin_init', 'ws_ls_capture_and_handle_form_post' );

/**
 * Prep a response object for other forms to process
 * @param $response
 * @param $error
 * @return array
 */
function ws_ls_save_form_error_prep($response, $error ) {

	if ( false === is_array( $response ) ) {
		return $response;
	}

	$response[ 'message' ] = ws_ls_blockquote_error( $error );

	return $response;
}
