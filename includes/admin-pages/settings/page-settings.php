<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_settings_page() {

	switch ( ws_ls_querystring_value( 'mode' ) ) {
		case 'groups':
			ws_ls_settings_page_group();
			break;
		case 'email-manager':
			ws_ls_settings_email_manager();
			break;
		default:
			ws_ls_settings_page_generic();
			break;
	}

}
