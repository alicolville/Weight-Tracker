<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Look for WT form submissions. If one is found, validate the form and attempt to save the data.
 *
 * @return array|bool|void
 */
function ws_ls_form_post_handler(){

		$submission_type = ws_ls_form_post_handler_determine_type();

		// Ignore non WLT posts
		if ( true === empty( $submission_type ) ) {
			return false;
		}

		$form_number = ws_ls_post_value( 'form-number', false );

		// Do we have a security hash?
		$user_hash = ws_ls_post_value( 'security' );

		if ( true === empty( $user_hash ) ) {
			return ws_ls_save_form_error_prep( $form_number, __( 'No user hash could be found', WE_LS_SLUG ) );
		}

		// Got a user ID?
		$user_id = ws_ls_post_value( 'user-id' );

		if ( true === empty( $user_id ) ) {
			return ws_ls_save_form_error_prep( $form_number, __( 'No user ID has been found', WE_LS_SLUG ) );
		}

		// Does the hash work for the given user ID?
		if( $user_hash !== wp_hash( $user_id ) ) {
			return ws_ls_save_form_error_prep( $form_number, __( 'The given user hash did not match the logged in user', WE_LS_SLUG ) );
		}

		$result = false;

		// Process posted form and save!
		if ( 'target' === $submission_type ) {
			$result = ws_ls_form_post_handler_target( $user_id );
		} else {    // weight / custom-fields
			$result = ws_ls_form_post_handler_weight( $user_id, $submission_type );
		}

		if ( true === empty( $result ) ) {
			return ws_ls_save_form_error_prep( $form_number, __( 'An error occurred while saving your data', WE_LS_SLUG ) );
		}

		// Redirect?
		$redirect_url = ws_ls_post_value( 'redirect-url' );

		if ( false === empty( $redirect_url ) ) {
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$message = apply_filters( 'wlt-filter-form-saved-message', __( 'Your entry has been successfully saved.', WE_LS_SLUG ) );

		return ws_ls_save_form_error_prep( $form_number, $message, false );
}
add_action( 'init', 'ws_ls_form_post_handler' );
add_action( 'admin_init', 'ws_ls_form_post_handler' );

/**
 * Update the user's target
 * @param $user_id
 *
 * @return bool
 */
function ws_ls_form_post_handler_target( $user_id ) {

	$kg = ws_ls_form_post_handler_extract_weight();

	// If nothing specified, then delete existing target
	if ( true === empty( $kg ) ) {
		return ( false !== ws_ls_db_target_delete( $user_id ) );
	}

	do_action( 'wlt-hook-data-added-edited', [ 'user-id' => $user_id, 'type' => 'target', 'mode' => 'update' ],  [ 'kg' => $kg ] );

	return ( false !== ws_ls_db_target_set( $user_id, $kg ) );
}

/**
 * Handle a form submission for Weight / Custom fields
 *
 * @param $user_id
 *
 * @param string $type
 *
 * @return bool
 */
function ws_ls_form_post_handler_weight( $user_id, $type = 'weight' ) {

	if ( true === empty( $user_id ) ) {
		return false;
	}

	$kg 	= ws_ls_form_post_handler_extract_weight();
	$date	= ws_ls_post_value( 'we-ls-date' );

	if ( true === empty( $date ) ) {
		return false;
	}

	$entry_data     = [     'weight_weight' => $kg,
	                        'weight_date'   => ws_ls_convert_date_to_iso( $date ),
							'weight_notes'  => ws_ls_post_value( 'we-ls-notes' )
	];

	$entry_data     = stripslashes_deep( $entry_data );
	$existing_id    = ws_ls_post_value( 'entry-id' );

	if ( true === empty( $existing_id ) ) {
		$existing_id = ws_ls_db_entry_for_date( $user_id, $entry_data[ 'weight_date' ] );
	}

	$entry_id       = ws_ls_db_entry_set( $entry_data, $user_id, $existing_id );

	if ( true === empty( $entry_id ) ) {
		return false;
	}

	// ---------------------------------------------
	// Process Meta Fields
	// ---------------------------------------------

	if ( true === ws_ls_meta_fields_is_enabled() &&
	     ws_ls_meta_fields_number_of_enabled() > 0 ) {

		// Loop through each enabled meta field. If the field exists in the $_POST object then update the database.
		foreach ( ws_ls_meta_fields_enabled() as $field ) {

			$field_key  = ws_ls_meta_fields_form_field_generate_id( $field['id'] );
			$value      = NULL;

			// If photo, we need to process the upload
			if ( true === WS_LS_IS_PRO && 3 === (int) $field[ 'field_type' ] ) {

				$photo_upload = ws_ls_meta_fields_photos_process_upload( $field_key, $date , $user_id, $entry_id, $field['id'] );

				if ( false === empty( $photo_upload ) ) {
					$value = $photo_upload;
				}

			} else if ( true === isset( $_POST[ $field_key ] ) ) {

				$value = $_POST[ $field_key ];
			}

			if ( NULL !== $value ) {

				ws_ls_meta_add_to_entry([
						'entry_id'      => $entry_id,
						'key'           => $field['id'],
						'value'         => $value
					]
				);
			}
		}
	}

	// Tidy up cache
	ws_ls_cache_user_delete( $user_id );

	// Update User stats table and throw notification hook
	if ( true === WS_LS_IS_PRO ) {

		ws_ls_stats_update_for_user( $user_id );

		$type = [	'user-id' 	=> $user_id,
					'type' 		=> ( false === empty( $kg ) ) ? 'weight-measurements' : 'custom-fields-only',
					'mode' 		=> ( $existing_id === $entry_id ) ? 'update' : 'add'
		];

		$entry = ws_ls_entry_get( [ 'user-id' => $user_id, 'id' => $entry_id, 'meta' => true ] );

		if ( false === empty( $entry ) ) {
			do_action( 'wlt-hook-data-added-edited', $type, $entry );
		}
	}

	return true;
}

/**
 * Determine the type of form submission
 * @return string|null
 */
function ws_ls_form_post_handler_determine_type() {
	$type = ws_ls_post_value( 'type' );

	return ( true === in_array( $type, [ 'custom-fields', 'target', 'weight' ] ) ) ?
				$type :
					NULL;
}

/**
 * Scan the form post for relevant weight fields and convert them into Kg
 * @return float|null
 */
function ws_ls_form_post_handler_extract_weight() {

	// Are we lucky? Metric by default?
	$kg = ws_ls_post_value( 'ws-ls-weight-kg', NULL, true );

	if ( NULL !== $kg ) {
		return $kg;
	}

	$stones = ws_ls_post_value( 'ws-ls-weight-stones', NULL, true );
	$pounds = ws_ls_post_value( 'ws-ls-weight-pounds', NULL, true );

	// Stones and Pounds
	if ( NULL !== $stones ) {

		// Force pounds to zero if not specified
		$pounds = ( true === empty( $pounds ) ) ? 0 : $pounds;

		return ws_ls_convert_stones_pounds_to_kg( $stones, $pounds );

	} elseif ( NULL !== $pounds ) {

		return ws_ls_convert_pounds_to_kg( $pounds );

	}

	return NULL;
}

/**
 * Prep a response object for other forms to process
 *
 * @param $form_number
 * @param null $message
 * @param bool $error
 *
 * @return bool
 */
function ws_ls_save_form_error_prep( $form_number, $message = '', $error = true ) {

	global $save_response;

	if ( false === is_array( $save_response ) ) {
		$save_response = [ 'form_number' => (int) $form_number, 'message' => '', 'error' => $error ];
	}

	$save_response[ 'message' ] = ( true === $error ) ? ws_ls_blockquote_error( $message ) : ws_ls_blockquote_success( $message );

	return $error;
}
