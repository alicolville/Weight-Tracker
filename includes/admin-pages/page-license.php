<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_advertise_pro() {

	global $pro_features;
	global $pro_plus_features;

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	// Remove existing license?
	if (isset($_GET['remove-license']) && 'true' == $_GET['remove-license']) {
		ws_ls_license_remove();
	}

	?>

		<div class="wrap ws-ls-admin-page">
			<?php
				if (isset($_GET['add-license']) && 'true' == $_GET['add-license'] && !empty($_POST['license-key'])){

					$valid_old_license = false;
					$entered_license = $_POST['license-key'];

					// First try validating and applying a new subscription license
					$valid_new_license = ws_ls_license_apply($entered_license);

					// If not a new license, see if an old legacy license
					if (true !== $valid_new_license) {
						$valid_old_license = ws_ls_is_validate_old_pro_license($entered_license);
					}

					if ($valid_old_license || true === $valid_new_license) {
						ws_ls_display_notice(__('Your license has been applied!', WE_LS_SLUG));
						ws_ls_delete_all_cache();
					} else {
						ws_ls_display_notice(__('An error occurred applying your license: ', WE_LS_SLUG) . $valid_new_license, 'error');
					}
				}

				 $license = '';

				 $license_type = ws_ls_has_a_valid_license();

				 $license_name = ws_ls_license_display_name($license_type);

				 $license_decoded = false;

				 if (true === in_array($license_type, ['pro', 'pro-plus'])) {
					 $license = ws_ls_license();
					 $license_decoded = ws_ls_license_decode($license);
				 }

				 $display_pro_plus_marketing = (false === $license_type || true === in_array($license_type, ['pro', 'pro-old']));
				 $display_pro_marketing = (true === empty($license_type));
			?>
			<div id="icon-options-general" class="icon32"></div>
					<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<?php
//todo: here
							$price = ($yeken_data && isset($yeken_data->price)) ? (int) $yeken_data->price : WS_LS_PRO_PRICE;
							$proprice = ($yeken_data && isset($yeken_data->plusprice)) ? (int) $yeken_data->plusprice : WS_LS_PRO_PLUS_PRICE;
						?>
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox">
								<h3 class="hndle"><span><?php echo __('Upgrade', WE_LS_SLUG) . ' ' . WE_LS_TITLE; ?></span></h3>
								<div class="inside">
									<?php if ($display_pro_plus_marketing && $display_pro_marketing) {

										echo sprintf('<p>%s %s %s</p><p>%s</p>',
											__('As you can see from the features listed below, the', WE_LS_SLUG),
											WE_LS_TITLE,
											__('can offer you and your members a lot more features to help you and them manage their weight. There are two types of License that you can purchase, Pro and Pro Plus. Pro contains an enriched feature set and user experience and is a must! Pro Plus extends Pro with features such as BMR, Calorie intake, Macronutrient Calculator, etc.', WE_LS_SLUG),
											__('Of course, by purchasing a license, you are supporting the future of this plugin and it is gratefully appreciated.', WE_LS_SLUG)
										);
									} elseif ('pro-old' === $license_type) {

										echo sprintf('<h3>%s</h3><p>%s</p><h3>%s</h3><p>%s</p>',
											__('Legacy Pro License', WE_LS_SLUG),
											__('Our licensing has model has changed, Prior to September 2017 it used to be a one off payment that enabled the Pro features of the plugin. This is still the case and any old licenses will still be honoured.', WE_LS_SLUG),
											__('Moving to a yearly license', WE_LS_SLUG),
											__('There is no reason to move to a yearly license - Pro features will remain the same for legacy and new Pro subscriptions. However, if you were to move to a yearly subscription you will be supporting the development of the plugin (and I\'ll give you a big hug).', WE_LS_SLUG)
										);

										echo sprintf('<h3>%s</h3><p>%s %s %s</p>',
											__('Pro Plus License', WE_LS_SLUG),
											__('Of course, thank you for purchasing a Pro license - it is much appreciated. As you can see below, you can further expand the features of', WE_LS_SLUG),
											WE_LS_TITLE,
											__('by extending your license to Pro Plus. Pro Plus extends Pro with features such as BMR, Calorie intake, Macronutrient Calculator, etc. You can view the additional features that Pro Plus offers you below.', WE_LS_SLUG)
										);

										$display_pro_marketing = true;

									} elseif ($display_pro_plus_marketing) {

										echo sprintf('<p>%s %s %s</p>',
											__('Of course, a big thank you purchasing a Pro license at some point - it is much appreciated. As you can see below, you can further expand the features of', WE_LS_SLUG),
											WE_LS_TITLE,
											__('by extending your license to Pro Plus. Pro Plus extends Pro with features such as BMR, Calorie intake, Macronutrient Calculator, etc. You can view the additional features that Pro Plus offers you below.', WE_LS_SLUG)
										);

									} else {

										echo sprintf('<p>%s</p><p>%s</p>',
											__('Thank you kind soul, you have purchased a Pro Plus license. A huge thank you for supporting me and this plugin!', WE_LS_SLUG),
											ws_ls_url_license_types()
										);

									} ?>

                                    <center>
                                        <h3><?php echo __('In case you need, your <strong>Site Hash</strong> is', WE_LS_SLUG); ?>: <?php echo ws_ls_generate_site_hash(); ?></h3>


                                        <?php if ($display_pro_plus_marketing || $display_pro_marketing)  {

                                            echo sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-angellist"></i> %s</a>',
                                                WE_LS_FREE_TRIAL_URL,
                                                ws_ls_generate_site_hash(),
                                                ws_ls_license_get_old_or_new(),
                                                __('Get a free 7 day trial!', WE_LS_SLUG)
                                            );

                                            }

										    if ($display_pro_plus_marketing)  {

                                            $text = __('Upgrade to Pro Plus for', WE_LS_SLUG) . ' &pound;' . $proprice . ' ' . __('a year', WE_LS_SLUG);
                                            $link = WE_LS_UPGRADE_TO_PRO_PLUS_URL;

                                            // If an old Pro license, then offer them 50% off upgrading!
                                            if ( true ===  in_array($license_type, ['pro', 'pro-old']) ) {
                                                $proprice = $proprice / 2;
                                                $text = __('Upgrade to Pro Plus for', WE_LS_SLUG) . ' &pound;' . $proprice . ' ' . __('a year', WE_LS_SLUG) .
                                                    /* xgettext:no-php-format */
                                                    __(' (50% discount)', WE_LS_SLUG);
                                                $link = WE_LS_UPGRADE_TO_PRO_PLUS_UPGRADE_URL;
                                            }

										    echo sprintf('<a href="%s?hash=%s&license=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-plus"></i> <i class="fa fa-plus"></i> %s</a>',
                                                $link,
                                                ws_ls_generate_site_hash(),
                                                ws_ls_license_get_old_or_new(),
                                                $text
                                            );

                                        } ?>


										<?php if ($display_pro_marketing) : ?>
      											<?php $button_text = ('pro-old' === $license_type) ?
												__('Switch to a yearly Pro license for ', WE_LS_SLUG) . '&pound;' . $price . __(' a year', WE_LS_SLUG) :
												__('Upgrade to Pro for ', WE_LS_SLUG) . '&pound;' . $price . __(' a year', WE_LS_SLUG); ?>

											<a href="<?php echo WE_LS_UPGRADE_TO_PRO_URL; ?>?hash=<?php echo ws_ls_generate_site_hash(); ?>" target="_blank" rel="noopener noreferrer" class="button-primary ws-ls-upgrade-button"><i class="fa fa-plus"></i> <?php echo $button_text; ?></a>
										<?php endif; ?>
									</center>
								</div>
							</div>
						</div>
					</div>

					<div id="postbox-container-1" class="postbox-container">

						<div class="meta-box-sortables">

							<div class="postbox">

								<h3 class="hndle"><span><?php echo __('Add or Update License', WE_LS_SLUG); ?></span></h3>

								<div class="inside">

									<form action="<?php echo admin_url('admin.php?page=ws-ls-license&add-license=true'); ?>" method="post">
										<p><?php echo __('Copy and paste the license given to you by YeKen into this box and click "Apply License"', WE_LS_SLUG); ?>.</p>
										<textarea rows="5" style="width:100%"  name="license-key"></textarea>
                                        <br /><br />
                                        <input type="submit" class="button-secondary large-text" value="<?php echo __('Apply License', WE_LS_SLUG); ?>" />
									</form>
								</div>
							</div>
							<div class="postbox">
                                <h3 class="hndle"><span><?php echo __('Your License Information', WE_LS_SLUG); ?></span></h3>
                                <div class="inside">
                                    <table class="ws-ls-sidebar-stats">
                                        <tr>
                                            <th><?php echo __('Site Hash', WE_LS_SLUG); ?></th>
                                            <td><?php echo esc_html( ws_ls_generate_site_hash() ); ?></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo __('Type', WE_LS_SLUG); ?></th>
                                            <td><a href="<?php echo esc_url( WE_LS_LICENSE_TYPES_URL ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $license_name ); ?></a></td>
                                        </tr>
                                        <tr>
                                            <th><?php echo __('Expires', WE_LS_SLUG); ?></th>
                                            <td>
                                                <?php

                                                    if('pro-old' === $license_type) {
                                                        echo __('Never', WE_LS_SLUG);
                                                    } elseif (true === in_array($license_type, ['pro', 'pro-plus'])) {
                                                        echo esc_html( ws_ls_iso_date_into_correct_format( $license_decoded['expiry-date'] ) );
                                                    } else {
                                                        echo __('n/a', WE_LS_SLUG);
                                                    }

                                                ?>
                                            </td>
                                        </tr>
										<?php $existing_license = ws_ls_license_get_old_or_new(); ?>

										<?php if ( false === empty($existing_license)): ?>
											<tr class="last">
	                                            <th colspan="2"><?php echo __('Your Existing License', WE_LS_SLUG); ?></th>
											</tr>
											<tr class="last">
												<td colspan="2"><textarea rows="5" style="width:100%"><?php echo esc_textarea($existing_license); ?></textarea></td>
											</tr>
											<tr class="last">
												<td colspan="2"><a href="<?php echo admin_url('admin.php?page=ws-ls-license&remove-license=true'); ?>" class="button-secondary delete-license"><?php echo __('Remove License', WE_LS_SLUG); ?></a></td>
											</tr>

										<?php endif; ?>
                                    </table>
                                </div>
                            </div>

                            <div class="postbox">
        						<h3 class="hndle"><span><?php echo __('Documentation', WE_LS_SLUG); ?></span></h3>

                                <div class="inside">
                                    <p><?php echo __('Need further help or information, please visit our documentation site:', WE_LS_SLUG); ?></p>
                                    <p><strong><a href="https://weight.yeken.uk" target="_blank" rel="noopener noreferrer">weight.yeken.uk</a></strong></p>
                                    <a href="https://weight.yeken.uk" target="_blank" rel="noopener noreferrer"><img class="widefat" src="<?php echo plugins_url( 'assets/images/weight.yeken.uk.jpg', __FILE__ ); ?>" /></a>
                                </div>
                            </div>
						</div>
					</div>
					<div id="post-body" class="metabox-holder columns-3">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<?php if ($display_pro_plus_marketing): ?>
									<div class="postbox ws-ls-advertise-pro-plus">
										<h3 class="hndle highlight-title">
                                            <?php echo __('Pro Plus Features', WE_LS_SLUG); ?>
	                                    </h3>
	                                   <div style="padding: 0px 15px 0px 15px">
											<div class="inside">
												<p><?php echo __('Below is a list of the intended features of the Pro Plus version', WE_LS_SLUG); ?>. <strong><?php echo __('You can upgrade for', WE_LS_SLUG); ?> &pound;<?php echo $proprice; ?> <?php echo __('a year', WE_LS_SLUG); ?>.</strong> <?php echo ws_ls_url_license_types(); ?>:</p>
												<?php ws_ls_display_features($pro_plus_features); ?>
											</div>
										</div>
									</div>
								<?php endif; ?>
								<?php if ($display_pro_marketing): ?>
									<div class="postbox ws-ls-advertise-pro">
										<h3 class="hndle highlight-title">
                                            <?php echo __('Pro Features', WE_LS_SLUG); ?>
	                                    </h3>
	                                   <div style="padding: 0px 15px 0px 15px">

										<div class="inside">
											<p><?php echo __('Below is a list of the intended features of the Pro version', WE_LS_SLUG); ?>.  <strong><?php echo __('You can upgrade for', WE_LS_SLUG); ?> &pound;<?php echo $price; ?> <?php echo __('a year', WE_LS_SLUG); ?>.</strong> <?php echo ws_ls_url_license_types(); ?>:</p>
											<?php ws_ls_display_features($pro_features); ?>
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

	ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
        __('Are you sure you wish to remove the license for this site? Removing it may cause your user\'s to lose functionality.', WE_LS_SLUG) . '<br /><br />',
        'delete-license');

}

function ws_ls_display_features($features) {


	if (false === empty($features)):
?>
	<table class="form-table" >
		<?php

		$class = '';

		foreach ($features as $feature) {

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
<?php

	endif;
}

 ?>
