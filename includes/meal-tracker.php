<?php

defined('ABSPATH') or die('Naw ya dinnie!');

/**
 * Is Meal Tracker enbaled?
 * @return bool
 */
function wlt_yk_mt_is_active() {
    return function_exists( 'yk_wt_upgrade' );
}

/**
 * Add Weight Tracker Record link to user header
 * @param $links
 * @return string
 */
function wlt_user_profile_add_header_link( $links ) {

    if ( false === wlt_yk_mt_is_active() ) {
        return $links;
    }

    $links .= sprintf( '<a href="%1$s" class="button-secondary"><i class="fa fa-line-chart"></i> <span>%2$s</span></a>',
    ws_ls_get_link_to_user_profile( get_current_user_id() ),
    __('Weight Tracker Record', WE_LS_SLUG )
    );

    return $links;
}
add_filter( 'yk_mt_user_profile_header_links', 'wlt_user_profile_add_header_link' );