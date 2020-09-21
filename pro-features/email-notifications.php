<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Is enabled notifications enabled?
 * @return bool
 */
function ws_ls_email_enabled() {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	return ( 'yes' === get_option( 'ws-ls-email-enable', 'no' ) );
}

/**
 * Send email notifications for weight / meta updates as wel as targets
 * @param $type
 * @param $weight_data
 */
function ws_ls_email_notification( $type, $weight_data ) {

	if( false === ws_ls_email_enabled() ) {
		return;
	}

	if( false === is_array( $type ) || false === is_array( $weight_data ) ) {
		return;
	}

	$email_addresses  = ws_ls_email_notification_addresses();

	if ( true === empty( $email_addresses ) ) {
		return;
	}

	// Do we actually have one or more email addresses?
	if( false === empty( $type['type'] ) && in_array( $type['type'], [ 'target', 'weight-measurements' ] )
		 && false === empty( $type['mode'] ) && true === in_array( $type[ 'mode' ], [ 'add', 'update' ] ) ) {

		// Email notifications enable for this type?
		if ( ( 'target' == $type['type'] && 'no' == get_option( 'ws-ls-email-notifications-targets', 'yes' ) ) ||
			( 'weight-measurements' == $type['type'] && 'add' == $type['mode'] && 'no' === get_option( 'ws-ls-email-notifications-new', 'yes' ) ) ||
			( 'weight-measurements' == $type['type'] && 'update' == $type['mode'] && 'no' === get_option( 'ws-ls-email-notifications-edit', 'yes' ) ) ) {
			return;
		}

		// Convert Weight into expected admin format
		$display_weight =  ws_ls_weight_display( $weight_data['kg'], NULL, 'display', true );

		$email_data = [ 'displayname'   => ws_ls_user_display_name( $type[ 'user-id' ] ),
						'mode'          => ('add' === $type[ 'mode' ] ) ? __( 'added' , WE_LS_SLUG ) : __( 'updated' , WE_LS_SLUG ),
						'type'          => ( 'weight-measurements' === $type[ 'type' ] ) ?
												__( 'their weight / custom fields for ' , WE_LS_SLUG) . ws_ls_convert_ISO_date_into_locale( $weight_data[ 'weight_date' ], 'display-date' ) :
													 __( 'their target to' , WE_LS_SLUG ),
						'data'          => sprintf('<h3>%s</h3>', $display_weight ),
						'subject'       => sprintf( '%s %s %s: %s',
										( 'weight-measurements' === $type[ 'type' ] ) ? __( 'Weight entry' , WE_LS_SLUG ) : __( 'Target' , WE_LS_SLUG ),
										('add' === $type[ 'mode' ] ) ? __( 'added for' , WE_LS_SLUG ) : __( 'updated for' , WE_LS_SLUG ),
										ws_ls_user_display_name( $type[ 'user-id' ] ),
										$display_weight
						)


		];

		// Do we have notes?
		if( 'weight-measurements' === $type[ 'type' ]
		        &&  false === empty( $weight_data[ 'notes' ] ) ) {


			$email_data[ 'data' ] .= sprintf('<h4>%s</h4><p>%s</p>', __( 'Notes', WE_LS_SLUG ), esc_html( $weight_data[ 'notes' ] ) );

		}

		// Meta Fields
		if ( true === ws_ls_meta_fields_is_enabled()
		     && false === empty( $weight_data['meta'] ) ) {

			$email_data['data'] .= sprintf('<h4>%s</h4>', __( 'Custom Fields', WE_LS_SLUG ) );

			foreach ( ws_ls_meta_fields_enabled() as $field ) {

				$value = ( false === empty( $weight_data[ 'meta' ][ $field[ 'id' ] ] ) ) ?
							ws_ls_fields_display_field_value( $weight_data[ 'meta' ][ $field[ 'id' ] ], $field[ 'id' ] ) :
								__( 'Not specified', WE_LS_SLUG );

				$email_data[ 'data' ] .= sprintf('<p><em>%s</em></p><p>%s</p>', esc_html( $field['field_name'] ), $value );
			}

		}

		// Add weight summary
		if ( 'yes' === get_option( 'ws-ls-email-include-weight-summary', 'yes' ) ) {
			$email_data[ 'data' ] .= ws_ls_email_user_summary( $type[ 'user-id' ] );
		}

		// Allow others to filter data
		$email_data = apply_filters( 'wlt-filter-email-data', $email_data, $type, $weight_data);

		$message    = ws_ls_emailer_get( 'notify' );

		if ( false === empty( $message[ 'email' ] ) ) {
			ws_ls_emailer_send( $email_addresses, $email_data[ 'subject' ], $message[ 'email' ], $email_data );
		}
	}

	return;
}
add_action( 'wlt-hook-data-added-edited', 'ws_ls_email_notification', 10, 2);

