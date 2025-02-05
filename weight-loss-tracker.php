<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Plugin Name:         Weight Tracker
 * Description:         Allow your users to track their weight, body measurements, photos and other pieces of custom data. Display in charts, tables, shortcodes and widgets. Manage their data, issue awards, email notifications, etc! Provide advanced data on Body Mass Index (BMI), Basal Metabolic Rate (BMR), Calorie intake, Harris Benedict Formula, Macronutrients Calculator and more.
 * Version:             10.20
 * Requires at least:   6.0
 * Tested up to:		6.5
 * Requires PHP:        7.4
 * Author:              Ali Colville
 * Author URI:          https://www.YeKen.uk
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         weight-loss-tracker
 * Domain Path:         /includes/languages
 */

define( 'WE_LS_CURRENT_VERSION', '10.20' );
define( 'WS_LS_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'WS_LS_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'WE_LS_TITLE', 'Weight Tracker' );
define( 'WE_LS_SLUG', 'weight-loss-tracker' );
define( 'WE_LS_YEKEN_UPDATES_URL', 'https://yeken.uk/downloads/_updates/weight-tracker.json' );
define( 'WE_LS_YEKEN_LATEST_RELEASE_MANIFEST', 'https://raw.githubusercontent.com/alicolville/Weight-Tracker/refs/heads/master/release.json' );
define( 'WE_LS_LICENSE_TYPES_URL', 'https://docs.yeken.uk/features.html' );
define( 'WE_LS_CALCULATIONS_URL', '	https://docs.yeken.uk/calculations.html' );
define( 'WE_LS_UPGRADE_TO_PREMIUM_URL', 'https://shop.yeken.uk/product/weight-tracker-premium/' );
define( 'WE_LS_FREE_TRIAL_URL', 'https://shop.yeken.uk/get-a-trial-license/' );
define( 'WE_LS_CDN_CHART_JS', WS_LS_BASE_URL . 'assets/js/libraries/chart-4.4.4.min.js' );
define( 'WE_LS_CDN_FONT_AWESOME_CSS', WS_LS_BASE_URL . 'assets/css/libraries/fontawesome-4.7.0.min.css' );
define( 'WE_LS_PREMIUM_PRICE', 70.00 );

global $form_number;        // This is used to keep track of multiple forms on a page allowing us to pass messages to each
global $save_response;      // This is used to keep track of form posts responses
global $kiosk_mode;         // If using [wt] in Kiosk mode

// -----------------------------------------------------------------------------------------
// AC: Activate / Deactivate / Uninstall Hooks
// ------------------------------------------------------------------------------------------

register_activation_hook( __FILE__, 'ws_ls_activate' );
register_deactivation_hook( __FILE__, 'ws_ls_deactivate' );

// -----------------------------------------------------------------------------------------
// AC: Check if valid Premium license
// ----------------------------------------------------------------------------------------

include WS_LS_ABSPATH . 'includes/license.php';

$license_type = ws_ls_has_a_valid_license();

// Premium
if( true === in_array( $license_type, [ 'pro', 'pro-plus' ] ) ){
	define( 'WS_LS_IS_PREMIUM', true );
	define( 'WS_LS_IS_PRO', true );			// Legacy
	define( 'WS_LS_IS_PRO_PLUS', true );	// Legacy
} else {
	define( 'WS_LS_IS_PREMIUM', false );
	define( 'WS_LS_IS_PRO', false );		// Legacy
	define( 'WS_LS_IS_PRO_PLUS', false );	// Legacy
}

// -----------------------------------------------------------------------------------------
// AC: Include all relevant PHP files
// -----------------------------------------------------------------------------------------

