<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_help_page() {
		?>
		<div class="wrap ws-ls-admin-page">

	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

                    <div class="postbox">
                        <h3 class="hndle"><span><?php echo __( 'Custom modifications / web development', WE_LS_SLUG); ?> </span></h3>
                        <div style="padding: 0px 15px 0px 15px">
                            <p><img src="<?php echo plugins_url( 'images/yeken-logo.png', __FILE__ ); ?>" width="150" height="150" style="margin-right:20px" align="left" /><?php echo __( 'If require plugin modifications to Weight Tracker, or need a new plugin built, or perhaps you need a developer to help you with your website then please don\'t hesitiate get in touch!', WE_LS_SLUG ); ?></p>
                            <p><strong><?php echo __( 'We provide fix priced quotes.', WE_LS_SLUG); ?></strong></p>
                            <p><a href="https://www.yeken.uk" rel="noopener noreferrer" target="_blank">YeKen.uk</a> /
                                <a href="https://profiles.wordpress.org/aliakro" rel="noopener noreferrer" target="_blank">WordPress Profile</a> /
                                <a href="mailto:email@yeken.uk" >email@yeken.uk</a></p>
                            <br clear="both"/>
                        </div>
                    </div>

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Documentation', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'You can find detailed documentation for this plugin at our site:', WE_LS_SLUG ); ?></p>
                            <p><a href="https://weight.yeken.uk" rel="noopener noreferrer" target="_blank">https://weight.yeken.uk</a></p>
						</div>
				    </div>

					<?php if ( true === current_user_can( 'manage_options' ) && 'y' === ws_ls_querystring_value('yeken') && 'stones_pounds' === WE_LS_DATA_UNITS )  : ?>

						<div class="postbox">
							<h3 class="hndle"><span><?php echo __( 'Tools', WE_LS_SLUG); ?> </span></h3>
							<div style="padding: 0px 15px 0px 15px">
								<p><?php echo __( 'Correct accuracy for measurements entered in Stones and Pounds (only run with the advice of YeKen)', WE_LS_SLUG ); ?></p>
								<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ws-ls-help&yeken=y&run=y') ); ?>" >Run</a></p>

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
