<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
 * AJAX handler for adding a note
 */
function ws_ls_note_ajax_add() {

	check_ajax_referer( 'ws-ls-add-note', 'security' );

	$user_id            = ws_ls_post_value('user-id' );
	$note               = ws_ls_post_value('note' );
	$visible_to_user    = ws_ls_post_value_to_bool('visible-to-user' );

	if ( false === ws_ls_note_add( $user_id, $note, $visible_to_user ) ) {
		return 0;
	}

	if ( true === ws_ls_post_value_to_bool('send-email', $user_id) &&
			true === ws_ls_emailer_user_has_optedin( 'notes', ) ) {

		$email_template = ws_ls_emailer_get( 'note-added' );

		$current_user = get_userdata( $user_id );

		if (false === empty( $current_user->user_email )) {

			$note = ws_ls_notes_sanitise( $note );

			$current_user = get_userdata( $user_id );

			$placeholders = [
				'first-name'    => ( false === empty( $current_user->first_name ) ) ? $current_user->first_name : '',
				'last-name'     => ( false === empty( $current_user->last_name ) ) ? $current_user->last_name : '',
				'data'          => do_shortcode( $note )
			];

			ws_ls_emailer_send( $current_user->user_email, $email_template['subject'], $email_template['email'], $placeholders );
		}
	}

	$stats = ws_ls_messages_db_stats( $user_id );

	wp_send_json( $stats[ 'notes-count' ] );
}
add_action( 'wp_ajax_ws_ls_add_note', 'ws_ls_note_ajax_add' );

/**
 * Delete notes
 */
function ws_ls_note_ajax_delete() {

	check_ajax_referer( 'ws-ls-delete-note', 'security' );

	$note_id = ws_ls_post_value('id' );

	if ( true === empty( $note_id ) ) {
		wp_send_json_error( 'Missing ID', 400 );
	}

	$response = ws_ls_messaging_db_delete( $note_id );

	wp_send_json( (int) $response );
}
add_action( 'wp_ajax_ws_ls_delete_note', 'ws_ls_note_ajax_delete' );

/**
 * Shortcode for [wt-notes]
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_note_shortcode( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PREMIUM ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments  = shortcode_atts( [ 'user-id'           => get_current_user_id(),
									'paging'            => true,
									'notes-per-page'    => 10,                    // Return 10 notes by default
									'message-no-data'   => esc_html__( 'You currently have no notes from the administrator.', WE_LS_SLUG ),
									'uikit'             => false
	], $user_defined_arguments );

	$stats  = ws_ls_messages_db_stats( $arguments[ 'user-id'] );
	$page   = max(1, ws_ls_querystring_value('notes-page', true ) );
	$notes  = ws_ls_notes_fetch( $arguments[ 'user-id'], true, (int) $arguments[ 'notes-per-page' ], ( $page - 1 ) * (int) $arguments[ 'notes-per-page' ] );

	if ( false === empty( $notes ) ) {

		$html       = '';
		$alternate  = true;

		foreach ( $notes as $note ) {
			$html .= ws_ls_notes_render( $note, false, $arguments[ 'uikit' ], $alternate );

			$alternate = !$alternate;
		}

		// Need paging?
		if ( (int) $stats[ 'notes-count-visible' ] > (int) $arguments[ 'notes-per-page' ] ) {

			$html .= '<br />' . paginate_links([	'base'          => add_query_arg( [ 'notes-page' => '%#%', 'tab' => 'messages' ] ),
													'format'        => '?tab=messages&notes-page=%#%',
													'current'       => $page,
													'total'         => ceil( $stats[ 'notes-count-visible' ] / $arguments[ 'notes-per-page' ] ),
													'prev_text'     => esc_html__( '« prev', WE_LS_SLUG ),
													'next_text'     => esc_html__('next »', WE_LS_SLUG ),
			]);
		}

		return $html;
	}

	return sprintf( '<p>%s</p>', esc_html( $arguments[ 'message-no-data'] ) );
}
add_shortcode( 'wt-notes', 'ws_ls_note_shortcode' );

/**
 * Shortcode for [wt-notices]
 *
 * @param $user_defined_arguments
 *
 * @param bool $disable_pro_check
 *
 * @return string
 */
function ws_ls_notifications_shortcode( $user_defined_arguments, $disable_pro_check = false ) {

	if ( false === WS_LS_IS_PREMIUM && false === $disable_pro_check ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'user-id'           => get_current_user_id(),
	                                'style'             => 'primary',
	                                'message-no-data'   => ''
	], $user_defined_arguments );

	$notifications  = ws_ls_messaging_db_select(  $arguments[ 'user-id'], NULL, false, true );
	$html           = '';

	if ( false === empty( $notifications ) ) {

		foreach ( $notifications as $notification ) {
			$html .= ws_ls_component_alert( [ 'message' => $notification[ 'message_text' ], 'type' => $arguments[ 'style' ], 'notification-id' => $notification[ 'id' ] ] );
		}

	} else {
		$html .= wp_kses_post( $arguments[ 'message-no-data' ] );
	}

	return $html;
}
add_shortcode( 'wt-notifications', 'ws_ls_notifications_shortcode' );   // TODO: Document this? One that's beneficial to surface to end users?

/**
 * Delete older notifications
 */
function ws_ls_notifications_delete_cron() {

	ws_ls_messages_db_delete_old();
}
add_action( 'weight_loss_tracker_daily', 'ws_ls_notifications_delete_cron' );


