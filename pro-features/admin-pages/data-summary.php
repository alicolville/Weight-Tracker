<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_summary() {


    // DELETE ALL DATA!! AHH!!
    if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {
        ws_ls_delete_existing_data();

        // Let others know we cleared all user data
        do_action( WE_LS_HOOK_DATA_ALL_DELETED );
    }

?>
<div class="wrap">
	<h1><?php echo __('Summary', WE_LS_SLUG); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<?php

							// If changing gain / loss set options
							if(false === empty($_GET['show-gain'])) {

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
                                if(update_option('ws-ls-version-number-stats', WE_LS_CURRENT_VERSION) || (false === empty($_GET['regenerate-stats']) && 'y' == $_GET['regenerate-stats'])) {
                                    ws_ls_stats_clear_last_updated_date();
                                    ws_ls_stats_run_cron();
                                    ws_ls_tidy_cache_on_delete();
									$ignore_cache = true;
                                }

                                echo ws_ls_shortcode_stats_league_total(['ignore_cache' => $ignore_cache, 'order' => (false === $show_gain) ? 'asc' : 'desc']);

                            ?>
                            <a class="btn button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-wlt-data-home&regenerate-stats=y' ); ?>"><?php echo __('Regenerate these stats', WE_LS_SLUG); ?></a>
							<?php

								echo sprintf(
												'<a class="btn button-secondary" href="%s">%s</a>',
												admin_url( 'admin.php?page=ws-ls-wlt-data-home&show-gain=') . ((false === $show_gain) ? 'y' : 'n'),
												(false === $show_gain) ? __('Show who has gained the most', WE_LS_SLUG) : __('Show who has lost the most', WE_LS_SLUG)
											);

						 	?>
						</div>
					</div>
					<div class="postbox">
						<?php

							// Show 100 most recent entries? Or show all?
							if(false === empty($_GET['show-all'])) {
								$value = ('y' === $_GET['show-all']) ? true : false;
								update_option('ws-ls-show-all', $value);
							}

							$show_all = get_option('ws-ls-show-all') ? true : false;

						?>
						<h2 class="hndle"><span><?php echo ($show_all) ? __('All entries', WE_LS_SLUG) : __('Last 100 entries', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php ws_ls_data_table_placeholder(false, ($show_all) ? false : 100); ?>
							<?php

								echo sprintf(
												'<a class="btn button-secondary" href="%s">%s</a>',
												admin_url( 'admin.php?page=ws-ls-wlt-data-home&show-all=') . ((false === $show_all) ? 'y' : 'n'),
												(false === $show_all) ? __('Show all entries', WE_LS_SLUG) : __('Show 100 recent entries', WE_LS_SLUG)
											);

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

								$entry_counts = ws_ls_get_entry_counts();

								if(false === empty($entry_counts)) {

									echo sprintf('
													<h4>%s</h4>
													<p>%s</p>
													<h4>%s</h4>
													<p>%s</p>
													<h4>%s</h4>
													<p>%s</p>
													<p><small>(* %s %s)</small></p>',
													__('Number of WordPress users', WE_LS_SLUG),
													$entry_counts['number-of-users'],
													__('Number of weight entries', WE_LS_SLUG),
													$entry_counts['number-of-entries'],
													__('Number of targets entered', WE_LS_SLUG),
													$entry_counts['number-of-targets'],
													__('refreshed every 15 minutes', WE_LS_SLUG),
                                                    '<a href="' . admin_url( 'admin.php?page=ws-ls-wlt-data-home&regenerate-stats=y' ) . '"><small>Regenerate these stats</small></a>'
									);
								}
							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('View all data', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<a class="button-primary" href="<?php echo ws_ls_get_link_to_user_data() . '&amp;mode=all'; ?>">
								<?php echo __('View all entries', WE_LS_SLUG); ?>
							</a>
						</div>
					</div>
					<div class="postbox">
                        <h2 class="hndle"><span><?php echo __('Export all data', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <a class="button-secondary" href="<?php echo ws_ls_get_link_to_export('csv'); ?>">
                                <?php echo __('To CSV', WE_LS_SLUG); ?>
                            </a>
                            <a class="button-secondary" href="<?php echo ws_ls_get_link_to_export('json'); ?>">
                                <?php echo __('To JSON', WE_LS_SLUG); ?>
                            </a>
                        </div>
                    </div>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo __('Delete Data', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <a class="button-secondary delete-confirm" href="<?php echo admin_url( 'admin.php?page=ws-ls-wlt-data-home&removedata=y' ); ?>">
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

    echo ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
        __('Are you sure you wish to remove all user data?', WE_LS_SLUG) . '<br /><br />',
        'delete-confirm');
}
