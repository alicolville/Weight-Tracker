<?php

    defined('ABSPATH') or die("Jog on!");


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
            }
        }
    }


    function t() {
        ws_ls_birthdays_send_email(1);
    }
    add_action('weight_loss_tracker_daily', 't');