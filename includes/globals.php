<?php
	defined('ABSPATH') or die("Jog on!");

	global $form_number;        // This is used to keep track of multiple forms on a page allowing us to pass messages to each
	global $save_response;      // This is used to keep track of form posts responses

    // -----------------------------------------------------------------------------------
	// YeKen Globals
	// -----------------------------------------------------------------------------------

	define( 'WE_LS_TITLE', 'Weight Tracker' );
	define( 'WE_LS_SLUG', 'weight-loss-tracker' );
	define( 'WE_LS_LICENSE_TYPES_URL', 'https://weight.yeken.uk/features' );
	define( 'WE_LS_CALCULATIONS_URL', '	https://weight.yeken.uk/calculations/' );
	define( 'WE_LS_UPGRADE_TO_PRO_URL', 'https://shop.yeken.uk/product/weight-tracker-pro/' );
	define( 'WE_LS_UPGRADE_TO_PRO_PLUS_URL', 'https://shop.yeken.uk/product/weight-tracker-pro-plus/' );
    define( 'WE_LS_FREE_TRIAL_URL', 'https://weight.yeken.uk/trial/' );
    define( 'WE_LS_UPGRADE_TO_PRO_PLUS_UPGRADE_URL', 'https://weight.yeken.uk/get-pro-plus-existing-license-holders/' );
    define( 'WE_LS_CDN_CHART_JS', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js' );
	define( 'WE_LS_PRO_PRICE', 50.00 );
	define( 'WE_LS_PRO_PLUS_PRICE', 100.00 );

    // -----------------------------------------------------------------------------------
	// Dynamic Settings based upon user settings, etc
	// -----------------------------------------------------------------------------------

    // Set defaults
	$globals = array(
		'WE_LS_DATA_UNITS' => 'kg',
		'WE_LS_IMPERIAL_WEIGHTS' => false,
		'WE_LS_ALLOW_TARGET_WEIGHTS' => true,
		'WE_LS_CSS_ENABLED' => true,
		'WE_LS_US_DATE' => false,
		'WS_LS_ADVANCED_TABLES' => true,
		'WE_LS_DISPLAY_BMI_IN_TABLES' => false,
		'WE_LS_ALLOW_STATS' => false,
		'WE_LS_EMAIL_ENABLE' => false,
		'WE_LS_EMAIL_ADDRESSES' => '',
		'WE_LS_EMAIL_NOTIFICATIONS_EDIT' => true,
		'WE_LS_EMAIL_NOTIFICATIONS_NEW' => true,
		'WE_LS_EMAIL_NOTIFICATIONS_TARGETS' => true,
		'WS_LS_CAL_CAP_MALE' => 1900,
        'WS_LS_CAL_CAP_FEMALE' => 1400,
		'WS_LS_MACRO_PROTEINS' => 25,
        'WS_LS_MACRO_CARBS' => 50,
        'WS_LS_MACRO_FATS' => 25,
		'WE_LS_PHOTOS_MAX_SIZE' => false,
        'WE_LS_THIRD_PARTY_GF_ENABLE' => false
	);

	// -----------------------------------------------------------------------------------
	// Define whether Imperial and Units
	// -----------------------------------------------------------------------------------
	if ('stones_pounds' == get_option('ws-ls-units') || 'pounds_only' == get_option('ws-ls-units')) {
		$globals['WE_LS_DATA_UNITS'] = get_option('ws-ls-units');
		$globals['WE_LS_IMPERIAL_WEIGHTS'] = true;
	}
	// -----------------------------------------------------------------------------------
	// UK or US date?
	// -----------------------------------------------------------------------------------
	if ('us' == get_option('ws-ls-use-us-dates')){
		$globals['WE_LS_US_DATE'] = true;
	}

	// -----------------------------------------------------------------------------------
	// Define if target weights enabled
	// -----------------------------------------------------------------------------------
	if ('no' == get_option('ws-ls-allow-targets')) {
		$globals['WE_LS_ALLOW_TARGET_WEIGHTS'] = false;
	}
	// -----------------------------------------------------------------------------------
	// Disable plugin CSS
	// -----------------------------------------------------------------------------------
	if ('yes' == get_option('ws-ls-disable-css')) {
		$globals['WE_LS_CSS_ENABLED'] = false;
	}
	// -----------------------------------------------------------------------------------
	// Display BMI in tables?
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && ('yes' == get_option('ws-ls-display-bmi-in-tables') || false == get_option('ws-ls-display-bmi-in-tables'))) {
		$globals['WE_LS_DISPLAY_BMI_IN_TABLES'] = true;
	}

	// -----------------------------------------------------------------------------------
	// Email Notifications
	// -----------------------------------------------------------------------------------

	if (WS_LS_IS_PRO) {

		if ('yes' == get_option('ws-ls-email-enable')) {
			$globals['WE_LS_EMAIL_ENABLE'] = true;
		}
		if (get_option('ws-ls-email-addresses')) {
			$globals['WE_LS_EMAIL_ADDRESSES'] = get_option('ws-ls-email-addresses');
		} else {
			$globals['WE_LS_EMAIL_ADDRESSES'] = '';
		}
		if ('no' == get_option('ws-ls-email-notifications-edit')) {
			$globals['WE_LS_EMAIL_NOTIFICATIONS_EDIT'] = false;
		}
		if ('no' == get_option('ws-ls-email-notifications-new')) {
			$globals['WE_LS_EMAIL_NOTIFICATIONS_NEW'] = false;
		}
		if ('no' == get_option('ws-ls-email-notifications-targets')) {
			$globals['WE_LS_EMAIL_NOTIFICATIONS_TARGETS'] = false;
		}
	}

    // -----------------------------------------------------------------------------------
    // Pro
    // -----------------------------------------------------------------------------------

    if (WS_LS_IS_PRO) {

        if ( 'yes' == get_option('ws-ls-gf-enable') ) {
            $globals['WE_LS_THIRD_PARTY_GF_ENABLE'] = true;
        }

	    $photo_max_size = get_option('ws-ls-photos-max-size');

	    if(is_numeric($photo_max_size)) {
		    $globals['WE_LS_PHOTOS_MAX_SIZE'] = (int) $photo_max_size;
	    }

    }

    // -----------------------------------------------------------------------------------
    // Pro Plus
    // -----------------------------------------------------------------------------------

    if (WS_LS_IS_PRO_PLUS) {

	    // Calories
	    $female_cal_cap = get_option('ws-ls-female-cal-cap');

	    if(is_numeric($female_cal_cap)) {
            $globals['WS_LS_CAL_CAP_FEMALE'] =  (int) $female_cal_cap;
        }

        $male_cal_cap = get_option('ws-ls-male-cal-cap');

        if(is_numeric($male_cal_cap)) {
            $globals['WS_LS_CAL_CAP_MALE'] =  (int) $male_cal_cap;
        }

	    // Macro N

        $macro_value = get_option('ws-ls-macro-proteins');

        if(is_numeric($macro_value) && $macro_value > 0 && $macro_value < 100) {
            $globals['WS_LS_MACRO_PROTEINS'] = (int) $macro_value;
        }

        $macro_value = get_option('ws-ls-macro-carbs');

        if(is_numeric($macro_value) && $macro_value > 0 && $macro_value < 100) {
            $globals['WS_LS_MACRO_CARBS'] = (int) $macro_value;
        }

        $macro_value = get_option('ws-ls-macro-fats');

        if(is_numeric($macro_value) && $macro_value > 0 && $macro_value < 100) {
            $globals['WS_LS_MACRO_FATS'] = (int) $macro_value;
        }

    }

	// -----------------------------------------------------------------------------------
	// Loop through array and set defines!
	// -----------------------------------------------------------------------------------
    foreach($globals as $key => $value) {
		define($key, $value);
	}

