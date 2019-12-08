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

/**
 * Add Challenge Opt in into the settings form
 * @param $html
 * @param $user_id
 * @return string
 */
function ws_ls_challenges_hook_settings_form_opt_in( $html, $user_id ) {

    $html           = ws_ls_title( __( 'Challenges', WE_LS_SLUG ) );
    $current_value  = ws_ls_get_user_setting( 'challenge_opt_in', $user_id );

    $html .= sprintf( '    <select name="ws-ls-challenge-opt-in" id="ws-ls-challenge-opt-in" tabindex="%1$d">
                                        <option value="no">%2$s</option>
                                        <option value="yes" %3$s>%4$s</option>      
                                    </select>',
                                    ws_ls_get_next_tab_index(),
                                    __( 'No - Do not opt me into any challenges', WE_LS_SLUG ),
                                    selected( '1', $current_value, false ),
                                    __( 'Yes - Opt me into challenges!', WE_LS_SLUG )

    );

    return $html;
}
add_filter( 'wlt-filter-user-settings-below-dob',  'ws_ls_challenges_hook_settings_form_opt_in', 10, 2 );

/**
 * Save user preference value
 *
 * @param $html
 *
 * @return string
 */
function ws_ls_challenges_hook_settings_form_save( $fields ) {

    $opt_in                         = ws_ls_ajax_post_value('ws-ls-challenge-opt-in');
    $fields[ 'challenge_opt_in' ]   = ( 'yes' === $opt_in );

    return $fields;
}
add_action( 'wlt-filter-user-settings-save-fields',  'ws_ls_challenges_hook_settings_form_save' );

/**
 * Add DB format for user settings
 * @param $formats
 * @return mixed
 */
function ws_ls_challenges_hook_setting_db_format( $formats ) {

    $formats[ 'challenge_opt_in' ] = '%d';

    return $formats;
}
add_filter( 'wlt-filter-user-settings-db-formats', 'ws_ls_challenges_hook_setting_db_format' );


