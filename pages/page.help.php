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
						<h3 class="hndle"><span><?php echo __( 'Help', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'You can find detailed help with this plugin at our site:', WE_LS_SLUG ); ?></p>
                            <p><a href="https://weight.yeken.uk" rel="noopener noreferrer" target="_blank">https://weight.yeken.uk</a></p>
						</div>
					</div>

                    <div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Our road map', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'Interested in the future development of this plugin? Then have a peek at our road map:', WE_LS_SLUG ); ?></p>
                            <p><a href="https://weight.yeken.uk/road-map/" rel="noopener noreferrer" target="_blank">https://weight.yeken.uk/road-map/</a></p>
						</div>
					</div>

					<?php if ( true === current_user_can( 'manage_options' ) && 'y' === ws_ls_querystring_value('yeken') && 'stones_pounds' === WE_LS_DATA_UNITS )  : ?>

						<div class="postbox">
							<h3 class="hndle"><span><?php echo __( 'Tools', WE_LS_SLUG); ?> </span></h3>
							<div style="padding: 0px 15px 0px 15px">
								<p><?php echo __( 'Correct accuracy for measurements entered in Stones and Pounds (only run with the advice of YeKen)', WE_LS_SLUG ); ?></p>
								<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ws-ls-weight-loss-tracker-help&yeken=y&run=y') ); ?>" >Run</a></p>

								<?php

								if ( 'y' === ws_ls_querystring_value('run') ) {
									ws_ls_fix_to_kg();
								}

								?>
							</div>
						</div>
					<?php endif; ?>

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
