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
 * Also provides the base function for [wlt-awards]
 *
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_photos_shortcode_gallery($user_defined_arguments) {

	if( false === WS_LS_IS_PREMIUM ) {
		return '';
	}

	$arguments = shortcode_atts([   'error-message'                         => ( false === empty( $user_defined_arguments[ 'source' ] ) ) ?
																				esc_html__('No awards.', WE_LS_SLUG ) : esc_html__('It doesn\'t look you\'ve uploaded any photos.', WE_LS_SLUG ),
									'user-id'                               => get_current_user_id(),
									'mode'                                  => 'default',                   // Gallery type: carousel, default or compact
									'height'                                => 800,                         // Height of slider if compact or default theme
									'css-class'                             => '',
									'display-title'                         => true,                        // Display title that overlays thumbs (tilegrid)
									'width'                                 => false,
									'limit'                                 => 20,
									'direction'                             => 'desc',
									'custom-fields-to-use'                  => '',
									'custom-fields-hide-from-shortcodes'    => true,
									'source'                                => 'photos'                     // Source of gallery photos e.g. photos or award badges
	], $user_defined_arguments );

	$arguments['custom-fields-hide-from-shortcodes']    = ws_ls_force_bool_argument($arguments['custom-fields-hide-from-shortcodes']);
	$arguments['display-title']                         = ws_ls_force_bool_argument( $arguments['display-title'] );
	$arguments['width']                                 = ws_ls_force_dimension_argument($arguments['width'], 800);
	$arguments['height']                                = ws_ls_force_numeric_argument($arguments['height'], 800);
	$arguments['user-id']                               = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
	$arguments['mode']                                  = ws_ls_photos_gallery_validate_mode($arguments['mode']);
	$arguments['limit']                                 = ( false === empty($arguments['limit']) ) ? (int) $arguments['limit'] : false;
	$arguments['direction']                             = ( false === in_array($arguments['direction'], ['asc', 'desc'])) ? 'desc' : $arguments['direction'];

	// Only allow this to render awards if in pro mode
	if ( 'awards' === $arguments['source'] && false === WS_LS_IS_PREMIUM ) {
		$arguments['source'] = 'photos';
	}

	$html = $arguments['error-message'];

	// Deal with 100%
	$thumb_width = ( $arguments['width'] === '100%') ? 1200 : (int) $arguments['width'];

	if ( 'awards' === $arguments['source'] ) {

		$photos = ws_ls_awards_previous_awards( $arguments['user-id'], $thumb_width, $arguments['height'] );

		if ( false === empty( $arguments['limit'] ) ) {
			$photos = array_slice( $photos, 0, $arguments['limit'] );
		}

	} else {

		$photos = ws_ls_photos_db_get_all_photos( $arguments['user-id'], true,  $arguments['limit'],
			$arguments['direction'], $thumb_width, $arguments['height'], $arguments['custom-fields-to-use'], $arguments['custom-fields-hide-from-shortcodes'] );

	}

	if ( false === empty( $photos ) ) {

		ws_ls_photos_gallery_js_css( $arguments[ 'mode' ] );

		// If compact / default pass config settings to JS
		wp_localize_script('ws-ls-pro-gallery', 'ws_ls_gallery_config', ['height' => $arguments['height'], 'width' => $arguments['width']]);

		$html = sprintf(    '<div id="ws-ls-%s" class="ws-ls-photos-%s%s" style="display:none" data-display-title="%s" >',
			uniqid(),
			$arguments['mode'],
			( false === empty( $arguments['css-class'] ) ) ? ' ' . esc_attr( $arguments['css-class'] ) : '',
			( true === $arguments['display-title'] ) ? 'true' : 'false'
		);

		foreach ( $photos as $photo ) {

			if ( 'awards' !== $arguments['source'] ) {
				$weight = ws_ls_weight_display( $photo[ 'kg' ], $arguments['user-id'] );
				$date   = ws_ls_convert_ISO_date_into_locale( $photo[ 'weight_date' ] );

				$photo['display-text'] = $date['display-date'] . ' &middot; ' . $photo[ 'field_name' ] . ' &middot; ' . $weight[ 'display' ];
			}

			$additional_data = sprintf(' alt="%1$s" data-image="%2$s" data-description="%1$s"',
				esc_html( $photo['display-text'] ),
				esc_html( $photo['full'] )
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
add_shortcode('wlt-gallery', 'ws_ls_photos_shortcode_gallery');
add_shortcode('wt-gallery', 'ws_ls_photos_shortcode_gallery');
add_shortcode('wt-photo-gallery', 'ws_ls_photos_shortcode_gallery');

/**
 * Used to validate the type of uniteGallery being used.
 *
 * @param $mode
 * @return string
 */
function ws_ls_photos_gallery_validate_mode($mode) {
	return ( false === in_array($mode, ['default', 'carousel', 'compact', 'tilesgrid']) ) ? 'default' : $mode;
}
