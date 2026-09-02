<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render the "Upgrade to Premium" admin page.
 *
 * This only ever shows when the Premium plugin isn't installed (see the
 * has_filter( 'wlt-filter-is-premium-active' ) check in includes/hooks.php) -
 * once Premium is active, it registers its own "Your License" page on this
 * same menu slug instead (weight-tracker-premium/includes/page-license.php),
 * which also handles applying/removing a license key.
 */
function ws_ls_upgrade_page() {

	$price = ws_ls_license_premium();
	?>
	<div class="wrap ws-ls-admin-page">
		<div id="icon-options-general" class="icon32"></div>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h3 class="hndle">
								<span><?php echo esc_html__( 'Upgrade to Weight Tracker Premium', WE_LS_SLUG ); ?></span>
							</h3>
							<div class="inside">
								<p>
									<?php echo esc_html__( 'The Weight Tracker Premium provides an array of enhanced features, such as BMR calculation, calorie intake tracking, and a macronutrient calculator, designed to help both you and your members manage weight more effectively. By purchasing a licence, you are not only unlocking these additional features but also supporting the continued development of this plugin — your support is truly appreciated.', WE_LS_SLUG ); ?>
								</p>
								<center>
									<h3><?php echo esc_html__( 'In case you need, your Site Hash is', WE_LS_SLUG ); ?>: <?php ws_ls_echo( ws_ls_generate_site_hash() ); ?></h3>
									<?php
										$button_html = sprintf( '<a href="%s?hash=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-clock-o"></i> %s</a>',
											WE_LS_FREE_TRIAL_URL,
											ws_ls_generate_site_hash(),
											esc_html__( 'Get a free 14 day trial', WE_LS_SLUG ) );

										ws_ls_echo_wp_kses( $button_html );

										$text = sprintf( '%s  &pound;%s %s', esc_html__( 'Upgrade to Premium for', WE_LS_SLUG ), $price, esc_html__( 'a year', WE_LS_SLUG ) );

										$button_html = sprintf( '<a href="%s?hash=%s" rel="noopener noreferrer" target="_blank" class="button-primary ws-ls-upgrade-button"><i class="fa fa-plus"></i> %s</a>',
											WE_LS_UPGRADE_TO_PREMIUM_URL,
											ws_ls_generate_site_hash(),
											$text );

										ws_ls_echo_wp_kses( $button_html );
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
								<span><?php echo esc_html__( 'Documentation', WE_LS_SLUG ); ?></span>
							</h3>
							<div class="inside">
								<p><?php echo esc_html__( 'For additional assistance or details, please visit our documentation site.', WE_LS_SLUG ); ?></p>
								<p><strong><a href="https://docs.yeken.uk" target="_blank" rel="noopener noreferrer">docs.yeken.uk</a></strong> or <a href="https://weighttracker.yeken.uk" target="_blank" rel="noopener noreferrer">weighttracker.yeken.uk</a></strong></p>
								<a href="https://docs.yeken.uk" target="_blank" rel="noopener noreferrer"><img class="widefat" src="<?php ws_ls_echo( plugins_url( 'assets/images/weight-yeken-uk.png', __FILE__ ) ); ?>" /></a>
							</div>
						</div>
					</div>
				</div>
				<div id="post-body" class="metabox-holder columns-3">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<div class="postbox ws-ls-advertise-pro-plus">
								<h3 class="hndle highlight-title">
									<?php echo esc_html__( 'Go Premium for', WE_LS_SLUG ); ?> &pound;<?php ws_ls_echo( WE_LS_PREMIUM_PRICE ); ?> <?php echo esc_html__( 'a year', WE_LS_SLUG ); ?>.
								</h3>
								<div style="padding: 0px 15px 0px 15px">
									<div class="inside">
										<h3><?php echo esc_html__( 'The following features are included with a Premium subscription', WE_LS_SLUG ); ?>:</h3>
										<?php ws_ls_display_features( ws_ls_feature_list_premium() ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
	<?php
}
