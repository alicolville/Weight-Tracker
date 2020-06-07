<?php

defined('ABSPATH') or die("Jog on!");

/*
 *
 * TODO: Refactor this file to use new email manager code
 *
 */

function ws_ls_email_notification($type, $weight_data) {

	if(!WE_LS_EMAIL_ENABLE) {
		return;
	}

	if(!is_array($type) && !is_array($weight_data)) {
		return;
	}

	$email_addresses  = ws_ls_email_notification_addresses();
	$allowed_types = array('target', 'weight-measurements');
	$allowed_modes = array('add', 'update');

	// Do we actually have one or more email addresses?
	if($email_addresses
		&& !empty($type['type']) && in_array($type['type'], $allowed_types)
		 && !empty($type['mode']) && in_array($type['mode'], $allowed_modes)) {

		// Email notifications enable for this type?
		if (('target' == $type['type'] && false == WE_LS_EMAIL_NOTIFICATIONS_TARGETS) ||
			('weight-measurements' == $type['type'] && 'add' == $type['mode'] && false == WE_LS_EMAIL_NOTIFICATIONS_NEW) ||
			('weight-measurements' == $type['type'] && 'update' == $type['mode'] && false == WE_LS_EMAIL_NOTIFICATIONS_EDIT)) {
			return;
		}

		$email_data = array();

		$email_data['displayname'] = ws_ls_user_display_name( $type[ 'user-id' ] );

		// Mode / Type
		$email_data['mode'] = ('add' == $type['mode']) ? __( 'added' , WE_LS_SLUG) : __( 'updated' , WE_LS_SLUG);
		$email_data['type'] = ('weight-measurements' == $type['type']) ?
				__( 'their weight / custom fields for ' , WE_LS_SLUG) . ws_ls_convert_ISO_date_into_locale( $weight_data[ 'weight_date' ], 'display' )
						: __( 'their target to' , WE_LS_SLUG);

		// Convert Weight into expected admin format
		$display_weight =  ws_ls_weight_display( $weight_data['kg'], $user_id = NULL, 'display', true );

		$email_data['data'] = $display_weight . '<br />';

		// Weight data
		if('weight-measurements' == $type['type'] && !empty($weight_data['weight_notes'])) {
			// Do we have notes?
			$email_data['data'] .= '<br />' . __( 'Notes' , WE_LS_SLUG) . ':' . '<br />' . '-----------------------' . '<br />';
			$email_data['data'] .= '<br /><strong>' . esc_html($weight_data['weight_notes']) . '</strong><br />' . '-----------------------' . '<br />';
		}

		// Meta Fields
		if ( true === ws_ls_meta_fields_is_enabled() && false === empty( $weight_data['meta'] ) ) {

			$meta_fields = ws_ls_meta_fields_enabled();

			$weight_data['meta'] = wp_list_pluck($weight_data['meta'], 'value', 'meta_field_id' );

			$email_data['data'] .= '<br /><strong>' . __( 'Custom Fields' , WE_LS_SLUG) . ':</strong>' . '<br />' . '-----------------------' . '<br />';

			foreach ( $meta_fields as $field ) {

				if ( false === empty( $weight_data['meta'][ $field['id'] ] ) ) {
					$email_data['data'] .= '<br /><em>' . $field['field_name'] . '</em><br />' . ws_ls_fields_display_field_value( $weight_data['meta'][ $field['id'] ], $field['id'] ) . '<br />';
				}
			}

		}

		// Allow others to filter data
		$email_data = apply_filters(WE_LS_FILTER_EMAIL_DATA, $email_data, $type, $weight_data);

		$email = ws_ls_email_notifications_template($email_data);

		// Send email
		wp_mail($email_addresses,
		 		__( 'Weight Tracker update for ' , WE_LS_SLUG) . $email_data['displayname'],
				$email,
                [ 'Content-Type: text/html; charset=UTF-8' ]
        );

	}

	return;

}
add_action( 'wlt-hook-data-added-edited', 'ws_ls_email_notification', 10, 2);

/*
	Returns a standard email template. This wil be expanded in future releases.
*/
function ws_ls_email_notifications_template($placeholders = array()) {

	$email = sprintf( '<p>%s,</p>', __( 'Hello' , WE_LS_SLUG) );
    $email .= __( '<p>Just a quick email to let you know that "{displayname}" has {mode} {type}:</p>' , WE_LS_SLUG);
	$email .= __( '{data}' , WE_LS_SLUG) . PHP_EOL . PHP_EOL;
	$email .= sprintf( '<p>%s</p>', __( 'Thank you!' , WE_LS_SLUG) );

	if(!empty($placeholders)) {
		foreach ($placeholders as $key => $value) {
			$email = str_replace('{' . $key . '}', $value, $email);
		}
	}
	return $email;
}

function ws_ls_email_notification_addresses() {

	if(!defined('WE_LS_EMAIL_ADDRESSES')) {
		return false;
	}

	$emails = explode(',',  WE_LS_EMAIL_ADDRESSES);
	return (is_array($emails) && !empty($emails)) ? $emails : false;
}
