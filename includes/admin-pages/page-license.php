<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render the Premium upgrade/license page
 *
 */
function ws_ls_advertise_premium() {

	if ( 'true' === ws_ls_querystring_value( 'remove-license') ) {
		ws_ls_license_remove();
	}

	?>
	<div class="wrap ws-ls-admin-page">
		<?php
			
			/**
			 * Apply a new or updated license
			 */
			$entered_license = ws_ls_post_value( 'wt-license-key' );

			if ( false === empty( $entered_license ) ){

				$valid_new_license = ws_ls_license_apply( $entered_license, false );

				if ( true === $valid_new_license ) {

					ws_ls_display_notice( esc_html__( 'Your new license has been applied, thank you for supporting this plugin!', WE_LS_SLUG ) );
					ws_ls_cache_delete_all();

				} else {
					ws_ls_display_notice( esc_html__('An error occurred applying your license: ', WE_LS_SLUG ) . $valid_new_license, 'error');
				}
			}
		?>
		<div id="icon-options-general" class="icon32"></div>
				<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<?php
						$price = ws_ls_license_premium();
					?>
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h3 class="hndle">
									<span>
										<?php if ( !WS_LS_IS_PREMIUM ){ 
											echo esc_html__( 'Upgrade to Weight Tracker Premium', WE_LS_SLUG );
										} else { 
											echo esc_html__( 'Thank you', WE_LS_SLUG );
										} ?>
									</span>
								</h3>
							<div class="inside">
								<p>
									<?php if ( !WS_LS_IS_PREMIUM ){ 
										echo esc_html__( 'The Weight Tracker Premium provides an array of enhanced features, such as BMR calculation, calorie intake tracking, and a macronutrient calculator, designed to help both you and your members manage weight more effectively. By purchasing a licence, you are not only unlocking these additional features but also supporting the continued development of this plugin — your support is truly appreciated.', WE_LS_SLUG );
									} else { 
										echo esc_html__( 'Thank you for purchasing a license! Your support means a lot to me, and I greatly appreciate you choosing to back this plugin!', WE_LS_SLUG );
									} ?>
								</p>
								<center>
									<h3><?php echo esc_html__('In case you need, your Site Hash is', WE_LS_SLUG); ?>: <?php ws_ls_echo( ws_ls_generate_site_hash() ); ?></h3>

									<?php

									if ( !WS_LS_IS_PREMIUM )  {

										$button_html = sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-clock-o"></i> %s</a>',
																WE_LS_FREE_TRIAL_URL,
																ws_ls_generate_site_hash(),
																ws_ls_license_get_old_or_new(),
																esc_html__( 'Get a free 14 day trial', WE_LS_SLUG ));

										ws_ls_echo_wp_kses( $button_html );					
									
										$text = sprintf( '%s  &pound;%s %s', esc_html__( 'Upgrade to Premium for', WE_LS_SLUG), $price, esc_html__( 'a year', WE_LS_SLUG ) );
										
										$button_html = sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-plus"></i> %s</a>',
																WE_LS_UPGRADE_TO_PREMIUM_URL,
																ws_ls_generate_site_hash(),
																ws_ls_license_get_old_or_new(),
																$text );

										ws_ls_echo_wp_kses( $button_html );	
									}
								?>
								</center>
							</div>
						</div>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">

					<div class="meta-box-sortables">

						<div class="postbox">
							<h3 class="hndle">
								<span>
									<?php echo esc_html__('Add or update your license', WE_LS_SLUG); ?>
								</span>
							</h3>
							<div class="inside">
								<form action="<?php ws_ls_echo( admin_url('admin.php?page=ws-ls-license') ); ?>" method="post">
									<p>
										<?php echo esc_html__('Copy and paste your license into this box and click "Apply License"', WE_LS_SLUG); ?>
									</p>
									<textarea rows="5" style="width:100%"  name="wt-license-key"></textarea>
									<br /><br />
									<input type="submit" class="button-secondary large-text" value="<?php echo esc_html__('Apply License', WE_LS_SLUG); ?>" />
								</form>
							</div>
						</div>
						<div class="postbox">
							<h3 class="hndle">
								<span>
									<?php echo esc_html__('Your license information', WE_LS_SLUG); ?>
								</span>
							</h3>
							<?php
								$valid_license = ws_ls_has_a_valid_license();		
							?>
							<div class="inside">
								<table class="ws-ls-sidebar-stats">
									<tr>
										<th><?php echo esc_html__('Site hash', WE_LS_SLUG); ?></th>
										<td><?php ws_ls_echo( ws_ls_generate_site_hash() ); ?></td>
									</tr>
									<tr>
										<th><?php echo esc_html__('Type', WE_LS_SLUG); ?></th>
										<td id="ws-ls-license-type">
											<a href="<?php echo esc_url( WE_LS_LICENSE_TYPES_URL ); ?>" target="_blank" rel="noopener noreferrer">
												<?php echo ( $valid_license ? 'Premium' : 'None' ); ?>
											</a>
										</td>
									</tr>
									<tr>
										<th><?php echo esc_html__('Expires', WE_LS_SLUG); ?></th>
										<td>
											<?php
												if ( $valid_license ) {

													$license 			= ws_ls_license();
													$license_decoded 	= ws_ls_license_decode( $license ) ;

													ws_ls_echo( ws_ls_iso_date_into_correct_format( $license_decoded[ 'expiry-date' ] ) );
							
												} else {
													echo esc_html__( 'n/a', WE_LS_SLUG );
												}
											?>
										</td>
									</tr>
									<?php if ( $valid_license ): ?>
										<tr class="last">
											<th colspan="2">
												<?php echo esc_html__( 'Your existing license', WE_LS_SLUG ); ?>
											</th>
										</tr>
										<tr class="last">
											<td colspan="2">
												<textarea rows="5" style="width:100%"><?php echo esc_textarea( ws_ls_license_get_old_or_new() ); ?></textarea>
											</td>
										</tr>
										<tr class="last">
											<td colspan="2">
												<a href="<?php echo esc_url( admin_url('admin.php?page=ws-ls-license&remove-license=true' ) ); ?>" class="button-secondary delete-license">
													<?php echo esc_html__('Remove License', WE_LS_SLUG); ?>
												</a>
											</td>
										</tr>
									<?php endif; ?>
								</table>
							</div>
						</div>

						<div class="postbox">
							<h3 class="hndle">
								<span>
									<?php echo esc_html__('Documentation', WE_LS_SLUG); ?>
								</span>
							</h3>
							<div class="inside">
								<p><?php echo esc_html__('For additional assistance or details, please visit our documentation site.', WE_LS_SLUG); ?></p>
								<p><strong><a href="https://docs.yeken.uk" target="_blank" rel="noopener noreferrer">docs.yeken.uk</a></strong> or <a href="https://weighttracker.yeken.uk" target="_blank" rel="noopener noreferrer">weighttracker.yeken.uk</a></strong></p>
								<a href="https://docs.yeken.uk" target="_blank" rel="noopener noreferrer"><img class="widefat" src="<?php ws_ls_echo( plugins_url( 'assets/images/weight-yeken-uk.png', __FILE__ ) ); ?>" /></a>
							</div>
						</div>
					</div>
				</div>
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<?php if ( false === WS_LS_IS_PREMIUM ): ?>
								<div class="postbox ws-ls-advertise-pro-plus">
									<h3 class="hndle highlight-title">
										<?php echo esc_html__('Go Premium for', WE_LS_SLUG ); ?> &pound;<?php ws_ls_echo( WE_LS_PREMIUM_PRICE ); ?> <?php echo esc_html__('a year', WE_LS_SLUG); ?>.
									</h3>
									<div style="padding: 0px 15px 0px 15px">
										<div class="inside">
											<h3>
												<?php echo esc_html__('The following features are included with a Premium subscription', WE_LS_SLUG); ?>:
											</h3>
											<?php ws_ls_display_features( ws_ls_feature_list_premium() ); ?>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
			</div>
			<br class="clear">
		</div>
	</div>

<?php

	ws_ls_create_dialog_jquery_code( esc_html__('Are you sure you?', WE_LS_SLUG ),
        esc_html__('Are you sure you wish to remove the license for this site? Removing it may cause your user\'s to lose functionality.', WE_LS_SLUG ) . '<br /><br />',
        'delete-license');

}