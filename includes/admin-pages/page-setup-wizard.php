<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_setup_wizard_page() {

	?>
	<div class="wrap ws-ls-admin-page">

		<div id="icon-options-general" class="icon32"></div>

		<div id="poststuff">

			<div id="post-body" class="metabox-holder columns-3">

				<!-- main content -->
				<div id="post-body-content">

					<div class="meta-box-sortables ui-sortable">

						<div class="postbox">
							<h3 class="hndle"><span><?php echo __( 'Setup Wizard', WE_LS_SLUG); ?> </span></h3>
							<div style="padding: 15px 15px 0px 15px">
                                <div id="ws-ls-tabs">
                                    <ul>
                                        <li><a>1. <?php echo __( 'Introduction', WE_LS_SLUG); ?><span><?php echo __( 'Thank you for using Weight Tracker', WE_LS_SLUG); ?></span></a></li>
                                        <li><a>2. <?php echo __( 'Setup', WE_LS_SLUG); ?><span><?php echo __( 'How to use Shortcodes and Widgets', WE_LS_SLUG); ?></span></a></li>
                                        <li><a>3. <?php echo __( 'Admin Interface', WE_LS_SLUG); ?><span><?php echo __( 'Viewing and interacting with your user\'s data', WE_LS_SLUG); ?></span></a></li>
                                        <li><a>4. <?php echo __( 'Features and Guides', WE_LS_SLUG); ?><span><?php echo __( 'Useful features and guides', WE_LS_SLUG); ?></span></a></li>
                                        <li><a>5. <?php echo __( 'Customisations', WE_LS_SLUG); ?><span><?php echo __( 'Custom modifications to Weight Tracker', WE_LS_SLUG); ?></span></a></li>
                                    </ul>
                                    <div>
                                        <div>
                                            <h3>Thank you</h3>
                                            <p>First of all, <strong>thank you</strong> for installing Weight Tracker on your website! Weight Tracker extends your website by giving your users the ability to track their weight, measurements and other custom defined data.
                                                The aim of the plugin is to allow you to extend your site with out-the-box functionality with minimal technical ability.</p>

                                            <p>The setup wizard should give you an overview of the plugin and how to set Weight Tracker up on your website.</p>

                                            <h3>Features of Weight Tracker</h3>
                                            <p>For a full list of Weight Tracker features, please visit our documentation site:</p>
                                            <a href="https://weight.yeken.uk/features/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Weight Tracker Features</a>

                                            <h3>Settings</h3>
                                            <p>The Weight Tracker plugin is highly configurable, you should review and modify the settings to suit your needs.</p>

                                            <a href="<?php echo ws_ls_get_link_to_settings(); ?>" class="button"><i class="fa fa-link"></i> View Settings Page</a>

                                            <h3>Documentation</h3>
                                            <p>Further help and documentation, please visit our documentation site:</p>
                                            <a href="https://weight.yeken.uk" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Visit Documentation Site</a>
                                        </div>
                                        <div>
                                            <p>Out of the box, Weight Tracker does not extend the public facing side of your site with features that allow the users of your site to interact with.
                                                You need to build these by using Weight Tracker <a href="https://weight.yeken.uk/shortcodes/" target="_blank" rel="noopener">shortcodes</a> and <a href="https://weight.yeken.uk/widgets/" target="_blank" rel="noopener">widgets</a>.

                                            <h3>Shortcodes</h3>
                                            <h4>What are they?</h4>
                                            <p>Shortcodes are a feature of WordPress that allow site administrators to extend the functionality of their site
                                                by simply placing shortcodes in page and post content. When the page or post is published, the shortcode is replaced with the relevant features.
                                                For example, a standard WordPress shortcode for creating a gallery is
                                                [gallery]. If you are unsure about shortcodes and their use, you should consider reading <a href="https://en.support.wordpress.com/shortcodes/" target="_blank" rel="noopener">WordPress's documentation</a>.
                                            </p>
                                            <h4>Weight Tracker shortcodes</h4>
                                            <p><img src="<?php echo plugins_url( 'assets/images/shortcode-example.png', __FILE__ ); ?>" style="margin-right:20px" align="left" class="setup-wizard-image"/>
                                                Weight Tracker ships with nearly 30 WordPress shortcodes. These  <a href="https://en.support.wordpress.com/shortcodes/" target="_blank" rel="noopener">shortcodes</a> render the required forms, graphs, interfaces, data, photos, etc that power Weight Tracker and the tools that will enrich your user's experience.</p>
                                            <p>It's highly recommended that you browse the <a href="https://en.support.wordpress.com/shortcodes/" target="_blank" rel="noopener">Weight Tracker shortcode documentation</a> to get an idea of the features you can build.</p>
                                            <p><strong>A simple, one page example</strong></p>
                                            <p>A simple example is to create a page and add the [wlt] shortcode into the content. The [wlt] is the most commonly used shortcode and encompasses the majority of the front end functionality that your user may require.</p>
                                            <p><strong>A little more complex</strong></p>
                                            <p>Using the [wlt] on a single page may not be the best option for your site and you may wish to create separate pages for various parts of functionality. Another route is to create seperate pages and use different Weight Tracker shortcodes. For example:</p>

                                            <table cellspacing="5">
                                                <tr>
                                                    <th width="100" align="left">Page</th>
                                                    <th>Shortcodes to use</th>
                                                </tr>
                                                <tr>
                                                    <td>Add an entry</td>
                                                    <td><a href="https://weight.yeken.uk/shortcodes/?section=weight-loss-tracker-form" target="_blank" rel="noopener">[wlt-form]</a></td>
                                                </tr>
                                                <tr>
                                                    <td>Your entries</td>
                                                    <td><a href="https://weight.yeken.uk/shortcodes/?section=wlt-chart" target="_blank" rel="noopener">[wlt-chart]</a>,
                                                        <a href="https://weight.yeken.uk/shortcodes/?section=wlt-chart" target="_blank" rel="noopener">[wlt-table]</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Your Photos</td>
                                                    <td><a href="https://weight.yeken.uk/shortcodes/?section=wlt-gallery" target="_blank" rel="noopener">[wlt-gallery]</a></td>
                                                </tr>
                                                <tr>
                                                    <td>Macronutrients</td>
                                                    <td>
                                                        <a href="https://weight.yeken.uk/shortcodes/?section=wlt-macronutrients-table" target="_blank" rel="noopener">[wlt-macronutrients]</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Your awards</td>
                                                    <td><a href="https://weight.yeken.uk/shortcodes/?section=wlt-awards-grid" target="_blank" rel="noopener">[wlt-awards-grid</a></td>
                                                </tr>
                                                <tr>
                                                    <td>Your settings</td>
                                                    <td><a href="https://weight.yeken.uk/shortcodes/?section=wlt-user-settings" target="_blank" rel="noopener">[wlt-user-settings]</a></td>
                                                </tr>
                                            </table>

                                            <p>The shortcodes cover the majority of Weight Tracker functionality and are designed to be flexible. For further information, please refer to the shortcode documentation:</p>
                                            <p>
                                                <a href="https://weight.yeken.uk/shortcodes/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> View avaliable shortcodes</a>
                                            </p>

                                            <h3>Widgets</h3>

                                            <p>Like shortcodes, WordPress widgets allow you to drag and drop "Widgets" into sidebars. As with shortcodes, if you aren't
                                                too sure how they work in WordPress, it's worth <a href="https://wordpress.org/support/article/wordpress-widgets/" target="_blank" rel="noopener">reading their documentation</a>.</p>

                                            <p>Weight Tracker has three Widgets that you can make use of, Entry Forms, Charts and Progress bars.</p>

                                            <p>Further help and documentation can be found on our website:</p>
                                            <a href="https://weight.yeken.uk/widgets/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> View Available Widgets</a>
                                            <br clear="all" />
                                        </div>
                                        <div>
                                            <p>
                                                <img src="<?php echo plugins_url( 'assets/images/user-data.png', __FILE__ ); ?>" style="margin-right:20px" align="left" class="setup-wizard-image"/>
                                                Weight Tracker contains an extensive admin section for viewing and manipulating your user's data. You can access it from the WordPress admin menu by navigating to Weight Tracker > <a href="<?php echo ws_ls_get_link_to_user_data(); ?>">Manage User Data</a>
                                            </p>

                                                <p>
                                                    <a href="<?php echo ws_ls_get_link_to_user_data(); ?>" class="button"><i class="fa fa-link"></i> View User Data</a>
                                                </p>

                                        </div>
                                        <div>
                                            <p>Weight Tracker has a lot of features, below is a list of some of the most popular:</p>

                                            <h4>Advanced Calculations</h4>
                                            <p>Support for BMR, Harris Benedict Formula, Calorie intake to lose and gain weight, Meal breakdowns, Macronutrient Calculators and more.</p>
                                            <a href="https://weight.yeken.uk/calculations/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>

                                            <h4>Custom Fields</h4>
                                            <p>Custom fields allow a site administrator to ask custom questions of their users when they complete a weight entry. For example, you may want to ask the user “How many cups of water have you drunk today?” or “Did you stick to your diet today?”. From the WP Dashboard, you will see a new option under “Weight Tracker” called “Custom Fields”. From here, you can see a list of all Custom Fields that have been added and have the ability to add, edit and delete them.</p>
                                            <a href="https://weight.yeken.uk/custom-fields/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>

                                            <h4>Awards</h4>
                                            <p>As of version 7.0, Pro Plus sites have the ability to give users awards for reaching defined goals. Currently you can set awards for change in Weight, change in Weight %, change in BMI and a BMI being met.</p>
                                            <a href="https://weight.yeken.uk/awards/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>

                                            <h4>Gravity Forms</h4>
                                            <p>Weight Tracker, can examine Gravity Form submissions for relevant Weight and Measurement data. If valid data is found, a weight entry will automatically be created for the user currently logged in. This allows you to mix Weight Tracker fields amongst your Gravity Forms to provide a more tailored experience.</p>
                                            <a href="https://weight.yeken.uk/gravity-forms/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>

                                            <h4>Groups</h4>
                                            <p>As of 7.0, site administrators will have the ability to create “Groups” to assign their user’s to. This allows Weight Difference to be calculated on a group basis and displayed on the “Manage User Data” summary page.</p>
                                            <a href="https://weight.yeken.uk/groups/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>

                                            <h4>Email Notifications</h4>
                                            <p>If enabled, one or more administrators can be notified by email when a user adds or updates their target weight, adds a weight entry or updates a weight entry.</p>
                                            <a href="https://weight.yeken.uk/email-notifications/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>

                                            <h4>Hooks and Filters</h4>
                                            <p>The plugin can be manipulated by various hooks and filters.</p>
                                            <a href="https://weight.yeken.uk/hooks-and-filters/" target="_blank" rel="noopener" class="button"><i class="fa fa-link"></i> Read more</a>


                                        </div>
                                        <div>
											<?php wl_ls_setup_wizard_custom_notification_html(); ?>
                                        </div>

                                    </div>
                                </div>
								<br clear="both"/>
                                <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=ws-ls-help&hide-setup-wizard=y') ); ?>" class="button button-primary"><i class="fa fa-check"></i> I've finished - hide the wizard!</a></p>

                            </div>
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
