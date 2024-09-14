<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_advertise_pro() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Remove existing license?
	if (isset($_GET['remove-license']) && 'true' == $_GET['remove-license']) {
		ws_ls_license_remove();
	}

	?>

		<div class="wrap ws-ls-admin-page">
			<?php
				if ( 'true' === ws_ls_querystring_value( 'add-license' ) && NULL !== ws_ls_post_value( 'license-key' ) ){

					$valid_old_license 	= false;
					$entered_license 	= ws_ls_post_value( 'license-key' );

					// First try validating and applying a new subscription license
					$valid_new_license = ws_ls_license_apply( $entered_license, false);

					// If not a new license, see if an old legacy license
					if (true !== $valid_new_license) {
						$valid_old_license = ws_ls_is_validate_old_pro_license($entered_license);
					}

					if ($valid_old_license || true === $valid_new_license) {
						ws_ls_display_notice( esc_html__( 'Your license has been applied!', WE_LS_SLUG ) );
						ws_ls_cache_delete_all();
					} else {
						ws_ls_display_notice( esc_html__('An error occurred applying your license: ', WE_LS_SLUG ) . $valid_new_license, 'error');
					}
				}

				 $license = '';

				 $license_type = ws_ls_has_a_valid_license();

				 $license_name = ws_ls_license_display_name($license_type);

				 $license_decoded = false;

				 if (true === in_array($license_type, ['pro', 'pro-plus'])) {
					 $license 			= ws_ls_license();
					 $license_decoded 	= ws_ls_license_decode( $license) ;
				 }

				 $display_pro_plus_marketing 	= (false === $license_type || 'pro' === $license_type );
				 $display_pro_marketing 		= (true === empty($license_type));
			?>
			<div id="icon-options-general" class="icon32"></div>
					<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<?php
                       		$price 		= ws_ls_license_pro_price();
							$proprice 	= ws_ls_license_pro_plus_price();
						?>
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="hndle"><span><?php echo esc_html__('Upgrade', WE_LS_SLUG) . ' ' . WE_LS_TITLE; ?></span></h3>
								<div class="inside">
									<?php if ($display_pro_plus_marketing && $display_pro_marketing) {

										ws_ls_echo_wp_kses( sprintf('<p>%s %s %s</p><p>%s</p>',
																esc_html__('As you can see from the features listed below, the', WE_LS_SLUG),
																WE_LS_TITLE,
																esc_html__('can offer you and your members a lot more features to help you and them manage their weight. There are two types of License that you can purchase, Pro and Pro Plus. Pro contains an enriched feature set and user experience and is a must! Pro Plus extends Pro with features such as BMR, Calorie intake, Macronutrient Calculator, etc.', WE_LS_SLUG),
																esc_html__('Of course, by purchasing a license, you are supporting the future of this plugin and it is gratefully appreciated.', WE_LS_SLUG )
															));
									} elseif ( $display_pro_plus_marketing ) {

										ws_ls_echo_wp_kses( sprintf('<p>%s %s %s</p>',
																esc_html__('Of course, a big thank you purchasing a Pro license at some point - it is much appreciated. As you can see below, you can further expand the features of', WE_LS_SLUG ),
																WE_LS_TITLE,
																esc_html__('by extending your license to Pro Plus. Pro Plus extends Pro with features such as BMR, Calorie intake, Macronutrient Calculator, etc. You can view the additional features that Pro Plus offers you below.', WE_LS_SLUG)
															));

									} else {

										echo sprintf('<p>%s</p>', esc_html__( 'Thank you for purchasing a license! Your support means a lot to me, and I greatly appreciate you choosing to back this plugin!', WE_LS_SLUG ) );

									} ?>

                                    <center>
                                        <h3><?php echo esc_html__('In case you need, your Site Hash is', WE_LS_SLUG); ?>: <?php ws_ls_echo( ws_ls_generate_site_hash() ); ?></h3>

                                        <?php

										if ( false === WS_LS_IS_PRO && false === WS_LS_IS_PRO_PLUS )  {

											$button_html = sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-angellist"></i> %s</a>',
																	WE_LS_FREE_TRIAL_URL,
																	ws_ls_generate_site_hash(),
																	ws_ls_license_get_old_or_new(),
																	esc_html__( 'Get a free 7 day trial!', WE_LS_SLUG ));

                                            ws_ls_echo_wp_kses( $button_html );					
										}

										if ( $display_pro_plus_marketing )  {

											$text = sprintf( '%s  &pound;%s %s', esc_html__( 'Upgrade to Pro Plus for', WE_LS_SLUG), $proprice, esc_html__( 'a year', WE_LS_SLUG ) );
											$link = WE_LS_UPGRADE_TO_PRO_PLUS_URL;

											$button_html = sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-plus"></i> <i class="fa fa-plus"></i> %s</a>',
																	$link,
																	ws_ls_generate_site_hash(),
																	ws_ls_license_get_old_or_new(),
																	$text );

											ws_ls_echo_wp_kses( $button_html );	

										}

										if ( $display_pro_marketing ) {
									
											$text = sprintf( '%s  &pound;%s %s', esc_html__( 'Upgrade to Pro for', WE_LS_SLUG), $price, esc_html__( 'a year', WE_LS_SLUG ) );
											$link = WE_LS_UPGRADE_TO_PRO_URL;

											$button_html = sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-plus"></i> %s</a>',
																	$link,
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

								<h3 class="hndle"><span><?php echo esc_html__('Add or Update License', WE_LS_SLUG); ?></span></h3>

								<div class="inside">

									<form action="<?php ws_ls_echo( admin_url('admin.php?page=ws-ls-license&add-license=true') ); ?>" method="post">
										<p><?php echo esc_html__('Copy and paste the license given to you by YeKen into this box and click "Apply License"', WE_LS_SLUG); ?>.</p>
										<textarea rows="5" style="width:100%"  name="license-key"></textarea>
                                        <br /><br />
                                        <input type="submit" class="button-secondary large-text" value="<?php echo esc_html__('Apply License', WE_LS_SLUG); ?>" />
									</form>
								</div>
							</div>
							<div class="postbox">
                                <h3 class="hndle"><span><?php echo esc_html__('Your License Information', WE_LS_SLUG); ?></span></h3>
                                <div class="inside">
                                    <table class="ws-ls-sidebar-stats">
                                        <tr>
                                            <th><?php echo esc_html__('Site Hash', WE_LS_SLUG); ?></th>
                                            <td><?php ws_ls_echo( ws_ls_generate_site_hash() ); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo esc_html__('Type', WE_LS_SLUG); ?></th>
                                            <td><a href="<?php echo esc_url( WE_LS_LICENSE_TYPES_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php ws_ls_echo( $license_name ); ?></a></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo esc_html__('Expires', WE_LS_SLUG); ?></th>
                                            <td>
                                                <?php

                                                    if (true === in_array($license_type, ['pro', 'pro-plus'])) {
                                                        ws_ls_echo( ws_ls_iso_date_into_correct_format( $license_decoded['expiry-date'] ) );
                                                    } else {
                                                        echo esc_html__('n/a', WE_LS_SLUG);
                                                    }

                                                ?>
                                            </td>
                                        </tr>
										<?php $existing_license = ws_ls_license_get_old_or_new(); ?>

										<?php if ( false === empty( $existing_license )): ?>
											<tr class="last">
	                                            <th colspan="2"><?php echo esc_html__('Your Existing License', WE_LS_SLUG); ?></th>
											</tr>
											<tr class="last">
												<td colspan="2"><textarea rows="5" style="width:100%"><?php echo esc_textarea($existing_license); ?></textarea></td>
											</tr>
											<tr class="last">
												<td colspan="2"><a href="<?php echo esc_url( admin_url('admin.php?page=ws-ls-license&remove-license=true' ) ); ?>" class="button-secondary delete-license"><?php echo esc_html__('Remove License', WE_LS_SLUG); ?></a></td>
											</tr>

										<?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <div class="postbox">
        						<h3 class="hndle"><span><?php echo esc_html__('Documentation', WE_LS_SLUG); ?></span></h3>

                                <div class="inside">
                                    <p><?php echo esc_html__('Need further help or information, please visit our documentation site:', WE_LS_SLUG); ?></p>
                                    <p><strong><a href="https://docs.yeken.uk" target="_blank" rel="noopener noreferrer">docs.yeken.uk</a></strong></p>
                                    <a href="https://docs.yeken.uk" target="_blank" rel="noopener noreferrer"><img class="widefat" src="<?php ws_ls_echo( plugins_url( 'assets/images/weight-yeken-uk.png', __FILE__ ) ); ?>" /></a>
                                </div>
                            </div>
						</div>
					</div>
					<div id="post-body" class="metabox-holder columns-3">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<?php if ( false === WS_LS_IS_PRO_PLUS && false === WS_LS_IS_PRO ): ?>
									<div class="postbox ws-ls-advertise-pro-plus">
										<h3 class="hndle highlight-title">
                                            <?php echo esc_html__('Pro Plus Features', WE_LS_SLUG); ?>
	                                    </h3>
	                                   <div style="padding: 0px 15px 0px 15px">
											<div class="inside">
												<p><?php echo esc_html__('Below is a list of the intended features of the Pro Plus version', WE_LS_SLUG); ?>. <strong><?php echo esc_html__('You can upgrade for', WE_LS_SLUG); ?> &pound;<?php ws_ls_echo( $proprice ); ?> <?php echo esc_html__('a year', WE_LS_SLUG); ?>.</strong> <?php ws_ls_echo_wp_kses( ws_ls_url_license_types() ); ?>:</p>
												<?php ws_ls_display_features( ws_ls_feature_list_pro_plus() ); ?>
											</div>
										</div>
									</div>
								<?php endif; ?>
								<?php if ($display_pro_marketing): ?>
									<div class="postbox ws-ls-advertise-pro">
										<h3 class="hndle highlight-title">
                                            <?php echo esc_html__('Pro Features', WE_LS_SLUG); ?>
	                                    </h3>
	                                   <div style="padding: 0px 15px 0px 15px">

										<div class="inside">
											<p><?php echo esc_html__('Below is a list of the intended features of the Pro version', WE_LS_SLUG); ?>.  <strong><?php echo esc_html__('You can upgrade for', WE_LS_SLUG); ?> &pound;<?php ws_ls_echo( $price ); ?> <?php echo esc_html__('a year', WE_LS_SLUG); ?>.</strong> <?php ws_ls_echo_wp_kses( ws_ls_url_license_types() ); ?>:</p>
											<?php ws_ls_display_features( ws_ls_feature_list_pro() ); ?>
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