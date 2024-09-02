<?php

	defined('ABSPATH') or die("Jog on!");

/**
 * Admin Menu
 */
function ws_ls_build_admin_menu() {

	$minimum_role_to_view = ws_ls_permission_role();

	add_menu_page( WE_LS_TITLE, WE_LS_TITLE, $minimum_role_to_view, 'ws-ls-data-home', 'ws_ls_admin_page_data_home', 'dashicons-chart-line');

    // Display manage user screens to relevant roles.
    add_submenu_page( 'ws-ls-data-home', esc_html__( 'User Data', WE_LS_SLUG ),  esc_html__( 'User Data', WE_LS_SLUG ), $minimum_role_to_view, 'ws-ls-data-home', 'ws_ls_admin_page_data_home' );
	add_submenu_page( 'ws-ls-data-home', esc_html__( 'User Groups', WE_LS_SLUG ),  esc_html__( 'User Groups', WE_LS_SLUG ), 'manage_options', 'ws-ls-user-groups', 'ws_ls_settings_page_group' );
	add_submenu_page( 'ws-ls-data-home', esc_html__( 'Custom Fields', WE_LS_SLUG ),  esc_html__('Custom Fields', WE_LS_SLUG), 'manage_options', 'ws-ls-meta-fields', 'ws_ls_meta_fields_page' );
    add_submenu_page( 'ws-ls-data-home', esc_html__( 'Awards', WE_LS_SLUG ),  esc_html__('Awards', WE_LS_SLUG), 'manage_options', 'ws-ls-awards', 'ws_ls_awards_page' );
    add_submenu_page( 'ws-ls-data-home', esc_html__( 'Challenges', WE_LS_SLUG ),  esc_html__('Challenges', WE_LS_SLUG), 'manage_options', 'ws-ls-challenges', 'ws_ls_challenges_admin_page' );

	$menu_text = ( false === WS_LS_IS_PRO && false === WS_LS_IS_PRO_PLUS ) ? esc_html__('Upgrade', WE_LS_SLUG) : esc_html__('Your License', WE_LS_SLUG);

	add_submenu_page( 'ws-ls-data-home', esc_html__('Settings', WE_LS_SLUG),  esc_html__('Settings', WE_LS_SLUG), 'manage_options', 'ws-ls-settings', 'ws_ls_settings_page');
	add_submenu_page( 'ws-ls-data-home', $menu_text,  $menu_text, 'manage_options', 'ws-ls-license', 'ws_ls_advertise_pro');

    if ( true === ws_ls_setup_wizard_show_notice() ) {
        add_submenu_page( 'ws-ls-data-home', esc_html__('Setup Wizard', WE_LS_SLUG),  esc_html__('Setup Wizard', WE_LS_SLUG), 'manage_options', 'ws-ls-data-setup-wizard', 'ws_ls_setup_wizard_page');
    }

	add_submenu_page( 'ws-ls-data-home', esc_html__('Help & Log', WE_LS_SLUG),  esc_html__('Help & Log', WE_LS_SLUG), 'manage_options', 'ws-ls-help', 'ws_ls_help_page');

}
add_action( 'admin_menu', 'ws_ls_build_admin_menu' );

/**
 * Enqueue required CSS
 */
