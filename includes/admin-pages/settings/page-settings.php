<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_settings_page() {

	// Determine page to display
	$page_to_display = ( false === empty($_GET['mode']) ) ? $_GET['mode'] : '';

	// Call relevant page function
	switch ( $page_to_display ) {
		case 'groups':
			ws_ls_settings_page_group();
			break;
		default:
			ws_ls_settings_page_generic();
			break;
	}

}