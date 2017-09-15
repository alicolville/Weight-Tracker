<?php

defined('ABSPATH') or die("Jog on!");


// ------------------------------------------------------------------
// Shortcodes
// ------------------------------------------------------------------

/**
 * Display recent photo
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_photos_shortcode_recent($user_defined_arguments) {

	$arguments = shortcode_atts([
		'error-message' => __('No recent photo found', WE_LS_SLUG ),
		'user-id' => get_current_user_id(),
		'width' => 200,
		'height' => 200
	], $user_defined_arguments );

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
	$arguments['width'] = ws_ls_force_numeric_argument($arguments['width'], 200);
	$arguments['height'] = ws_ls_force_numeric_argument($arguments['height'], 200);

	// Fetch photo
	$photo = ws_ls_photos_db_get_recent_or_latest($arguments['user-id'], true, $arguments['width'], $arguments['height']);

	if ( false === empty($photo) ) {
		return ws_ls_photos_shortcode_render($photo, 'ws-ls-photo-recent');
	} else {
		return esc_html($arguments['error-message']);
	}

}
add_shortcode('wlt-photo-recent', 'ws_ls_photos_shortcode_recent');

/**
 * Display oldest photo
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_photos_shortcode_oldest($user_defined_arguments) {

	$arguments = shortcode_atts([
		'error-message' => __('No recent photo found', WE_LS_SLUG ),
		'user-id' => get_current_user_id(),
		'width' => 200,
		'height' => 200
	], $user_defined_arguments );

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
	$arguments['width'] = ws_ls_force_numeric_argument($arguments['width'], 200);
	$arguments['height'] = ws_ls_force_numeric_argument($arguments['height'], 200);

	// Fetch photo
	$photo = ws_ls_photos_db_get_recent_or_latest($arguments['user-id'], false, $arguments['width'], $arguments['height']);

	if ( false === empty($photo) ) {
		return ws_ls_photos_shortcode_render($photo, 'ws-ls-photo-oldest');
	} else {
		return esc_html($arguments['error-message']);
	}

}
add_shortcode('wlt-photo-oldest', 'ws_ls_photos_shortcode_oldest');

/**
 * Create HTML to render image
 * @param $image
 */
function ws_ls_photos_shortcode_render($image, $css_class = '') {

	if ( isset( $image['id'], $image['thumb-src'], $image['thumb-width'], $image['thumb-height'], $image['full']) ) {

		return sprintf('	<a href="%1$s" target="_blank" class="ws-ls-progress-photo%6$s" data-id="%5$s">
									<img src="%2$s" width="%3$s" height="%4$s" />
								</a>',
			esc_url($image['full']),
			esc_url($image['thumb-src']),
			esc_attr($image['thumb-width']),
			esc_attr($image['thumb-height']),
			esc_attr($image['id']),
			false === empty($css_class) ? ' ' . esc_attr($css_class) : ''
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
	$sql = $wpdb->prepare("SELECT photo_id FROM $table_name where weight_user_id = %d order by weight_date " . $direction . " limit 0, 1", $user_id);
	$photo_id = $wpdb->get_var($sql);

	if ( false === empty($photo_id) ) {

		// We have a Photo ID, build an object to return with thumbnail and full url
		$photo_id = intval($photo_id);


		$thumbnail = wp_get_attachment_image_src($photo_id, array($width, $height));

		if ( false === empty($thumbnail) ) {

			$image['id'] = $photo_id;
			$image['thumb-src'] = $thumbnail[0];
			$image['thumb-width'] = $thumbnail[1];
			$image['thumb-height'] = $thumbnail[2];
			$image['full'] = wp_get_attachment_url($photo_id);

			ws_ls_cache_user_set($user_id, $cache_key, $image);
			return $image;
		}
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