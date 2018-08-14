<?php

	defined('ABSPATH') or die('Jog on!');

	function ws_ls_meta_fields_photos_process_upload( $field_name, $date_text = NULL, $user_id = NULL ) {

		if ( false === ws_ls_meta_fields_is_enabled() || false === WE_LS_PHOTOS_ENABLED ) {
			ws_ls_log_add('photo-upload', 'Looking for a photo field but Photos disabled?' );
			return false;
		}

		//--------------------------------------------------------------------------------
		// Existing Image? Do nothing? Do we have an existing image we're happy to keep?
		//--------------------------------------------------------------------------------

		$field_key_previous = $field_name . '-previous';
		$field_key_delete = $field_name . '-delete';

		if ( false === empty( $_POST[ $field_key_previous ] ) &&
		        true === is_numeric( $_POST[ $field_key_previous ] ) &&
		            false === empty( $_POST[ $field_key_delete ] ) ) {
			return intval( $_POST[ $field_key_previous ] );
		}

		//--------------------------------------------------------------------------------
		// Delete Existing?
		//--------------------------------------------------------------------------------

		// Got a previous photo to delete?
		if ( false === empty( $_POST[ $field_key_delete ] ) && true === is_numeric( $_POST[ $field_key_previous ] ) ) {

			$previous_photo = $_POST[ $field_key_previous ];

			// User check "Delete this photo" box?
			if ( false === empty( $previous_photo ) && true === is_numeric( $previous_photo ) ) {
				wp_delete_attachment( intval( $previous_photo ) );
			}
		}

		//--------------------------------------------------------------------------------
		// Handle a new upload
		//--------------------------------------------------------------------------------

		if ( false === empty( $_FILES[ $field_name ] ) ) {

			$max_field_size = ws_ls_photo_max_upload_size();

			$photo_uploaded = $_FILES[ $field_name ];

			// Within max file size?
			if ( intval( $photo_uploaded['size'] ) < 0 || intval( $photo_uploaded['size'] ) > $max_field_size ) {
				ws_ls_log_add('photo-upload', sprintf( 'Photo too big: %s. Details: %s / Max Size: %s', $field_name, json_encode( $photo_uploaded ), $max_field_size ) );
				return false;
			}

			// Bring in WP file functionality
			if ( false === function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$mime_type = wp_check_filetype( ($photo_uploaded['name'] ) );

			// Check file Mime type
			if ( false === in_array( $mime_type['type'], [ 'image/jpg','image/jpeg','image/gif','image/png' ] ) ) {
				ws_ls_log_add('photo-upload', sprintf( 'Photo of wrong type: %s', json_encode( $photo_uploaded ) ) );
				return false;
			}

			// Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
			$uploaded_file = wp_handle_upload($photo_uploaded, ['test_form' => false, 'unique_filename_callback' => 'ws_ls_photo_generate_unique_name']);

			// Error uploading file
			if ( true === empty( $uploaded_file ) || true === isset( $uploaded_file['error'] ) ) {
				ws_ls_log_add('photo-upload', sprintf( 'Error handing upload: %s Detail: %s', json_encode( $uploaded_file ), json_encode( $photo_uploaded ) ) );
				return false;
			}

			// The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
			$file_name_and_location = $uploaded_file['file'];

			if( true == empty( $user_id ) ){
				$user_id = get_current_user_id();
			}

			$user_data = get_userdata( $user_id );

			$date_text = ( false === empty( $date_text ) ) ?: '';

			// Set up options array to add this file as an attachment
			$attachment = array(
				'post_mime_type' => $mime_type['type'],
				'post_title' => ( $user_data ) ? $user_data->user_nicename . ' (' . $date_text . ')' : $date_text,
				'post_content' => ( $user_data ) ? __('The user ', WE_LS_SLUG) . $user_data->user_nicename . ', ' . __('uploaded this photo of them for their entry on the', WE_LS_SLUG) . ' ' . $date_text : '',
				'post_status' => 'inherit'
			);

			// Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails.
			$attach_id = wp_insert_attachment( $attachment, $file_name_and_location );

			if ( true === empty( $attach_id ) ) {
				ws_ls_log_add('photo-upload', sprintf( 'Failed to add photo to Media Library: %s', json_encode( $photo_uploaded ) ) );
				return false;
			}

			if ( ! function_exists( 'wp_crop_image' ) ) {
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			}

			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_name_and_location );

			wp_update_attachment_metadata($attach_id,  $attach_data);

			// Set flag to hide image from attachment page
			update_post_meta($attach_id, 'ws-ls-hide-image', '1');

			return $attach_id;

		}

		 return false;
	}