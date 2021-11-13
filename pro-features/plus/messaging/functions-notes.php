<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Are notes enabled?
 * @return bool
 */
function ws_ls_note_is_enabled() {
	return WS_LS_IS_PRO;
}

/**
 * Fetch notes for the given user
 * @param $to
 * @param bool $visible_to_user
 * @param null $limit
 *
 * @return bool|null
 */
function ws_ls_notes_fetch( $to, $visible_to_user = false, $limit = NULL ) {

	if ( false === ws_ls_note_is_enabled() ) {
		return false;
	}

	return ws_ls_messaging_db_select( $to, NULL, true, $visible_to_user, $limit );
}

/**
 * Add an admin note for a user
 *
 * @param $user_id
 * @param $note
 *
 * @param bool $visible_to_user
 *
 * @return bool
 */
function ws_ls_note_add( $user_id, $note, $visible_to_user = false ) {

	if ( false === ws_ls_note_is_enabled() ) {
		return false;
	}

	if ( true === empty( $user_id ) ||
	     true === empty( $note ) ) {
		return false;
	}

	return ws_ls_messaging_db_add( $user_id, get_current_user_id(), $note, true, $visible_to_user );
}

/**
 * Render note
 * @param $note
 * @param bool $echo
 *
 * @return string
 */
function ws_ls_notes_render( $note, $echo = true ) {

	if ( true === empty( $note ) ) {
		return '';
	}

	$note[ 'message_text' ] = ws_ls_notes_sanitise( $note[ 'message_text' ] );

	$html = sprintf( '	<div id="%5$s" class="postbox ws-ls-postbox ws-ls-note">
							<div class="postbox-header ws-ls-note-header">
								<h2 class="hndle"><span>by %2$s on %3$s %6$s</span></h2>
							<div class="handle-actions hide-if-no-js ws-note-delete-div">
								<a href="#" class="button-secondary ws-note-delete-action" data-id="%1$d" data-div-id="%5$s"><i class="fa fa-trash"></i></a>
							</div>
							</div>
							<div class="ws-ls-note-content inside">
								<p>%4$s</p>
							</div>
						</div>',
						$note[ 'id' ],
						ws_ls_user_display_name( $note[ 'from' ] ),
						ws_ls_iso_datetime_into_correct_format( $note[ 'created' ] ),
						$note[ 'message_text' ],
						ws_ls_component_id(),
						true === ws_ls_to_bool( $note[ 'visible_to_user' ] ) ? __( ' (Visible via [wt-notes])', WE_LS_SLUG ) : ''

    );

	if ( false === $echo ) {
		return $html;
	}

	echo $html;
}

/**
 * Render note content
 * @param $content
 *
 * @return string|string[]
 */
function ws_ls_notes_sanitise( $content ) {

	if ( true === empty( $content ) ) {
		return '';
	}

	$content = stripslashes( $content );
	$content = wp_kses_post( $content );
	$content = str_replace( PHP_EOL, '<br />', $content );

	return $content;
}
