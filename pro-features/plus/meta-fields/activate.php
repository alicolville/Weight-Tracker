<?php

    defined('ABSPATH') or die("Jog on!");

    define( 'WE_LS_MYSQL_META_FIELDS', 'WS_LS_META_FIELDS' );
    define( 'WE_LS_MYSQL_META_ENTRY', 'WS_LS_META_ENTRY' );
	define( 'WE_LS_MYSQL_META_GROUPS', 'WS_LS_META_GROUPS' );

    /**
     * Create the relevant database tables required to support meta fields
     */
    function ws_ls_meta_fields_create_mysql_tables() {

        global $wpdb;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_FIELDS;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                field_key varchar(40) NOT NULL,
                field_name varchar(200) NOT NULL,
                abv varchar(5) NOT NULL,
                suffix varchar(10) NOT NULL,
                mandatory int DEFAULT 1,
                include_empty int DEFAULT 0,
                enabled int DEFAULT 1,
                hide_from_shortcodes int DEFAULT 0,
                plot_on_graph int DEFAULT 0,
                min_value float DEFAULT 0,
                max_value float DEFAULT 0,
                step float DEFAULT 0,
                show_all_labels int default 1,
                plot_colour varchar(10) NOT NULL,
                `system` BIT DEFAULT 0,
                field_type int NOT NULL,
                sort int DEFAULT 100,
                group_id int DEFAULT 0,
                migrate int DEFAULT 0,
                `options-values` text NULL,
                `options-labels` text NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

        dbDelta( $sql );

        $table_name = $wpdb->prefix . WE_LS_MYSQL_META_ENTRY;

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                entry_id int NOT NULL,
                meta_field_id int NOT NULL,
                value text NOT NULL,
                migrate int DEFAULT 0,
                UNIQUE KEY id (id)
            ) $charset_collate;";

        dbDelta( $sql );

	    $table_name = $wpdb->prefix . WE_LS_MYSQL_META_GROUPS;

	    $sql = "CREATE TABLE $table_name (
	                id mediumint(9) NOT NULL AUTO_INCREMENT,
	                slug varchar(60) NOT NULL,
	                name varchar(60) NOT NULL,
	                UNIQUE KEY id (id)
	            ) $charset_collate;";

	    dbDelta( $sql );

    }
    add_action('ws-ls-rebuild-database-tables', 'ws_ls_meta_fields_create_mysql_tables');

    /**
     *  Activate Meta Fields feature
     */
    function ws_ls_activate_meta_fields_activate() {

        // Only run this when the plugin version has changed
        if( true === update_option('ws-ls-meta-version-number', WE_LS_DB_VERSION )) {

            ws_ls_meta_fields_create_mysql_tables();

            ws_ls_cache_user_delete( 'meta-fields' );

            $existing_meta_fields = ws_ls_meta_fields( true, true );

	        // If no meta fields exist, then add some examples
	        if ( true === empty( $existing_meta_fields ) ) {
	            ws_ls_meta_fields_load_examples();
	        }

			// Do we have Photos to migrate from the old photo system to new?
	        if ( ws_ls_meta_fields_photos_do_we_need_to_migrate() ) {

                // If example Photo meta field doesn't exist, then add it!
                ws_ls_meta_fields_photos_create_example_field();

		        ws_ls_log_add('photo-migrate', 'Photos have been identified for migrating from old photo system to new!' );

		        ws_ls_meta_fields_photos_migrate_old();
	        }

	        // Do we need to migrate measurements?
			ws_ls_migrate_measurements_into_meta_fields();

        }
    }
    add_action( 'admin_init', 'ws_ls_activate_meta_fields_activate' );

    /**
     * Simple function to load some example
     */
    function ws_ls_meta_fields_load_examples() {

	    ws_ls_log_add('meta-field-setup', 'Adding some example custom fields' );

	    if ( false === ws_ls_meta_fields_key_exist( 'cups-of-water-drunk-today' ) &&
				false === ws_ls_meta_fields_key_exist( 'cups-of-water-drank-today' ) ) {
            // Number
            ws_ls_meta_fields_add([
                'field_name' => __('Cups of water drank today?', WE_LS_SLUG),
                'abv' => __('Water', WE_LS_SLUG),
                'field_type' => 0,
                'suffix' => __('cups', WE_LS_SLUG),
                'mandatory' => 2,
                'enabled' => 1,
                'sort' => 100,
                'hide_from_shortcodes' => 0
            ]);

        }

	    if ( false === ws_ls_meta_fields_key_exist( 'waist' ) ) {
		    // Number
		    ws_ls_meta_fields_add([
			    'field_name' => __('Waist', WE_LS_SLUG),
			    'abv' => __('Waist', WE_LS_SLUG),
			    'field_type' => 0,
			    'suffix' => __('cm', WE_LS_SLUG),
			    'mandatory' => 1,
			    'enabled' => 1,
			    'sort' => 100,
			    'hide_from_shortcodes' => 0
		    ]);

	    }

	    if ( false === ws_ls_meta_fields_key_exist( 'leg' ) ) {
		    // Number
		    ws_ls_meta_fields_add([
			    'field_name' => __('Leg', WE_LS_SLUG),
			    'abv' => __('Leg', WE_LS_SLUG),
			    'field_type' => 0,
			    'suffix' => __('cm', WE_LS_SLUG),
			    'mandatory' => 1,
			    'enabled' => 1,
			    'sort' => 100,
			    'hide_from_shortcodes' => 0
		    ]);

	    }

        if ( false === ws_ls_meta_fields_key_exist( 'did-you-stick-to-your-diet' ) ) {
            // Yes / No
            ws_ls_meta_fields_add([
                'field_name' => __('Did you stick to your diet?', WE_LS_SLUG),
                'abv' => __('Diet', WE_LS_SLUG),
                'field_type' => 2,
                'suffix' => '',
                'mandatory' => 1,
                'enabled' => 1,
                'sort' => 130,
                'hide_from_shortcodes' => 0
            ]);
        }

        ws_ls_cache_user_delete( 'meta-fields' );

    }
