<?php

defined('ABSPATH') or die("Jog on!");

/**
 * display relveant awards page
 */
function ws_ls_awards_page() {

    $page_to_display = ws_ls_querystring_value( 'mode', false, 'summary' );

    switch ( $page_to_display ) {
        case 'add-edit':
            ws_ls_awards_add_update_page();
            break;
        default:
            ws_ls_awards_list_page();
            break;
    }

}