<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_challenges_admin_page() {

    ws_ls_user_data_permission_check();

    ws_ls_data_table_enqueue_scripts();

    $challenge_id   =  ws_ls_querystring_value( 'challenge-id', true );
    $mode           =  ws_ls_querystring_value( 'mode' );

    if ( 'close' === $mode && false !== $challenge_id ) {
        ws_ls_challenges_enabled( $challenge_id, false );
    }

    if ( 'delete' === $mode && false !== $challenge_id ) {
        ws_ls_challenges_delete( $challenge_id);
    }

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
                        <h2 class="hndle"><span><?php echo __( 'Current Challenges', YK_MT_SLUG ); ?></span></h2>
                        <div class="inside">
                            <?php ws_ls_challenges_table(); ?>
                        </div>
                    </div>
                    <?php if ( 'view' === $mode ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __('Entries for this user', YK_MT_SLUG ); ?></span></h2>
                            <div class="inside">
                                <?php

                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}
