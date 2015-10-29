<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_advertise_pro() {

	global $pro_features;

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

		?>
		<div class="wrap">



	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __('Get ' . WE_LS_TITLE . ' Pro!', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __('We are shortly releasing a pro version of this plugin. We aim to keep the price under &pound;30 and have the following features', WE_LS_SLUG); ?>:</p>
							<center>
							<a href="https://www.yeken.uk/show-interest-in-weight-loss-tracker-pro/" target="_blank" class="button-primary"><?php echo __('Show your interest now and get 25% off when released', WE_LS_SLUG); ?></a>
					<br /><br /><small><?php echo __('Seeing the level of interest will help motivate me to develop it!', WE_LS_SLUG); ?> :)</small>	</centeR><br />
						</div>
						</div>
						<div class="postbox">
							<h3 class="hndle"><span><?php echo __('Features of Pro version', WE_LS_SLUG); ?> </span></h3>
							<div style="padding: 0px 15px 0px 15px">
								<p><?php echo __('Below is a list of the intended features of the Pro version:', WE_LS_SLUG); ?></p>
						<table class="form-table" >
							<?php

							$class = '';

							foreach ($pro_features as $feature) {

								$class = ('alternate' == $class) ? '' : 'alternate';

								?>
								<tr valign="top" class="<?php echo $class; ?>">
									<td scope="row" style="padding-left:30px"><label for="tablecell">
										&middot; <?php echo $feature; ?>
									</label></td>

								</tr>

								<?php
							}
							?>


						</table>
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
<?php } ?>
