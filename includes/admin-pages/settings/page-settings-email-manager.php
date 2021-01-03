<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_settings_email_manager() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
	}

	?>
	<div class="wrap ws-ls-admin-page">


	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3 ws-ls-settings">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Email Manager', WE_LS_SLUG); ?></span></h3>

						<div class="inside">


						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

					<div class="postbox">
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
