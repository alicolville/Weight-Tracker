<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_search_results() {

    ws_ls_user_data_permission_check();

    ?>
    <div class="wrap ws-ls-user-data ws-ls-admin-page">
    <h1><?php echo __( 'Search Results', WE_LS_SLUG ); ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <h2 class="hndle"><span><?php echo __( 'Search Results', WE_LS_SLUG ); ?></span></h2>

                        <div class="inside">
                         <?php
                            if ( true !== WS_LS_IS_PRO ) {
                                ws_ls_display_pro_upgrade_notice();
                            }

                            $search_term = ws_ls_querystring_value( 'search', false );

                            if( true === WS_LS_IS_PRO && false === empty( $search_term ) ) {

                                $user_query     = new WP_User_Query( [ 'search' => sprintf( '*%s*', $search_term ) ] );
                                $count          = $user_query->get_total();

                                if( 0 !== $count ) {

                                    printf('<p>%1$d %2$s: <em>"%3$s"</em></p>',
                                                    $count,
                                                    __( 'results were found for', WE_LS_SLUG ),
                                                    esc_html( $search_term )
                                    );

                                    ?>

                                    <table class="widefat">
                                        <tr>
                                            <th class="row-title"><?php echo __( 'Username', WE_LS_SLUG ) ?></th>
                                            <th><?php echo __( 'Email', WE_LS_SLUG ) ?></th>
                                            <th><?php echo __( 'Start Weight', WE_LS_SLUG ) ?></th>
                                            <th><?php echo __( 'Latest Weight', WE_LS_SLUG ) ?></th>
	                                        <th><?php echo __( 'Target Weight', WE_LS_SLUG ) ?></th>
                                        </tr>
                                        <?php
                                            foreach ( $user_query->get_results() as $user ) {
                                                ws_ls_search_row( $user );
                                            }
                                        ?>
                                        </table>
                                        <?php

                                } else {
                                    echo sprintf('<p>%s: <em>"%s"</em></p>',
                                        __( 'No users were found for the given search criteria:', WE_LS_SLUG ),
                                        esc_html( $search_term )
                                    );
                                }
                            } else {
                                echo __( 'No search terms were specified', WE_LS_SLUG );
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}

/**
 * Render out a row in the user search results
* @param $user
* @param string $class
 */
function ws_ls_search_row( $user, $class = '') {

    if( true === empty( $user ) ) {
        return;
    }

    printf('<tr valign="top" class="%1$s">
                            <td><a href="%2$s">%3$s</a></td>
                            <td><a href="mailto:%4$s">%4$s</a></td>
                            <td><a href="%2$s">%5$s</a></td>
                            <td>%6$s</td>
                            <td>%7$s</td>
                        </tr>',
            esc_attr( $class ),
            ws_ls_get_link_to_user_profile( $user->data->ID ),
            esc_html( $user->data->display_name ),
            esc_attr( $user->data->user_email ),
            ws_ls_weight_start( $user->data->ID ),
            ws_ls_weight_recent( $user->data->ID ),
	        ws_ls_target_get( $user->data->ID, 'display' )
    );
 }
