<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_page() {

        // Determine page to display
        $page_to_display = ws_ls_querystring_value( 'mode', false, 'summary' );

        // Call relevant page function
        switch ( $page_to_display ) {
            case 'add-edit':
                ws_ls_meta_fields_add_update_page();
                break;
	        case 'groups':
		        ws_ls_meta_fields_page_group();
		        break;
            default:
                ws_ls_meta_fields_list_page();
                break;
        }

    }
