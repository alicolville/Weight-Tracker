<?php

	defined('ABSPATH') or die("Jog on!");

	function ws_ls_build_admin_menu()
	{
		add_menu_page(WE_LS_TITLE, WE_LS_TITLE, 'manage_options', 'ws-ls-weight-loss-tracker-main-menu', 'ws_ls_settings_page', 'dashicons-chart-line');
		add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', __('Settings', WE_LS_SLUG),  __('Settings', WE_LS_SLUG), 'manage_options', 'ws-ls-weight-loss-tracker-main-menu');
        add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', __('Manage User Data', WE_LS_SLUG),  __('Manage User Data', WE_LS_SLUG), 'manage_options', 'ws-ls-wlt-data-home', 'ws_ls_admin_page_data_home');

        if(!WS_LS_IS_PRO) {
			add_submenu_page( 'ws-ls-weight-loss-tracker-main-menu', __('Get Pro Version', WE_LS_SLUG),  __('Get Pro Version', WE_LS_SLUG), 'manage_options', 'ws-ls-weight-loss-tracker-pro', 'ws_ls_advertise_pro');
		}

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
		if (WE_LS_USE_TABS)	{
			wp_enqueue_style('wlt-tabs', plugins_url( '../css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs-flat', plugins_url( '../css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		}
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
		ws_ls_enqueue_chart_dependencies();
		wp_enqueue_script('jquery-validate',plugins_url( '../js/jquery.validate.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-validate-additional',plugins_url( '../js/additional-methods.min.js', __FILE__ ), array('jquery', 'jquery-validate'), WE_LS_CURRENT_VERSION);

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('wl-ls-js', plugins_url( '../js/ws-ls' . 	$minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);
		wp_enqueue_script('wl-ls-js-form', plugins_url( '../js/ws-ls-entry-form.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);

		// Add localization data for JS
		wp_localize_script('wl-ls-js', 'ws_ls_config', ws_ls_get_js_config());

		// Tabs enabled?
		if (WE_LS_USE_TABS)	{
			wp_enqueue_script('jquery-tabs',plugins_url( '../js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION, true);
		}

		// Enqueue Data
		if(WS_LS_IS_PRO && WS_LS_ADVANCED_TABLES) {
	  	  ws_ls_enqueue_datatable_scripts();
	    }

		$ws_already_enqueued = true;

	}

	function ws_ls_enqueue_admin_files(){

		$minified = ws_ls_use_minified();

		wp_enqueue_style('ws-ls-admin-style', plugins_url( '../css/admin.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

		// Settings page
		if(false === empty($_GET['page']) && 'ws-ls-weight-loss-tracker-main-menu' == $_GET['page']) {
			wp_enqueue_script('jquery-tabs',plugins_url( '../js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs', plugins_url( '../css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs-flat', plugins_url( '../css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
			wp_enqueue_script('ws-ls-admin', plugins_url( '../js/admin' . 	$minified . '.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
		}

		// User Data pages
		if(false === empty($_GET['mode'])) {

			// If on the add / edit entry page, then include relevant CSS / JS for form
			if('entry' == $_GET['mode'] ) {
				ws_ls_enqueue_form_dependencies();
			} else if ('user' == $_GET['mode'] ) {
				ws_ls_enqueue_chart_dependencies();
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
		wp_enqueue_script('wl-ls-js', plugins_url( '../js/ws-ls-entry-form' . 	$minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);
		wp_localize_script('wl-ls-js', 'ws_ls_config', ws_ls_get_js_config());
	}

	function ws_ls_enqueue_chart_dependencies() {

		$minified = ws_ls_use_minified();

		wp_enqueue_script('chart-js', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js', array( 'jquery' ), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-chart-ws-ls', plugins_url( '../js/ws-ls-chart' . 	$minified . '.js', __FILE__ ), array('chart-js'), WE_LS_CURRENT_VERSION, true);
	}

	function ws_ls_use_minified() {
		return (defined('SCRIPT_DEBUG') && false == SCRIPT_DEBUG) ? '.min' : '';
	}

	// Tidy up various things (cache etc) when user data is deleted.
    function ws_ls_tidy_cache_on_delete(){
        ws_ls_delete_cache(WE_LS_CACHE_KEY_ENTRY_COUNTS);
		ws_ls_delete_cache(WE_LS_CACHE_STATS_TABLE);
    }
    add_action(WE_LS_HOOK_DATA_ALL_DELETED, 'ws_ls_tidy_cache_on_delete');
    add_action(WE_LS_HOOK_DATA_USER_DELETED, 'ws_ls_tidy_cache_on_delete');
