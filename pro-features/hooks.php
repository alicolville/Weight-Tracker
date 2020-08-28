<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Setup widgets
 */
function we_ls_register_widgets() {

    if ( false === WS_LS_IS_PRO ) {
        return;
    }

    register_widget( 'ws_ls_widget_chart' );
    register_widget( 'ws_ls_widget_form' );
    register_widget( 'ws_ls_widget_progress_bar' );
}
add_action( 'after_setup_theme', 'we_ls_register_widgets', 20 );
