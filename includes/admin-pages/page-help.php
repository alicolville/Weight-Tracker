<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_help_page() {

	ws_ls_data_table_enqueue_scripts();

    ?>
		<div class="wrap ws-ls-admin-page">

	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Custom modifications / web development', WE_LS_SLUG); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
	                        <?php wl_ls_setup_wizard_custom_notification_html(); ?>
                        </div>
                    </div>

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Meal Tracker', WE_LS_SLUG); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
                            <?php wl_ls_setup_wizard_meal_tracker_html(); ?>
                        </div>
                    </div>

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Documentation', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'You can find detailed documentation for this plugin at our site:', WE_LS_SLUG ); ?></p>
                            <p><a href="https://weight.yeken.uk" rel="noopener noreferrer" target="_blank">https://weight.yeken.uk</a></p>
						</div>
				    </div>
				    </div>
					<?php if ( true === current_user_can( 'manage_options' ) && 'y' === ws_ls_querystring_value('yeken') ) : ?>

                        <div class="postbox">
                            <h3 class="hndle"><span><?php echo __( 'Custom Fields: Migrate photos from old system to Custom Fields', WE_LS_SLUG); ?> </span></h3>
                            <div style="padding: 0px 15px 0px 15px">
                                <p><?php echo __( 'This will migrate photos from the old system to the new custom fields. It will create a custom field with the key "photo". Note! It will remove any existing migrated photos before re-adding them.', WE_LS_SLUG ); ?></p>
                                    <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ws-ls-help&yeken=y&photomigrate=y') ); ?>" >Run</a></p>

								<?php

								ws_ls_log_add('help-page', 'YeKen Tool options shown!' );

								if ( 'y' === ws_ls_querystring_value('photomigrate') ) {

								    ws_ls_log_add('photo-migrate', 'Started manually via help page.' );

								    // If example Photo meta field doesn't exist, then add it!
									ws_ls_meta_fields_photos_create_example_field();

									// Do we have Photos to migrate from the old photo system to new?
									if ( ws_ls_meta_fields_photos_do_we_need_to_migrate( true ) ) {

										ws_ls_meta_delete_migrated();

										ws_ls_log_add('photo-migrate', 'Photos have been identified for migrating from old photo system to new!' );

										ws_ls_meta_fields_photos_migrate_old( true );

										echo '<p><strong>' . __( 'Done. View Log below for further information.', WE_LS_SLUG) . '</strong></p>';
									}
								}

								?>
                            </div>
                        </div>
					<?php endif; ?>

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Admin Tools', WE_LS_SLUG); ?> </span></h3>
                        <div class="ws-ls-help-admin" style="padding: 0px 15px 0px 15px">
                            <p>
                                <?php

                                    if ( false === ws_ls_setup_wizard_show_notice() ) {

                                        printf('<a class="button" href="%1$s" >%2$s</a>',
                                            esc_url( admin_url( 'admin.php?page=ws-ls-help&wlt-show-setup-wizard-links=y') ),
                                            __('Show Setup Wizard link', WE_LS_SLUG)
                                        );
                                    }

                                    if ( true === ws_ls_awards_is_enabled() ) {

                                       if ( true === isset( $_GET['deleteallawards'] )) {

                                           ws_ls_awards_delete_all_previously_given();

                                           echo sprintf( '<span>%s!</span>', __('Done', WE_LS_SLUG ) ) ;
                                       }

                                       printf('<a class="button awards-confirm" href="%1$s" >%2$s</a>',
                                           esc_url( admin_url( 'admin.php?page=ws-ls-help&deleteallawards=y') ),
                                           __('Delete all issued awards', WE_LS_SLUG)
                                       );

                                    }

                                    if ( true === isset( $_GET['deletelog'] )) {

                                        ws_ls_log_delete_all();

                                        echo sprintf( '<span>%s!</span>', __('Done', WE_LS_SLUG ) ) ;
                                    }

                                    printf('<a class="button logs-confirm" href="%1$s" >%2$s</a>',
                                        esc_url( admin_url( 'admin.php?page=ws-ls-help&deletelog=y') ),
                                        __('Delete all log entries', WE_LS_SLUG)
                                    );

                                ?>
                            </p>
                        </div>
                    </div>
                        <div class="postbox">
                            <h3 class="hndle"><span><?php echo __('Weight Tracker Debug Log', WE_LS_SLUG); ?> </span></h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <p><?php echo __('Below is a list of the debug information logged by the Weight Tracker plugin over the last 31 days.', WE_LS_SLUG); ?></p>

                                <table class="ws-ls-errors-list-ajax table" id="errors-list"
                                       data-paging="true"
                                       data-filtering="true"
                                       data-sorting="true"
                                       data-editing="false"
                                       data-cascade="true"
                                       data-toggle="true"
                                       data-use-parent-width="true">
                                </table>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
<?php

    ws_ls_create_dialog_jquery_code(__('Are you sure?', WE_LS_SLUG),
        __('Are you sure you wish to remove all issued awards?', WE_LS_SLUG) . '<br /><br />',
        'awards-confirm');

    ws_ls_create_dialog_jquery_code(__('Are you sure?', WE_LS_SLUG),
        __('Are you sure you wish to clear all log entries?', WE_LS_SLUG) . '<br /><br />',
        'logs-confirm');

}