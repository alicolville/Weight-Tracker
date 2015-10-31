<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_help_page() {
		?>
		<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( WE_LS_TITLE . ' Instructions', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'Place the tag [weight-loss-tracker] on a given page and the user is presented with a form to enter a date, weight and notes for that entry. When the person saves their entry the data table and graph is refreshed.', WE_LS_SLUG ); ?></p>
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
