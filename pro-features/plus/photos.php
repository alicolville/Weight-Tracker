<?php

defined('ABSPATH') or die("Jog on!");


// ------------------------------------------------------------------
// Shortcodes
// ------------------------------------------------------------------

/**
 * Display count for user
 *
 * @param $user_defined_arguments
 * @return array|null
 */
function ws_ls_photos_shortcode_count($user_defined_arguments) {

	$arguments = shortcode_atts([
		'user-id' => false
	], $user_defined_arguments );

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());

	return ws_ls_photos_db_count_photos($arguments['user-id']);

}
add_shortcode('wlt-photo-count', 'ws_ls_photos_shortcode_count');

/**
 * Display recent photo
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_photos_shortcode_recent($user_defined_arguments) {

    if ( false === is_array($user_defined_arguments) ) {
        $user_defined_arguments = [];
    }

    $user_defined_arguments['recent'] = true;

    return ws_ls_photos_shortcode_core($user_defined_arguments);

}
add_shortcode('wlt-photo-recent', 'ws_ls_photos_shortcode_recent');

/**
 * Display oldest photo
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_photos_shortcode_oldest($user_defined_arguments) {

    if ( false === is_array($user_defined_arguments) ) {
        $user_defined_arguments = [];
    }

	$user_defined_arguments['error-message'] = (true === empty($user_defined_arguments['error-message'])) ? __('No photos were found.', WE_LS_SLUG ) : $user_defined_arguments['error-message'];
    $user_defined_arguments['recent'] = false;

	return ws_ls_photos_shortcode_core($user_defined_arguments);

}
add_shortcode('wlt-photo-oldest', 'ws_ls_photos_shortcode_oldest');


function ws_ls_photos_shortcode_core($user_defined_arguments) {

    $arguments = shortcode_atts([
		'css-class' => '',
    	'error-message' => __('No recent photo found.', WE_LS_SLUG ),
		'height' => 200,
		'hide-date' => false,
        'user-id' => get_current_user_id(),
		'recent' => true,
        'width' => 200
    ], $user_defined_arguments );

    $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
    $arguments['width'] = ws_ls_force_numeric_argument($arguments['width'], 200);
    $arguments['height'] = ws_ls_force_numeric_argument($arguments['height'], 200);
    $arguments['recent'] = ws_ls_force_bool_argument($arguments['recent']);
    $arguments['hide-date'] = ws_ls_force_bool_argument($arguments['hide-date']);
	$arguments['css-class'] = (false === empty($arguments['css-class'])) ? esc_attr($arguments['css-class']) . ' ' : '';

    // Fetch photo
    $photo = ws_ls_photos_db_get_recent_or_latest($arguments['user-id'], $arguments['recent'], $arguments['width'], $arguments['height']);

    if ( false === empty($photo) ) {
        return ws_ls_photos_shortcode_render($photo, $arguments['css-class']. 'ws-ls-photo-' . ( true === $arguments['recent'] ? 'recent' : 'oldest' ), $arguments['hide-date']);
    } else {
        return esc_html($arguments['error-message']);
    }
}

/**
 * Create HTML to render image
 * @param $image
 */
function ws_ls_photos_shortcode_render($image, $css_class = '', $hide_date = true) {

	if ( isset( $image['photo_id'], $image['thumb'], $image['full']) ) {

		return sprintf('	
	                        <div class="ws-ls-photo-frame%4$s">
	                            <div class="ws-ls-photo-embed">
	                                <a href="%1$s" target="_blank" data-id="%3$s">
                                        %2$s
                                    </a>
                                </div>
                                <div class="ws-ls-photo-date%6$s">%5$s</div>
							</div>
							',
			esc_url($image['full']),
			$image['thumb'],
			esc_attr($image['photo_id']),
			false === empty($css_class) ? ' ' . esc_attr($css_class) : '',
            esc_html($image['date']),
            true === $hide_date ? ' ws-ls-hide' : ''
		);
	}
	return '';
}

// ------------------------------------------------------------------
// Hooks
// ------------------------------------------------------------------

/**
 * If an entry is deleted, check for a photo ID. If it exists, delete attachment from media library
 */
function ws_ls_photos_tidy_up_after_entry_deleted($entry) {
    if ( false === empty($entry['photo_id']) && true === is_numeric($entry['photo_id'])) {
        wp_delete_attachment(intval($entry['photo_id']) ,true);
        ws_ls_delete_cache_for_given_user($entry['user_id']);
    }
}
add_action(WE_LS_HOOK_DATA_ENTRY_DELETED, 'ws_ls_photos_tidy_up_after_entry_deleted');

