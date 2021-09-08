<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Render Opt-in or Opt-out buttons
 *
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_challenges_shortcodes_opt_in( $user_defined_arguments ) {

    if( false === WS_LS_IS_PRO_PLUS ) {
        return '';
    }

	$arguments = shortcode_atts( [  'always-show' => false ] , $user_defined_arguments );

	// Opt-in set?
	$get_optin = ws_ls_querystring_value( 'opt-in' );

	// Update user preferences;
	if ( false !== $get_optin && true === in_array( $get_optin, [ 'yes', 'no' ] ) ) {
        ws_ls_set_user_preference_simple( 'challenge_opt_in', ( 'yes' === $get_optin ) ? 1 : 0 );

        return sprintf( '<p>%1$s %2$s.</p> ',
                            __( 'You have been', WE_LS_SLUG ),
                            ( 'yes' === $get_optin ) ? __( 'opted into all challenges', WE_LS_SLUG ) : __( 'opted out of all challenges', WE_LS_SLUG )
        );
    }

	$opt_int_status     = ws_ls_user_preferences_get( 'challenge_opt_in' );
	$status_never_set   = ( -1 === (int) $opt_int_status );

	// Only show the buttons if a User had never specified an opt-in preference before.
	if ( false === $status_never_set
	        && true !== ws_ls_to_bool( $arguments[ 'always-show' ] ) ) {
		return '';
	}

    global $wp;
	$current_url = home_url( $wp->request );

	return sprintf( '   <div class="wlt-opt-in-buttons">
							<a href="%1$s%2$s" class="btn button ws-ls-button" />%3$s</a>
							<a href="%1$s%4$s" class="btn button ws-ls-button" />%5$s</a>
						</div>
						 ',
		esc_url( $current_url ),
		'?opt-in=yes',
		__( 'Opt in', WE_LS_SLUG ),
		'?opt-in=no',
		__( 'Opt Out', WE_LS_SLUG )
	);
}
add_shortcode( 'wlt-challenges-optin', 'ws_ls_challenges_shortcodes_opt_in' );
add_shortcode( 'wt-challenges-optin', 'ws_ls_challenges_shortcodes_opt_in' );

/**
 * Display Challenge entries
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_challenges_shortcodes_list_entries( $user_defined_arguments ) {

    if( false === WS_LS_IS_PRO_PLUS ) {
        return '';
    }

    ws_ls_data_table_enqueue_scripts();

    return ws_ls_challenges_view_entries( $user_defined_arguments );
}
add_shortcode( 'wlt-challenges', 'ws_ls_challenges_shortcodes_list_entries' );
add_shortcode( 'wt-challenges', 'ws_ls_challenges_shortcodes_list_entries' );
/**
 * Examine filters and render out the example shortcode
 */
function ws_ls_challenges_view_entries_guide() {

    $challenge_id = ws_ls_querystring_value( 'challenge-id', true );

    if ( false === $challenge_id ) {
        return;
    }

    printf( '<h3>%1$s</h3><p>%2$s</p>',
                __( 'Display data on the front end ', WE_LS_SLUG ),
                __( 'If you wish to display this table on a page or post, use the following shortcode: ', WE_LS_SLUG )
    );

    $arguments = '';

    foreach ( [ 'age-range', 'gender', 'group-id', 'min-wt-entries' ] as $key ) {

        $value = ws_ls_querystring_value( 'filter-' . $key, true );

        if( false === empty( $value ) ) {
            $arguments .= sprintf(' %1$s="%2$d"', $key, $value );
        }
    }

    $value = ws_ls_querystring_value( 'filter-opt-in', true );

    if( 0 === $value ) {
        $arguments .= ' opted-in="0"';
    }

    printf( '<p>[wt-challenges id="%1$d"%2$s show-filters="false" sums-and-averages="true"]</p>', $challenge_id, $arguments );
}
