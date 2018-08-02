<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_page() {

        // Determine page to display
        $page_to_display = (!empty($_GET['mode'])) ? $_GET['mode'] : 'summary';

        // Call relevant page function
        switch ($page_to_display) {
            case 'add-edit':
                ws_ls_meta_fields_add_update_page();
                break;
//     todo       case 'user':
//                ws_ls_admin_page_data_user();
//                break;
//            case 'user-settings':
//                ws_ls_admin_page_settings_user();
//                break;
//            case 'entry':
//                ws_ls_admin_page_data_add_edit();
//                break;
//            case 'target':
//                ws_ls_admin_page_data_edit_target();
//                break;
//            case 'photos':
//                ws_ls_admin_page_photos();
//                break;
//            case 'all':
//                ws_ls_admin_page_view_all();
//                break;
            default:
                ws_ls_meta_fields_list_page();
                break;
        }

    }