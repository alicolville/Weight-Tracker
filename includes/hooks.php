<?php

	defined('ABSPATH') or die("Jog on!");

	function ws_ls_build_admin_menu() {

		$minimum_role_to_view = ws_ls_permission_role();

		add_menu_page( WE_LS_TITLE, WE_LS_TITLE, $minimum_role_to_view, 'ws-ls-data-home', 'ws_ls_admin_page_data_home', 'dashicons-chart-line');

        // Display manage user screens to relevant roles.
        add_submenu_page( 'ws-ls-data-home', __('Manage User Data', WE_LS_SLUG),  __('Manage User Data', WE_LS_SLUG), $minimum_role_to_view, 'ws-ls-data-home', 'ws_ls_admin_page_data_home');
		add_submenu_page( 'ws-ls-data-home', __('Custom Fields', WE_LS_SLUG),  __('Custom Fields', WE_LS_SLUG), 'manage_options', 'ws-ls-meta-fields', 'ws_ls_meta_fields_page');
        add_submenu_page( 'ws-ls-data-home', __('Awards', WE_LS_SLUG),  __('Awards', WE_LS_SLUG), 'manage_options', 'ws-ls-awards', 'ws_ls_awards_page');

        if ( true === ws_ls_challenges_is_enabled() ) {
            add_submenu_page( 'ws-ls-data-home', __( 'Challenges', WE_LS_SLUG),  __('Challenges', WE_LS_SLUG), 'manage_options', 'ws-ls-challenges', 'ws_ls_challenges_admin_page' );
        }

		$menu_text = (false === WS_LS_IS_PRO && false === WS_LS_IS_PRO_PLUS) ? __('Upgrade', WE_LS_SLUG) : __('Your License', WE_LS_SLUG);

        add_submenu_page( 'ws-ls-data-home', $menu_text,  $menu_text, 'manage_options', 'ws-ls-license', 'ws_ls_advertise_pro');
		add_submenu_page( 'ws-ls-data-home', __('Settings', WE_LS_SLUG),  __('Settings', WE_LS_SLUG), 'manage_options', 'ws-ls-settings', 'ws_ls_settings_page');

        if ( true === ws_ls_setup_wizard_show_notice() ) {
            add_submenu_page( 'ws-ls-data-home', __('Setup Wizard', WE_LS_SLUG),  __('Setup Wizard', WE_LS_SLUG), 'manage_options', 'ws-ls-data-setup-wizard', 'ws_ls_setup_wizard_page');
        }

		add_submenu_page( 'ws-ls-data-home', __('Help & Log', WE_LS_SLUG),  __('Help & Log', WE_LS_SLUG), 'manage_options', 'ws-ls-help', 'ws_ls_help_page');

	}
	add_action( 'admin_menu', 'ws_ls_build_admin_menu' );

	function ws_ls_enqueue_css(){

		$minified = ws_ls_use_minified();

		// CSS
		if ( false === ws_ls_css_is_disabled() ) {
			wp_enqueue_style('wlt-style', plugins_url( '../assets/css/ws-ls' . 	$minified . '.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-style-both', plugins_url( '../assets/css/admin-and-public' . 	$minified . '.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		}

		wp_enqueue_style('jquery-style', plugins_url( '../assets/css/jquery-ui.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

		// Tabs enabled?
		wp_enqueue_style('wlt-tabs', plugins_url( '../assets/css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		wp_enqueue_style('wlt-tabs-flat', plugins_url( '../assets/css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

	}
	add_action( 'wp_head', 'ws_ls_enqueue_css');

	$ws_already_enqueued = false;

	function ws_ls_enqueue_files(){

		global $ws_already_enqueued;

		if( $ws_already_enqueued ) {
			return;
		}

		$minified = ws_ls_use_minified();

		// JavaScript files
        wp_enqueue_script('jquery-validate',plugins_url( '../assets/js/jquery.validate.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-validate-additional',plugins_url( '../assets/js/additional-methods.min.js', __FILE__ ), array('jquery', 'jquery-validate'), WE_LS_CURRENT_VERSION);

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('ws-ls-js', plugins_url( '../assets/js/ws-ls' . 	$minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);
		wp_enqueue_script('ws-ls-js-form', plugins_url( '../assets/js/ws-ls-entry-form' . 	$minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);


        // Add localization data for JS
		wp_localize_script('ws-ls-js', 'ws_ls_config', ws_ls_config_js());

		// Tabs enabled?
		wp_enqueue_style('wlt-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-tabs',plugins_url( '../assets/js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION, true);

		$ws_already_enqueued = true;

	}

	function ws_ls_enqueue_admin_files(){

		$minified = ws_ls_use_minified();

		wp_enqueue_style('ws-ls-admin-style', plugins_url( '../assets/css/admin.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

        // Enqueue admin.js regardless (needed to dismiss notices)
        wp_enqueue_script('ws-ls-admin', plugins_url( '../assets/js/admin.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);

       	// Settings page
		if( false === empty( $_GET['page'] ) && true === in_array( $_GET['page'], [ 'ws-ls-settings', 'ws-ls-data-setup-wizard' ] ) ) {
			wp_enqueue_script('jquery-tabs',plugins_url( '../assets/js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs', plugins_url( '../assets/css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
			wp_enqueue_style('wlt-tabs-flat', plugins_url( '../assets/css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		}

		if( false === empty( $_GET['page'] ) && true === in_array( $_GET['page'], ['ws-ls-data-home', 'ws-ls-license', 'ws-ls-data-setup-wizard', 'ws-ls-challenges' ] ) ) {

			wp_enqueue_style('wlt-font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), WE_LS_CURRENT_VERSION);
        }

		// Include relevant JS for admin "Manage User data" pages
        if(false === empty($_GET['page']) && 'ws-ls-data-home' == $_GET['page'] &&
            false === empty($_GET['mode']) && 'user-settings' == $_GET['mode']) {

			wp_enqueue_script('ws-ls-admin-user-pref', plugins_url( '../assets/js/admin.user-preferences' . 	$minified . '.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
			wp_localize_script('ws-ls-admin-user-pref', 'ws_ls_user_pref_config', ws_ls_admin_config());
		}

		// User Data pages
		if( false === empty($_GET['mode']) ) {

			// If on the add / edit entry page, then include relevant CSS / JS for form
			if( true === in_array( $_GET[ 'mode' ], [ 'add', 'entry', 'user-settings' ] ) ) {
				ws_ls_enqueue_form_dependencies();
			} else if ('user' == $_GET['mode'] ) {
                wp_enqueue_script('ws-ls-chart-js', WE_LS_CDN_CHART_JS, array( 'jquery' ), WE_LS_CURRENT_VERSION);
                wp_enqueue_script('jquery-chart-ws-ls', plugins_url( '../assets/js/ws-ls-chart' . 	$minified . '.js', __FILE__ ), array('ws-ls-chart-js'), WE_LS_CURRENT_VERSION, true);
				wp_localize_script('jquery-chart-ws-ls', 'ws_ls_config', ws_ls_config_js());
			}
		}
	}
	add_action( 'admin_enqueue_scripts', 'ws_ls_enqueue_admin_files');

	function ws_ls_enqueue_form_dependencies() {

		$minified = ws_ls_use_minified();

		// CSS
		wp_enqueue_style('wlt-style', plugins_url( '../assets/css/ws-ls' . 	$minified . '.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
		wp_enqueue_style('jquery-style', plugins_url( '../assets/css/jquery-ui.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);

		// JavaScript
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-validate',plugins_url( '../assets/js/jquery.validate.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('jquery-validate-additional',plugins_url( '../assets/js/additional-methods.min.js', __FILE__ ), array('jquery', 'jquery-validate'), WE_LS_CURRENT_VERSION);
		wp_enqueue_script('ws-ls-js', plugins_url( '../assets/js/ws-ls-entry-form' . $minified . '.js', __FILE__ ), array(), WE_LS_CURRENT_VERSION, true);
		wp_localize_script('ws-ls-js', 'ws_ls_config', ws_ls_config_js());
	}

/**
 * Should we use a minified version of an asset file?
 * @return string
 */
function ws_ls_use_minified() {
    return ( defined('SCRIPT_DEBUG' ) && false == SCRIPT_DEBUG ) ? '.min' : '';
}

function ws_ls_admin_config() {
	return [
			'ajax-security-nonce'       => wp_create_nonce( 'ws-ls-nonce' ),
			'preferences-save-ok'       => __('The preferences for this user have been saved.', WE_LS_SLUG),
			'preferences-saved-fail'    => __('An error occurred while trying to save the user\'s preferences.', WE_LS_SLUG),
			'preferences-page'          => ws_ls_get_link_to_user_profile((false === empty($_GET['user-id'])) ? esc_attr($_GET['user-id']) : '')
			];
}

/**
 * Delete all cache in the event admin delete's all data.
 */
function ws_ls_tidy_cache_on_delete(){
	ws_ls_cache_delete_all();
}
add_action( 'wlt-hook-data-all-deleted', 'ws_ls_tidy_cache_on_delete' );
add_action( 'wlt-hook-data-user-deleted', 'ws_ls_tidy_cache_on_delete' );

/**
 * Add view link alongside WP action links
 * @param $actions
 * @param $user_object
 * @return mixed
 */
function wlt_user_action_links( $actions, $user_object ) {
    $actions[ 'weight-tracker' ] = sprintf(  '<a href="%s">%s</a>',
        ws_ls_get_link_to_user_profile( $user_object->ID ),
        __( 'Weight entries', WE_LS_SLUG )
    );

    return $actions;
}
add_filter( 'user_row_actions', 'wlt_user_action_links', 10, 2 );
