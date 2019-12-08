<?php

defined('ABSPATH') or die("Jog on!");

define( 'WE_LS_MYSQL_CHALLENGES', 'WS_LS_CHALLENGES' );
define( 'WE_LS_MYSQL_CHALLENGES_DATA', 'WS_LS_CHALLENGES_DATA' );

/**
 * Create the relevant database tables required to support meta fields
 */
function ws_ls_challenges_create_mysql_tables() {

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = $wpdb->prefix . WE_LS_MYSQL_CHALLENGES;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                start_date datetime NULL,
                end_date datetime NULL,
                timestamp TIMESTAMP NULL,
                enabled BIT DEFAULT 0 NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

    dbDelta( $sql );

    $table_name = $wpdb->prefix . WE_LS_MYSQL_CHALLENGES_DATA;

    $sql = "CREATE TABLE $table_name (
                user_id int NOT NULL,
                challenge_id int NOT NULL,
                group_id int NULL,
                count_wt_entries int NULL,
                count_mt_entries int NULL,
                weight_start float NULL,
                weight_latest float NULL,
                weight_diff float NULL,
                date_start date DEFAULT NULL,
                date_latest date DEFAULT NULL,
                bmi_start float NULL,
                bmi_latest float NULL,
                bmi_diff float NULL,
                height float NULL,
                age int NULL,
                gender int NULL,
                last_processed datetime DEFAULT NULL,
                PRIMARY KEY ( user_id, challenge_id )              
            ) $charset_collate;";

    dbDelta( $sql );
}
add_action('ws-ls-rebuild-database-tables', 'ws_ls_challenges_create_mysql_tables');

/**
 *  Activate Challenges feature
 */
function ws_ls_challenges_activate() {

    // Only run this when the plugin version has changed
    if( true === update_option( 'ws-ls-challenges-db-number', WE_LS_DB_VERSION ) ) {
        ws_ls_challenges_create_mysql_tables();
    }
}
add_action( 'admin_init', 'ws_ls_challenges_activate' );