<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_group_view() {

    ws_ls_user_data_permission_check();

    ?>
    <div class="wrap ws-ls-user-data ws-ls-admin-page">
    <h1><?php echo __('View Group', WE_LS_SLUG); ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                    if ( true !== WS_LS_IS_PRO ) {
                        ws_ls_display_pro_upgrade_notice();
                    }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span>TITLE</span></h2>
                        <div class="inside">

                            TODO

                        </div>
                    </div>
                </div>
            </div>

        </div>
        <br class="clear">
    </div>
    <?php

}
