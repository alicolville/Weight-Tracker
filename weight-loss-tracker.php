<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Plugin Name: Weight Loss Tracker
 * Description: Allow registered users of your website to track their weight and relevant body measurements. History can be displayed in both tables & charts.
 * Version: 6.0.2
 * Author: YeKen
 * Author URI: https://www.YeKen.uk
 * License: GPL2
 * Text Domain: weight-loss-tracker
 */
/*  Copyright 2018 YeKen.uk

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
    define('WE_LS_CURRENT_VERSION', '6.0.2');
    define('WE_LS_DB_VERSION', '6.0.2');

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

    // Bring in Globals first
    require_once 'includes/globals.php';

    $files_to_include = [
        'includes/caching.php',
        'includes/db.php',
        'includes/activate.php',
        'includes/hooks.php',
        'includes/cron.php',
        'includes/fixes.php',
        'includes/functions.php',
        'includes/converters.php',
        'includes/core.php',
        'includes/ajax-handler.php',
        'includes/shortcode-weight-loss-tracker.php',
        'includes/shortcode-various.php',
        'includes/save-data.php',
        'includes/admin-pages/page.settings.php',
        'includes/admin-pages/page.license.php',
        'includes/admin-pages/page.help.php',
        'includes/admin-pages/meta-fields/page-meta-fields.php',
        'includes/admin-pages/meta-fields/page-meta-fields-add-update.php',
        'includes/admin-pages/meta-fields/page-meta-fields-list.php',
        'pro-features/plus/meta-fields/activate.php',
        'pro-features/plus/meta-fields/db.php',
        'pro-features/plus/meta-fields/hooks.php',
        'pro-features/plus/meta-fields/functions.php',
        'pro-features/feature-list.php',
        'includes/comms-with-yeken.php',
        'includes/admin-notifications.php',
        'pro-features/functions.php',
        'pro-features/functions.pages.php',
        'includes/admin-pages/user-data/data-home.php',
        'includes/admin-pages/user-data/data-summary.php',
        'includes/admin-pages/user-data/data-view-all.php',
        'includes/admin-pages/user-data/data-add-edit-entry.php',
        'includes/admin-pages/user-data/data-edit-target.php',
        'includes/admin-pages/user-data/data-user.php',
        'includes/admin-pages/user-data/data-user-edit-settings.php',
        'includes/admin-pages/user-data/data-search-results.php',
        'includes/admin-pages/user-data/data-photos.php'
    ];

    $pro_files = [
        'pro-features/user-preferences.php',
        'pro-features/ajax-handler-public.php',
        'pro-features/ajax-handler-admin.php',
        'pro-features/shortcode-chart.php',
        'pro-features/shortcode-form.php',
        'pro-features/shortcode-footable.php',
        'pro-features/shortcode-various.php',
        'pro-features/shortcode-stats.php',
        'pro-features/shortcode-reminders.php',
        'pro-features/shortcode-progress-bar.php',
        'pro-features/shortcode-messages.php',
        'pro-features/shortcode-if.php',
        'pro-features/widget-chart.php',
        'pro-features/widget-form.php',
        'pro-features/widget-progress.php',
        'pro-features/footable.php',
        'pro-features/db.php',
        'pro-features/functions.measurements.php',
        'pro-features/functions.stats.php',
        'pro-features/export.php',
        'pro-features/init.php'
    ];

    $files_to_include = array_merge( $files_to_include, $pro_files );

    // Gravity Forms
    if ( true === WE_LS_THIRD_PARTY_GF_ENABLE ) {
        $files_to_include[] = 'pro-features/hook-gravity-forms.php';
    }

    // Include files for those that have a Pro Plus license
    if( true === WS_LS_IS_PRO_PLUS ) {

        $files_to_include = array_merge( $files_to_include,[
            'pro-features/plus/bmr.php',
            'pro-features/plus/harris.benedict.php',
            'pro-features/plus/macronutrient.calculator.php',
            'pro-features/plus/shortcode.wlt.php'
        ]);

        // Photos enabled?
        if( true === WE_LS_PHOTOS_ENABLED ) {
            $files_to_include[] = 'pro-features/plus/photos.php';
            $files_to_include[] = 'pro-features/plus/photos.gallery.php';
        }

    }

    // Email notifications enabled?
    if( true === WE_LS_EMAIL_ENABLE ) {
        $files_to_include[] = 'pro-features/emails.php';
    }

    foreach ( $files_to_include as $file ) {
        require_once( WS_LS_ABSPATH . $file );
    }

// -----------------------------------------------------------------------------------------
// AC: Load relevant language files (https://wpcentral.io/internationalization/)
// -----------------------------------------------------------------------------------------

    function ws_ls_load_textdomain() {
      load_plugin_textdomain( WE_LS_SLUG, false, dirname( plugin_basename( __FILE__ )  ) . '/includes/languages/' );
    }
    add_action('plugins_loaded', 'ws_ls_load_textdomain');
