<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Plugin Name: Weight Loss Tracker
 * Description: Allow registered users of your website to track their weight and relevant body measurements. History can be displayed in both tables & charts.
 * Version: 5.2.18
 * Author: YeKen
 * Author URI: https://www.YeKen.uk
 * License: GPL2
 * Text Domain: weight-loss-tracker
 */
/*  Copyright 2017 YeKen.uk

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('WS_LS_ABSPATH', plugin_dir_path( __FILE__ ));
define('WE_LS_CURRENT_VERSION', '5.2.18');
define('WE_LS_DB_VERSION', '5.2.18');

// -----------------------------------------------------------------------------------------
// AC: Activate / Deactivate / Uninstall Hooks
// ------------------------------------------------------------------------------------------

register_activation_hook(__FILE__, 'ws_ls_activate');
register_deactivation_hook(__FILE__, 'ws_ls_deactivate');

// -----------------------------------------------------------------------------------------
// AC: Check if valid pro license (if valid license)
// ----------------------------------------------------------------------------------------

include WS_LS_ABSPATH . 'includes/license.php';

$license_type = ws_ls_has_a_valid_license();

// Standard Pro license?
if(in_array($license_type, ['pro', 'pro-old', 'pro-plus']) ){
 define('WS_LS_IS_PRO', true);
} else {
 define('WS_LS_IS_PRO', false);
}

// Pro Plus license?
if('pro-plus' === $license_type){
 define('WS_LS_IS_PRO_PLUS', true);
} else {
 define('WS_LS_IS_PRO_PLUS', false);
}

// -----------------------------------------------------------------------------------------
// AC: Include all relevant PHP files
// -----------------------------------------------------------------------------------------
include WS_LS_ABSPATH . 'includes/caching.php';
include WS_LS_ABSPATH . 'includes/db.php';
include WS_LS_ABSPATH . 'includes/globals.php';
include WS_LS_ABSPATH . 'includes/activate.php';
include WS_LS_ABSPATH . 'includes/hooks.php';
include WS_LS_ABSPATH . 'includes/cron.php';
// include WS_LS_ABSPATH . 'includes/incompatibility.php';
include WS_LS_ABSPATH . 'includes/functions.php';
include WS_LS_ABSPATH . 'includes/converters.php';
include WS_LS_ABSPATH . 'includes/core.php';
include WS_LS_ABSPATH . 'includes/ajax-handler.php';
include WS_LS_ABSPATH . 'includes/shortcode-weight-loss-tracker.php';
include WS_LS_ABSPATH . 'includes/shortcode-various.php';
include WS_LS_ABSPATH . 'includes/save-data.php';
include WS_LS_ABSPATH . 'pages/page.settings.php';
include WS_LS_ABSPATH . 'pages/page.license.php';
include WS_LS_ABSPATH . 'pages/page.help.php';
include WS_LS_ABSPATH . 'pro-features/feature-list.php';
include WS_LS_ABSPATH . 'includes/comms-with-yeken.php';
include WS_LS_ABSPATH . 'includes/admin-notifications.php';

// -----------------------------------------------------------------------------------------
// AC: Include Pro files
// --------------------------------------------------------------------------------------
if(WS_LS_IS_PRO){
    include WS_LS_ABSPATH . 'pro-features/init.php';
} else {
    include WS_LS_ABSPATH . 'pages/page.user.data.advertise.php';
}
// -----------------------------------------------------------------------------------------
// AC: Load relevant language files (https://wpcentral.io/internationalization/)
// -----------------------------------------------------------------------------------------

function ws_ls_load_textdomain() {
  load_plugin_textdomain( WE_LS_SLUG, false, dirname( plugin_basename( __FILE__ )  ) . '/languages/' );
}
add_action('plugins_loaded', 'ws_ls_load_textdomain');
