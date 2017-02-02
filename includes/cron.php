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
