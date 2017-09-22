<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Enqueue relevant JS / CSS for Photo / uniteGallery
 * @param string $mode
 */
function ws_ls_photos_gallery_js_css($mode = 'default') {

	$mode = ws_ls_photos_gallery_validate_mode($mode);

	wp_enqueue_script('ws-ls-pro-gallery', plugins_url( 'plus/unitegallery/js/unitegallery.min.js', dirname(__FILE__)) , array('jquery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('ws-ls-pro-gallery-css', plugins_url( 'plus/unitegallery/css/unite-gallery.css', dirname(__FILE__)), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-pro-gallery-run', plugins_url( 'plus/unitegallery/js/ws-ls-gallery.js', dirname(__FILE__)) , array('ws-ls-pro-gallery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-pro-gallery-theme', plugins_url( 'plus/unitegallery/skins/ug-theme-' . $mode . '.js', dirname(__FILE__)) , array('ws-ls-pro-gallery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('ws-ls-pro-gallery-css-theme', plugins_url( 'plus/unitegallery/skins/ug-theme-default.css', dirname(__FILE__)), array(), WE_LS_CURRENT_VERSION);
}

/**
 * [wlt-gallery] shortcode
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_photos_shortcode_gallery($user_defined_arguments) {

    $arguments = shortcode_atts([
        'error-message' => __('It doesn\'t look you\'ve uploaded any photos.', WE_LS_SLUG ),
        'user-id' => get_current_user_id(),
        'mode' => 'default',                    // Gallery type: carousel, default or compact
        'height' => 800,                        // Height of slider if compact or default theme
		'css-class' => '',
        'width' => false,
        'limit' => 20,
        'direction' => 'desc'
    ], $user_defined_arguments );

	$arguments['width'] = ws_ls_force_dimension_argument($arguments['width'], 800);
    $arguments['height'] = ws_ls_force_numeric_argument($arguments['height'], 800);
    $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
    $arguments['mode'] = ws_ls_photos_gallery_validate_mode($arguments['mode']);
    $arguments['limit'] = ( false === empty($arguments['limit']) ) ? intval($arguments['limit']) : false;
    $arguments['direction'] = ( false === in_array($arguments['direction'], ['asc', 'desc'])) ? 'desc' : $arguments['direction'];

    $html = $arguments['error-message'];

	// Deal with 100%
	$thumb_width = ($arguments['width'] === '100%') ? 1200 : intval($arguments['width']);

  	$photos = ws_ls_photos_db_get_all_photos($arguments['user-id'], true,  $arguments['limit'],
												$arguments['direction'], $thumb_width, $arguments['height']);

	if ( false === empty($photos) ) {

		ws_ls_photos_gallery_js_css($arguments['mode']);

        // If compact / default pass config settings to JS
		wp_localize_script('ws-ls-pro-gallery', 'ws_ls_gallery_config', ['height' => $arguments['height'], 'width' => $arguments['width']]);

		$html = '<div id="ws-ls-'. uniqid() . '" class="ws-ls-photos-' . $arguments['mode'] . ((false === empty($arguments['css-class'])) ? ' ' . esc_attr($arguments['css-class']) : '' ) . '" style="display:none;">';

		foreach ($photos as $photo) {

			$additional_data = sprintf(' alt="%s" data-image="%s" data-description="%s - %s"',
												esc_html($photo['date-display'] . ' &middot; ' . $photo['display']),
												esc_html($photo['full']),
												esc_html($photo['date-display']),
												esc_html($photo['display'])
									);

			$photo['thumb'] = str_replace('src', $additional_data . ' src', $photo['thumb']);

			// $html .= '<a href="" target="_blank" class="ug-tile-icon ug-icon-link">';

			$html .= sprintf('%s',
				$photo['thumb']
			);

			//$html .= '</a>';
		}

		$html .= '</div>';
	}

	return $html;
}
add_shortcode('wlt-gallery', 'ws_ls_photos_shortcode_gallery');

/**
 * Used to validate the type of uniteGallery being used.
 *
 * @param $mode
 * @return string
 */
function ws_ls_photos_gallery_validate_mode($mode) {
	return ( false === in_array($mode, ['default', 'carousel', 'compact', 'tilesgrid']) ) ? 'default' : $mode;
}