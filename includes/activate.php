<?php

	defined('ABSPATH') or die("Jog on!");


	function ws_ls_activate()
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

	}

	function ws_ls_upgrade() {

		$option_key = 'ws-ls-version-number';

		$existing_version = get_option($option_key);

		if(empty($existing_version) || $existing_version != WE_LS_CURRENT_VERSION) {
			ws_ls_activate();
			update_option($option_key, WE_LS_CURRENT_VERSION);
		}
	}
	add_action('admin_init', 'ws_ls_upgrade');
