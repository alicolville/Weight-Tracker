<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_view_all() {

    ws_ls_user_data_permission_check();

?>
<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<h1><?php echo __('View All Data', WE_LS_SLUG); ?></h1>
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

						<h2 class="hndle"><span><?php echo __('View All Data', WE_LS_SLUG); ?></span></h2>

						<div class="inside">
							<?php

									$entry_counts = ws_ls_db_entries_count();

									if(false === empty($entry_counts)) {

										echo sprintf('
														<p>
															<strong>%s:</strong> %s | <strong>%s:</strong> %s | <strong>%s:</strong> %s |
															<a href="%s">%s</a> | <a href="%s">%s</a>
														</p>',
														__('Number of WordPress users', WE_LS_SLUG),
														$entry_counts['number-of-users'],
														__('Number of weight entries', WE_LS_SLUG),
														$entry_counts['number-of-entries'],
														__('Number of targets entered', WE_LS_SLUG),
														$entry_counts['number-of-targets'],
                                                        ws_ls_get_link_to_export(),
														__('Export to CSV', WE_LS_SLUG),
                                                        ws_ls_get_link_to_export('json'),
														__('Export to JSON', WE_LS_SLUG)
										);
									}
								?>
								<?php echo ws_ls_data_table_placeholder(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php
}
