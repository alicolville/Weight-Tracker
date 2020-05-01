<?php

defined('ABSPATH') or die('Jog on!');

/*
	This file is used to send stats to YeKen.uk and fetch read only data

	No personal or identifiable data is sent to YeKen. Nor is any passwords or technical details.

*/

// ---------------------------------------------------------------------------------
// Record activated license with YeKen
// ---------------------------------------------------------------------------------

function ws_ls_stats_send_license_activation_to_yeken() {

	// If not PRO then nothing to do
	if(false == WS_LS_IS_PRO) {
		return;
	}

	$previously_sent_key = 'ws-ls-license-notify-2020';

	if(false == get_option($previously_sent_key)) {

		// Build payload to send to Yeken
		$data = array();
		$data['reason']	= 'license-activation';
		$data['url'] = get_site_url();
		$data['valid-license'] = ws_ls_has_a_valid_license();
		$data['site-hash'] = get_option(WS_LS_LICENSE_SITE_HASH);
		$data['license'] = ws_ls_license_get_old_or_new();
		wp_remote_post(WE_LS_STATS_URL, array('body' => $data));
	}

	update_option($previously_sent_key, true);
}
add_action(WE_LS_CRON_NAME_YEKEN_COMMS, 'ws_ls_stats_send_license_activation_to_yeken');	// Notification via weekly job
add_action('admin_init', 'ws_ls_stats_send_license_activation_to_yeken');					// Instant notification

// ---------------------------------------------------------------------------------
// Record expired or removed license
// ---------------------------------------------------------------------------------

function ws_ls_stats_send_license_expire_to_yeken() {

    // Delete flag to notify YeKen of another license activation!
    delete_option('ws-ls-license-notify-2020');

    // Build payload to send to Yeken
    $data = array();
    $data['reason']	= 'license-expire';
    $data['url'] = get_site_url();
    $data['valid-license'] = ws_ls_has_a_valid_license();
    $data['site-hash'] = get_option(WS_LS_LICENSE_SITE_HASH);
    $data['license'] = ws_ls_license_get_old_or_new();
    wp_remote_post(WE_LS_STATS_URL, array('body' => $data));

}
add_action( WE_LS_HOOK_LICENSE_EXPIRED, 'ws_ls_stats_send_license_expire_to_yeken' );
