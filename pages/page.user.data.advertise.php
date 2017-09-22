<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_home() {
		?>
		<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Looking to view and manipulate your user\'s data?', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'Upgrade to the Pro version of this plugin to view, edit and delete your user\'s weight entries', WE_LS_SLUG); ?></p>
							<ul style="list-style: circle !important; margin-left: 20px;">
								<li><?php echo __( 'Manage and view their photos.', WE_LS_SLUG); ?></li>
								<li><?php echo __( 'View all user entries in tabular and chart format.', WE_LS_SLUG); ?></li>
								<li><?php echo __( 'Add, edit and delete user entries.', WE_LS_SLUG); ?></li>
								<li><?php echo __( 'Sortable and responsive tables.', WE_LS_SLUG); ?></li>
								<li><?php echo __( 'View league tables of most lost / gained.', WE_LS_SLUG); ?></li>
								<li><?php echo __( 'View user stats, weight lost, recent weight, start weight, BMI, etc.', WE_LS_SLUG); ?></li>
								<li><?php echo __( 'Export all or a particular user\'s data in CSV / JSON.', WE_LS_SLUG); ?></li>
							</ul>
							<p><a href="<?php echo admin_url('admin.php?page=ws-ls-weight-loss-tracker-pro'); ?>" class="button-primary"><?php echo __( 'Upgrade to Pro Version', WE_LS_SLUG); ?></a></p>
						</div>
					</div>

                    <div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Example screenshots', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'Below are a couple of screenshots from the user data screens', WE_LS_SLUG); ?>:</p>
							<div class="ws-ls-user-preview">
								<a href="<?php echo admin_url('admin.php?page=ws-ls-weight-loss-tracker-pro'); ?>"><img src="<?php echo plugins_url( 'images/data-summary.jpg', __FILE__ ); ?>" /></a>
								<a href="<?php echo admin_url('admin.php?page=ws-ls-weight-loss-tracker-pro'); ?>"><img src="<?php echo plugins_url( 'images/user-card.jpg', __FILE__ ); ?>" /></a>
							</div>
						</div>
					</div>
				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
<?php
}
?>
