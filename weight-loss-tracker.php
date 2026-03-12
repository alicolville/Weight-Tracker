<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Plugin Name:         Weight Tracker
 * Description:         Allow your users to track their weight, body measurements, photos and other pieces of custom data. Display in charts, tables, shortcodes and widgets. Manage their data, issue awards, email notifications, etc! Provide advanced data on Body Mass Index (BMI), Basal Metabolic Rate (BMR), Calorie intake, Harris Benedict Formula, Macronutrients Calculator and more.
 * Version:             10.20.3
 * Requires at least:   6.0
 * Tested up to:		6.8.1
 * Requires PHP:        7.4
 * Author:              Ali Colville
 * Author URI:          https://www.YeKen.uk
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         weight-loss-tracker
 * Domain Path:         /includes/languages
 */

define( 'WE_LS_CURRENT_VERSION', '10.20.3' );
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
// AC: Include all relevant PHP files
// -----------------------------------------------------------------------------------------

require_once( WS_LS_ABSPATH . 'includes/caching.php' );
require_once( WS_LS_ABSPATH . 'includes/db.php' );
require_once( WS_LS_ABSPATH . 'includes/activate.php' );
require_once( WS_LS_ABSPATH . 'includes/hooks.php' );
require_once( WS_LS_ABSPATH . 'includes/cron.php' );
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
require_once( WS_LS_ABSPATH . 'includes/deprecated.php' );

// -----------------------------------------------------------------------------------------
// AC: Load relevant language files (https://wpallinfo.com/complete-list-of-wordpress-locale-codes/)
// -----------------------------------------------------------------------------------------
function ws_ls_load_textdomain() {
	load_plugin_textdomain( WE_LS_SLUG, false, dirname( plugin_basename( __FILE__ )  ) . '/includes/languages/' );
}
add_action('plugins_loaded', 'ws_ls_load_textdomain');
