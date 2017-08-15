<?php
	defined('ABSPATH') or die("Jog on!");

	global $form_number;

    // -----------------------------------------------------------------------------------
	// Constants - highly recommended that you don't change these
	// -----------------------------------------------------------------------------------
	define('WE_LS_TITLE', 'Weight Tracker');
	define('WE_LS_SLUG', 'weight-loss-tracker');
	define('WE_LS_DATA_URL', 'https://weight.yeken.uk/wlt/plugin-info.json');
	define('WE_LS_STATS_URL', 'https://weight.yeken.uk/wlt/stats.php');
    define('WE_LS_CDN_CHART_JS', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js');
	define('WE_LS_TABLENAME', 'WS_LS_DATA');
	define('WE_LS_TARGETS_TABLENAME', 'WS_LS_DATA_TARGETS');
  	define('WE_LS_USER_PREFERENCES_TABLENAME', 'WS_LS_DATA_USER_PREFERENCES');
	define('WE_LS_USER_STATS_TABLENAME', 'WS_LS_DATA_USER_STATS');
	define('WE_LS_CACHE_ENABLED', true);
	define('WE_LS_CACHE_TIME', 15 * MINUTE_IN_SECONDS);
	define('WE_LS_CACHE_COMMS_KEY', 'comm-with-yeken');
	define('WE_LS_CACHE_COMMS_KEY_TIME', 4 * WEEK_IN_SECONDS);
	define('WE_LS_CACHE_STATS_TABLE', 'stats-table-html');
	define('WE_LS_CACHE_KEY_TARGET', 'target-data');
	define('WE_LS_CACHE_KEY_DATA', 'weight-data');
	define('WE_LS_CACHE_KEY_MIN_MAX_DATES', 'min-max-dates');
	define('WE_LS_CACHE_KEY_TARGET_WEIGHT', 'target-weight');
	define('WE_LS_CACHE_KEY_START_WEIGHT', 'start-weight-index');
	define('WE_LS_CACHE_KEY_WEIGHT_EXTREME', 'weight-extreme-');
	define('WE_LS_CACHE_KEY_USER_PREFERENCE', 'user-preference');
	define('WE_LS_CACHE_KEY_USER_HEIGHT', 'user-height');
	define('WE_LS_CACHE_KEY_YEKEN_JSON', 'yeken-json-lookup-wlt');
	define('WE_LS_CACHE_KEY_STATS_SUMMARY', 'user-stats-summary');
	define('WE_LS_CACHE_KEY_WEIGHT_FOR_DAY', 'user-weight-for-day');
    define('WE_LS_CACHE_KEY_ENTRY_COUNTS', 'entry-counts');
	define('WE_LS_CACHE_KEY_BMR', 'bmr-');
	define('WE_LS_CACHE_KEY_HARRIS_BENEDICT', 'harris-benedict');
	define('WE_LS_KEY_YEKEN_ADMIN_NOTIFICATION', 'yeken-admin-notification');
	define('WE_LS_CACHE_ADMIN_USER_DATA', 'admin-user-data');
	define('WE_LS_TABLE_MAX_WEEK_FILTERS', 150);
	define('WS_LS_PRO_PRICE', 25.00);
	define('WE_LS_USE_MINIFIED_SCRIPTS', true);
	define('WE_LS_CRON_NAME', 'weight_loss_tracker_hourly');
	define('WE_LS_CRON_NAME_YEKEN_COMMS', 'weight_loss_tracker_yeken_comms');
	define('WE_LS_CRON_SCHEDULE_WEEKLY', 'weight_loss_tracker_weekly');

	// -----------------------------------------------------------------------------------
	// Hooks / Filters
	// -----------------------------------------------------------------------------------

	define('WE_LS_HOOK_DATA_ADDED_EDITED', 'wlt-hook-data-added-edited');
	define('WE_LS_HOOK_DATA_ALL_DELETED', 'wlt-hook-data-all-deleted');
	define('WE_LS_HOOK_DATA_USER_DELETED', 'wlt-hook-data-user-deleted');

	define('WE_LS_FILTER_EMAIL_DATA', 'wlt-filter-email-data');
	define('WE_LS_FILTER_STATS_SHORTCODE', 'wlt-filter-stats-shortcode');
	define('WE_LS_FILTER_STATS_ROW', 'wlt-filter-stats-table-row');
	define('WE_LS_FILTER_STATS_TABLE_HTML', 'wlt-filter-stats-table-html');

    // -----------------------------------------------------------------------------------
	// Dynamic Settings based upon user settings, etc
	// -----------------------------------------------------------------------------------

    // Set defaults
	$globals = array(
		'WE_LS_DATA_UNITS' => 'kg',
		'WE_LS_IMPERIAL_WEIGHTS' => false,
		'WE_LS_ALLOW_TARGET_WEIGHTS' => true,
		'WE_LS_ALLOW_POINTS' => true,
		'WE_LS_USE_TABS' => true,
		'WE_LS_CSS_ENABLED' => true,
		'WE_LS_TARGET_LINE_COLOUR' => '#76bada',
		'WE_LS_WEIGHT_LINE_COLOUR' => '#aeaeae',
		'WE_LS_WEIGHT_FILL_COLOUR' => '#f9f9f9',
		'WE_LS_US_DATE' => false,
		'WE_LS_CHART_TYPE' => 'line', //line, bar
		'WS_LS_ADVANCED_TABLES' => true,
		'WE_LS_CHART_MAX_POINTS' => 25,
		'WE_LS_CHART_HEIGHT' => 250,
		'WE_LS_CHART_BEZIER_CURVE' => true,
		'WE_LS_CHART_POINT_SIZE' => 3,
		'WE_LS_CHART_SHOW_GRID_LINES' => true,
		'WE_LS_DISPLAY_BMI_IN_TABLES' => false,
		'WE_LS_AXES_START_AT_ZERO' => false,
		'WE_LS_ALLOW_STATS' => false,
		'WE_LS_DISABLE_USER_STATS' => false,
		'WE_LS_EMAIL_ENABLE' => false,
		'WE_LS_EMAIL_ADDRESSES' => '',
		'WE_LS_EMAIL_NOTIFICATIONS_EDIT' => true,
		'WE_LS_EMAIL_NOTIFICATIONS_NEW' => true,
		'WE_LS_EMAIL_NOTIFICATIONS_TARGETS' => true,
		'WE_LS_DISABLE_YEKEN_NOTIFICATIONS' => false,
		'WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL' => 'manage_options' // Default to admin only being allowed to edit / view user data
	);

    // -----------------------------------------------------------------------------------
	// Measurements (4.0+)
	// -----------------------------------------------------------------------------------

    // Supported measurements:
    // Bust/Chest, Waist, Navel, Hips, Buttocks, Right and Left Thighs, Right and Left Biceps, Calves and Height
    $globals['WE_LS_MEASUREMENTS_ENABLED'] = false;
	$globals['WE_LS_MEASUREMENTS_UNIT'] = (false == get_option('ws-ls-measurement-units')) ? 'cm' : get_option('ws-ls-measurement-units');
	$globals['WE_LS_MEASUREMENTS_MANDATORY'] = (false == get_option('ws-ls-measurements-mandatory') || 'no' == get_option('ws-ls-measurements-mandatory')) ? false : true;

    if (WS_LS_IS_PRO && ('yes' == get_option('ws-ls-allow-measurements'))) {
    	$globals['WE_LS_MEASUREMENTS_ENABLED'] = true;
	}

    $supported_measurements = array(
		'left_forearm' => array('title' => __('Forearm - Left', WE_LS_SLUG), 'abv' => __('FL', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#f279ed'),
        'right_forearm' => array('title' => __('Forearm - Right', WE_LS_SLUG), 'abv' => __('FR', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#a2039b'),
        'left_bicep' => array('title' => __('Bicep - Left', WE_LS_SLUG), 'abv' => __('BL', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#b00125'),
        'right_bicep' => array('title' => __('Bicep - Right', WE_LS_SLUG), 'abv' => __('BR', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#035e60'),
		'left_calf' => array('title' => __('Calf - Left', WE_LS_SLUG), 'abv' => __('CL', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#ffc019'),
        'right_calf' => array('title' => __('Calf - Right', WE_LS_SLUG), 'abv' => __('CR', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#ff7b9c'),
		'left_thigh' => array('title' => __('Thigh - Left', WE_LS_SLUG), 'abv' => __('TL', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#eaec13'),
        'right_thigh' => array('title' => __('Thigh - Right', WE_LS_SLUG), 'abv' => __('TR', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#0101DF'),
    	'waist' => array('title' => __('Waist', WE_LS_SLUG), 'abv' => __('W', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#8b860b'),
		'bust_chest' => array('title' => __('Bust / Chest', WE_LS_SLUG), 'abv' => __('BC', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#9600ff'),
		'shoulders' => array('title' => __('Shoulders', WE_LS_SLUG), 'abv' => __('S', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#70c7c7'),
		'height' => array('title' => __('Height', WE_LS_SLUG), 'abv' => __('H', WE_LS_SLUG), 'enabled' => true, 'user_preference' => true),
        'buttocks' => array('title' => __('Buttocks', WE_LS_SLUG), 'abv' => __('B', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#240d1d'),
        'hips' => array('title' => __('Hips', WE_LS_SLUG), 'abv' => __('HI', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#35e364'),
        'navel' => array('title' => __('Navel', WE_LS_SLUG), 'abv' => __('NA', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#a28c87'),
		'neck' => array('title' => __('Neck', WE_LS_SLUG), 'abv' => __('NE', WE_LS_SLUG), 'user_preference' => false, 'enabled' => false, 'chart_colour' => '#FA8072')
    );

    $globals['WE_LS_MEASUREMENTS'] = json_encode($supported_measurements);

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
	// Plot points
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && get_option('ws-ls-max-points')){
		$globals['WE_LS_CHART_MAX_POINTS'] = get_option('ws-ls-max-points');
	}
	// -----------------------------------------------------------------------------------
	// Point Size
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && get_option('ws-ls-point-size')){
		$globals['WE_LS_CHART_POINT_SIZE'] = get_option('ws-ls-point-size');
	}
	// -----------------------------------------------------------------------------------
	// Bezier Curve
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && 'no' == get_option('ws-ls-bezier-curve')){
		$globals['WE_LS_CHART_BEZIER_CURVE'] = false;
	}
	// -----------------------------------------------------------------------------------
	// Grid Lines?
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && 'no' == get_option('ws-ls-grid-lines')){
		$globals['WE_LS_CHART_SHOW_GRID_LINES'] = false;
	}
	// -----------------------------------------------------------------------------------
	// Bar chart?
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && in_array(get_option('ws-ls-chart-type'), array('bar', 'line'))){
		$globals['WE_LS_CHART_TYPE'] = get_option('ws-ls-chart-type');
	}
	// -----------------------------------------------------------------------------------
	// Advanced tables?
	// -----------------------------------------------------------------------------------
	if (WS_LS_IS_PRO && ('yes' == get_option('ws-ls-allow-advanced-tables') || false == get_option('ws-ls-allow-advanced-tables')) ){
		$globals['WS_LS_ADVANCED_TABLES'] = true;
	}

	// -----------------------------------------------------------------------------------
	// Who can view / edit user data
	// -----------------------------------------------------------------------------------
	$permission_check = get_option('ws-ls-edit-permissions');
	if (false === empty($permission_check) && in_array($permission_check, ['manage_options', 'read_private_posts', 'publish_posts']) ){
		$globals['WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL'] = $permission_check;
	}

	// -----------------------------------------------------------------------------------
	// y Axes start at zero
	// -----------------------------------------------------------------------------------
	if ('yes' == get_option('ws-ls-axes-start-at-zero')) {
		$globals['WE_LS_AXES_START_AT_ZERO'] = true;
	}
	// -----------------------------------------------------------------------------------
	// Allow stats to be sent to YeKEn
	// -----------------------------------------------------------------------------------
	if ('yes' == get_option('ws-ls-allow-stats')) {
		$globals['WE_LS_ALLOW_STATS'] = true;
	}
	// -----------------------------------------------------------------------------------
	// Define if target weights enabled
	// -----------------------------------------------------------------------------------
	if ('no' == get_option('ws-ls-allow-targets')) {
		$globals['WE_LS_ALLOW_TARGET_WEIGHTS'] = false;
	}
	// -----------------------------------------------------------------------------------
	// Plot points on graph?
	// -----------------------------------------------------------------------------------
	if ('no' == get_option('ws-ls-allow-points')) {
		$globals['WE_LS_ALLOW_POINTS'] = false;
	}
	// -----------------------------------------------------------------------------------
	// Define whether to use tabs
	// -----------------------------------------------------------------------------------
	if ('no' == get_option('ws-ls-use-tabs')) {
		$globals['WE_LS_USE_TABS'] = false;
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
	// Disable admin notifications from YeKen
	// -----------------------------------------------------------------------------------
	if ('yes' == get_option('ws-ls-disable-yeken-notifications')) {
		$globals['WE_LS_DISABLE_YEKEN_NOTIFICATIONS'] = true;
	}
	// -----------------------------------------------------------------------------------
	// Line Colours
	// -----------------------------------------------------------------------------------
	if (get_option('ws-ls-target-colour')) {
		$globals['WE_LS_TARGET_LINE_COLOUR'] = get_option('ws-ls-target-colour');
	}
	if (get_option('ws-ls-line-colour')) {
		$globals['WE_LS_WEIGHT_LINE_COLOUR'] = get_option('ws-ls-line-colour');
	}
	if (get_option('ws-ls-line-fill-colour')) {
		$globals['WE_LS_WEIGHT_FILL_COLOUR'] = get_option('ws-ls-line-fill-colour');
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
	// Loop through array and set defines!
	// -----------------------------------------------------------------------------------
    foreach($globals as $key => $value) {
		define($key, $value);
	}
