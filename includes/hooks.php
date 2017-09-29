<?php

	defined('ABSPATH') or die("Jog on!");

	function ws_ls_build_admin_menu()
	{
		add_menu_page(WE_LS_TITLE, WE_LS_TITLE, 'manage_options', 'ws-ls-weight-loss-tracker-main-menu', 'ws_ls_settings_page', 'dashicons-chart-line');
		add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', __('Settings', WE_LS_SLUG),  __('Settings', WE_LS_SLUG), 'manage_options', 'ws-ls-weight-loss-tracker-main-menu');

		// Display manage user screens to relevant roles.
        add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', __('Manage User Data', WE_LS_SLUG),  __('Manage User Data', WE_LS_SLUG), WE_LS_VIEW_EDIT_USER_PERMISSION_LEVEL, 'ws-ls-wlt-data-home', 'ws_ls_admin_page_data_home');

        $menu_text = (false === WS_LS_IS_PRO && false === WS_LS_IS_PRO_PLUS) ? __('Upgrade', WE_LS_SLUG) : __('Your License', WE_LS_SLUG);

        add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', $menu_text,  $menu_text, 'manage_options', 'ws-ls-weight-loss-tracker-pro', 'ws_ls_advertise_pro');

		add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', __('Help', WE_LS_SLUG),  __('Help', WE_LS_SLUG), 'manage_options', 'ws-ls-weight-loss-tracker-help', 'ws_ls_help_page');

	}
	add_action( 'admin_menu', 'ws_ls_build_admin_menu' );

  /* Register the relevant WP shortcodes */
	function ws_ls_register_shortcodes(){

		/*
			[wlt-weight-diff] - total weight lost by the logged in member
			[wlt-weight-start] - start weight of the logged in member
			[wlt-weight-most-recent] - end weight of the logged in member
			[wlt-weight-diff-from-target] - difference between latest and target
			[wlt-target] - target weight
		*/

	 	add_shortcode( 'weightlosstracker', 'ws_ls_shortcode' );
		add_shortcode( 'weight-loss-tracker', 'ws_ls_shortcode' );
		add_shortcode( 'wlt', 'ws_ls_shortcode' );
	 	add_shortcode( 'weightloss_weight_difference', 'ws_ls_weight_difference' );
		add_shortcode( 'wlt-weight-diff', 'ws_ls_weight_difference' );
	 	add_shortcode( 'weightloss_weight_start', 'ws_ls_weight_start' );
		add_shortcode( 'wlt-weight-start', 'ws_ls_weight_start' );
	 	add_shortcode( 'weightloss_weight_most_recent', 'ws_ls_weight_recent' );
		add_shortcode( 'wlt-weight-most-recent', 'ws_ls_weight_recent' );
		add_shortcode( 'weightloss_weight_difference_from_target', 'ws_ls_weight_difference_target' );
		add_shortcode( 'wlt-weight-diff-from-target', 'ws_ls_weight_difference_target' );
		add_shortcode( 'weightloss_target_weight', 'ws_ls_weight_target_weight' );
		add_shortcode( 'wlt-target', 'ws_ls_weight_target_weight' );

        // If user has deleted all their data then delete it here. That way cache isn't displayed
        if(WE_LS_ALLOW_USER_PREFERENCES && isset($_GET['user-delete-all']) && 'true' == $_GET['user-delete-all'])	{
            ws_ls_delete_data_for_user();
        }

	}
	add_action( 'init', 'ws_ls_register_shortcodes');

	function ws_ls_enqueue_css(){

		$minified = ws_ls_use_minified();

		// CSS
		if (WE_LS_CSS_ENABLED) {
			wp_enqueue_style('wlt-style', plugins_url( '../css/ws-ls' . 	$minified . '.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		}

		wp_enqueue_style('jquery-style', plugins_url( '../css/jquery-ui.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

		// Tabs enabled?
		wp_enqueue_style('wlt-tabs', plugins_url( '../css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		wp_enqueue_style('wlt-tabs-flat', plugins_url( '../css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

	}
	add_action( 'wp_head', 'ws_ls_enqueue_css');

	$ws_already_enqueued = false;

	function ws_ls_enqueue_files(){

		global $ws_already_enqueued;

		if($ws_already_enqueued) {
			return;
		}

		$minified = ws_ls_use_minified();

		// JavaScript files
        wp_enqueue_script('jquery-validate',plugins_url( '../js/jquery.validate.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-validate-additional',plugins_url( '../js/additional-methods.min.js', __FILE__ ), array('jquery', 'jquery-validate'), WE_LS_CURRENT_VERSION);

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('ws-ls-js', plugins_url( '../js/ws-ls' . 	$minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);
		wp_enqueue_script('ws-ls-js-form', plugins_url( '../js/ws-ls-entry-form.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);

        wp_enqueue_script('ws-ls-chart-js', WE_LS_CDN_CHART_JS, array( 'jquery', 'ws-ls-js' ), WE_LS_CURRENT_VERSION);
        wp_enqueue_script('jquery-chart-ws-ls', plugins_url( '../js/ws-ls-chart' . 	$minified . '.js', __FILE__ ), array('ws-ls-chart-js'), WE_LS_CURRENT_VERSION, true);

        // Add localization data for JS
		wp_localize_script('ws-ls-js', 'ws_ls_config', ws_ls_get_js_config());

		// Tabs enabled?
		wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-tabs',plugins_url( '../js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION, true);

		$ws_already_enqueued = true;

	}

	function ws_ls_enqueue_admin_files(){

		$minified = ws_ls_use_minified();

		wp_enqueue_style('ws-ls-admin-style', plugins_url( '../css/admin.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

        // Enqueue admin.js regardless (needed to dismiss notices)
        wp_enqueue_script('ws-ls-admin', plugins_url( '../js/admin' .     $minified . '.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);

       	// Settings page
		if(false === empty($_GET['page']) && 'ws-ls-weight-loss-tracker-main-menu' == $_GET['page']) {
			wp_enqueue_script('jquery-tabs',plugins_url( '../js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs', plugins_url( '../css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs-flat', plugins_url( '../css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		}

		if(false === empty($_GET['page']) && 'ws-ls-wlt-data-home' == $_GET['page']) {
			wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), WE_LS_CURRENT_VERSION);
        }

		// Include relevant JS for admin "Manage User data" pages
        if(false === empty($_GET['page']) && 'ws-ls-wlt-data-home' == $_GET['page'] &&
            false === empty($_GET['mode']) && 'user-settings' == $_GET['mode']) {
			wp_enqueue_script('ws-ls-admin-user-pref', plugins_url( '../js/admin.user-preferences' . 	$minified . '.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
			wp_localize_script('ws-ls-admin-user-pref', 'ws_ls_user_pref_config', ws_ls_admin_config());
		}

		// User Data pages
		if(false === empty($_GET['mode'])) {

			// If on the add / edit entry page, then include relevant CSS / JS for form
			if(in_array($_GET['mode'], ['entry', 'user-settings'])) {
				ws_ls_enqueue_form_dependencies();
			} else if ('user' == $_GET['mode'] ) {
                wp_enqueue_script('ws-ls-chart-js', WE_LS_CDN_CHART_JS, array( 'jquery' ), WE_LS_CURRENT_VERSION);
                wp_enqueue_script('jquery-chart-ws-ls', plugins_url( '../js/ws-ls-chart' . 	$minified . '.js', __FILE__ ), array('ws-ls-chart-js'), WE_LS_CURRENT_VERSION, true);
				wp_localize_script('jquery-chart-ws-ls', 'ws_ls_config', ws_ls_get_js_config());
			}
		}
	}
	add_action( 'admin_enqueue_scripts', 'ws_ls_enqueue_admin_files');

	function ws_ls_enqueue_form_dependencies() {

		$minified = ws_ls_use_minified();

		// CSS
		wp_enqueue_style('wlt-style', plugins_url( '../css/ws-ls' . 	$minified . '.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		wp_enqueue_style('jquery-style', plugins_url( '../css/jquery-ui.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

		// JavaScript
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-validate',plugins_url( '../js/jquery.validate.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-validate-additional',plugins_url( '../js/additional-methods.min.js', __FILE__ ), array('jquery', 'jquery-validate'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('ws-ls-js', plugins_url( '../js/ws-ls-entry-form' . 	$minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);
		wp_localize_script('ws-ls-js', 'ws_ls_config', ws_ls_get_js_config());
	}

	function ws_ls_use_minified() {
		return (defined('SCRIPT_DEBUG') && false == SCRIPT_DEBUG) ? '.min' : '';
	}

	function ws_ls_admin_config() {
		return [
				'ajax-security-nonce' => wp_create_nonce( 'ws-ls-nonce' ),
				'preferences-save-ok' => __('The preferences for this user have been saved.', WE_LS_SLUG),
				'preferences-saved-fail' => __('An error occurred while trying to save the user\'s preferences.', WE_LS_SLUG),
				'preferences-page' => ws_ls_get_link_to_user_profile((false === empty($_GET['user-id'])) ? esc_attr($_GET['user-id']) : '')
				];
	}

	// Tidy up various things (cache etc) when user data is deleted.
    function ws_ls_tidy_cache_on_delete(){
        ws_ls_delete_cache(WE_LS_CACHE_KEY_ENTRY_COUNTS);
		ws_ls_delete_cache(WE_LS_CACHE_STATS_TABLE);
    }
    add_action(WE_LS_HOOK_DATA_ALL_DELETED, 'ws_ls_tidy_cache_on_delete');
    add_action(WE_LS_HOOK_DATA_USER_DELETED, 'ws_ls_tidy_cache_on_delete');

    /**
     * Send email to let the admin know the WLT has expired.
     */
//    function ws_ls_send_email_to_site_owner_on_license_expire() {
//
//        $admin_email = get_bloginfo('admin_email');
//
//        if ( false === empty($admin_email) ) {
//
//            $r = wp_mail($admin_email,
//                __( 'Weight Loss Tracker plugin has expired on your site!' , WE_LS_SLUG),
//                __( 'Please visit your Dashboard to renew your license (Weight Tracker > Upgrade) ' , WE_LS_SLUG));
//
//        }
//    }
    //add_action(WE_LS_HOOK_LICENSE_EXPIRED, 'ws_ls_send_email_to_site_owner_on_license_expire');