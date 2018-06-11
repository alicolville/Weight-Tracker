<?php

    defined('ABSPATH') or die("Jog on!");

    define( 'WE_LS_MYSQL_META_FIELDS', 'WS_LS_META_FIELDS' );
    define( 'WE_LS_MYSQL_META_UNITS', 'WS_LS_META_UNITS' );
    define( 'WE_LS_MYSQL_META_ENTRY', 'WS_LS_META_ENTRY' );

    /**
     * Create the relevant database tables required to support meta fields
     */
    function ws_ls_meta_create_mysql_tables() {

        // Only run this when the plugin version has changed
        if( false === update_option('ws-ls-meta-version-number', WE_LS_DB_VERSION )) {
            return;
        }

        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_FIELDS;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                field_key varchar(10) NOT NULL,
                field_name varchar(40) NOT NULL,
                abv varchar(4) NOT NULL,
                display_on_chart BIT DEFAULT 0,
                system BIT DEFAULT 1,
                unit_id int NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

        dbDelta( $sql );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_UNITS;

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                field_key varchar(10) NOT NULL,
                field_name varchar(40) NOT NULL,
                abv varchar(4) NOT NULL,
                chartable BIT DEFAULT 0,
                unit_id int NOT NULL,
                UNIQUE KEY id (id)              
            ) $charset_collate;";

        dbDelta( $sql );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_ENTRY;

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                entry_id int NOT NULL,
                meta_field_id int NOT NULL,
                value varchar(800) NOT NULL,
                UNIQUE KEY id (id)              
            ) $charset_collate;";

        dbDelta( $sql );

    }
    add_action( 'admin_init', 'ws_ls_meta_create_mysql_tables' );

