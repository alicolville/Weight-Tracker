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
 *
 * @param $to
 * @param bool $visible_to_user
 * @param null $limit
 *
 * @param null $offset
 *
 * @return bool|null
 */
function ws_ls_notes_fetch( $to, $visible_to_user = false, $limit = NULL, $offset = NULL ) {

	if ( false === ws_ls_note_is_enabled() ) {
		return false;
	}

	return ws_ls_messaging_db_select( $to, NULL, true, false, $visible_to_user, $offset, $limit );
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
 *
 * @param $note
 * @param bool $echo
 *
 * @param bool $uikit
 * @param bool $alternate
 *
 * @return string
 */
function ws_ls_notes_render( $note, $echo = true, $uikit = false, $alternate = false ) {

	if ( true === empty( $note ) ) {
		return '';
	}

	$note[ 'message_text' ] = ws_ls_notes_sanitise( $note[ 'message_text' ] );
	$note[ 'message_text' ] = do_shortcode( $note[ 'message_text' ] );

	if ( true === $uikit ) {

		$html = sprintf( '<article class="ykuk-comment ykuk-margin-medium-top ykuk-visible-toggle ykuk-padding-small ykuk-text-small%1$s" tabindex="-1">
					            <header class="ykuk-comment-header ykuk-position-relative">
					                <div class="ykuk-grid-medium ykuk-flex-middle" ykuk-grid>
					                    <div class="ykuk-width-auto">%2$s</div>
					                    <div class="ykuk-width-expand">
					                        <h4 class="ykuk-comment-title ykuk-margin-remove">%3$s</h4>
					                        <p class="ykuk-comment-meta ykuk-margin-remove-top">%4$s</p>
					                    </div>
					                </div>
					            </header>
					            <div class="ykuk-comment-body">
					                <p>%5$s</p>
					            </div>
					        </article>',
							( true === $alternate ) ? ' ykuk-background-muted' : '',
							get_avatar( $note[ 'from' ], 80 ),
							ws_ls_user_display_name( $note[ 'from' ] ),
							ws_ls_iso_datetime_into_correct_format( $note[ 'created' ] ),
							$note[ 'message_text' ]
		);
	} else {
		$html = sprintf( '	<div id="%5$s" class="postbox ws-ls-postbox ws-ls-note">
							<div class="postbox-header ws-ls-note-header">
								<%7$s class="hndle"><span>by %2$s on %3$s %6$s</span></%7$s>
							<div class="handle-actions hide-if-no-js ws-note-delete-div %8$s">
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
			true === ws_ls_to_bool( $note[ 'visible_to_user' ] ) && true === is_admin() ? __( ' (Visible via [wt-notes])', WE_LS_SLUG ) : '',
			is_admin() ? 'h2' : 'h6',
			! is_admin() ? 'ws-ls-hide' : ''
		);
	}

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
