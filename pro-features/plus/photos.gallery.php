<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_photos_gallery_js_css($mode = 'default') {

	$mode = ws_ls_photos_gallery_validate_mode($mode);

	wp_enqueue_script('ws-ls-pro-gallery', plugins_url( 'plus/unitegallery/js/unitegallery.min.js', dirname(__FILE__)) , array('jquery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('ws-ls-pro-gallery-css', plugins_url( 'plus/unitegallery/css/unite-gallery.css', dirname(__FILE__)), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-pro-gallery-run', plugins_url( 'plus/unitegallery/js/ws-ls-gallery.js', dirname(__FILE__)) , array('ws-ls-pro-gallery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-pro-gallery-theme', plugins_url( 'plus/unitegallery/skins/ug-theme-' . $mode . '.js', dirname(__FILE__)) , array('ws-ls-pro-gallery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('ws-ls-pro-gallery-css-theme', plugins_url( 'plus/unitegallery/skins/ug-theme-default.css', dirname(__FILE__)), array(), WE_LS_CURRENT_VERSION);
}



function ws_ls_photos_shortcode_gallery($user_id) {

	$html = '';

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id; // TODO: move to shortcode arg

	$mode = ws_ls_photos_gallery_validate_mode('compact'); // TODO: move to shortcode arg

	$photos = ws_ls_photos_db_get_all_photos($user_id, true, false, 'desc', 800, 800);

	if ( false === empty($photos) ) {

		ws_ls_photos_gallery_js_css($mode);

		$html = '<div id="ws-ls-'. uniqid() . '" class="ws-ls-photos-' . $mode . '" style="display:none;">';

		foreach ($photos as $photo) {

			$additional_data = sprintf(' alt="%s" data-image="%s" data-description="%s - %s"',
												esc_html($photo['date-display']),
												esc_html($photo['full']),
												esc_html($photo['date-display']),
												esc_html($photo['display'])
									);

			$photo['thumb'] = str_replace('src', $additional_data . ' src', $photo['thumb']);

			$html .= sprintf('%s',
				$photo['thumb']
			);

		}

		$html .= '</div>';

	}

	return $html;
}
add_shortcode('ws-ls-gallery', 'ws_ls_photos_shortcode_gallery');

function ws_ls_photos_gallery_validate_mode($mode) {
	return ( false === in_array($mode, ['default', 'carousel', 'compact']) ) ? 'default' : $mode;
}