/**
 * If admin deletes a user's photo from the media library, ensure there is no foreign key to it in DB
 * @param $attachment_id
 */
function ws_ls_photos_tidy_up_after_attachment_deleted($attachment_id) {
    if ( false === empty($attachment_id) && true === is_numeric($attachment_id)) {
        global $wpdb;
        $sql = $wpdb->prepare('Update ' . $wpdb->prefix . WE_LS_TABLENAME . ' SET photo_id = null where photo_id = %d', $attachment_id);
        $wpdb->query($sql);
    }
}
add_action('delete_attachment', 'ws_ls_photos_tidy_up_after_attachment_deleted');

// ------------------------------------------------------------------
// DB
// ------------------------------------------------------------------

/**
 *
 * Fetch the most recent or oldest photo for a user
 *
 * @param bool $user_id
 * @param bool $recent
 * @param int $width
 * @param int $height
 * @return bool|null
 */
function ws_ls_photos_db_get_recent_or_latest($user_id = false, $recent = false, $width = 200, $height = 200) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	global $wpdb;

	// Validate fields
	$direction = (true === $recent) ? 'desc' : 'asc';
	$width = ws_ls_force_numeric_argument($width, 200);
	$height = ws_ls_force_numeric_argument($height, 200);

	$cache_key = WE_LS_CACHE_KEY_PHOTOS . '-' . $direction . $width . $height;

	// Return cache if found!
	if ($cache = ws_ls_cache_user_get($user_id, $cache_key))   {
		return $cache;
	}

	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql = $wpdb->prepare("SELECT id as entry_id, weight_date, photo_id FROM $table_name where weight_user_id = %d and photo_id is not null and photo_id <> 0 order by weight_date " . $direction . " limit 0, 1", $user_id);
	$photo = $wpdb->get_row($sql, ARRAY_A);

	if ( false === empty($photo) ) {

		$photo_src = ws_ls_photo_get($photo['photo_id'], $width, $height);

		if ( false === empty($photo) ) {

			$photo = array_merge($photo_src, $photo);
            $photo['date'] = ws_ls_iso_date_into_correct_format($photo['weight_date']);

			ws_ls_cache_user_set($user_id, $cache_key, $photo);
			return $photo;
		}
	}

	return false;
}

/**
 *
 * Fetch all photos for given user from the database
 *
 * @param bool $user_id
 * @param bool $include_image_object
 * @param string $direction
 * @param int $width
 * @param int $height
 * @return array|bool|null
 */
function ws_ls_photos_db_get_all_photos($user_id = false, $include_image_object = false, $limit = false, $direction = 'asc', $width = 200, $height = 200) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    global $wpdb;

    // Validate fields
    $direction = (false === in_array($direction, ['asc', 'desc'])) ? 'desc' : $direction;
    $width = ws_ls_force_numeric_argument($width, 200);
    $height = ws_ls_force_numeric_argument($height, 200);

    $cache_key = WE_LS_CACHE_KEY_PHOTOS_ALL . '-' . $direction . $include_image_object . $limit . $width . $height;

    // Return cache if found!
    if ($cache = ws_ls_cache_user_get($user_id, $cache_key))   {
   		return $cache;
    }

    $limit = ( false === empty($limit) && is_numeric($limit) ) ? ' limit 0, ' . intval($limit) : '';

    $table_name = $wpdb->prefix . WE_LS_TABLENAME;
    $sql = $wpdb->prepare("SELECT * FROM $table_name where weight_user_id = %d and photo_id is not null and photo_id <> 0 order by weight_date " . $direction . $limit, $user_id);

    $photos = $wpdb->get_results($sql);

    $photos_to_return = [];

    if ( false === empty($photos) ) {

        foreach ($photos as $row) {

			$photo = ws_ls_weight_object($user_id,
				$row->weight_weight,
				$row->weight_pounds,
				$row->weight_stones,
				$row->weight_only_pounds,
				$row->weight_notes,
				$row->weight_date,
				false,
				$row->id,
				'',
				false,
				$row->photo_id
			);

            // Embed image attachment data?
            if ( true === $include_image_object ) {
				$photo_src = ws_ls_photo_get($photo['photo_id'], $width, $height);

				if ( false === empty($photo_src) ) {
					$photo = array_merge($photo_src, $photo);
				}
            }

            $photos_to_return[] = $photo;
        }
    }

	ws_ls_cache_user_set($user_id, $cache_key, $photos_to_return);
	return $photos_to_return;
}

