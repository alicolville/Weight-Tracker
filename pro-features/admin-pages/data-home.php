<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_home() {

	// Determine page to display
	$page_to_display = (!empty($_GET['mode'])) ? $_GET['mode'] : 'summary';

	// Call relevant page function
	switch ($page_to_display) {
		case 'user':
			ws_ls_admin_page_data_user();
			break;
		case 'entry':
			ws_ls_admin_page_data_add_edit();
			break;
		default:
			ws_ls_admin_page_data_summary();
			break;
	}
}
