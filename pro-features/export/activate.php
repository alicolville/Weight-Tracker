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
           		completed BIT DEFAULT 0,
                created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                finished TIMESTAMP NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

        dbDelta( $sql );

    }
    add_action('ws-ls-rebuild-database-tables', 'ws_ls_meta_fields_create_mysql_tables');

    /**
     *  Activat Export feature
     */
    function ws_ls_export_activate() {

        // Only run this when the plugin version has changed
        if( true === update_option('ws-ls-export-version-number', WE_LS_DB_VERSION )) {

			ws_ls_export_create_mysql_tables();

        }
    }
    add_action( 'admin_init', 'ws_ls_export_activate' );

