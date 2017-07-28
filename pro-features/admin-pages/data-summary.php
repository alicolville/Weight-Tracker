<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_summary() {

?>
<div class="wrap">
	<h1><?php echo __('Summary', WE_LS_SLUG); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('League Table', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php echo ws_ls_shortcode_stats_league_total([]); ?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Last 100 entries', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php ws_ls_data_table_placeholder(false, 100); ?>
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
													<p><small>(* %s)</small></p>',
													__('Number of WordPress users', WE_LS_SLUG),
													$entry_counts['number-of-users'],
													__('Number of weight entries', WE_LS_SLUG),
													$entry_counts['number-of-entries'],
													__('Number of targets entered', WE_LS_SLUG),
													$entry_counts['number-of-targets'],
													__('refreshed every 15 minutes', WE_LS_SLUG)
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
                            <a class="button-secondary" href="#">
                                <?php echo __('To CSV', WE_LS_SLUG); ?>
                            </a>
                            <a class="button-secondary" href="#">
                                <?php echo __('To JSON', WE_LS_SLUG); ?>
                            </a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php
}
