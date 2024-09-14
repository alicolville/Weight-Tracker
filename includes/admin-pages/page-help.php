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
                        <h3 class="hndle"><span><?php echo esc_html__( 'Custom modifications / web development', WE_LS_SLUG); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
	                        <?php wl_ls_setup_wizard_custom_notification_html(); ?>
                        </div>
                    </div>

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo esc_html__( 'Meal Tracker', WE_LS_SLUG); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
                            <?php wl_ls_setup_wizard_meal_tracker_html(); ?>
                        </div>
                    </div>

					<div class="postbox">
						<h3 class="hndle"><span><?php echo esc_html__( 'Useful Links', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo esc_html__( 'The following websites may be helpful for gaining a better understanding of the weight tracker:', WE_LS_SLUG ); ?></p>
                            <p>
                                <a href="https://weighttracker.yeken.uk" rel="noopener noreferrer"  class="button"  target="_blank"><?php echo esc_html__( 'Weight Tracker plugin site', WE_LS_SLUG ); ?></a>  
	                            <a href="https://docs.yeken.uk" rel="noopener noreferrer"  class="button"  target="_blank"><?php echo esc_html__( 'Documentation Site', WE_LS_SLUG ); ?></a>
	                            <a href="https://github.com/alicolville/Weight-Tracker/releases"  class="button"  rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'Release Notes', WE_LS_SLUG ); ?></a>
                            </p>
						</div>
				    </div>
				    </div>
					<?php if ( true === current_user_can( 'manage_options' ) && 'y' === ws_ls_querystring_value('yeken') ) : ?>

						<div class="postbox">
							<h3 class="hndle"><span><?php echo esc_html__( 'Custom Fields: Migrate measurements from old system to Custom Fields', WE_LS_SLUG); ?> </span></h3>
							<div style="padding: 0px 15px 0px 15px">
								<p><?php echo esc_html__( 'This will migrate enabled measurement fields and data across to Custom Fields.', WE_LS_SLUG ); ?></p>
								<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ws-ls-help&yeken=y&custom-fields-migrate=y') ); ?>" >Run</a></p>
								<?php
									if ( false === empty( $_GET[ 'custom-fields-migrate' ] ) ) {
										do_action( 'ws-ls-migrate-old-measurements' );
									}
								?>
							</div>
						</div>
					<?php endif; ?>

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo esc_html__( 'Admin Tools', WE_LS_SLUG); ?> </span></h3>
                        <div class="ws-ls-help-admin" style="padding: 0px 15px 0px 15px">
                            <p>
                                <?php

                                    if ( false === ws_ls_setup_wizard_show_notice() ) {

                                        printf('<a class="button" href="%1$s" >%2$s</a>',
                                            esc_url( admin_url( 'admin.php?page=ws-ls-help&wlt-show-setup-wizard-links=y') ),
                                            esc_html__('Show Setup Wizard link', WE_LS_SLUG)
                                        );
                                    }

                                    if ( true === ws_ls_awards_is_enabled() ) {

                                       if ( true === isset( $_GET['deleteallawards'] )) {

                                           ws_ls_awards_delete_all_previously_given();

                                           echo sprintf( '<span>%s!</span>', esc_html__('Done', WE_LS_SLUG ) ) ;
                                       }

                                       printf('<a class="button awards-confirm" href="%1$s" >%2$s</a>',
                                           esc_url( admin_url( 'admin.php?page=ws-ls-help&deleteallawards=y') ),
                                           esc_html__('Delete all issued awards', WE_LS_SLUG)
                                       );

                                    }

                                    if ( true === isset( $_GET['deletelog'] )) {

                                        ws_ls_log_delete_all();

                                        echo sprintf( '<span>%s!</span>', esc_html__('Done', WE_LS_SLUG ) ) ;
                                    }

                                    printf('<a class="button logs-confirm" href="%1$s" >%2$s</a>',
                                        esc_url( admin_url( 'admin.php?page=ws-ls-help&deletelog=y') ),
                                        esc_html__('Delete all log entries', WE_LS_SLUG)
                                    );

                                ?>
                            </p>
                        </div>
                    </div>
                        <div class="postbox">
                            <h3 class="hndle"><span><?php echo esc_html__('Weight Tracker Debug Log', WE_LS_SLUG); ?> </span></h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <p><?php echo esc_html__('Below is a list of the debug information logged by the Weight Tracker plugin over the last 31 days.', WE_LS_SLUG); ?></p>

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
<?php

    ws_ls_create_dialog_jquery_code(esc_html__('Are you sure?', WE_LS_SLUG),
        esc_html__('Are you sure you wish to remove all issued awards?', WE_LS_SLUG) . '<br /><br />',
        'awards-confirm');

    ws_ls_create_dialog_jquery_code(esc_html__('Are you sure?', WE_LS_SLUG),
        esc_html__('Are you sure you wish to clear all log entries?', WE_LS_SLUG) . '<br /><br />',
        'logs-confirm');

}
