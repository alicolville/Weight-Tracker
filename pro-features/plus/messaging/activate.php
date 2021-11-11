<?php

defined('ABSPATH') or die("Jog on!");

define( 'WE_LS_MYSQL_MESSAGES', 'WS_LS_MESSAGING' );

/**
 * Create the relevant database tables required to support messaging/admin notes
 */
function ws_ls_messaging_create_mysql_tables() {

	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	$table_name = $wpdb->prefix . WE_LS_MYSQL_MESSAGES;

	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                message BIT DEFAULT 0,
                note BIT DEFAULT 0,
                `read` BIT DEFAULT 0,
                `to` int DEFAULT 0 NOT NULL,
                `from` int DEFAULT 0 NOT NULL,
                visible_to_user int DEFAULT 0 NOT NULL,
                message_text text NOT NULL,
                created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY id (id)
            ) $charset_collate;";

	dbDelta( $sql );
}
add_action('ws-ls-rebuild-database-tables', 'ws_ls_messaging_create_mysql_tables');

/**
 *  Activate Meta Fields feature
 */
function ws_ls_messaging_activate() {

	// Only run this when the plugin version has changed
	if( true === update_option('ws-ls-messaging-version-number', WE_LS_DB_VERSION )) {
		ws_ls_messaging_create_mysql_tables();

		ws_ls_note_email_activate();
	}
}
add_action( 'admin_init', 'ws_ls_messaging_activate' );

/**
 *  Add email template for email notifications
 */
function ws_ls_note_email_activate() {

		// Insert the notification template
		if ( false === ws_ls_emailer_get('note-added') ) {

			$email = sprintf( '<p>%s,</p>', __( 'Hello' , WE_LS_SLUG) );
			$email .= __( '<p><strong>A new note has been sent to you from {name}:</strong></p>' , WE_LS_SLUG);
			$email .= __( '<p>{data}</p>' , WE_LS_SLUG) . PHP_EOL . PHP_EOL;

			ws_ls_emailer_add( 'note-added', 'Weight Tracker: New note', $email, __( 'Note added' , WE_LS_SLUG ) );
		}
}

