<?php

	defined('ABSPATH') or die('Jog on!');

	/**
	 *
	 * Do we have any photo fields enabled?
	 *
	 * @param bool $hide_from_shortcodes
	 *
	 * @return bool
	 */
	function ws_ls_meta_fields_photo_any_enabled( $hide_from_shortcodes = false ) {

		$photo_fields = ws_ls_meta_fields_photos_all( $hide_from_shortcodes );

		return true === WS_LS_IS_PRO && ! empty( $photo_fields );
	}

/**
 * Process a photo upload / deletion
 *
 * @param $field_name
 * @param null $date_text
 * @param null $user_id
 *
 * @param null $entry_id
 * @param null $meta_field_id
 * @param string $module
 * @return bool|int
 */
	function ws_ls_meta_fields_photos_process_upload( $field_name, $date_text = NULL, $user_id = NULL,
                                                            $entry_id = NULL, $meta_field_id = null, $module = 'photo-upload' ) {

	    if ( 'award-badge-yeken' === $field_name && false === ws_ls_awards_is_enabled() ) {
            ws_ls_log_add( $module, 'Awards disabled so not going to try and upload image.' );
        }

        if ( 'award-badge-yeken' !== $field_name && ( false === ws_ls_meta_fields_is_enabled() || false === ws_ls_meta_fields_photo_any_enabled() ) ) {
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
		            true === empty( $_POST[ $field_key_delete ] ) &&
		                true === empty( $_FILES[ $field_name ]['size'] ) ) {

			return (int) $_POST[ $field_key_previous ];
		}

		//--------------------------------------------------------------------------------
		// Delete Existing?
		//--------------------------------------------------------------------------------

		// 1) Has the delete checkbox been checked?
		// 2) Has a new image been uploaded for that meta field key? If so, delete
		if ( ( false === empty( $_POST[ $field_key_delete ] ) && 'y' == $_POST[ $field_key_delete ] && true === is_numeric( $_POST[ $field_key_previous ] ) ) ||
		        ( false === empty( $_POST[ $field_key_previous ] ) && false === empty( $_FILES[ $field_name ]['name'] ) ) ) {

			$previous_photo = $_POST[ $field_key_previous ];

			wp_delete_attachment( (int) $previous_photo );

            if ( false === empty( $entry_id ) && false === empty( $meta_field_id ) ) {
               ws_ls_meta_delete( (int) $entry_id, (int) $meta_field_id );
            }
		}

		//--------------------------------------------------------------------------------
		// Handle a new upload
		//--------------------------------------------------------------------------------

		if ( false === empty( $_FILES[ $field_name ]['type'] ) ) {

			$max_field_size = ws_ls_photo_max_upload_size();

			$photo_uploaded = $_FILES[ $field_name ];

			// Within max file size?
			if ( (int) $photo_uploaded['size'] < 0 || (int) $photo_uploaded['size'] > $max_field_size ) {
				ws_ls_log_add( $module, sprintf( 'Photo too big: %s. Details: %s / Max Size: %s', $field_name, json_encode( $photo_uploaded ), $max_field_size ) );
				return false;
			}

			// Bring in WP file functionality
			if ( false === function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$mime_type = wp_check_filetype( ($photo_uploaded['name'] ) );

			// Check file Mime type
			if ( false === in_array( $mime_type['type'], [ 'image/jpg','image/jpeg','image/gif','image/png' ] ) ) {
				ws_ls_log_add( $module, sprintf( 'Photo of wrong type: %s', json_encode( $photo_uploaded ) ) );
				return false;
			}

			// Handle the upload using WP's wp_handle_upload function. Takes the posted file and an options array
			$uploaded_file = wp_handle_upload($photo_uploaded, ['test_form' => false, 'unique_filename_callback' => 'ws_ls_photo_generate_unique_name']);

			// Error uploading file
			if ( true === empty( $uploaded_file ) || true === isset( $uploaded_file['error'] ) ) {
				ws_ls_log_add( $module, sprintf( 'Error handing upload: %s Detail: %s', json_encode( $uploaded_file ), json_encode( $photo_uploaded ) ) );
				return false;
			}

			// The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
			$file_name_and_location = $uploaded_file['file'];

			if( empty( $user_id ) ){
				$user_id = get_current_user_id();
			}

			$user_data = get_userdata( $user_id );

			$date_text = ( false === empty( $date_text ) ) ? $date_text : '';

			// Set up options array to add this file as an attachment
			$attachment = array(
				'post_mime_type' => $mime_type['type'],
				'post_title' => ( $user_data ) ? $user_data->user_nicename . ' (' . $date_text . ')' : $date_text,
				'post_content' => ( $user_data ) ? esc_html__('The user ', WE_LS_SLUG) . $user_data->user_nicename . ', ' . esc_html__('uploaded this photo of them for their entry on the', WE_LS_SLUG) . ' ' . $date_text : '',
				'post_status' => 'inherit'
			);

			// If this wasn't uploaded via a Photo Meta field (e.g. an Award then blank additional data)
            if ( 'photo-upload' !== $module ) {
                unset( $attachment['post_title'], $attachment['post_content'] );
            }

			// Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails.
			$attach_id = wp_insert_attachment( $attachment, $file_name_and_location );

			if ( true === empty( $attach_id ) ) {
				ws_ls_log_add( $module, sprintf( 'Failed to add photo to Media Library: %s', json_encode( $photo_uploaded ) ) );
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

/**
 * Return all enabled Photo fields
 *
 * @param bool $hide_from_shortcodes
 * @param bool $ids_only
 * @param bool $ignore_enabled
 * @return array
 */
	function ws_ls_meta_fields_photos_all( $hide_from_shortcodes = false, $ids_only = true, $ignore_enabled = false ) {

	    $fields = ws_ls_meta_fields_enabled();

	    $return = [];

	    foreach ( $fields as $field ) {

	    	// Remove non photos
            if ( 3 !== (int) $field['field_type'] ) {
                continue;
            }

		    if ( false === $ignore_enabled && false === $field['enabled'] ) {
			    continue;
		    }

			// If admin has stated to not show in shortcode then strip them out
            if ( true === $hide_from_shortcodes && 2 === (int) $field['hide_from_shortcodes'] ) {
                continue;
            }

            $return[] = $field;
        }

	    return ( true === $ids_only && false === empty( $return ) ) ? wp_list_pluck( $return, 'id' ) : $return;
    }

/**
 *
 * Take an array or comma delimited string of meta field keys and translate them into meta field IDs
 *
 * @param $keys
 *
 * @param bool $ids_only
 * @param bool $hide_from_shortcodes
 * @return array
 */
    function ws_ls_meta_fields_photos_keys_to_ids( $keys, $ids_only = true, $hide_from_shortcodes = false ) {

	    $return = [];

		if ( false === empty( $keys ) ) {

			if ( false === is_array( $keys ) ) {
				$keys = explode( ',', $keys );
			}

			if ( false === empty( $keys ) ) {

				$photo_fields = ws_ls_meta_fields_photos_all( $hide_from_shortcodes, false );

				foreach ( $keys as $key ) {

					foreach ( $photo_fields as $photo_field ) {

						if ( $photo_field['field_key'] === trim( $key ) ) {
							$return[ $photo_field['id'] ] = $photo_field['field_key'];
							break;
						}
					}
				}
			}

		}

		if ( true === $ids_only && false === empty( $return ) ) {
			$return = array_keys( $return );
		}

		return $return;
    }

    /**
     * Is this meta field a photo field?
     *
     * @param $meta_field_id
     * @return bool
     */
    function ws_ls_meta_fields_photos_is_photo_field( $meta_field_id ) {

        $photo_fields = ws_ls_meta_fields_photos_all( false, true, true );

        return ( true === is_array( $photo_fields ) && in_array( (int) $meta_field_id, $photo_fields ) );
    }

/**
 * Determine what
 *
 * @param $meta_fields_to_use
 *
 * @param bool $hide_from_shortcodes
 * @return array
 */
    function ws_ls_meta_fields_photos_ids_to_use( $meta_fields_to_use, $hide_from_shortcodes = false ) {

	    // Identify which photo fields to use
	    if ( false === empty( $meta_fields_to_use ) ) {
		    $photo_fields = ws_ls_meta_fields_photos_keys_to_ids( $meta_fields_to_use, true, $hide_from_shortcodes );
	    } else {
		    $photo_fields = ws_ls_meta_fields_photos_all( !is_admin() );
	    }

	    // If no active photo fields, then we don't have any photos.
	    return ( true === empty( $photo_fields ) ) ? [] : $photo_fields;

    }

/**
 * Delete all attachments for a given photo meta field
 *
 * @param $meta_field_id
 * @return bool|int
 */
    function ws_ls_meta_fields_photos_delete_all_photos_for_meta_field( $meta_field_id ) {

        if ( false === is_admin() ) {
            return false;
        }

        if ( false === ws_ls_meta_fields_photos_is_photo_field( $meta_field_id ) ) {
            return false;
        }

        // Fetch all attachment IDs
        $photos = ws_ls_meta_for_given_meta_field( $meta_field_id );

        $count = 0;

        foreach ( $photos as $photo ) {
            if ( false === empty( $photo['value'] ) ) {
                wp_delete_attachment( (int) $photo['value'] , true );
                $count++;
            }
        }

        return $count;
    }

	/**
	 * Create example Photo custom field if needed
	 */
    function ws_ls_meta_fields_photos_create_example_field() {

        if ( false === ws_ls_meta_fields_key_exist( 'photo' ) ) {

	        ws_ls_log_add('meta-field-setup', 'Adding photo field.' );

	        ws_ls_meta_fields_add([
		        'field_name' => esc_html__('Photo', WE_LS_SLUG),
		        'abv' => esc_html__('Photo', WE_LS_SLUG),
		        'field_type' => 3,
		        'suffix' => '',
		        'mandatory' => 1,
		        'enabled' => ( 'no' !== get_option('ws-ls-photos-enable', 'no') ) ? 2 : 1 ,
		        'sort' => 160,
		        'hide_from_shortcodes' => 0
	        ]);

        }

	}

    /**
     * Display info about photo uploads
     */
	function ws_ls_meta_fields_photos_form_display_info() {

        return sprintf( '<p><small><strong>%3$s</strong>: %1$s%2$s</small></p>',
            esc_html__('Photos are only visible to you and administrators. ', WE_LS_SLUG),
            esc_html__('Photos must be under', WE_LS_SLUG) . ' ' . ws_ls_photo_display_max_upload_size() . ' ' . esc_html__('or they will silently fail to upload.', WE_LS_SLUG),
            esc_html__('A note about photos', WE_LS_SLUG)
        );
    }
