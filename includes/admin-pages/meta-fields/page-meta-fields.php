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
            default:
                ws_ls_meta_fields_list_page();
                break;
        }

    }