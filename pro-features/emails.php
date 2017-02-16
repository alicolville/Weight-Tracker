<?php

defined('ABSPATH') or die("Jog on!");

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


			 // TODO ignore certain types.

		$email_data = array();

		// Add display name
		if(!empty($weight_data['user']['display-name'])) {
			$email_data['displayname'] = $weight_data['user']['display-name'];
		}

		// Mode / Type
		$email_data['mode'] = ('add' == $type['mode']) ? __( 'added' , WE_LS_SLUG) : __( 'updated' , WE_LS_SLUG);
		$email_data['type'] = ('weight-measurements' == $type['type']) ? __( 'their weight / measurements for ' , WE_LS_SLUG) . ws_ls_render_date($weight_data, true) : __( 'their target to' , WE_LS_SLUG);

		// Weight data
		if('target' == $type['type']) {
			$email_data['data'] = PHP_EOL . $weight_data['display-admin'] . PHP_EOL;
		} else {

				$email_data['data'] = PHP_EOL . $weight_data['display-admin'] . PHP_EOL;

		}



		// TODO add filter to email_data and templaye


		$email = ws_ls_email_notifications_template($email_data);
		var_dump($email,$email_data, $weight_data, $type); die;
	}

	return;

}
add_action(WE_LS_HOOK_DATA_ADDED_EDITED, 'ws_ls_email_notification', 10, 2);


/*
	Returns a standard email template. This wil be expanded in future releases.
*/
function ws_ls_email_notifications_template($placeholders = array()) {

	$email = __( 'Hello' , WE_LS_SLUG) . ',' . PHP_EOL;
	$email .= __( 'Just a quick email to let you know that {displayname} has {mode} {type}:' , WE_LS_SLUG) . PHP_EOL;
	$email .= __( '{data}' , WE_LS_SLUG) . PHP_EOL;
	$email .= __( 'Thank you!' , WE_LS_SLUG) . PHP_EOL;

	if(!empty($placeholders)) {
		foreach ($placeholders as $key => $value) {
			$email = str_replace('{' . $key . '}', $value, $email);
		}
	}
	return $email;
}

function ws_ls_email_notification_addresses() {

	if(empty(WE_LS_EMAIL_ADDRESSES)) {
		return false;
	}

	$emails = explode(',',  WE_LS_EMAIL_ADDRESSES);
	return (is_array($emails) && !empty($emails)) ? $emails : false;
}


// $globals['WE_LS_EMAIL_ENABLE'] = true;
// }
// if (!empty(get_option('ws-ls-email-addresses'))) {
// $globals['WE_LS_EMAIL_ADDRESSES'] = get_option('ws-ls-email-addresses');
// }
// if ('no' == get_option('ws-ls-email-notifications-edit')) {
// $globals['WE_LS_EMAIL_NOTIFICATIONS_EDIT'] = false;
// }
// if ('no' == get_option('ws-ls-email-notifications-new')) {
// $globals['WE_LS_EMAIL_NOTIFICATIONS_NEW'] = false;
// }
// if ('no' == get_option('ws-ls-email-notifications-targets')) {
// $globals['WE_LS_EMAIL_NOTIFICATIONS_TARGETS'] = false;
