<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Render Opt-in or Opt-out buttons
 */
function ws_ls_challenges_shortcodes_opt_in() {

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

	$opt_int_status     = ws_ls_get_user_setting( 'challenge_opt_in' );
	$status_never_set   = ( -1 === (int) $opt_int_status );

	// Only show the buttons if a User had never specified an opt-in preference before.
	if ( false === $status_never_set ) {
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