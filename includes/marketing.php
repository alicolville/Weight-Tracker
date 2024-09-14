<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render a list of features
 * @param array features
 * @param bool $echo
 * @param string $format 'table' or 'ul' or 'markdown'
 */
function ws_ls_display_features( $features, $echo = true, $format = 'table'  ) {

	if ( true === empty( $features ) ) {
		return;
	}

	switch( $format ) {
		case 'table':
			$html = '';
			break;
		case 'ul':
			$html = '';
			break;
		default:
			$html = '';
	}

	$html = 'table' === $format ? '<table class="form-table yk-mt-features-table">' : '<ul>';

	$class = '';

	foreach ( $features as $feature ) {

		$class 	= ('alternate' == $class) ? '' : 'alternate';

		switch( $format ) {
			case 'table':
				$html_template = '<tr class="%1$s">
										<td>
											&middot; <strong>%2$s</strong> - %3$s
										</td>
									</tr>';
				break;
			case 'ul':
				$html_template = '<li><strong>%2$s</strong> - %3$s</li>';
				break;
			default:	
				$html_template = '* **%2$s** - %3$s' . PHP_EOL;
		}

		$row 	= sprintf( 	$html_template,
							$class,
							$feature[ 'title' ],
							$feature[ 'description' ] );

		$html .= $row;
	}	

	switch( $format ) {
		case 'table':
			$html .= '</table>';
			break;
		case 'ul':
			$html .= '</ul>';
			break;
		default:
			$html .= '';
	}

	if ( false === $echo ) {
		return $html;
	}

	ws_ls_echo_wp_kses( $html );	
}

/**
 * Render a list of features
 * @param array features
 */
function ws_ls_shortcode_display_features() {
	return ws_ls_display_features( ws_ls_feature_list_pro(), false, 'ul' );
}
add_shortcode( 'wt-features-table', 'ws_ls_shortcode_display_features' );

/**
 * Render a list of pro features
 * @param array features
 */
function ws_ls_shortcode_display_pro_features() {
	return ws_ls_display_features( ws_ls_feature_list_pro_plus(), false, 'ul' );
}
add_shortcode( 'wt-pro-features-table', 'ws_ls_shortcode_display_pro_features' );

/**
 * Display WP Version
 * @return text
 */
function ws_ls_shortcode_version() {
	return esc_html( WE_LS_CURRENT_VERSION );
}
add_shortcode( 'wt-version', 'ws_ls_shortcode_version' );

// add_action( 'init', function() {
// 	ws_ls_display_features(  ws_ls_feature_list_pro(), true, $format = 'markdown'  );
// 	ws_ls_display_features(  ws_ls_feature_list_pro_plus(), true, $format = 'markdown'  );
// 	die;
// });