/**
 * Count number of photos for given user
 *
 * @param bool $user_id
 * @return array|null
 *
 */
function ws_ls_photos_db_count_photos($user_id = false) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    global $wpdb;

    $cache_key = WE_LS_CACHE_KEY_PHOTOS_COUNT ;

    // Return cache if found!
    if ($cache = ws_ls_cache_user_get($user_id, $cache_key))   {
        return $cache;
    }

    $table_name = $wpdb->prefix . WE_LS_TABLENAME;

    $count = $wpdb->get_var( $wpdb->prepare("SELECT count(id) FROM $table_name where weight_user_id = %d and photo_id is not null and photo_id <> 0", $user_id) );

    $count = ( false === empty($count) ) ? intval($count) : 0;
    
    ws_ls_cache_user_set($user_id, $cache_key, $count);
    return $count;
}

/**
 * Fetch HTML for given image
 *
 * @param $attachment_id
 * @param $width
 * @param $height
 * @return bool
 */
function ws_ls_photo_get($attachment_id, $width = 200, $height = 200, $include_full_url = true) {

	$photo['thumb'] = wp_get_attachment_image( $attachment_id, array( $width, $height) );

	if ( false === empty($photo['thumb'])) {

		if(true === $include_full_url) {
			$photo['full'] = wp_get_attachment_url($attachment_id);
		}

		return $photo;
	}

	return false;
}

// ------------------------------------------------------------------
//
// Attachments
//
// Add custom fields to attachment. This will allow a check box that we can use to hide
// photo uploads from public attachment pages.
// ------------------------------------------------------------------

// Add additional field.
function ws_ls_photos_attachment_fields_to_edit( $form_fields, $post ) {

	// Has the checkbox previously been set?
	$already_hidden = (bool) get_post_meta($post->ID, 'ws-ls-hide-image', true);

	$form_fields['ws_ls_hide_image'] = array(
		'label' => __( 'Don\'t show to public?', WE_LS_SLUG ),
		'input' => 'html',
		'html' => '<label for="attachments-'.$post->ID.'-ws-ls-hide-image"><input type="checkbox" id="attachments-'.$post->ID.'-ws-ls-hide-image" name="attachments['.$post->ID.'][ws-ls-hide-image]" value="1"'.($already_hidden ? ' checked="checked"' : '').' /></label>',
		'helps' => __( 'If the user has uploaded this as part of their Weight Tracker progress, they probably don\'t want it viewable on a public attachment page!', WE_LS_SLUG ),
	);

	return $form_fields;
}
add_filter("attachment_fields_to_edit", "ws_ls_photos_attachment_fields_to_edit", null, 2);

// Save additional field
function ws_ls_photos_attachment_fields_to_save($post, $attachment) {

	// Process checkbox for hiding an image and save.
	update_post_meta(	$post['ID'],
						'ws-ls-hide-image',
						$attachment['ws-ls-hide-image'] == '1' ? '1' : '0'
	);
	return $post;
}
add_filter( 'attachment_fields_to_save', 'ws_ls_photos_attachment_fields_to_save', null, 2 );

// If set to hidden, then hide attachment if front end.
function ws_ls_photos_hide_if_required() {
	if ( is_attachment() ) {
		global $post;

		$already_hidden = (bool) get_post_meta($post->ID, 'ws-ls-hide-image', true);

		// If hidden, redirect the user to the homepage!
		if (true === $already_hidden) {
			wp_redirect( esc_url( home_url( '/' ) ), 301 );
		}

	}
}
add_action( 'template_redirect', 'ws_ls_photos_hide_if_required' );

/**
 * Create a standard image size for thumbs
 */
function ws_ls_photos_additional_image_size() {
	add_image_size( 'ws-ls-small', 200, 200 );
}
add_action('init', 'ws_ls_photos_additional_image_size');
add_action('admin_init', 'ws_ls_photos_additional_image_size');

/**
 * Function used by wp_handle_upload() (core.php) to generate a unique file name for Photo uploads. This will help stop people guessing them.
 *
 * @param $dir
 * @param $name
 * @param $ext
 * @return string
 */
function ws_ls_photo_generate_unique_name($dir, $name, $ext){
	return uniqid().$ext;
}