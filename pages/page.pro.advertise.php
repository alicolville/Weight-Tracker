<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_advertise_pro() {

	global $pro_features;

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$yeken_data = ws_ls_get_data_from_yeken();

	$price = ($yeken_data && isset($yeken_data->price)) ? $yeken_data->price : WS_LS_PRO_PRICE;

		?>

		<div class="wrap">

			<div id="icon-options-general" class="icon32"></div>
					<div id="poststuff">

				<div id="post-body" class="metabox-holder columns-2">

					<!-- main content -->
					<div id="post-body-content">

						<div class="meta-box-sortables ui-sortable">

							<div class="postbox">

								<div class="handlediv" title="Click to toggle"><br></div>
								<!-- Toggle -->

								<h3 class="hndle"><span><?php echo __('Get ' . WE_LS_TITLE . ' Pro!', WE_LS_SLUG); ?></span>
								</h3>

								<div class="inside">
									<p><?php echo __('Get Weight Loss Tracker Pro for', WE_LS_SLUG) . ' &pound;' . $price .  __(' and have the following features listed below.', WE_LS_SLUG); ?> </p>
                                    <center>
                                        <h3><?php echo __('In case you need, your <strong>Site Hash</strong> is', WE_LS_SLUG); ?>: <?php echo ws_ls_generate_site_hash(); ?></h3>
										<a href="https://weight.yeken.uk/pro?hash=<?php echo ws_ls_generate_site_hash(); ?>" style="width:60%;font-size:15px;text-align:center;" target="_blank" class="button-primary"><?php echo __('Upgrade now for', WE_LS_SLUG); ?> &pound;<?php echo $price; ?></a>
									</center>
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

						</div>
						<!-- .meta-box-sortables .ui-sortable -->

					</div>

					<!-- sidebar -->
                    <?php

                        $license = '';

                        $license_type = ws_ls_has_a_valid_license();

                        $license_name = ws_ls_license_display_name($license_type);

                        $license_decoded = false;

                        if (true === in_array($license_type, ['pro', 'pro-plus'])) {
                            $license = ws_ls_license();
                            $license_decoded = ws_ls_license_decode($license);
                        }

                    ?>
					<div id="postbox-container-1" class="postbox-container">

						<div class="meta-box-sortables">
                            <div class="postbox">
                                <h3 class="hndle"><span><?php echo __('Your License Information', WE_LS_SLUG); ?></span></h3>
                                <div class="inside">
                                    <table class="ws-ls-sidebar-stats">
                                        <tr>
                                            <th><?php echo __('Site Hash', WE_LS_SLUG); ?></th>
                                            <td><?php echo esc_html_e(ws_ls_generate_site_hash()); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo __('Type', WE_LS_SLUG); ?></th>
                                            <td><a href="https://weight.yeken.uk/license-types" target="_blank"><?php echo esc_html_e($license_name); ?></a></td>
                                        </tr>
                                        <tr class="last">
                                            <th><?php echo __('Expires', WE_LS_SLUG); ?></th>
                                            <td>
                                                <?php

                                                    if('pro-old' === $license_type) {
                                                        echo __('Never', WE_LS_SLUG);
                                                    } elseif (true === in_array($license_type, ['pro', 'pro-plus'])) {
                                                        esc_html_e(ws_ls_iso_date_into_correct_format($license_decoded['expiry-date'], true));
                                                    } else {
                                                        echo __('n/a', WE_LS_SLUG);
                                                    }

                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <?php $existing_license = ws_ls_license_get_old_or_new(); ?>

                            <?php if(false === empty($existing_license)): ?>
                                <div class="postbox">
                                    <h3 class="hndle"><span><?php echo __('Your Existing license', WE_LS_SLUG); ?></span></h3>
                                    <div class="inside">
                                        <textarea rows="5" style="width:100%"><?php echo esc_textarea($existing_license); ?></textarea>
                                    </div>
                                </div>
                            <?php endif; ?>

							<div class="postbox">

								<h3 class="hndle"><span><?php echo __('Add or Update License', WE_LS_SLUG); ?></span></h3>
								<?php
                                if (isset($_GET['add-license']) && 'true' == $_GET['add-license'] && !empty($_POST['license-key'])){

									$entered_license = $_POST['license-key'];
									$valid_license = ws_ls_is_validate_old_pro_license($entered_license);

									if ($valid_license) {
										$display_form = false;
                                        ws_ls_delete_all_cache();
									}
								}

								?>
								<div class="inside">
									<form action="<?php echo admin_url('admin.php?page=ws-ls-weight-loss-tracker-pro&add-license=true'); ?>" method="post">
										<p><?php echo __('Copy and paste the license given to you by YeKen into this box and click "Add License"', WE_LS_SLUG); ?>.</p>
										<textarea rows="5" style="width:100%"  name="license-key"></textarea>
                                        <br /><br />
										<input type="submit" class="button-secondary large-text" value="<?php echo __('Add License', WE_LS_SLUG); ?>" />
									</form>
								</div>
								<!-- .inside -->

							</div>
							<!-- .postbox -->

                            <div class="postbox">

                                <div class="handlediv" title="Click to toggle"><br></div>
                                <!-- Toggle -->

                                <h3 class="hndle"><span><?php echo __('Documentation', WE_LS_SLUG); ?></span></h3>

                                <div class="inside">
                                    <p><?php echo __('Need further help or information, please visit our documentation site:', WE_LS_SLUG); ?></p>
                                    <p><strong><a href="https://weight.yeken.uk" target="_blank">weight.yeken.uk</a></strong></p>
                                    <a href="https://weight.yeken.uk" target="_blank"><img class="widefat" src="<?php echo plugins_url( 'images/weight.yeken.uk.jpg', __FILE__ ); ?>" /></a>
                                </div>
                                <!-- .inside -->

                            </div>

						</div>
						<!-- .meta-box-sortables -->

					</div>
					<!-- #postbox-container-1 .postbox-container -->

					<!-- post-body-content -->
					<div id="post-body" class="metabox-holder columns-3">

						<!-- main content -->
						<div id="post-body-content">

							<div class="meta-box-sortables ui-sortable">

								<div class="postbox">

									<div class="handlediv" title="Click to toggle"><br></div>
									<!-- Toggle -->

									<h3 class="hndle">
                                        <img src="<?php echo plugin_dir_url( __FILE__ ); ?>/images/upgrade-pro-standard.png" />
                                    </h3>
                                   <div style="padding: 0px 15px 0px 15px">

									<div class="inside">
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
									<!-- .inside -->

								</div>
								<!-- .postbox -->

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
