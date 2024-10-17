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
function ws_ls_photos_shortcode_count( $user_defined_arguments ) {

   $arguments = shortcode_atts([
		'user-id' => false
	], $user_defined_arguments );

	$arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());

	return ws_ls_photos_db_count_photos($arguments['user-id']);

}
add_shortcode('wlt-photo-count', 'ws_ls_photos_shortcode_count');
add_shortcode('wt-photo-count', 'ws_ls_photos_shortcode_count');

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
add_shortcode('wt-photo-recent', 'ws_ls_photos_shortcode_recent');

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

	$user_defined_arguments['error-message'] = (true === empty($user_defined_arguments['error-message'])) ? esc_html__('No photos were found.', WE_LS_SLUG ) : $user_defined_arguments['error-message'];
    $user_defined_arguments['recent'] = false;

	return ws_ls_photos_shortcode_core($user_defined_arguments);

}
add_shortcode('wlt-photo-oldest', 'ws_ls_photos_shortcode_oldest');
add_shortcode('wt-photo-oldest', 'ws_ls_photos_shortcode_oldest');

function ws_ls_photos_shortcode_core($user_defined_arguments) {

    if( false === WS_LS_IS_PRO ) {
        return '';
    }

    $arguments = shortcode_atts([
		'css-class' => '',
    	'error-message' => esc_html__('No recent photo found.', WE_LS_SLUG ),
		'height' => 200,
		'hide-date' => false,
        'user-id' => get_current_user_id(),
		'recent' => true,
        'width' => 200,
	    'custom-fields-to-use' => '',
	    'maximum' => 1
    ], $user_defined_arguments );

    $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
    $arguments['width'] = ws_ls_force_numeric_argument($arguments['width'], 200);
    $arguments['height'] = ws_ls_force_numeric_argument($arguments['height'], 200);
    $arguments['recent'] = ws_ls_force_bool_argument($arguments['recent']);
    $arguments['hide-date'] = ws_ls_force_bool_argument($arguments['hide-date']);
	$arguments['css-class'] = (false === empty($arguments['css-class'])) ? esc_attr($arguments['css-class']) . ' ' : '';
	$arguments['maximum'] = ws_ls_force_numeric_argument($arguments['maximum'], 1 );

    // Fetch photo
    $photos = ws_ls_photos_db_get_recent_or_latest( $arguments['user-id'], $arguments['recent'], $arguments['width'],
	                                                    $arguments['height'], $arguments['custom-fields-to-use'], true );

    if ( false === empty( $photos ) ) {

    	$html = '';
    	$number_displayed = 0;

    	foreach ( $photos as $photo ) {
    	    $html .= ws_ls_photos_shortcode_render( $photo, $arguments['css-class']. 'ws-ls-photo-' . ( true === $arguments['recent'] ? 'recent' : 'oldest' ), $arguments['hide-date']);

		    $number_displayed++;

		    if ( $number_displayed >= $arguments['maximum'] ) {
		    	break;
		    }
    	}

        return $html;

    } else {
        return esc_html($arguments['error-message']);
    }
}

/**
 * Create HTML to render image
 * @param $image
 * @param string $css_class
 * @param bool $hide_date
 * @return string
 */
