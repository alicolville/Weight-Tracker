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

	$arguments = wp_parse_args( $arguments, [ 'querystring-key-user-id' => 'wt-user-id', 'open' => '' ] );

	wp_enqueue_script( 'wt-kiosk-barcode', 'https://unpkg.com/html5-qrcode' );
	wp_enqueue_script( 'yk-barcode-scanner', plugins_url( 'assets/js/barcode-scanner.js', __FILE__ ), [ 'wt-kiosk-barcode' ] , WE_LS_CURRENT_VERSION );

	$config = [ 'current-url'                   => get_permalink(),
	            'querystring-key-user-id'       => $arguments[ 'querystring-key-user-id' ],
				'text-error-loading-cameras'    => __( 'Could not load any cameras for the barcode reader. Please ensure you have one or more cameras attached to this device and you are accessing it via https.', WE_LS_SLUG ),
				'open'                          => $arguments[ 'open' ]
	];

	wp_localize_script( 'yk-barcode-scanner', 'wt_barcode_scanner_config', $config );

	return '<div id="ykuk-barcode-reader-container" class="ykuk-child-width-1-1 ykuk-text-center ws-ls-hide" ykuk-grid >
			    <div>
			        <div class="ykuk-card ykuk-card-default ykuk-card-body">
			        	<center>
			        		<div id="wt-barcode-reader" class="ykuk-margin-bottom"></div>
			        		<select id="wt-barcode-reader-devices-list"></select>
						</center>
			        </div>
			    </div>
			</div>
			<div id="ykuk-barcode-lazer-container" class="ykuk-child-width-1-1 ykuk-text-center ws-ls-hide" ykuk-grid >
			    <div>
			        <div class="ykuk-card ykuk-card-default ykuk-card-body">
			        	<div class="ykuk-margin ykuk-child-width-1-1">
					        <div class="ykuk-inline">
					            <span class="ykuk-form-icon" ykuk-icon="icon: credit-card"></span>
					            <input class="ykuk-input" type="text" id="ykuk-barcode-lazer-value">
					        </div>
					    </div>
			        </div>
			    </div>
			</div>';
}
