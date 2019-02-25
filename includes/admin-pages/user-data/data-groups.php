<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_group_view() {

    ws_ls_user_data_permission_check();

    ws_ls_data_table_enqueue_scripts();

    $group_id = ws_ls_querystring_value( 'id', true, 0 );

    ?>
    <div class="wrap ws-ls-admin-page">
        <div id="icon-options-general" class="icon32"></div>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-3">

                <!-- main content -->
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">
                        <?php
                            if ( true !== WS_LS_IS_PRO ) {
                                ws_ls_display_pro_upgrade_notice();
                            }

                            $group = ws_ls_groups_get( $group_id );
                        ?>
                        <div class="postbox">
                            <h3 class="hndle">
                                    <span><?php echo __('View Group', WE_LS_SLUG); ?>
                                    <?php printf('%s', false === empty( $group['name'] ) ? ': ' . esc_html( $group['name']) : '' ); ?></span>
                                    <?php printf(' ( %d %s )', ws_ls_groups_count( $group_id ), __('user(s)', WE_LS_SLUG) ); ?>
                            </h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <?php if ( false === empty( $group ) ) : ?>

                                    <table class="ws-ls-settings-groups-users-list-ajax table ws-ls-loading-table" id="groups-users-list"
                                           data-group-id="<?php echo $group_id; ?>"
                                           data-paging="true"
                                           data-filtering="false"
                                           data-sorting="true"
                                           data-editing-allow-add="false"
                                           data-editing-allow-edit="false"
                                           data-cascade="true"
                                           data-toggle="true"
                                           data-use-parent-width="true">
                                    </table>

                                    <p>
                                        <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-settings&mode=groups' ); ?>">
                                            <i class="fa fa-arrow-left"></i>
                                            <?php echo __('All Groups', WE_LS_SLUG); ?>
                                        </a>
                                    </p>

                                <?php else: ?>

                                    <p><?php echo __('The group could not be found', WE_LS_SLUG); ?>.</p>

                                <?php endif; ?>
                                <br clear="both"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

}