/**
 * Include an additional weight summary for the user
 * @param $user_id
 * @param bool $target
 * @return string
 */
function ws_ls_email_user_summary( $user_id ) {

	$summary = sprintf('<h4>%s</h4>', __( 'Weight Summary', WE_LS_SLUG ) );

	$latest_entry = ws_ls_entry_get_latest( [ 'user-id' => $user_id, 'meta' => false ] );

	if ( false === empty( $latest_entry ) ) {
		$summary .= sprintf('<h5>%s (%s)</h5>', __( 'Most Recent Weight', WE_LS_SLUG ), ws_ls_convert_ISO_date_into_locale( $latest_entry[ 'weight_date' ], 'display-date', true ) );
		$summary .= sprintf('<p>%s</p>', ws_ls_weight_display( $latest_entry[ 'kg' ], $user_id, 'display', true ) );
	}

	$previous_entry = ws_ls_entry_get_previous( [ 'user-id' => $user_id, 'meta' => false ] );

	if ( false === empty( $previous_entry[ 'kg' ] ) ) {

		$difference = $latest_entry[ 'kg' ] - $previous_entry[ 'kg' ];
		$sign       = ( $difference > 0 ) ? '+' : '';
		$difference = ws_ls_weight_display( $difference, $user_id, false, true, true );

		$summary .= sprintf('<h5>%s (%s)</h5>', __( 'Previous Weight', WE_LS_SLUG ), ws_ls_convert_ISO_date_into_locale( $previous_entry[ 'weight_date' ], 'display-date', true ) );
		$summary .= sprintf('<p>%s</p>', ws_ls_weight_display( $previous_entry[ 'kg' ], $user_id, 'display', true ) );

		$summary .= sprintf('<h5>%s</h5>', __( 'Difference between recent and previous entries', WE_LS_SLUG ) );
		$summary .= sprintf('<p>%s</p>', $difference[ 'display' ] );

	}

	$start_entry = ws_ls_entry_get_oldest( [ 'user-id' => $user_id, 'meta' => false ] );

	$summary .= sprintf('<h5>%s (%s)</h5>', __( 'Start Weight', WE_LS_SLUG ), ws_ls_convert_ISO_date_into_locale( $start_entry[ 'weight_date' ], 'display-date', true )  );
	$summary .= sprintf('<p>%s</p>', ws_ls_weight_display( $latest_entry[ 'first_weight' ], $user_id, 'display', true ) );

	$summary .= sprintf('<h5>%s</h5>', __( 'Difference from Start Weight', WE_LS_SLUG ) );
	$summary .= sprintf('<p>%s</p>', ws_ls_weight_display( $latest_entry[ 'difference_from_start_kg' ], $user_id, 'display', true ) );

	return $summary;
}

/**
 * Return an array of emails to send email notifications too
 * @return string[]|null
 */
function ws_ls_email_notification_addresses() {

	$email_addresses = get_option( 'ws-ls-email-addresses', '' );

	if ( true === empty( $email_addresses ) ) {
		return NULL;
	}

	$emails = explode(',',  $email_addresses );

	return ( true === is_array( $emails ) && false === empty( $emails ) ) ? $emails : NULL;
}

/**
 *  Add email template for email notifications
 */
function ws_ls_email_notification_activate() {

	// Only run this when the plugin version has changed
	if( true === update_option('ws-ls-email-notification-db-number', WE_LS_DB_VERSION ) ) {

		// Insert the notification template
		if ( false === ws_ls_emailer_get('notify') ) {

			$email = sprintf( '<p>%s,</p>', __( 'Hello' , WE_LS_SLUG) );
			$email .= __( '<p>Just a quick email to let you know that "{displayname}" has {mode} {type}:</p>' , WE_LS_SLUG);
			$email .= __( '<p>{data}</p>' , WE_LS_SLUG) . PHP_EOL . PHP_EOL;

			ws_ls_emailer_add( 'notify', 'Weight Tracker Update', '<center>' . $email . '</center>' );
		}
	}
}
add_action( 'admin_init', 'ws_ls_email_notification_activate' );
