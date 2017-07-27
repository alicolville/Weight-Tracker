<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_view_all() {

?>
<div class="wrap">
	<h1><?php echo __('View All Data', WE_LS_SLUG); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">

						<h2 class="hndle"><span><?php echo __('View All Data', WE_LS_SLUG); ?></span></h2>
						<p><?php var_Dump(ws_ls_get_entry_counts()); ?>
							//TODO: Finish this off -- tidy layout and add stats
							<a class="button-secondary" href="#">
								<?php echo __('Export to CSV', WE_LS_SLUG); ?>
							</a>
							<a class="button-secondary" href="#">
								<?php echo __('Export to JSON', WE_LS_SLUG); ?>
							</a>
						</p>
						<div class="inside">
							<?php ws_ls_data_table_placeholder(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php
}
