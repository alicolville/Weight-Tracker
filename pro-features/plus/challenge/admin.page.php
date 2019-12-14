<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_challenges_admin_page() {

    ws_ls_user_data_permission_check();

    ws_ls_data_table_enqueue_scripts();

    $challenge_id   = ws_ls_querystring_value( 'challenge-id', true );
    $mode           = ws_ls_querystring_value( 'mode', false, 'list' );
    $error          = NULL;

    if ( 'close' === $mode && false !== $challenge_id ) {
        ws_ls_challenges_enabled( $challenge_id, false );
    }

    if ( 'delete' === $mode && false !== $challenge_id ) {
        ws_ls_challenges_delete( $challenge_id);
    }

    if ( 'true' === ws_ls_ajax_post_value( 'add-challenge', false, false ) ) {

        // Do we have a name? If so, insert Challenge otherwise show error
        $name = ws_ls_ajax_post_value( 'ws-ls-name' );

        if( true === empty( $name ) ) {
            $error = __( 'Please ensure you enter a name for the challenge.', WE_LS_SLUG );
        }

        if( true === empty( $error ) ) {

            $start_date = ws_ls_ajax_post_value( 'ws-ls-start-date', false, NULL );
            $end_date   = ws_ls_ajax_post_value( 'ws-ls-end-date', false, NULL );

            if ( true === ws_ls_challenges_add( $name, ws_ls_convert_date_to_iso( $start_date ), ws_ls_convert_date_to_iso( $end_date ) ) ) {

                ws_ls_challenges_process();

                $mode = 'processing';
            } else {
                $error = __( 'There was an error saving the challenge to the database.', WE_LS_SLUG );
            }
        }
    }

    ?>
    <div class="wrap ws-ls-challenges ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        if ( true !== WS_LS_IS_PRO ) {
                            ws_ls_display_pro_upgrade_notice();
                        }
                    ?>
                    <?php if ( true === in_array( $mode, [ 'delete', 'list' ] ) ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __( 'Current Challenges', WS_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p>
                                    <a href="<?php echo ws_ls_challenge_link( 1, 'add' ); ?>" class="btn btn-default button-primary">
                                        <i class="fa fa-plus"></i>
                                        <?php echo __( 'Add a challenge', WE_LS_SLUG ); ?>
                                    </a>
                                </p>
                                <?php ws_ls_challenges_table(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( 'add' === $mode ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __('Add a new challenge', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p>
                                    <a href="<?php echo ws_ls_challenge_link( 1, 'list' ); ?>" class="btn btn-default button">
                                        <i class="fa fa-arrow-left"></i>
                                        <?php echo __( 'Cancel', WE_LS_SLUG ); ?>
                                    </a>
                                </p>
                                <?php
                                    if ( false === empty( $error ) ) {
                                        printf( '<p class="ws-ls-validation-error">%s</p>', $error );
                                    }
                                ?>
                                <form method="post" action="<?php echo ws_ls_challenge_link( 1, 'add' ); ?>" class="we-ls-weight-form ws_ls_display_form">
                                    <label for="ws-ls-name"><?php echo __( 'Name', WE_LS_SLUG ); ?></label>
                                    <input type="text" name="ws-ls-name" id="ws-ls-name" tabindex="1" value="<?php echo ws_ls_ajax_post_value( 'ws-ls-name', false,'' ); ?>" />
                                    <label for="ws-ls-start-date"><?php echo __( 'Start Date', WE_LS_SLUG ); ?></label>
                                    <input type="text" name="ws-ls-start-date" id="ws-ls-start-date" tabindex="2" value="<?php echo ws_ls_ajax_post_value( 'ws-ls-start-date', false,'' ); ?>" class="we-ls-datepicker" />
                                    <label for="ws-ls-end-date"><?php echo __( 'End Date', WE_LS_SLUG ); ?></label>
                                    <input type="text" name="ws-ls-end-date" id="ws-ls-end-date" tabindex="3" value="<?php echo ws_ls_ajax_post_value( 'ws-ls-end-date', false,'' ); ?>" class="we-ls-datepicker" />
                                    <br />
                                    <input type="hidden" name="add-challenge" value="true" />
                                    <input type="submit" class="button" value="Add" />
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( 'view' === $mode ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __('Entries for this user', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <?php

                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( 'processing' === $mode ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __('Processing...', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p><?php echo __('Processing existing entries for this challenge', WE_LS_SLUG ); ?>...</p>
                                <?php
                                    $entries_processed = ws_ls_challenges_process( NULL, true, 150 );

                                    if ( false !== $entries_processed ) {
                                        printf( '<p>- %d %s</p>', $entries_processed, __('entries processed', WE_LS_SLUG ) );

                                        printf( '<script>window.location.replace( "%s" )</script>', ws_ls_challenge_link( 1, 'processing' ) );
                                    } else {
                                        printf( '<script>window.location.replace( "%s" )</script>', ws_ls_challenge_link( 1, 'list' ) );
                                    }
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
