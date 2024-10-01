<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_search_results() {

    ws_ls_permission_check_message();

    ?>
    <div class="wrap ws-ls-user-data ws-ls-admin-page">
    <h1><?php echo esc_html__( 'Search Results', WE_LS_SLUG ); ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <h2 class="hndle"><span><?php echo esc_html__( 'Search Results', WE_LS_SLUG ); ?></span></h2>

                        <div class="inside">
                         <?php
                            if ( true !== WS_LS_IS_PRO ) {
                                ws_ls_display_pro_upgrade_notice();
                            }

                            $search_term = ws_ls_querystring_value( 'search' );

                            if( true === WS_LS_IS_PRO && false === empty( $search_term ) ) {

                                $search_results = ws_ls_user_search( $search_term );

                                if( false === empty( $search_results ) ) {

                                    printf('<p>%1$d %2$s: <em>"%3$s"</em></p>',
                                                    count( $search_results ),
                                                    esc_html__( 'results were found for', WE_LS_SLUG ),
                                                    esc_html( $search_term )
                                    );

                                    ?>

                                    <table class="widefat">
                                        <tr>
                                            <th class="row-title"><?php echo esc_html__( 'Username', WE_LS_SLUG ) ?></th>
                                            <th><?php echo esc_html__( 'Email', WE_LS_SLUG ) ?></th>
                                            <th><?php echo esc_html__( 'Start Weight', WE_LS_SLUG ) ?></th>
                                            <th><?php echo esc_html__( 'Latest Weight', WE_LS_SLUG ) ?></th>
	                                        <th><?php echo esc_html__( 'Target Weight', WE_LS_SLUG ) ?></th>
											<th><?php echo esc_html__( 'Diff. from Start Weight', WE_LS_SLUG ) ?></th>
                                        </tr>
                                        <?php
                                            foreach ( $search_results as $user ) {
                                                ws_ls_search_row( $user );
                                            }
                                        ?>
                                        </table>
                                        <?php

                                } else {
                                    echo sprintf('<p>%s: <em>"%s"</em></p>',
                                        esc_html__( 'No users were found for the given search criteria:', WE_LS_SLUG ),
                                        esc_html( $search_term )
                                    );
                                }
                            } else {
                                echo esc_html__( 'No search terms were specified', WE_LS_SLUG );
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
                            <td>%8$s</td>
                        </tr>',
            esc_attr( $class ),
            ws_ls_get_link_to_user_profile( $user->data->ID ),
            esc_html( $user->data->display_name ),
            esc_attr( $user->data->user_email ),
            ws_ls_shortcode_start_weight( $user->data->ID ),
            ws_ls_shortcode_recent_weight( $user->data->ID ),
	        ws_ls_target_get( $user->data->ID, 'display' ),
			ws_ls_shortcode_difference_in_weight_from_oldest( $user->data->ID )
    );
 }
