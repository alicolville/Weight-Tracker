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
			return ws_ls_save_form_error_prep( $form_number, esc_html__( 'No user hash could be found', WE_LS_SLUG ) );
		}

		// Got a user ID?
		$user_id = ws_ls_post_value( 'user-id' );

		if ( true === empty( $user_id ) ) {
			return ws_ls_save_form_error_prep( $form_number, esc_html__( 'No user ID has been found', WE_LS_SLUG ) );
		}

		// Does the hash work for the given user ID?
		if( $user_hash !== wp_hash( $user_id ) ) {
			return ws_ls_save_form_error_prep( $form_number, esc_html__( 'The given user hash did not match the logged in user', WE_LS_SLUG ) );
		}

		do_action( 'wlt-hook-data-attempting-added-edited', $user_id );

		$result = false;

		// Process posted form and save!
		if ( true === in_array( $submission_type, [ 'mixed', 'target' ] ) ) {

			$prefix = ( 'mixed' === $submission_type ) ? 'ws-ls-target-' : 'ws-ls-weight-';

			$result = ws_ls_form_post_handler_target( $user_id, $prefix );
		}

		if ( true === in_array( $submission_type, [ 'custom-fields', 'mixed', 'weight' ] ) ) {
			$result = ws_ls_form_post_handler_weight( $user_id );
		}

		if ( true === empty( $result ) ) {
			return ws_ls_save_form_error_prep( $form_number, esc_html__( 'An error occurred while saving your data', WE_LS_SLUG ) );
		}

		// Redirect?
		$redirect_url = ws_ls_post_value( 'redirect-url' );

		if ( false === empty( $redirect_url ) ) {
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$message = apply_filters( 'wlt-filter-form-saved-message', esc_html__( 'Your entry has been successfully saved.', WE_LS_SLUG ) );

		return ws_ls_save_form_error_prep( $form_number, $message, false );
}
add_action( 'init', 'ws_ls_form_post_handler' );
add_action( 'admin_init', 'ws_ls_form_post_handler' );

/**
 * Update the user's target
 *
 * @param $user_id
 * @param string $prefix
 *
 * @return bool
 */
function ws_ls_form_post_handler_target( $user_id, $prefix = 'ws-ls-weight-' ) {

	// Start by searching for standard weight field names, if nothing (e.g. potentially in "mixed" mode) then look for target weight field names
	$kg = ws_ls_form_post_handler_extract_weight( 'post', $prefix );

	// If nothing specified, then delete existing target
	if ( true === empty( $kg ) ) {
		return ( false !== ws_ls_db_target_delete( $user_id ) );
	}

	do_action( 'wlt-hook-data-added-edited', [ 'user-id' => $user_id, 'type' => 'target', 'mode' => 'update' ],  [ 'kg' => $kg ] );

	ws_ls_stats_update_for_user( $user_id );

	return ( false !== ws_ls_db_target_set( $user_id, $kg ) );
}

/**
 * Handle a form submission for Weight / Custom fields
 *
 * @param $user_id
 *
 *
 * @return bool
 */
function ws_ls_form_post_handler_weight( $user_id ) {

	if ( true === empty( $user_id ) ) {
		return false;
	}

	$date	= ws_ls_post_value( 'we-ls-date' );

	if ( true === empty( $date ) ) {
		return false;
	}

	$entry_data     = [ 'weight_date'   => ws_ls_convert_date_to_iso( $date ) ];

	// Were weight fields present on the form?
	if ( true === ws_ls_form_post_handler_any_weight_fields() ) {
		$entry_data[ 'weight_weight' ] = ws_ls_form_post_handler_extract_weight();
	}

	$weight_notes = ws_ls_post_value( 'we-ls-notes' );

	if ( NULL !== $weight_notes ) {
		$entry_data[ 'weight_notes' ] = $weight_notes;
	}

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
			if ( true === WS_LS_IS_PRO
			        && 3 === (int) $field[ 'field_type' ]
						&& ( false === empty( $_FILES[ $field_key ]['type'] ) ) ) {

				$photo_upload = ws_ls_meta_fields_photos_process_upload( $field_key, $date , $user_id, $entry_id, $field['id'] );

				if ( false === empty( $photo_upload ) ) {
					$value = $photo_upload;
				} else {
					$value = '';
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
					'type' 		=> ( false === empty( $entry_data[ 'weight_weight' ]  ) ) ? 'weight-measurements' : 'custom-fields-only',
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

	/*
	 * "Mixed" isn't widely supported at the moment and was intended for forms that contain both latest weight and target.
	 *
	 * In "Mixed" mode, target weight fields are prefixed with "ws-ls-target-" instead of "ws-ls-weight-"
	 *
	 * Currently, "mixed" is only supported by custom plugins.
	 *
	 */

	return ( true === in_array( $type, [ 'custom-fields', 'mixed' , 'target', 'weight' ] ) ) ?
				$type :
					NULL;
}

/**
 * Scan the form post for relevant weight fields and convert them into Kg
 *
 * @param string $get_or_post
 * @param string $prefix "ws-ls-weight-" or "ws-ls-target-"
 *
 * @return float|null
 */
function ws_ls_form_post_handler_extract_weight( $get_or_post = 'post', $prefix = 'ws-ls-weight-' ) {

	$key = sprintf( '%s%s', $prefix, 'kg' );

	// Are we lucky? Metric by default?
	$kg = 'post' === $get_or_post ?
									ws_ls_post_value( $key, NULL, true ) :
										ws_ls_querystring_value( $key, false, NULL );

	if ( NULL !== $kg ) {
		return $kg;
	}

	$key = sprintf( '%s%s', $prefix, 'stones' );

	$stones = 'post' === $get_or_post ?
										ws_ls_post_value( $key, NULL, true ) :
											ws_ls_querystring_value( $key, false, NULL );

	$key = sprintf( '%s%s', $prefix, 'pounds' );

	$pounds = 'post' === $get_or_post ?
										ws_ls_post_value( $key, NULL, true ) :
											ws_ls_querystring_value( $key, false, NULL );

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
 * Were any weight fields detected?
 * @return bool
 */
function ws_ls_form_post_handler_any_weight_fields() {

	$field_keys = [ 'ws-ls-weight-kg', 'ws-ls-weight-stones', 'ws-ls-weight-pounds' ];

	foreach ( $field_keys as $key ) {
		if ( true === isset( $_POST[ $key ] ) ) {
			return true;
		}
	}

	return false;
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
