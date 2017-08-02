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
						<h3 class="hndle"><span><?php echo __( 'Looking to view and maniupulate your user\'s data?', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">

						</div>
					</div>

                    <div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Example screenshots', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">

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