function ws_ls_enqueue_css(){

	$minified = ws_ls_use_minified();

	// CSS
	if ( false === ws_ls_css_is_disabled() ) {
		wp_enqueue_style( 'wlt-style', plugins_url( '../assets/css/ws-ls' . 	$minified . '.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
		wp_enqueue_style( 'wlt-style-both', plugins_url( '../assets/css/admin-and-public' . 	$minified . '.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
	}

	wp_enqueue_style( 'jquery-style', plugins_url( '../assets/css/libraries/jquery-ui.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );

	wp_enqueue_style( 'wlt-tabs', plugins_url( '../assets/css/libraries/tabs.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
	wp_enqueue_style( 'wlt-tabs-flat', plugins_url( '../assets/css/libraries/tabs.flat.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );

}
add_action( 'wp_head', 'ws_ls_enqueue_css' );

$ws_already_enqueued = false;

/**
 * Enqueue required JS
 */
function ws_ls_enqueue_files(){

	global $ws_already_enqueued;

	if( true === $ws_already_enqueued ) {
		return;
	}

	$minified = ws_ls_use_minified();

	// JavaScript files
    wp_enqueue_script( 'jquery-validate', plugins_url( '../assets/js/libraries/jquery.validate.min.js', __FILE__ ), [ 'jquery' ] , WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'jquery-validate-additional', plugins_url( '../assets/js/libraries/additional-methods.min.js', __FILE__ ), [ 'jquery', 'jquery-validate' ], WE_LS_CURRENT_VERSION );

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'ws-ls-js', plugins_url( '../assets/js/ws-ls' . 	$minified . '.js', __FILE__ ), [], WE_LS_CURRENT_VERSION, true );
	wp_enqueue_script( 'ws-ls-js-form', plugins_url( '../assets/js/ws-ls-entry-form' . 	$minified . '.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION, true );

    // Add localization data for JS
	wp_localize_script('ws-ls-js', 'ws_ls_config', ws_ls_config_js() );

	// Tabs enabled?
	wp_enqueue_style( 'wlt-font-awesome', WE_LS_CDN_FONT_AWESOME_CSS, [], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'jquery-tabs',plugins_url( '../assets/js/libraries/tabs.min.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION, true );

	$ws_already_enqueued = true;

}

/**
 * Add scoping for UIkit
 */
add_filter( 'body_class', function( $classes ) {
	$classes[]  = 'uk-scope';
	$classes[]  = 'ykuk-scope';
	return $classes;
});

$uikit_js_enqueued = false;

/**
 * Enqueue relevant dependencies for UI Kit
 *
 * @param bool $include_theme
 * @param bool $include_font
 * @param null $load_ui_script
 */
function ws_ls_enqueue_uikit( $include_theme = true, $include_font = true, $load_ui_script = NULL ) {

	$minified = ws_ls_use_minified();

	wp_enqueue_style( 'yk-uikit', plugins_url( '../assets/uikit/css/uikit' . 	$minified . '.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );

	if ( true === $include_theme ) {
		wp_enqueue_style( 'yk-uikit-theme', plugins_url( '../assets/uikit/css/uikit-theme.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
	}

	if ( true === $include_font ) {
		wp_add_inline_style( 'yk-uikit', '	@import url(\'https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300&display=swap\');

										.ws-ls-tracker, .ws-ls-tracker-force-font *, .ws-ls-tracker *, .ykuk-modal-dialog *, .ykuk-notification *, .ykuk-table *   {
										  font-family: "Roboto Mono", monospace !important;
										}' );
	}

	wp_enqueue_script( 'yk-uikit', plugins_url( '../assets/uikit/js/uikit' . 	$minified . '.js', __FILE__ ), [] , WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'yk-uikit-icons', plugins_url( '../assets/uikit/js/uikit-icons' . 	$minified . '.js', __FILE__ ), [] , WE_LS_CURRENT_VERSION);

	global $uikit_js_enqueued;

	if ( false === $uikit_js_enqueued ) {
		wp_localize_script( 'yk-uikit', 'wt_config', ws_ls_enqueue_uikit_js() );
	}

	$uikit_js_enqueued = true;

	if ( false === empty( $load_ui_script ) ) {
		wp_enqueue_script( 'yk-uikit-' . $load_ui_script, plugins_url( '../assets/js/' . $load_ui_script . $minified . '.js', __FILE__ ), [ 'jquery' ] , WE_LS_CURRENT_VERSION, true );
	}

}

/**
 * Add JS config for uikit script
 * @return array
 */
function ws_ls_enqueue_uikit_js() {
	return [ 'ajax-url'             => admin_url( 'admin-ajax.php' ),
	         'ajax-security-nonce'  => wp_create_nonce( 'ws-ls-nonce' ) ];
}

/**
 * Enqueue ws-ls.js only JS
 */
function ws_ls_enqueue_files_ws_ls_only() {

	$minified = ws_ls_use_minified();

	wp_enqueue_script( 'ws-ls-js', plugins_url( '../assets/js/ws-ls' . 	$minified . '.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION, true );

	// Add localization data for JS
	wp_localize_script('ws-ls-js', 'ws_ls_config', ws_ls_config_js() );
}

/**
 * Enqueue relevant CSS / JS for admin
 */
function ws_ls_enqueue_admin_files(){

	$minified = ws_ls_use_minified();

	wp_enqueue_style( 'ws-ls-admin-style', plugins_url( '../assets/css/admin' . 	$minified . '.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );

    wp_enqueue_script( 'ws-ls-admin', plugins_url( '../assets/js/admin' . 	$minified . '.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION );

	wp_localize_script( 'ws-ls-admin', 'ws_ls_security', [ 'ajax-security-nonce' => wp_create_nonce( 'ws-ls-nonce' ) ] );

    // Settings page
	if( false === empty( $_GET[ 'page' ] ) && true === in_array( $_GET[ 'page' ], [ 'ws-ls-settings', 'ws-ls-data-setup-wizard' ] ) ) {
		wp_enqueue_script('jquery-tabs', plugins_url( '../assets/js/libraries/tabs.min.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION );
		wp_enqueue_style('wlt-tabs', plugins_url( '../assets/css/libraries/tabs.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
		wp_enqueue_style('wlt-tabs-flat', plugins_url( '../assets/css/libraries/tabs.flat.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
	}

	if( false === empty( $_GET[ 'page' ] ) && true === in_array( $_GET['page'], [ 'ws-ls-data-home', 'ws-ls-license', 'ws-ls-data-setup-wizard', 'ws-ls-challenges' ] ) ) {
		wp_enqueue_style('wlt-font-awesome', WE_LS_CDN_FONT_AWESOME_CSS, [], WE_LS_CURRENT_VERSION );
    }

	// Include relevant JS for admin "Manage User data" pages
    if( 'ws-ls-data-home' === ws_ls_querystring_value( 'page' ) && 'user-settings' === ws_ls_querystring_value( 'mode' ) ) {
		wp_enqueue_script( 'ws-ls-admin-user-pref', plugins_url( '../assets/js/admin.user-preferences' . 	$minified . '.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION );
		wp_localize_script( 'ws-ls-admin-user-pref', 'ws_ls_user_pref_config', ws_ls_admin_config() );
	}

	// User Data pages
	if( false === empty( $_GET['mode'] ) ) {

		// If on the add / edit entry page, then include relevant CSS / JS for form
		if( true === in_array( $_GET[ 'mode' ], [ 'add', 'entry', 'user-settings' ] ) ) {
			ws_ls_enqueue_form_dependencies();
		} else if ( 'user' == $_GET[ 'mode' ] ) {
            wp_enqueue_script( 'ws-ls-chart-js', WE_LS_CDN_CHART_JS, [ 'jquery' ], WE_LS_CURRENT_VERSION );
            wp_enqueue_script( 'jquery-chart-ws-ls', plugins_url( '../assets/js/ws-ls-chart' . 	$minified . '.js', __FILE__ ), [ 'ws-ls-chart-js' ], WE_LS_CURRENT_VERSION, true );
			wp_localize_script( 'jquery-chart-ws-ls', 'ws_ls_config', ws_ls_config_js() );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'ws_ls_enqueue_admin_files');

/**
 * Include relevant JS for forms in Admin
 */
function ws_ls_enqueue_form_dependencies() {

	$minified = ws_ls_use_minified();

	// CSS
	wp_enqueue_style( 'wlt-style', plugins_url( '../assets/css/ws-ls' . 	$minified . '.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
	wp_enqueue_style('jquery-style', plugins_url( '../assets/css/libraries/jquery-ui.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );

	// JavaScript
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-validate',plugins_url( '../assets/js/libraries/jquery.validate.min.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'jquery-validate-additional',plugins_url( '../assets/js/libraries/additional-methods.min.js', __FILE__ ), [ 'jquery', 'jquery-validate' ], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'ws-ls-js', plugins_url( '../assets/js/ws-ls-entry-form' . $minified . '.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION, true );
	wp_localize_script( 'ws-ls-js', 'ws_ls_config', ws_ls_config_js() );
}

/**
 * Should we use a minified version of an asset file?
 * @return string
 */
function ws_ls_use_minified() {
	return ( defined('SCRIPT_DEBUG' ) && ! SCRIPT_DEBUG ) ? '.min' : '';
}

function ws_ls_admin_config() {
	return [	'ajax-security-nonce'       => wp_create_nonce( 'ws-ls-nonce' ),
				'preferences-save-ok'       => esc_html__('The preferences for this user have been saved.', WE_LS_SLUG ),
				'preferences-saved-fail'    => esc_html__('An error occurred while trying to save the user\'s preferences.', WE_LS_SLUG ),
				'preferences-page'          => ws_ls_get_link_to_user_profile(( false === empty( $_GET[ 'user-id'] ) ) ? (int) $_GET[ 'user-id' ] : '' ) ];
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
        esc_html__( 'Weight entries', WE_LS_SLUG )
    );

    return $actions;
}
add_filter( 'user_row_actions', 'wlt_user_action_links', 10, 2 );

/**
 * Add a CSS classes to the <body>
 * @param $classes
 * @return array
 */
function wlt_body_class( $classes ) {

	$classes[]  = 'yk-wt';
	$classes[]  = 'yk-wt-' . ws_ls_generate_site_hash();
	$license    = ws_ls_has_a_valid_license();
	$classes[]  = sprintf( 'yk-wt-%s', ( false === empty( $license ) ) ? esc_attr( $license ) : 'no-license' );

	return $classes;
}
add_filter( 'body_class','wlt_body_class' );

