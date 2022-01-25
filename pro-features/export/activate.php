<?php

    defined('ABSPATH') or die("Jog on!");

    define( 'WE_LS_MYSQL_EXPORT', 'WS_LS_EXPORT' );
    define( 'WE_LS_MYSQL_EXPORT_REPORT', 'WS_LS_EXPORT_REPORT' );

    /**
     * Create the relevant database tables required to support exports
     */
    function ws_ls_export_create_mysql_tables() {

        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_EXPORT;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                options TEXT NOT NULL,
           		step INT DEFAULT 0,
           		number_of_records INT DEFAULT 0,
           		folder varchar( 200 ) DEFAULT NULL,
           		file varchar( 200 ) DEFAULT NULL,
           		created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
           		UNIQUE KEY id (id)
            ) $charset_collate;";

        dbDelta( $sql );

	    $table_name = $wpdb->prefix . WE_LS_MYSQL_EXPORT_REPORT;

	    $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                export_id INT NOT NULL,
                entry_id INT NOT NULL,
           		completed BIT DEFAULT 0,
           		saved_to_disk BIT DEFAULT 0,
           		data TEXT NOT NULL,
           		PRIMARY KEY (`export_id`,`entry_id`),
                UNIQUE KEY id (id)
            ) $charset_collate;";

	    dbDelta( $sql );

    }
    add_action('ws-ls-rebuild-database-tables', 'ws_ls_export_create_mysql_tables');

    /**
     *  Activate Export feature
     */
    function ws_ls_export_activate() {

        // Only run this when the plugin version has changed
        if( true === update_option('ws-ls-export-version-number', WE_LS_CURRENT_VERSION )) {

			ws_ls_export_create_mysql_tables();

        }
    }
    add_action( 'admin_init', 'ws_ls_export_activate' );

