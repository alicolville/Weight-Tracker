<?php

	defined('ABSPATH') or die("Jog on!");

	function ws_ls_schedules( $schedules ) {

		$schedules[WE_LS_CRON_SCHEDULE_WEEKLY] = array(
			'interval' => 604800,					// Every Week
			'display' => 'Weight Loss Tracker Weekly'
		);

		return $schedules;
	}
	add_action('cron_schedules', 'ws_ls_schedules');

	// ------------------------------------------------------------------------------------------------------------
	// Cron job to expire old licenses
	// ------------------------------------------------------------------------------------------------------------

	// Fetch the existing license from WP Options and run it through validation again.
	function ws_ls_licences_cron() {

		$existing_license = ws_ls_license();

		ws_ls_license_apply($existing_license);
	}
	add_action(WE_LS_CRON_NAME, 'ws_ls_licences_cron');
