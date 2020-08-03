<?php

defined('ABSPATH') or die("Jog on!");

/**
 * If a user's cache has been flushed, then lets clear the last_processed flag on challenges data column (i.e. we need to reprocess)
 * @param $user_id
 */
function ws_ls_challenges_hook_clear_last_processed_on_data_change( $user_id ) {

    if ( false === ws_ls_challenges_is_enabled() ) {
        return;
    }

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

    if ( false === ws_ls_challenges_is_enabled() ) {
        return $html;
    }

    $html           .= ws_ls_title( __( 'Challenges', WE_LS_SLUG ) );
    $current_value  = ws_ls_user_preferences_get( 'challenge_opt_in', $user_id );

    $html .= sprintf( '    <select name="ws-ls-challenge-opt-in" id="ws-ls-challenge-opt-in" tabindex="%1$d">
                                        <option value="no">%2$s</option>
                                        <option value="yes" %3$s>%4$s</option>
                                    </select>',
                                    ws_ls_form_tab_index_next(),
                                    __( 'No - Do not opt me into any challenges', WE_LS_SLUG ),
                                    selected( '1', $current_value, false ),
                                    __( 'Yes - Opt me into challenges!', WE_LS_SLUG )

    );

    return $html;
}
add_filter( 'wlt-filter-user-settings-below-dob',  'ws_ls_challenges_hook_settings_form_opt_in', 15, 2 );

/**
 * Save user preference value
 *
 * @param $fields
 * @return string
 */
function ws_ls_challenges_hook_settings_form_save( $fields ) {

    if ( false === ws_ls_challenges_is_enabled() ) {
        return $fields;
    }

    $opt_in                         = ws_ls_post_value('ws-ls-challenge-opt-in');
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

    if ( false === ws_ls_challenges_is_enabled() ) {
        return $formats;
    }

    $formats[ 'challenge_opt_in' ] = '%d';

    return $formats;
}
add_filter( 'wlt-filter-user-settings-db-formats', 'ws_ls_challenges_hook_setting_db_format' );

/**
 * Ajax handler for processing challenge data
 */
function ws_ls_challenges_ajax_process() {

    if ( false === ws_ls_challenges_is_enabled() ) {
        return;
    }

    check_ajax_referer( 'process-challenges', 'security' );

    $process = ws_ls_challenges_process();

    wp_send_json( $process );

}
add_action( 'wp_ajax_process_challenges', 'ws_ls_challenges_ajax_process' );

/**
 * Delete challenge data for given user
 * @param $user_id
 */
function ws_ls_challenges_delete_for_given_user( $user_id ) {
	ws_ls_challenges_delete_for_user( $user_id );
}
add_action( 'wlt-hook-data-user-deleted', 'ws_ls_challenges_delete_for_given_user' );
