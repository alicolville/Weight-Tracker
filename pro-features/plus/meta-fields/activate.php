<?php

    defined('ABSPATH') or die("Jog on!");

    define( 'WE_LS_MYSQL_META_FIELDS', 'WS_LS_META_FIELDS' );
    define( 'WE_LS_MYSQL_META_UNITS', 'WS_LS_META_UNITS' );
    define( 'WE_LS_MYSQL_META_ENTRY', 'WS_LS_META_ENTRY' );


    /**
     * Create the relevant database tables required to support meta fields
     */
    function ws_ls_activate_meta_create_mysql_tables() {

    	//TODO
//		$r = ws_ls_meta_add_to_entry([
//			'entry_id' => 6,
//			'key' => 'leg-left',
//			'value' => '45'
//		]);

		var_dump(ws_ls_meta(6) );

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
                field_key varchar(40) NOT NULL,
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
                field_key varchar(40) NOT NULL,
                field_name varchar(40) NOT NULL,
                abv varchar(4) NOT NULL,
                chartable BIT DEFAULT 0,
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

		// If no units exist (i.e. WE_LS_MYSQL_META_UNITS is empty) then add some defaults the once e.g. CM, inches, cups, feet, etc
		if ( true === empty( ws_ls_meta_units() ) ) {
			ws_ls_activate_meta_units_add_defaults();
		}

    }
    add_action( 'admin_init', 'ws_ls_activate_meta_create_mysql_tables' );

	/**
	 * 	Insert some default Units into WE_LS_MYSQL_META_UNITS
	 */
    function ws_ls_activate_meta_units_add_defaults() {

    	// TODO: Add some more defaults?

		ws_ls_meta_unit_add([
			'abv' => 'CM',
			'chartable' => 1,
			'field_key' => 'cm',
			'field_name' => 'Centimetres'
		]);

		ws_ls_meta_unit_add([
			'abv' => 'Cups',
			'chartable' => 1,
			'field_key' => 'cups',
			'field_name' => 'Cups'
		]);

		ws_ls_meta_unit_add([
			'abv' => 'Feet',
			'chartable' => 1,
			'field_key' => 'feet',
			'field_name' => 'Feet'
		]);

		ws_ls_meta_unit_add([
			'abv' => 'I',
			'chartable' => 1,
			'field_key' => 'inches',
			'field_name' => 'Inches'
		]);

	}

