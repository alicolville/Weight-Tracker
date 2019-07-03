<?php

defined('ABSPATH') or die('Jog on!');

define( 'WE_LS_SETUP_WIZARD_DIMISS_OPTION', 'ws-ls-setup-wizard-dismiss' );

/**
 * Display HTML for admin notice
 */
function ws_ls_setup_wizard_notice() {

    printf('<div class="updated notice is-dismissible setup-wizard-dismiss">
                        <p>%1$s <strong>%2$s</strong>! %3$s.</p>
                        <p><a href="%4$s" class="button button-primary">Run wizard</a></p>
                    </div>',
                    __( 'Welcome to' , WE_LS_SLUG),
                    WE_LS_TITLE,
                    __( 'You\'re almost there, but a wizard might help you setup the plugin' , WE_LS_SLUG),
                    '#'
    );
}

/**
 * Show setup wizard
 *
 * @return bool
 */
function ws_ls_setup_wizard_show_notice() {
    return ( false === (bool) get_option( WE_LS_SETUP_WIZARD_DIMISS_OPTION ) );
}

/**
 * Show admin links again?
 */
function ws_ls_setup_wizard_help_page_show_links_again() {

    if ( true === isset( $_GET[ 'show-setup-wizard-links' ] ) ) {
        ws_ls_setup_wizard_show_notice_links_again();
    }
}
add_action( 'admin_init', 'ws_ls_setup_wizard_help_page_show_links_again' );

/**
 * Show Wizard Links again
 */
function ws_ls_setup_wizard_show_notice_links_again() {
    delete_option( WE_LS_SETUP_WIZARD_DIMISS_OPTION, true );
}

/**
 * Show Admin Notice
 */
function ws_ls_setup_wizard_show_admin_notice() {
    if ( true === ws_ls_setup_wizard_show_notice() ) {
        ws_ls_setup_wizard_notice();
    }
}
add_action( 'admin_notices', 'ws_ls_setup_wizard_show_admin_notice' );

/**
 * Update option on whether to show wizard
 */
function ws_ls_setup_wizard_dismiss_notice() {

    update_option( WE_LS_SETUP_WIZARD_DIMISS_OPTION, true );
    wp_send_json( 1 );
}
add_action( 'wp_ajax_ws_ls_setup_wizard_dismiss', 'ws_ls_setup_wizard_dismiss_notice' );
