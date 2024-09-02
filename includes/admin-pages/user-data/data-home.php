<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_home() {

    ws_ls_permission_check_message();

	$page_to_display = ws_ls_querystring_value( 'mode', false, 'summary' );

	switch ($page_to_display) {
        case 'search-results':
            ws_ls_admin_page_search_results();
            break;
		case 'user':
			ws_ls_admin_page_data_user();
			break;
		case 'user-settings':
			ws_ls_admin_page_settings_user();
			break;
		case 'entry':
			ws_ls_admin_page_data_add_edit();
			break;
		case 'target':
			ws_ls_admin_page_data_edit_target();
			break;
        case 'photos':
            ws_ls_admin_page_photos();
            break;
        case 'all':
			ws_ls_admin_page_view_all();
			break;
        case 'groups':
            ws_ls_admin_page_group_view();
            break;
		case 'notes':
			ws_ls_admin_page_data_notes_for_user();
			break;
		default:
			ws_ls_admin_page_data_summary();
			break;
	}
}
