<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Add challenges to main menu
 */
function ws_ls_challenges_menu() {
    add_submenu_page( 'ws-ls-data-home', __('Challenges', WE_LS_SLUG),  __('Challenges', WE_LS_SLUG), 'manage_options', 'ws-ls-challenges', 'ws_ls_challenges_admin_page' );
}
add_action( 'admin_menu', 'ws_ls_challenges_menu' );

/**
 * If a user's cache has been flushed, then lets clear the last_processed flag on challenges data column (i.e. we need to reprocess)
 * @param $user_id
 */
function ws_ls_challenges_hook_clear_last_processed_on_data_change( $user_id ) {

    // Clear last processed flag for all challenges for this user i.e. we want to re calculate everything for them.
    ws_ls_challenges_data_last_processed_reset( $user_id );

    // Update the user's data for all challenges
    ws_ls_challenges_process( $user_id, false );
}
add_action( 'wlt-hook-delete-cache-for-user', 'ws_ls_challenges_hook_clear_last_processed_on_data_change' );