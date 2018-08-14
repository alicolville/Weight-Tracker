<?php

	defined('ABSPATH') or die("Jog on!");

	// Code that should run when plugin is activated
	function ws_ls_activate() {

        // Register user stats / license check cron job
        if (!wp_next_scheduled(WE_LS_CRON_NAME)) {
            wp_schedule_event(time(), 'hourly', WE_LS_CRON_NAME);
        }

        // Register weekly comms to yeken stats cron job
        if (!wp_next_scheduled(WE_LS_CRON_NAME_YEKEN_COMMS)) {
            wp_schedule_event(time(), WE_LS_CRON_SCHEDULE_WEEKLY, WE_LS_CRON_NAME_YEKEN_COMMS);
        }
    }

	// Code that should run when plugin is deactivated
	function ws_ls_deactivate() {

		// Remove cron jobs
		wp_clear_scheduled_hook( WE_LS_CRON_NAME );
		wp_clear_scheduled_hook( WE_LS_CRON_NAME_YEKEN_COMMS );
	}

	function ws_ls_create_mysql_tables() {

		global $wpdb;

   		$table_name = $wpdb->prefix . WE_LS_TABLENAME;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			weight_date datetime NOT NULL,
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
			neck float NULL,
			photo_id int NULL,
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
			     activity_level float DEFAULT 0 NULL,
				 settings text not null,
                 height float DEFAULT 0 NULL,
                 gender float DEFAULT 0 NULL,
                 aim float DEFAULT 0 NULL,
                 dob datetime NULL,
                 body_type float DEFAULT 0 NULL,
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
				no_entries integer DEFAULT 0 NULL,
				target_added integer DEFAULT 0 NULL,
				last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				UNIQUE KEY user_id (user_id)
		) $charset_collate;";

	    dbDelta( $sql );

		$table_name = $wpdb->prefix . WE_LS_LOG_TABLENAME;

		$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				module varchar(20) NOT NULL,
				message text NOT NULL,
				UNIQUE KEY id (id)
		) $charset_collate;";

		dbDelta( $sql );

	}
    add_action('ws-ls-rebuild-database-tables', 'ws_ls_create_mysql_tables');

	function ws_ls_upgrade() {

		if(update_option('ws-ls-version-number', WE_LS_DB_VERSION)) {
			ws_ls_create_mysql_tables();
			ws_ls_activate();
            ws_ls_stats_clear_last_updated_date(); // This will force all stat entries to be recreated.

			// Delete all cache for plugin
			ws_ls_delete_all_cache();

            // Check the license is still valid
            ws_ls_licences_cron();
 		}
	}
	add_action('admin_init', 'ws_ls_upgrade');
