<?php

    defined('ABSPATH') or die("Jog on!");

	/**
	 * Are Birthday emails enabled?
	 *
	 * @return bool
	 */
	function ws_ls_birthdays_enabled() {

		if ( false === WS_LS_IS_PRO ) {
			return false;
		}

		return 'yes' === get_option('ws-ls-enable-birthdays', false ) ? true : false;
	}

    /**
     *  Activate Birthdays feature
     */
    function ws_ls_birthdays_activate() {

        // Only run this when the plugin version has changed
        if( true === update_option('ws-ls-birthday-db-number', WE_LS_DB_VERSION )) {

             // Insert the Birthday template
            if ( false === ws_ls_emailer_get('birthday') ) {

                ws_ls_emailer_add( 'birthday', 'Happy Birthday!', '<center>
                                                    <h1>Happy Birthday {name}!</h1>
                                                    <p>We thought we\'d drop you a quick message to wish you all the best and hope you have a great day!</p>
                                                    <p>All the best,</p>
                                                    <p><a href="{url}" target="_blank" rel="noopener">{name}</a></p>
                                                </center>'
                );
            }

        }
    }
    add_action( 'admin_init', 'ws_ls_birthdays_activate' );

/**
 * Send Birthday email
 *
 * @param null $user_id
 *
 * @return bool
 */
    function ws_ls_birthdays_send_email( $user_id = NULL ) {

        $user_id = $user_id ?: get_current_user_id();

        $email_template = ws_ls_emailer_get( 'birthday' );

        if ( false === empty( $email_template ) ) {

            $current_user = get_userdata( $user_id );

            $placeholders = [
                'name' => ( false === empty( $current_user->user_nicename ) ) ? $current_user->user_nicename : ''
            ];

            if (false === empty( $current_user->user_email )) {
                ws_ls_emailer_send( $current_user->user_email, $email_template['subject'], $email_template['email'], $placeholders );

                return true;
            }

	        ws_ls_log_add( 'birthdays', sprintf( 'Email not sent. No email address: %d', $user_id ) );
        }

        return false;
    }

	/**
	 * Identify who has a birthday today
	 */
    function ws_ls_birthdays_identify() {

		global $wpdb;

		$todays_date = date("Y-m-d");

		$sql = 'SELECT user_id, dob FROM ' . $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME . '
		            WHERE DATE_FORMAT( dob , \'%m-%d\') = DATE_FORMAT(\'' . $todays_date . '\', \'%m-%d\')';

        return $wpdb->get_results( $sql, ARRAY_A );
    }

	/**
	 * Identify who has a birthday and send email :)
	 */
    function ws_ls_birthdays_send_daily_emails() {

    	if ( false === ws_ls_birthdays_enabled() ) {
    		return;
	    }

	    $birthday_boys_or_girls = ws_ls_birthdays_identify();

	    $i = 0;

	    if ( false === empty( $birthday_boys_or_girls ) ) {

	    	foreach ( $birthday_boys_or_girls as $celebrate ) {

	    		if ( true === ws_ls_birthdays_send_email( $celebrate[ 'user_id' ] ) ) {
				    $i++;
			    }
		    }
	    }

	    ws_ls_log_add( 'birthdays', sprintf( 'Birthday emails sent today: %d', $i ) );
    }
	add_action('weight_loss_tracker_daily', 'ws_ls_birthdays_send_daily_emails');

