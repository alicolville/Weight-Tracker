<?php
	defined('ABSPATH') or die("Jog on!");

	global $form_number;        // This is used to keep track of multiple forms on a page allowing us to pass messages to each
	global $save_response;      // This is used to keep track of form posts responses

	define( 'WE_LS_TITLE', 'Weight Tracker' );
	define( 'WE_LS_SLUG', 'weight-loss-tracker' );
	define( 'WE_LS_LICENSE_TYPES_URL', 'https://weight.yeken.uk/features' );
	define( 'WE_LS_CALCULATIONS_URL', '	https://weight.yeken.uk/calculations/' );
	define( 'WE_LS_UPGRADE_TO_PRO_URL', 'https://shop.yeken.uk/product/weight-tracker-pro/' );
	define( 'WE_LS_UPGRADE_TO_PRO_PLUS_URL', 'https://shop.yeken.uk/product/weight-tracker-pro-plus/' );
    define( 'WE_LS_FREE_TRIAL_URL', 'https://weight.yeken.uk/trial/' );
    define( 'WE_LS_CDN_CHART_JS', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js' );
	define( 'WE_LS_PRO_PRICE', 50.00 );
	define( 'WE_LS_PRO_PLUS_PRICE', 100.00 );
