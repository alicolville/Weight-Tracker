<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_summary() {

    ws_ls_permission_check_message();

    // DELETE ALL DATA!! AHH!!
    if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {

	    ws_ls_delete_existing_data();

        // Let others know we cleared all user data
        do_action( 'wlt-hook-data-all-deleted' );
    }

?>
<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<h1><?php echo __('Summary', WE_LS_SLUG); ?></h1>
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
						<?php

							// If changing gain / loss set options
							if( false === empty($_GET['show-gain'])) {

								$value = ('y' === $_GET['show-gain']) ? true : false;
								update_option('ws-ls-show-gains', $value);
							}

							// Are we wanting to see who has lost the most? Or gained?
							$show_gain = get_option('ws-ls-show-gains') ? true : false;
						?>
						<h2 class="hndle"><span><?php echo (false === $show_gain) ? __('League table for those that have lost the most', WE_LS_SLUG) : __('League Table for those that have gained the most', WE_LS_SLUG) ; ?></span></h2>
						<div class="inside">
							<?php
								$ignore_cache = false;

                                // Run stats if plugin version number has changed!
                                if( true === WS_LS_IS_PRO && update_option('ws-ls-version-number-stats', WE_LS_CURRENT_VERSION) || (false === empty($_GET['regenerate-stats']) && 'y' == $_GET['regenerate-stats'])) {
                                    ws_ls_db_stats_clear_last_updated_date();
                                    ws_ls_stats_run_cron();
                                    ws_ls_tidy_cache_on_delete();
									$ignore_cache = true;
                                }

                                echo ws_ls_shortcode_stats_league_total(['ignore_cache' => $ignore_cache, 'order' => (false === $show_gain) ? 'asc' : 'desc']);

								if( true === WS_LS_IS_PRO ) {
									?>
									<p>
										<small><?php echo __( 'Please note: For performance reasons, this table only will update every hour. Click the following button to manually update.', WE_LS_SLUG ); ?></small>
									</p>
									<a class="btn button-secondary"
									   href="<?php echo admin_url( 'admin.php?page=ws-ls-data-home&regenerate-stats=y' ); ?>"><i
											class="fa fa-refresh"></i> <?php echo __( 'Regenerate these stats', WE_LS_SLUG ); ?>
									</a>
									<?php

									echo sprintf(
										'<a class="btn button-secondary" href="%s"><i class="fa fa-arrows-v"></i> %s</a>',
										admin_url( 'admin.php?page=ws-ls-data-home&show-gain=' ) . ( ( false === $show_gain ) ? 'y' : 'n' ),
										( false === $show_gain ) ? __( 'Show who has gained the most', WE_LS_SLUG ) : __( 'Show who has lost the most', WE_LS_SLUG )
									);
								}
						 	?>
						</div>
					</div>
                    <?php if ( true === ws_ls_groups_do_we_have_any() ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __('Weight change by group', WE_LS_SLUG); ?></span></h2>
                            <div class="inside">
                                <table class="ws-ls-settings-groups-list-ajax table ws-ls-loading-table" id="groups-list-stats"
                                       data-paging="true"
                                       data-filtering="false"
                                       data-sorting="true"
                                       data-editing-allow-add="false"
                                       data-editing-allow-delete="false"
                                       data-editing-allow-edit="false"
                                       data-cascade="true"
                                       data-paging-size="10"
                                       data-toggle="true"
                                       data-use-parent-width="true">
                                </table>
                                <a class="btn button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-data-home&regenerate-stats=y' ); ?>"><i class="fa fa-refresh"></i> <?php echo __('Regenerate these stats', WE_LS_SLUG); ?></a>
                                <a class="btn button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-settings&mode=groups' ); ?>"><i class="fa fa-eye"></i> <?php echo __('View / Edit', WE_LS_SLUG); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
					<div class="postbox">
						<?php

							// Show 100 most recent entries? Or show 500?
							if( false === empty( $_GET['show-all'] ) ) {
								$value = ( 'y' === $_GET['show-all'] ) ? true : false;
								update_option('ws-ls-show-all', $value );
							}

							// Show meta data?
							if( false === empty( $_GET['show-meta'] ) ) {
								$value = ( 'y' === $_GET['show-meta'] ) ? true : false;
								update_option('ws-ls-show-meta', $value );
							}

							$show_all   = get_option( 'ws-ls-show-all' ) ? true : false;
							$show_meta  = get_option( 'ws-ls-show-meta' ) ? true : false;
						?>
						<h2 class="hndle"><span><?php echo ($show_all) ? __('Last 500 entries', WE_LS_SLUG) : __('Last 100 entries', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php

								echo ws_ls_data_table_render( [ 'limit' => ( $show_all ) ? 500 : 100, 'smaller-width' => true, 'enable-meta-fields' => $show_meta, 'page-size' => 20 ] );

								echo sprintf(
												'<a class="btn button-secondary" href="%s"><i class="fa fa-book"></i> %s</a>',
												admin_url( 'admin.php?page=ws-ls-data-home&show-all=' ) . ( ( false === $show_all ) ? 'y' : 'n'),
												( false === $show_all ) ? __( 'Show 500 recent entries', WE_LS_SLUG ) : __( 'Show 100 recent entries', WE_LS_SLUG )
											);

								if ( ws_ls_meta_fields_number_of_enabled() > 0 ) {
									echo sprintf(
										'&nbsp;<a class="btn button-secondary" href="%s"><i class="fas fa-book-reader"></i> %s</a>',
										admin_url( 'admin.php?page=ws-ls-data-home&show-meta=' ) . ( ( false === $show_meta ) ? 'y' : 'n'),
										( false === $show_meta ) ? __( 'Include Custom Fields (Slower)', WE_LS_SLUG ) : __( 'Hide Custom Fields (Quicker)', WE_LS_SLUG )
									);
								}

						 	?>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h2 class="hndle"><?php echo __('User Search', WE_LS_SLUG); ?></h2>
						<div class="inside">
							<?php ws_ls_box_user_search_form(); ?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><?php echo __('Quick Stats', WE_LS_SLUG); ?></h2>
						<div class="inside">
							<?php

								$entry_counts = ws_ls_db_entries_count();

								if(false === empty($entry_counts)) {

									echo sprintf('<table class="ws-ls-sidebar-stats">
                                                        <tr>
                                                            <th>%1$s</th>
                                                            <td>%2$s</td>
                                                        </tr>
                                                        <tr>
                                                            <th>%3$s</th>
                                                            <td class="%9$s">%4$s</td>
                                                        </tr>
                                                        <tr>
                                                            <th>%5$s</th>
                                                            <td class="%9$s">%6$s</td>
                                                        </tr>
                                                   </table>

													<p><small>(* %7$s %8$s)</small></p>',
													__('No. of WordPress users', WE_LS_SLUG),
													$entry_counts['number-of-users'],
													__('No. of Weight Entries', WE_LS_SLUG),
                                                    ws_ls_blur_text( $entry_counts['number-of-entries'] ),
													__('No. of Target Entries', WE_LS_SLUG),
                                                    ws_ls_blur_text( $entry_counts['number-of-targets'] ),
													__('refreshed every 15 minutes', WE_LS_SLUG),
                                                    '<a href="' . admin_url( 'admin.php?page=ws-ls-data-home&regenerate-stats=y' ) . '"><small>Regenerate these stats</small></a>',
                                                    ws_ls_blur( false, false )
									);
								}
							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('View all data', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<a class="button-primary" href="<?php echo ws_ls_get_link_to_user_data() . '&amp;mode=all'; ?>">
								<i class="fa fa-book"></i>
								<?php echo __('View all entries', WE_LS_SLUG); ?>
							</a>
						</div>
					</div>
					<div class="postbox">
                        <h2 class="hndle"><span><?php echo __('Export all data', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <a class="button-secondary" href="<?php echo ws_ls_get_link_to_export(); ?>">
								<i class="fa fa-file-excel-o"></i>
                                <?php echo __('To CSV', WE_LS_SLUG); ?>
                            </a>
                            <a class="button-secondary" href="<?php echo ws_ls_get_link_to_export('json'); ?>">
								<i class="fa fa-file-code-o"></i>
                                <?php echo __('To JSON', WE_LS_SLUG); ?>
                            </a>
                        </div>
                    </div>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo __('Delete Data', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <a class="button-secondary delete-confirm" href="<?php echo admin_url( 'admin.php?page=ws-ls-data-home&removedata=y' ); ?>">
								<i class="fa fa-exclamation-circle"></i>
								<?php echo __('Delete data for ALL users', WE_LS_SLUG); ?>
                            </a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php

    ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
        __('Are you sure you wish to remove all user data?', WE_LS_SLUG) . '<br /><br />',
        'delete-confirm');
}
