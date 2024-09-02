<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_awards_list_page() {

        ws_ls_data_table_enqueue_scripts();

    ?>
        <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-3">
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">
                    <?php

                        if ( false === ws_ls_awards_is_enabled() ) {
                            ws_ls_display_pro_upgrade_notice();
                        }

                        // Save email notification preferences
                        if ( false === empty( $_GET['email-notifications'] ) )     {
                            update_option( 'ws-ls-awards-email-notifications',  ( 'y' === $_GET['email-notifications'] ) ? 'y' : 'n' );
                        }

                        $emails_enabled = ws_ls_awards_email_notifications_enabled();

                        $url = admin_url('admin.php?page=ws-ls-awards' );

                        ?>
                        <div class="postbox">
                            <h3 class="hndle"><span><?php echo esc_html__('Awards', WE_LS_SLUG); ?></span></h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <div class="ws-ls-table">
                                    <div class="ws-ls-row">
                                        <div class="ws-ls-cell">
                                            <p><?php echo esc_html__('Issue awards to your user\'s for meeting certain goals.' , WE_LS_SLUG); ?>
                                                <a href="https://docs.yeken.uk/awards.html" target="_blank" rel="noopener"><?php echo esc_html__('Read more about Awards', WE_LS_SLUG); ?></a>
                                            </p>
                                            <?php if ( false === $emails_enabled ): ?>
                                                <p class="ws-ls-validation-error"><strong><?php echo esc_html__('Emails Disabled', WE_LS_SLUG); ?></strong>: <?php echo esc_html__('Emails will not be sent for email enabled awards. This allows you to test awards without emails being sent. Use the button to the right enable them', WE_LS_SLUG); ?>.</p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ws-ls-cell" style="text-align: right">
                                            <?php

                                                if ( true === $emails_enabled ) {
                                                    $qs_value = 'n';
                                                    $button_text = 'Disable Email Notifications';
                                                } else {
                                                    $qs_value = 'y';
                                                    $button_text = 'Enable Email Notifications';
                                                }

                                                $url = add_query_arg( 'email-notifications', $qs_value, $url);

                                                printf('<p>
                                                            <a class="button-secondary" href="%1$s" %3$s>
                                                                <i class="fa fa-envelope"></i>
                                                                %2$s
                                                            </a>
                                                        </p>',
                                                        esc_url( $url ),
                                                        $button_text,
                                                        ( false === $emails_enabled ) ? 'style="color:red"' : ''
                                                );

                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <table class="table ws-ls-awards-list-ajax ws-ls-loading-table" id="awards-list"
                                       data-paging="true"
                                       data-filtering="false"
                                       data-sorting="true"
                                       data-editing="true"
                                       data-cascade="true"
                                       data-toggle="true"
                                       data-use-parent-width="true">
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
        </div>
    <?php

    }

