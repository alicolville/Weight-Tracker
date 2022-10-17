<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render Barcode Reader
 *
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_barcode_reader( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [ 'querystring-key-user-id' => 'wt-user-id' ] );

	wp_enqueue_script( 'wt-kiosk-barcode', 'https://unpkg.com/html5-qrcode' );
	wp_enqueue_script( 'yk-barcode-scanner', plugins_url( 'assets/js/barcode-scanner.js', __FILE__ ), [ 'wt-kiosk-barcode' ] , WE_LS_CURRENT_VERSION );

	wp_localize_script( 'yk-barcode-scanner', 'wt_barcode_scanner_config', [  'current-url' => get_permalink(), 'querystring-key-user-id' => $arguments[ 'querystring-key-user-id' ] ] );

	return '<div class="ykuk-child-width-1-1 ykuk-text-center" ykuk-grid >
			    <div>
			        <div class="ykuk-card ykuk-card-default ykuk-card-body">
			            <div id="wt-barcode-reader" ></div>
			        </div>
			    </div>
			</div>';
}