require_once( WS_LS_ABSPATH . 'includes/caching.php' );
require_once( WS_LS_ABSPATH . 'includes/db.php' );
require_once( WS_LS_ABSPATH . 'includes/activate.php' );
require_once( WS_LS_ABSPATH . 'includes/hooks.php' );
require_once( WS_LS_ABSPATH . 'includes/cron.php' );
require_once( WS_LS_ABSPATH . 'includes/plugin-update-checker/plugin-update-checker.php' );
require_once( WS_LS_ABSPATH . 'includes/functions.php' );
require_once( WS_LS_ABSPATH . 'includes/converters.php' );
require_once( WS_LS_ABSPATH . 'includes/core.php' );
require_once( WS_LS_ABSPATH . 'includes/core-forms.php' );
require_once( WS_LS_ABSPATH . 'includes/core-tables.php' );
require_once( WS_LS_ABSPATH . 'includes/core-charting.php' );
require_once( WS_LS_ABSPATH . 'includes/ajax.php' );
require_once( WS_LS_ABSPATH . 'includes/setup-wizard.php' );
require_once( WS_LS_ABSPATH . 'includes/components.php' );
require_once( WS_LS_ABSPATH . 'includes/shortcode-wt.php' );
require_once( WS_LS_ABSPATH . 'includes/shortcode-various.php' );
require_once( WS_LS_ABSPATH . 'includes/form-handler.php' );
require_once( WS_LS_ABSPATH . 'includes/email-manager.php' );
require_once( WS_LS_ABSPATH . 'includes/meal-tracker.php' );
require_once( WS_LS_ABSPATH . 'includes/marketing.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings-generic.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings-groups.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings-email-manager.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-groups.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/page-help.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/page-license.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/page-setup-wizard.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/meta-fields/page-meta-fields.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/meta-fields/page-meta-fields-add-update.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/meta-fields/page-meta-fields-list.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/meta-fields/page-meta-fields-groups.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/awards/page-awards.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/awards/page-awards-list.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/awards/page-awards-add-update.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/activate.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/db.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/hooks.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/functions.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/functions-photos.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/functions-slider.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/meta-fields/shortcodes.php' );
require_once( WS_LS_ABSPATH . 'pro-features/feature-list.php' );
require_once( WS_LS_ABSPATH . 'pro-features/functions.php' );
require_once( WS_LS_ABSPATH . 'pro-features/functions-pages.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-home.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-summary.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-view-all.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-add-edit-entry.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-edit-target.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-user.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-notes.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-user-edit-settings.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-search-results.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/user-data/data-photos.php' );
require_once( WS_LS_ABSPATH . 'pro-features/user-groups.php' );
require_once( WS_LS_ABSPATH . 'pro-features/user-birthdays.php' );
require_once( WS_LS_ABSPATH . 'pro-features/user-preferences.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-chart.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-form.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-footable.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-various.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-stats.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-reminders.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-progress-bar.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-messages.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-bmi-calculator.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-hip-waist-ratio-calculator.php' );
require_once( WS_LS_ABSPATH . 'pro-features/shortcode-if.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/barcode-reader.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/shortcode.wlt.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/photos.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/photos-gallery.php' );
require_once( WS_LS_ABSPATH . 'pro-features/widget-chart.php' );
require_once( WS_LS_ABSPATH . 'pro-features/widget-form.php' );
require_once( WS_LS_ABSPATH . 'pro-features/widget-progress.php' );
require_once( WS_LS_ABSPATH . 'pro-features/footable.php' );
require_once( WS_LS_ABSPATH . 'pro-features/db.php' );
require_once( WS_LS_ABSPATH . 'pro-features/functions-stats.php' );
require_once( WS_LS_ABSPATH . 'pro-features/hooks.php' );

if ( true === WS_LS_IS_PREMIUM ) {
	require_once( WS_LS_ABSPATH . 'pro-features/gamification.php' );
}

require_once( WS_LS_ABSPATH . 'pro-features/plus/awards/activate.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/awards/functions.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/awards/db.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/awards/hooks.php' );
require_once( WS_LS_ABSPATH . 'pro-features/email-notifications.php' );
require_once( WS_LS_ABSPATH . 'pro-features/web-hooks.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/challenge/inc.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/messaging/inc.php' );

require_once( WS_LS_ABSPATH . 'pro-features/plus/bmr.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/harris-benedict.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/macronutrient-calculator.php' );
require_once( WS_LS_ABSPATH . 'pro-features/plus/shortcode.calculator.php' );
require_once( WS_LS_ABSPATH . 'pro-features/export/inc.php' );

// Gravity Forms
if ( true === WS_LS_IS_PREMIUM && 'yes' == get_option( 'ws-ls-gf-enable', 'yes' ) ) {
	require_once( WS_LS_ABSPATH . 'pro-features/hook-gravity-forms.php' );
}

require_once( WS_LS_ABSPATH . 'includes/deprecated.php' );

// -----------------------------------------------------------------------------------------
// AC: Load relevant language files (https://wpallinfo.com/complete-list-of-wordpress-locale-codes/)
// -----------------------------------------------------------------------------------------
function ws_ls_load_textdomain() {
	load_plugin_textdomain( WE_LS_SLUG, false, dirname( plugin_basename( __FILE__ )  ) . '/includes/languages/' );
}
add_action('plugins_loaded', 'ws_ls_load_textdomain');

// -----------------------------------------------------------------------------------------
// Since we're no longer hosted on WordPress.org, use the following for auto updates
// -----------------------------------------------------------------------------------------
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$wt_plugin_updater = PucFactory::buildUpdateChecker( WE_LS_YEKEN_LATEST_RELEASE_MANIFEST, __FILE__, WE_LS_SLUG );