function ws_ls_photos_shortcode_render( $image, $css_class = '', $hide_date = true ) {
print_r($image);
	if ( isset( $image['photo_id'], $image['thumb'], $image['full']) ) {

		return sprintf('
	                        <div class="ws-ls-photo-frame%4$s">
	                            <div class="ws-ls-photo-embed" ykuk-lightbox="animation: fade">
	                                <a href="%1$s" target="_blank" rel="noopener noreferrer" data-id="%3$s">
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
 * @param string $meta_fields_to_use
 * @param bool $hide_from_shortcodes
 * @return array|bool|null
 */
function ws_ls_photos_db_get_recent_or_latest( $user_id = false,
													$recent = false,
														$width = 200,
															$height = 200,
																$meta_fields_to_use = '',
																	$hide_from_shortcodes = false ) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	global $wpdb;

	// Validate fields
	$direction 	= (true === $recent) ? 'desc' : 'asc';
	$width 		= ws_ls_force_numeric_argument($width, 200);
	$height 	= ws_ls_force_numeric_argument($height, 200);
	$cache_key 	= sprintf( 'photos-extreme-%s-%s-%s-%s',  $direction, $width, $height, md5( json_encode( $meta_fields_to_use ) ) );

	// Return cache if found!
	if ( $cache = ws_ls_cache_user_get( $user_id, $cache_key ) )   {
		return $cache;
	}

	$photo_fields = ws_ls_meta_fields_photos_ids_to_use( $meta_fields_to_use, $hide_from_shortcodes );

	// If no photo fields to consider then return false
	if ( true === empty( $photo_fields ) ) {
		return false;
	}

	$sql = 'Select d.id as entry_id, d.weight_date, e.value as photo_id, f.sort from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' e ' .
	       ' inner join ' . $wpdb->prefix . WE_LS_TABLENAME . ' d on e.entry_id = d.id
	         inner join ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' f on f.id = e.meta_field_id
	         where weight_user_id = %d and meta_field_id in (' . implode( ',', $photo_fields) . ') and e.value <> ""
 	         order by weight_date ' . $direction . ', f.sort asc';

	$sql = $wpdb->prepare( $sql, $user_id );

	$photos = $wpdb->get_results($sql, ARRAY_A);

	if ( false === empty( $photos ) ) {

		$first_date = $photos[0]['weight_date'];
		$return = [];

		foreach ( $photos as $photo ) {

			// Only consider photos with the same date.
			if ( $photo['weight_date'] !== $first_date ) {
				break;
			}

			$photo_src = ws_ls_photo_get( $photo['photo_id'], $width, $height );

			if ( false === empty( $photo_src ) ) {

				$photo = array_merge($photo_src, $photo);
				$photo['date'] = ws_ls_iso_date_into_correct_format($photo['weight_date']);

				$return[] = $photo;
			}

		}

		ws_ls_cache_user_set($user_id, $cache_key, $return);

		return $return;
	}

	return false;
}


/**
 *
 * Return all photo ids for a given entry id
 *
 * @param $entry_id
 * @return array
 */
function ws_ls_meta_fields_photos_for_given_entry_id( $entry_id ) {

	global $wpdb;

	$photo_fields = ws_ls_meta_fields_photos_all( false, true, true );

	if ( true === empty( $photo_fields ) ) {
		return [];
	}

	$sql = 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' where meta_field_id in (' . implode( ',', $photo_fields) . ') and value <> "" and entry_id = %d';

	$sql = $wpdb->prepare( $sql, $entry_id );

	return $wpdb->get_results( $sql, ARRAY_A );

}

/**
 *
 * Delete all photo meta entries for given attachment ID
 *
 * @param $attachment_id
 * @return array
 */
function ws_ls_meta_fields_photos_delete_entry( $attachment_id ) {

	global $wpdb;

	$photo_fields = ws_ls_meta_fields_photos_all( false, true, true );

	if ( true === empty( $photo_fields ) ) {
		return [];
	}

	$sql = 'DELETE from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' where meta_field_id in (' . implode( ',', $photo_fields) . ') and value = %d';

	$sql = $wpdb->prepare( $sql, $attachment_id );

	return $wpdb->query( $sql );
}


/**
 *
 * Fetch all photos for given user from the database
 *
 * @param bool $user_id
 * @param bool $include_image_object
 * @param bool $limit
 * @param string $direction
 * @param int $width
 * @param int $height
 * @param string $meta_fields_to_use
 * @param bool $hide_from_shortcodes
 * @return array|bool|null
 */
function ws_ls_photos_db_get_all_photos( $user_id = false, $include_image_object = false, $limit = false, $direction = 'asc',
											$width = 200, $height = 200, $meta_fields_to_use = '', $hide_from_shortcodes = false) {

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	$photo_fields = ws_ls_meta_fields_photos_ids_to_use( $meta_fields_to_use, $hide_from_shortcodes );

	// If no photo fields to consider then return empty array
	if ( true === empty( $photo_fields ) ) {
		return [];
	}

	global $wpdb;

	// Validate fields
	$direction  = (false === in_array($direction, ['asc', 'desc'])) ? 'desc' : $direction;
	$width      = ws_ls_force_numeric_argument($width, 200);
	$height     = ws_ls_force_numeric_argument($height, 200);
	$cache_key  = 'photos-all-' . $direction . $include_image_object . $limit . $width . $height;

	// Return cache if found!
	if ( $cache = ws_ls_cache_user_get( $user_id, $cache_key ) )   {
		return $cache;
	}

	$limit = ( false === empty( $limit ) && is_numeric( $limit ) ) ? ' limit 0, ' . (int) $limit : '';

	$sql = 'Select d.id, d.weight_weight as kg, d.weight_notes, d.weight_date, e.value as photo_id, f.field_name, f.sort
			from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' e
	        inner join ' . $wpdb->prefix . WE_LS_TABLENAME . ' d on e.entry_id = d.id
	        inner join ' . $wpdb->prefix . WE_LS_MYSQL_META_FIELDS . ' f on f.id = e.meta_field_id
	        where weight_user_id = %d and e.value <> "" and meta_field_id in (' . implode( ',', $photo_fields) . ') order by weight_date ' . $direction . ', f.sort asc ' . $limit;

	$sql                = $wpdb->prepare( $sql, $user_id );
	$photos             = $wpdb->get_results( $sql, ARRAY_A );
	$photos_to_return   = [];

	if ( false === empty($photos) ) {

		foreach ( $photos as $photo ) {

			// Embed image attachment data?
			if ( true === $include_image_object ) {
				$photo_src = ws_ls_photo_get( $photo['photo_id'], $width, $height);

				if ( false === empty( $photo_src ) ) {
					$photo = array_merge( $photo_src, $photo);
				}
			}

			$photos_to_return[] = $photo;
		}
	}

	ws_ls_cache_user_set( $user_id, $cache_key, $photos_to_return );

	return $photos_to_return;
}

/**
 * Count number of photos for given user
 *
 * @param bool $user_id
 * @param bool $hide_from_shortcodes
 * @return array|null
 */
function ws_ls_photos_db_count_photos( $user_id = false, $hide_from_shortcodes = false ) {

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    global $wpdb;

    // Return cache if found!
    if ( $cache = ws_ls_cache_user_get( $user_id, 'no-photos' ) )   {
    	return $cache;
    }

	$photo_fields = ws_ls_meta_fields_photos_all( $hide_from_shortcodes );

	// If no active photo fields, then we don't have any photos.
	if ( true === empty( $photo_fields ) ) {
		return 0;
	}

	$sql = 'Select count(*) from ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' e ' .
	       ' inner join ' . $wpdb->prefix . WE_LS_TABLENAME . ' d on e.entry_id = d.id where weight_user_id = %d
	        and e.value <> "" and meta_field_id in (' . implode( ',', $photo_fields) . ')';

	$count = $wpdb->get_var( $wpdb->prepare( $sql, $user_id) );

    $count = ( false === empty($count) ) ? (int) $count : 0;

    ws_ls_cache_user_set( $user_id, 'no-photos', $count );
    return $count;
}

/**
 * Fetch HTML for given image
 *
 * @param $attachment_id
 * @param int $width
 * @param int $height
 * @param bool $include_full_url
 * @param null $css_class
 *
 * @return bool
 */
function ws_ls_photo_get( $attachment_id, $width = 200, $height = 200, $include_full_url = true, $css_class = NULL ) {

	$attributes = ( false === empty( $css_class ) ) ? [ 'class' => $css_class ] : '';

	$size = add_image_size( 'wt-photo', $width, $height, true );

	$photo['thumb'] = wp_get_attachment_image( $attachment_id, 'thumbnail', false, $attributes );

	if ( false === empty( $photo['thumb'] )) {

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
		'label' => esc_html__( 'Don\'t show to public?', WE_LS_SLUG ),
		'input' => 'html',
		'html' => '<label for="attachments-'.$post->ID.'-ws-ls-hide-image"><input type="checkbox" id="attachments-'.$post->ID.'-ws-ls-hide-image" name="attachments['.$post->ID.'][ws-ls-hide-image]" value="1"'.($already_hidden ? ' checked="checked"' : '').' /></label>',
		'helps' => esc_html__( 'If the user has uploaded this as part of their Weight Tracker progress, they probably don\'t want it viewable on a public attachment page!', WE_LS_SLUG ),
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
