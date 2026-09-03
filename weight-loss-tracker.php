<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Plugin Name:         Weight Tracker
 * Description:         Allow your users to track their weight, body measurements, photos and other pieces of custom data. Display in charts, tables, shortcodes and widgets. Manage their data, issue awards, email notifications, etc! Provide advanced data on Body Mass Index (BMI), Basal Metabolic Rate (BMR), Calorie intake, Harris Benedict Formula, Macronutrients Calculator and more.
 * Version:             10.20.4
 * Requires at least:   6.0
 * Tested up to:		6.9.4
 * Requires PHP:        7.4
 * Author:              Ali Colville
 * Author URI:          https://www.YeKen.uk
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         weight-loss-tracker
 * Domain Path:         /includes/languages
 */

define( 'WE_LS_CURRENT_VERSION', '10.20.4' );
define( 'WS_LS_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'WS_LS_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'WE_LS_TITLE', 'Weight Tracker' );
define( 'WE_LS_SLUG', 'weight-loss-tracker' );
define( 'WE_LS_YEKEN_UPDATES_URL', 'https://yeken.uk/downloads/_updates/weight-tracker.json' );
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
//
// The Weight Tracker Premium plugin (a separate plugin - see CLAUDE.md for why) announces
// itself via the 'wlt-filter-is-premium-active' filter, registered at its own top-level file
// load time. We can't read that until our own plugins_loaded callback: WordPress finishes
// including every active plugin's main file (this one and Premium's) before firing
// plugins_loaded for any of them, so by then Premium's filter is guaranteed to be registered
// regardless of which of the two plugins happens to load first alphabetically. Reading it any
// earlier than that would always see the "no Premium" default.
//
// WS_LS_IS_PREMIUM is only read from inside function bodies invoked later (shortcode render,
// AJAX, admin pages) - never at file-parse time - so defining it here instead of immediately
// is safe.

add_action( 'plugins_loaded', 'ws_ls_bootstrap_premium_flag', 5 );

function ws_ls_bootstrap_premium_flag() {

	$is_premium = true === apply_filters( 'wlt-filter-is-premium-active', false );

	define( 'WS_LS_IS_PREMIUM', $is_premium );
	define( 'WS_LS_IS_PRO', $is_premium );			// Legacy
	define( 'WS_LS_IS_PRO_PLUS', $is_premium );	// Legacy
}

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
require_once( WS_LS_ABSPATH . 'includes/feature-list.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings-generic.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/settings/page-settings-email-manager.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/page-help.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/page-license.php' );
require_once( WS_LS_ABSPATH . 'includes/admin-pages/page-setup-wizard.php' );

require_once( WS_LS_ABSPATH . 'includes/deprecated.php' );

// -----------------------------------------------------------------------------------------
// AC: Load relevant language files (https://wpallinfo.com/complete-list-of-wordpress-locale-codes/)
// -----------------------------------------------------------------------------------------
function ws_ls_load_textdomain() {
	load_plugin_textdomain( WE_LS_SLUG, false, dirname( plugin_basename( __FILE__ )  ) . '/includes/languages/' );
}
add_action('plugins_loaded', 'ws_ls_load_textdomain');

// -----------------------------------------------------------------------------------------
// This plugin is (again) hosted on wordpress.org, which handles update checks/delivery
// natively - the custom plugin-update-checker + private release manifest now only matter
// for the separately-sold Premium plugin, so that wiring has moved there.
// -----------------------------------------------------------------------------------------