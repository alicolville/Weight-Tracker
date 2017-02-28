<?php

	defined('ABSPATH') or die("Jog on!");

	// Code that should run when plugin is activated
	function ws_ls_activate() {

		// Register user stats cron job
		if (! wp_next_scheduled ( WE_LS_CRON_NAME )) {
			wp_schedule_event(time(), 'hourly', WE_LS_CRON_NAME);
		}

		// Register weekly comms to yeken stats cron job
		if (! wp_next_scheduled ( WE_LS_CRON_NAME_YEKEN_COMMS )) {
			wp_schedule_event(time(), WE_LS_CRON_SCHEDULE_WEEKLY, WE_LS_CRON_NAME_YEKEN_COMMS);
		}
	}

	// Code that should run when plugin is deactivated
	function ws_ls_deactivate() {

		// Remove cron jobs
		wp_clear_scheduled_hook( WE_LS_CRON_NAME );
		wp_clear_scheduled_hook( WE_LS_CRON_NAME );
	}

	function ws_ls_create_mysql_tables()
	{
		global $wpdb;

   		$table_name = $wpdb->prefix . WE_LS_TABLENAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			weight_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			weight_user_id integer NOT NULL,
			weight_weight float NOT NULL,
			weight_stones float NOT NULL,
			weight_pounds float NOT NULL,
			weight_only_pounds float NOT NULL,
			weight_notes text null,
			bust_chest float NULL,
			waist float NULL,
			navel float NULL,
			hips float NULL,
			buttocks float NULL,
			left_thigh float NULL,
			right_thigh float NULL,
			left_bicep float NULL,
			right_bicep float NULL,
			left_calf float NULL,
			right_calf float NULL,
			left_forearm float NULL,
			right_forearm float NULL,
			shoulders float NULL,
		  UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		$table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;

		$sql = "CREATE TABLE $table_name (
			  id mediumint(9) NOT NULL AUTO_INCREMENT,
			  weight_user_id integer NOT NULL,
			  target_weight_weight float NOT NULL,
			  target_weight_stones float NOT NULL,
			  target_weight_pounds float NOT NULL,
			  target_weight_only_pounds float NOT NULL,
			  UNIQUE KEY id (id)
		) $charset_collate;";

		 dbDelta( $sql );

		 $table_name = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;

		 $sql = "CREATE TABLE $table_name (
				 user_id integer NOT NULL,
				 settings text not null,
                 height float DEFAULT 0 NULL,
				 UNIQUE KEY user_id (user_id)
		 ) $charset_collate;";

		dbDelta( $sql );

		$table_name = $wpdb->prefix . WE_LS_USER_STATS_TABLENAME;

		$sql = "CREATE TABLE $table_name (
				user_id integer NOT NULL,
				start_weight float DEFAULT 0 NULL,
				recent_weight float DEFAULT 0 NULL,
				weight_difference float DEFAULT 0 NULL,
				sum_of_weights float DEFAULT 0 NULL,
				last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				UNIQUE KEY user_id (user_id)
		) $charset_collate;";

	   dbDelta( $sql );

	}

	function ws_ls_upgrade() {

		if(update_option('ws-ls-version-number', WE_LS_CURRENT_VERSION)) {
			ws_ls_create_mysql_tables();
			ws_ls_activate();
		}
	}
	add_action('admin_init', 'ws_ls_upgrade');
