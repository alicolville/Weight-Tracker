<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Add challenges to main menu
 */
function ws_ls_challenges_menu() {
    add_submenu_page( 'ws-ls-data-home', __('Challenges', WE_LS_SLUG),  __('Challenges', WE_LS_SLUG), 'manage_options', 'ws-ls-challenges', 'ws_ls_challenges_admin_page' );
}
add_action( 'admin_menu', 'ws_ls_challenges_menu' );