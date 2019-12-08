<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_challenges_admin_page() {

    ws_ls_user_data_permission_check();

    ?>
    <div class="wrap ws-ls-user-data ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        if ( true !== WS_LS_IS_PRO ) {
                            ws_ls_display_pro_upgrade_notice();
                        }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo __( 'Chart', YK_MT_SLUG ); ?></span></h2>
                        <div class="inside">

                        </div>
                    </div>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo __('Entries for this user', YK_MT_SLUG ); ?></span></h2>
                        <div class="inside">
                            <?php
                               ws_ls_table_challenge( [ 'challenge_id'   => 1 ] );
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
