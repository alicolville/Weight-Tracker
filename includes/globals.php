<?php
	defined('ABSPATH') or die("Jog on!");

	global $form_number;

    // -----------------------------------------------------------------------------------
	// Constants - highly recommended that you don't change these
	// -----------------------------------------------------------------------------------

	define('WE_LS_TITLE', 'Weight Tracker');
	define('WE_LS_SLUG', 'weight-loss-tracker');
	define('WE_LS_STATS_URL', 'https://weight.yeken.uk/wlt/stats.php');
	define('WE_LS_LICENSE_TYPES_URL', 'https://weight.yeken.uk/features');
	define('WE_LS_CALCULATIONS_URL', '	https://weight.yeken.uk/calculations/');
	define('WE_LS_UPGRADE_TO_PRO_URL', 'https://shop.yeken.uk/product/weight-tracker-pro/');
	define('WE_LS_UPGRADE_TO_PRO_PLUS_URL', 'https://shop.yeken.uk/product/weight-tracker-pro-plus/');
    define('WE_LS_FREE_TRIAL_URL', 'https://weight.yeken.uk/trial/');
    define('WE_LS_UPGRADE_TO_PRO_PLUS_UPGRADE_URL', 'https://weight.yeken.uk/get-pro-plus-existing-license-holders/');
    define('WE_LS_CDN_CHART_JS', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js');
	define('WE_LS_KEY_YEKEN_ADMIN_NOTIFICATION', 'yeken-admin-notification');
	define('WE_LS_TABLE_MAX_WEEK_FILTERS', 150);
	define('WS_LS_PRO_PRICE', 50.00);
	define('WS_LS_PRO_PLUS_PRICE', 100.00);

	// -----------------------------------------------------------------------------------
	// Hooks / Filters
	// -----------------------------------------------------------------------------------


	define('WE_LS_HOOK_DATA_ALL_DELETED', 'wlt-hook-data-all-deleted');
	define('WE_LS_HOOK_DATA_ENTRY_DELETED', 'wlt-hook-data-entry-deleted');
    define('WE_LS_HOOK_LICENSE_EXPIRED', 'wlt-hook-license-expired');

    define('WE_LS_FILTER_EMAIL_DATA', 'wlt-filter-email-data');
	define('WE_LS_FILTER_STATS_SHORTCODE', 'wlt-filter-stats-shortcode');
	define('WE_LS_FILTER_STATS_ROW', 'wlt-filter-stats-table-row');
	define('WE_LS_FILTER_STATS_TABLE_HTML', 'wlt-filter-stats-table-html');
	define('WE_LS_FILTER_ADMIN_USER_SIDEBAR_TOP', 'wlt-filter-admin-user-sidebar-top');
	define('WE_LS_FILTER_ADMIN_USER_SIDEBAR_MIDDLE', 'wlt-filter-admin-user-sidebar-middle');
	define('WE_LS_FILTER_ADMIN_USER_SIDEBAR_BOTTOM', 'wlt-filter-admin-user-sidebar-bottom');
	define('WE_LS_FILTER_USER_SETTINGS_BELOW_AIM', 'wlt-filter-user-settings-below-aim');
    define('WE_LS_FILTER_USER_SETTINGS_BELOW_GENDER', 'wlt-filter-user-settings-below-gender');
    define('WE_LS_FILTER_USER_SETTINGS_DB_FORMATS', 'wlt-filter-user-settings-db-formats');
    define('WE_LS_FILTER_USER_SETTINGS_SAVE_FIELDS', 'wlt-filter-user-settings-save-fields');
    define('WE_LS_FILTER_JS_WS_LS_CONFIG', 'wlt-filter-js-ws-ls-config');
    define('WE_LS_FILTER_HARRIS', 'wlt-filter-harris-benedict');
    define('WE_LS_FILTER_HARRIS_ALLOWED_PROGRESS', 'wlt-filter-harris-benedict-allowed-progresses');
    define('WE_LS_FILTER_HARRIS_TOP_OF_TABLE', 'wlt-filter-harris-benedict-top-of-table');

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
		'WE_LS_DISABLE_USER_STATS' => false,
		'WE_LS_EMAIL_ENABLE' => false,
		'WE_LS_EMAIL_ADDRESSES' => '',
		'WE_LS_ABOUT_YOU_MANDATORY' => false,
		'WE_LS_EMAIL_NOTIFICATIONS_EDIT' => true,
		'WE_LS_EMAIL_NOTIFICATIONS_NEW' => true,
		'WE_LS_EMAIL_NOTIFICATIONS_TARGETS' => true,
		'WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL' => 'manage_options', // Default to admin only being allowed to edit / view user data
        'WS_LS_CAL_CAP_MALE' => 1900,
        'WS_LS_CAL_CAP_FEMALE' => 1400,
		'WS_LS_MACRO_PROTEINS' => 25,
        'WS_LS_MACRO_CARBS' => 50,
        'WS_LS_MACRO_FATS' => 25,
		'WE_LS_PHOTOS_MAX_SIZE' => false,
        'WE_LS_THIRD_PARTY_GF_ENABLE' => false
	);

	// -----------------------------------------------------------------------------------
	// Allow user's to override the default admin settings?
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && (false == get_option('ws-ls-allow-user-preferences') || 'yes' == get_option('ws-ls-allow-user-preferences'))) {
		define('WE_LS_ALLOW_USER_PREFERENCES', true);
	} else {
		define('WE_LS_ALLOW_USER_PREFERENCES', false);
	}
	// -----------------------------------------------------------------------------------
	// Define whether Imperial and Units
	// -----------------------------------------------------------------------------------
	if ('yes' == get_option('ws-ls-about-you-mandatory')){
		$globals['WE_LS_ABOUT_YOU_MANDATORY'] = true;
	}
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
	// Who can view / edit user data
	// -----------------------------------------------------------------------------------
	$permission_check = get_option('ws-ls-edit-permissions');
	if (false === empty($permission_check) && in_array($permission_check, ['manage_options', 'read_private_posts', 'publish_posts']) ){
		$globals['WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL'] = $permission_check;
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

