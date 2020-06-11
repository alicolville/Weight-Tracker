<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_settings_page_generic() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
	}

	$disable_if_not_pro_class = (WS_LS_IS_PRO) ? '' : 'ws-ls-disabled';
    $disable_if_not_pro_plus_class = (WS_LS_IS_PRO_PLUS) ? '' : 'ws-ls-disabled-pro-plus';

  	$clear_cache = ( isset($_GET['settings-updated']) && 'true' == $_GET['settings-updated'] ) ? true : false;

	if ( true === is_admin() && false === empty( $_GET['recreatetables'] ) ) {

		do_action('ws-ls-rebuild-database-tables');

		$clear_cache = true;
	}

	if( $clear_cache ) {
		ws_ls_delete_all_cache();
	}

	?>
	<div class="wrap ws-ls-admin-page">

<?php

	$mysql_table_check = ws_ls_admin_check_mysql_tables_exist();

	if ( false !== $mysql_table_check ): ?>
		<div class="error">
			<p><?php echo $mysql_table_check; ?></p>
 			<p><a href="<?php echo get_permalink() . '?page=ws-ls-settings';  ?>&amp;recreatetables=y"><?php echo __('Rebuild them now', WE_LS_SLUG); ?></a></p>
		</div>
	<?php
	endif;

?>


	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3 ws-ls-settings">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">


						<h3 class="hndle"><span><?php echo __( WE_LS_TITLE . ' Settings', WE_LS_SLUG); ?></span></h3>

						<div class="inside">

							<form method="post" action="options.php">
								<?php

									settings_fields( 'we-ls-options-group' );
									do_settings_sections( 'we-ls-options-group' );

								?>

								<div id="ws-ls-tabs">
									<ul>
                                        <li><a><?php echo __( 'General', WE_LS_SLUG); ?><span><?php echo __( 'General settings', WE_LS_SLUG); ?></span></a></li>
                                        <li><a><?php echo __( 'Pro Plus', WE_LS_SLUG); ?><span><?php echo __( 'Adjust settings for your Pro Plus features', WE_LS_SLUG); ?></span></a></li>
                                        <li><a><?php echo __( 'Chart', WE_LS_SLUG); ?><span><?php echo __( 'Chart styling and config', WE_LS_SLUG); ?></span></a></li>
										<li><a><?php echo __( 'Notifications', WE_LS_SLUG); ?><span><?php echo __( 'Configure email notifications', WE_LS_SLUG); ?></span></a></li>
                                        <li><a><?php echo __( 'Measurements', WE_LS_SLUG); ?><span><?php echo __( 'Allow users to record their measurements', WE_LS_SLUG); ?></span></a></li>
                                        <li><a><?php echo __( '3rd Party', WE_LS_SLUG); ?><span><?php echo __( '3rd Party Integrations', WE_LS_SLUG); ?></span></a></li>
                                    </ul>
									<div>
										<div>
                                                <?php
                                                    if ( false === WS_LS_IS_PRO ) {
                                                        ws_ls_display_pro_upgrade_notice();
                                                    }
                                                ?>

                                                <h3><?php echo __( 'Caching' , WE_LS_SLUG); ?></h3>
                                                <table class="form-table">
                                                    <tr>
                                                        <th scope="row"><?php echo __( 'Enable Caching?' , WE_LS_SLUG); ?></th>
                                                        <td>
                                                            <select id="ws-ls-caching" name="ws-ls-caching">
                                                                <option value="yes" <?php selected( get_option('ws-ls-caching'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                                <option value="no" <?php selected( get_option('ws-ls-caching'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                            </select>
                                                            <p><?php echo __('If enabled, additional caching will be performed to reduce database queries. It is highly recommended that this remains enabled and only disabled for testing or to enable other caching mechanisms.', WE_LS_SLUG); ?></p>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <h3><?php echo __( 'Default units / formats to be used by plugin' , WE_LS_SLUG); ?></h3>
												<table class="form-table">
													<tr>
														<th scope="row"><?php echo __( 'Weight Units' , WE_LS_SLUG); ?></th>
														<td>
															<select id="ws-ls-units" name="ws-ls-units">
																<option value="kg" <?php selected( get_option('ws-ls-units'), 'kg' ); ?>><?php echo __('kg', WE_LS_SLUG); ?></option>
																<option value="stones_pounds" <?php selected( get_option('ws-ls-units'), 'stones_pounds' ); ?>><?php echo __('Stones & Pounds', WE_LS_SLUG); ?></option>
																<option value="pounds_only" <?php selected( get_option('ws-ls-units'), 'pounds_only' ); ?>><?php echo __('Pounds', WE_LS_SLUG); ?></option>
															</select>
															<p><?php echo __('You can specify whether to display weights in Kg, Stones & Pounds or just Pounds. Please note: The graph will be displayed in Pounds if "Stones & Pounds" is selected.', WE_LS_SLUG);?></p>
														</td>
													</tr>
													<tr>
														<th scope="row"><?php echo __( 'UK or US Date format?' , WE_LS_SLUG); ?></th>
														<td>
															<select id="ws-ls-use-us-dates" name="ws-ls-use-us-dates">
																<option value="uk" <?php selected( get_option('ws-ls-use-us-dates'), 'uk' ); ?>><?php echo __('UK (DD/MM/YYYY)', WE_LS_SLUG); ?></option>
																<option value="us" <?php selected( get_option('ws-ls-use-us-dates'), 'us' ); ?>><?php echo __('US (MM/DD/YYYY)', WE_LS_SLUG); ?></option>
															</select>
															<p><?php echo __('Specify what format dates should be displayed in (i.e. UK or US format)', WE_LS_SLUG); ?></p>
														</td>
													</tr>
                                                </table>
                                                <h3><?php echo __( 'User Experience' , WE_LS_SLUG); ?></h3>
                                                <table class="form-table">
													<tr>
                                                        <th scope="row"><?php echo __( 'Allow target weights?' , WE_LS_SLUG); ?></th>
                                                        <td>
                                                            <select id="ws-ls-allow-targets" name="ws-ls-allow-targets">
                                                                <option value="yes" <?php selected( get_option('ws-ls-allow-targets'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                                <option value="no" <?php selected( get_option('ws-ls-allow-targets'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                            </select>
                                                            <p><?php echo __('If enabled, a user is allowed to enter a target weight. This will be displayed as a horizontal bar on the line chart.', WE_LS_SLUG); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                        <th scope="row"><?php echo __( 'Display BMI in tables?' , WE_LS_SLUG); ?></th>
                                                        <td>
                                                            <select id="ws-ls-display-bmi-in-tables" name="ws-ls-display-bmi-in-tables">
                                                                <option value="yes" <?php selected( get_option('ws-ls-display-bmi-in-tables'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                                <option value="no" <?php selected( get_option('ws-ls-display-bmi-in-tables'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            </select>
                                                            <p><?php echo __('If enabled, BMI values will be displayed alongside weight entries in data tables.', WE_LS_SLUG)?></p>
                                                        </td>
                                                    </tr>
                                                    <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                        <th scope="row"><?php echo __( 'Allow user settings' , WE_LS_SLUG); ?></th>
                                                        <td>
                                                            <select id="ws-ls-allow-user-preferences" name="ws-ls-allow-user-preferences">
                                                                <option value="yes" <?php selected( get_option('ws-ls-allow-user-preferences'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                                <option value="no" <?php selected( get_option('ws-ls-allow-user-preferences'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            </select>
                                                            <p><?php echo __('Allow your users to select their own data units, complete their "About You" fields and remove all their data.', WE_LS_SLUG)?></p>
                                                        </td>
                                                    </tr>
                                                    <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                        <th scope="row"><?php echo __( '"About You" fields mandatory?' , WE_LS_SLUG); ?></th>
                                                        <td>
                                                            <select id="ws-ls-about-you-mandatory" name="ws-ls-about-you-mandatory">
                                                                <option value="no" <?php selected( get_option('ws-ls-about-you-mandatory'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                                <option value="yes" <?php selected( get_option('ws-ls-about-you-mandatory'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                            </select>
                                                            <p><?php echo __('If User Settings is enabled, should all the "About You" (height, activity level, etc) be mandatory?', WE_LS_SLUG)?></p>
                                                        </td>
                                                    </tr>
											</table>

                                            <h3><?php echo __( 'Photos' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Max. Photo Size?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-photos-max-size" name="ws-ls-photos-max-size">
															<?php

															$max_size = ws_ls_file_upload_max_size();

															$current_size = ws_ls_photo_max_upload_size();

															foreach ( ws_ls_photo_get_sizes() as $size => $label ) {

																if ( $size < $max_size ) {
																	printf('<option value="%s" %s>%s</option>',
																		$size,
																		selected( $size, $current_size, false ),
																		$label
																	);
																}
															}
															?>
                                                        </select>
                                                        <p><?php echo sprintf( '%s %s <a href="%s">%s</a>' ,
                                                                                        __('Maximum photo size (in MB) that is allowed to be uploaded. ' , WE_LS_SLUG),
                                                                                        __('This is used as part of Custom Fields.' , WE_LS_SLUG),
		                                                                                ws_ls_meta_fields_base_url(),
		                                                                                __('View Custom Fields' , WE_LS_SLUG)
                                                            ); ?></em></p>
                                                    </td>
                                                </tr>
                                            </table>
											<h3><?php echo __( 'Number formatting' , WE_LS_SLUG); ?></h3>
											<table class="form-table">
												<tr>
													<th scope="row"><?php echo __( 'Include thousand separator?' , WE_LS_SLUG); ?></th>
													<td>
														<?php
															$include_separator = get_option( 'ws-ls-number-formatting-separator', 'yes' );
														?>
														<select id="ws-ls-number-formatting-separator" name="ws-ls-number-formatting-separator">
															<option value="yes" <?php selected( $include_separator, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
															<option value="no" <?php selected( $include_separator, 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
														</select>
														<p><?php echo __('If enabled, larger numbers shall be split up by commas e.g. 2,300 instead of 2300', WE_LS_SLUG); ?>.</p>
													</td>
												</tr>
											</table>
                                            <h3><?php echo __( 'Permissions' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Who can view and modify user data?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-edit-permissions" name="ws-ls-edit-permissions">
                                                            <option value="manage_options" <?php selected( get_option('ws-ls-edit-permissions'), 'manage_options' ); ?>><?php echo __('Administrators Only', WE_LS_SLUG)?></option>
                                                            <option value="read_private_posts" <?php selected( get_option('ws-ls-edit-permissions'), 'read_private_posts' ); ?>><?php echo __('Editors and above', WE_LS_SLUG)?></option>
                                                            <option value="publish_posts" <?php selected( get_option('ws-ls-edit-permissions'), 'publish_posts' ); ?>><?php echo __('Authors and above', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('Specify the minimum level of user role that maybe view or edit user data.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <h3><?php echo __( 'Groups' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Enable Groups?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-enable-groups" name="ws-ls-enable-groups">
                                                            <option value="yes" <?php selected( get_option('ws-ls-enable-groups'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                            <option value="no" <?php selected( get_option('ws-ls-enable-groups'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('Add the ability to place your user\'s into groups.', WE_LS_SLUG)?></p>
                                                        <br /><p><a href="<?php echo ws_ls_groups_link(); ?>" class="button"><?php echo __( 'Manage User Groups' , WE_LS_SLUG); ?></a></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Allow user\'s to edit their own group?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-enable-groups-user-edit" name="ws-ls-enable-groups-user-edit">
                                                            <option value="no" <?php selected( get_option('ws-ls-enable-groups-user-edit'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( get_option('ws-ls-enable-groups-user-edit'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                            <h3><?php echo __( 'Birthday Emails' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Enable?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-enable-birthdays" name="ws-ls-enable-birthdays">
                                                            <option value="no" <?php selected( get_option('ws-ls-enable-birthdays'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( get_option('ws-ls-enable-birthdays'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('If enabled, a Happy Birthday email shall be sent to users on their birthday.', WE_LS_SLUG)?></p>

                                                    </td>
                                                </tr>
                                            </table>
                                            <h3><?php echo __( 'Advanced' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Disable plugin CSS?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-disable-css" name="ws-ls-disable-css">
                                                            <option value="no" <?php selected( get_option('ws-ls-disable-css'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( get_option('ws-ls-disable-css'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('If you wish to style the forms in your own way, you can use this option to disable WLT\'s style sheets.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Disable notifications from YeKen?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-disable-yeken-notifications" name="ws-ls-disable-yeken-notifications">
                                                            <option value="no" <?php selected( get_option('ws-ls-disable-yeken-notifications'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( get_option('ws-ls-disable-yeken-notifications'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('Occasionally YeKen likes to display simple notifications within your WordPress dashboard. Use this setting if you wish to disable them.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
                                            </table>
										</div>
										<div>
                                            <?php
                                                if ( false === WS_LS_IS_PRO_PLUS ) {
                                                    ws_ls_display_pro_upgrade_notice();
                                                }
                                            ?>

                                            <h3><?php echo __( 'Challenges' , WE_LS_SLUG); ?></h3>

                                            <table class="form-table">
                                               <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Enabled?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-challenges-enabled" name="ws-ls-challenges-enabled">
                                                            <option value="yes" <?php selected( get_option('ws-ls-challenges-enabled'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                            <option value="no" <?php selected( get_option('ws-ls-challenges-enabled'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('If set to No, challenge data will not be calculated in the background.' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                               </tr>
                                            </table>

                                            <h3><?php echo __( 'Calculating daily calorie intake to lose weight' , WE_LS_SLUG); ?></h3>

                                            <table class="form-table">
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Show Loss figures?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-cal-show-loss" name="ws-ls-cal-show-loss">
                                                            <option value="yes" <?php selected( get_option('ws-ls-cal-show-loss'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                            <option value="no" <?php selected( get_option('ws-ls-cal-show-loss'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('Show loss figures to your users? For example, if your site is aimed at muscle building, you may wish not to.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
                                                 <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Female Calorie Cap' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <input  type="number"  step="any" min="0" max="5000" name="ws-ls-female-cal-cap" id="ws-ls-female-cal-cap" value="<?php esc_attr_e(WS_LS_CAL_CAP_FEMALE); ?>" size="11" />
                                                        <p><?php echo __('Specify a maximum value for number of daily calories allowed to achieve weight loss. As per NHS guidelines, females are set to 1400kcal by default', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Male Calorie Cap' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <input  type="number"  step="any" min="0" max="5000" name="ws-ls-male-cal-cap" id="ws-ls-male-cal-cap" value="<?php esc_attr_e(WS_LS_CAL_CAP_MALE); ?>" size="11" />
														<p><?php echo __('Specify a maximum value for number of daily calories allowed to achieve weight loss. As per NHS guidelines, males are set to 1900kcal by default', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p></td>
                                                </tr>
											    <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Calories to subtract' , WE_LS_SLUG); ?></th>
                                                    <?php

														$subtract_ranges = ( true === WS_LS_IS_PRO_PLUS ) ? ws_ls_harris_benedict_calorie_subtract_ranges() : [];
                                                    ?>
                                                    <td>
														<p><?php echo __( 'Once the daily calorie intake to maintain weight has been established, use the following table to define how many calories should be subtracted for the user to lose weight. You have the ability to set up ranges - if the user\'s calorie intake figure to maintain weight lands within that range you have the ability to specify whether to subtract a fixed number of calories or a percentage of the calorie intake.
														' , WE_LS_SLUG); ?></p>
														<br />
														<table class="widefat ws-ls-calories-modify-table">
															<thead>
																<tr>
																	<th class="row-title"><?php echo __( 'Status' , WE_LS_SLUG); ?></th>
																	<th><?php echo __( 'Apply to' , WE_LS_SLUG); ?></th>
																	<th><?php echo __( 'From (Kcal)' , WE_LS_SLUG); ?></th>
																	<th><?php echo __( 'To (Kcal)' , WE_LS_SLUG); ?></th>
																	<th><?php echo __( 'Fixed calories / percentage to subtract' , WE_LS_SLUG); ?></th>
																	<th><?php echo __( 'Fixed / Percentage' , WE_LS_SLUG); ?></th>
																</tr>
															</thead>
															<tbody>
															<?php

																foreach ( $subtract_ranges as $range ) {

																	printf(
																		'<tr class="%14$s">
																					<td>
																						<select id="%1$s-enabled" name="%1$s-enabled">
																							<option value="1" %15$s>%16$s</option>
																							<option value="0" %17$s>%18$s</option>
																						</select>
																					</td>
																					<td>
																						<select id="%1$s-gender" name="%1$s-gender">
																							<option value="0" %8$s>%9$s</option>
																							<option value="1" %10$s>%11$s</option>
																							<option value="2" %12$s>%13$s</option>
																						</select>
																					</td>
																					<td>
																						<input type="number" step="any" min="0" max="9999" name="%1$s-from" id="%1$s-from" value="%2$d" size="5" />
																					</td>
																					<td>
																						<input type="number" step="any" min="0" max="9999" name="%1$s-to" id="%1$s-to" value="%3$d" size="5" />
																					</td>
																					<td>
																						<input type="number" step="any" min="0" max="9999" name="%1$s" id="%1$s" value="%4$d" size="5" />
																					</td>
																					<td>
																						<select id="%1$s-unit" name="%1$s-unit">
																							<option value="fixed" %5$s>%6$s</option>
																							<option value="percentage" %7$s>%%</option>
																						</select>
																					</td>
																				</tr>',
																				esc_attr( $range[ 'name' ] ),
																				( float ) $range[ 'from' ],
																				( float ) $range[ 'to' ],
																				( float ) $range[ 'amount' ],
																				selected(  $range[ 'unit' ], 'fixed', false ),
																				__( 'Fixed Calories', WE_LS_SLUG ),
																				selected(  $range[ 'unit' ], 'percentage', false ),
																				selected(  $range[ 'gender' ], '', false ),
																				__( 'Everyone', WE_LS_SLUG ),
																				selected(  $range[ 'gender' ], '1', false ),
																				__( 'Females Only', WE_LS_SLUG ),
																				selected(  $range[ 'gender' ], '2', false ),
																				__( 'Males Only', WE_LS_SLUG ),
																				( true === empty( $range[ 'default' ] ) ) ? 'ws-ls-calorie-subtract-ranges-rows' : '',
																				selected(  $range[ 'enabled' ], '1', false ),
																				__( 'Enabled', WE_LS_SLUG ),
																				selected(  $range[ 'enabled' ], '0', false ),
																				__( 'Disabled', WE_LS_SLUG )
																	);
																}

															?>
															</tbody>
														</table>
														<br />
														<p><a class="button ws-ls-calorie-subtract-ranges-show-more"><?php echo __( 'Show more rows' , WE_LS_SLUG); ?></a></p>
														 <p><?php echo __('Please note, there is no validation around these ranges. Each range will be considered and the first successful match shall be applied - others will be ignored', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                                </tr>

                                            </table>

                                            <h3><?php echo __( 'Calculating daily calorie intake to gain weight' , WE_LS_SLUG); ?></h3>

                                            <table class="form-table">
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Show Gain figures?' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-cal-show-gain" name="ws-ls-cal-show-gain">
                                                            <option value="no" <?php selected( get_option('ws-ls-cal-show-gain'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( get_option('ws-ls-cal-show-gain'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('Show gain figures to your users? For example, if your site is aimed at weight loss only, you may wish not to.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
												<tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
													<th scope="row"><?php echo __( 'Calories to add' , WE_LS_SLUG); ?></th>
													<?php

													$add_ranges = ( true === WS_LS_IS_PRO_PLUS ) ? ws_ls_harris_benedict_calorie_add_ranges() : [];
													?>
													<td>
														<p><?php echo __( 'Once the daily calorie intake to maintain weight has been established, use the following table to define how many calories should be subtracted for the user to gain weight. You have the ability to set up ranges - if the user\'s calorie intake figure to maintain weight lands within that range you have the ability to specify whether to add a fixed number of calories or a percentage of the calorie intake.
														' , WE_LS_SLUG); ?></p>
														<br />
														<table class="widefat ws-ls-calories-modify-table">
															<thead>
															<tr>
																<th class="row-title"><?php echo __( 'Status' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'Apply to' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'From (Kcal)' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'To (Kcal)' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'Fixed calories / percentage to add' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'Fixed / Percentage' , WE_LS_SLUG); ?></th>
															</tr>
															</thead>
															<tbody>
															<?php

															foreach ( $add_ranges as $range ) {

																printf(
																	'<tr class="%14$s">
																					<td>
																						<select id="%1$s-enabled" name="%1$s-enabled">
																							<option value="1" %15$s>%16$s</option>
																							<option value="0" %17$s>%18$s</option>
																						</select>
																					</td>
																					<td>
																						<select id="%1$s-gender" name="%1$s-gender">
																							<option value="0" %8$s>%9$s</option>
																							<option value="1" %10$s>%11$s</option>
																							<option value="2" %12$s>%13$s</option>
																						</select>
																					</td>
																					<td>
																						<input type="number" step="any" min="0" max="9999" name="%1$s-from" id="%1$s-from" value="%2$d" size="5" />
																					</td>
																					<td>
																						<input type="number" step="any" min="0" max="9999" name="%1$s-to" id="%1$s-to" value="%3$d" size="5" />
																					</td>
																					<td>
																						<input type="number" step="any" min="0" max="9999" name="%1$s" id="%1$s" value="%4$d" size="5" />
																					</td>
																					<td>
																						<select id="%1$s-unit" name="%1$s-unit">
																							<option value="fixed" %5$s>%6$s</option>
																							<option value="percentage" %7$s>%%</option>
																						</select>
																					</td>
																				</tr>',
																	esc_attr( $range[ 'name' ] ),
																	( float ) $range[ 'from' ],
																	( float ) $range[ 'to' ],
																	( float ) $range[ 'amount' ],
																	selected(  $range[ 'unit' ], 'fixed', false ),
																	__( 'Fixed Calories', WE_LS_SLUG ),
																	selected(  $range[ 'unit' ], 'percentage', false ),
																	selected(  $range[ 'gender' ], '', false ),
																	__( 'Everyone', WE_LS_SLUG ),
																	selected(  $range[ 'gender' ], '1', false ),
																	__( 'Females Only', WE_LS_SLUG ),
																	selected(  $range[ 'gender' ], '2', false ),
																	__( 'Males Only', WE_LS_SLUG ),
																	( true === empty( $range[ 'default' ] ) ) ? 'ws-ls-calorie-add-ranges-rows' : '',
																	selected(  $range[ 'enabled' ], '1', false ),
																	__( 'Enabled', WE_LS_SLUG ),
																	selected(  $range[ 'enabled' ], '0', false ),
																	__( 'Disabled', WE_LS_SLUG )
																);
															}

															?>
															</tbody>
														</table>
														<br />
														<p><a class="button ws-ls-calorie-add-ranges-show-more"><?php echo __( 'Show more rows' , WE_LS_SLUG); ?></a></p>
														<p><?php echo __('Please note, there is no validation around these ranges. Each range will be considered and the first successful match shall be applied - others will be ignored', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
													</td>
												</tr>

                                            </table>

                                            <h3><?php echo __( 'Macronutrient Calculator' , WE_LS_SLUG); ?></h3>

                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Proteins' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <input  type="number"  step="any" min="0" max="100" name="ws-ls-macro-proteins" id="ws-ls-macro-proteins" class="ws-ls-macro" value="<?php esc_attr_e(WS_LS_MACRO_PROTEINS); ?>" size="3" />%
                                                        <p><?php echo __('Percentage of Proteins to make up a moderate diet', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calorie calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Carbohydrates' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <input  type="number"  step="any" min="0" max="100" name="ws-ls-macro-carbs" id="ws-ls-macro-carbs" class="ws-ls-macro" value="<?php esc_attr_e(WS_LS_MACRO_CARBS) ?>" size="3" />%
                                                        <p><?php echo __('Percentage of Carbohydrates to make up a moderate diet', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Fats' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <input  type="number"  step="any" min="0" max="100" name="ws-ls-macro-fats" id="ws-ls-macro-fats"  class="ws-ls-macro" value="<?php esc_attr_e(WS_LS_MACRO_FATS); ?>" size="3" />%
                                                        <p><?php echo __('Percentage of Fats to make up a moderate diet', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <h3><?php echo __( 'Macronutrient Calculator: Meals' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <?php

                                                    if ( true === WS_LS_IS_PRO_PLUS ) {
                                                        foreach ( ws_ls_harris_benedict_meal_ratio_defaults() as $key => $default ) {

                                                            printf( '<tr class="%1$s">
                                                                                        <th scope="row">%2$s</th>
                                                                                        <td>
                                                                                            <input  type="number" step="any" min="0" max="100" name="ws-ls-meal-ratio-%3$s" id="ws-ls-meal-ratio-%3$s"  class="ws-ls-macro-meals" value="%4$d" size="3" />%%
                                                                                            <p>%5$s %2$s. %6$s. <em>%7$s</em></p>
                                                                                        </td>
                                                                                    </tr>
                                                                ',
                                                                $disable_if_not_pro_plus_class,
                                                                esc_html( ucfirst( $key ) ),
                                                                $key,
                                                                ws_ls_harris_benedict_meal_ratio_get( $key ),
                                                                __( 'Percentage of calories to split into ', WE_LS_SLUG ),
                                                                ws_ls_calculations_link(),
                                                                __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG )
                                                            );
                                                        }
                                                    }

                                                ?>
                                            </table>
                                        </div>
										<div>
                                            <?php
                                            if ( false === WS_LS_IS_PRO ) {
                                                ws_ls_display_pro_upgrade_notice();
                                            }
                                            ?>
											<table class="form-table">
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Default chart type', WE_LS_SLUG ); ?></th>
													<td>
														<select id="ws-ls-chart-type" name="ws-ls-chart-type">
															<option value="line" <?php selected( get_option('ws-ls-chart-type'), 'line' ); ?>><?php echo __('Line Chart', WE_LS_SLUG)?> - (<?php echo __('Highly Recommended', WE_LS_SLUG)?>)</option>
															<option value="bar" <?php selected( get_option('ws-ls-chart-type'), 'bar' ); ?>><?php echo __('Bar Chart', WE_LS_SLUG)?></option>

														</select>
														<p><?php echo __('If enabled, "Allows points and labels to be displayed on graph. <strong>Note: If using measurements</strong>, graph type will be forced to Line.', WE_LS_SLUG); ?></p>
													</td>
												</tr>

													<tr  class="<?php echo $disable_if_not_pro_class; ?>" >
														<th scope="row"><?php echo __( 'Display gridlines?', WE_LS_SLUG ); ?></th>
														<td>
															<select id="ws-ls-grid-lines" name="ws-ls-grid-lines">
																<option value="yes" <?php selected( get_option('ws-ls-grid-lines'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( get_option('ws-ls-grid-lines'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('If enabled, gridlines will be displayed on the Graph canvas.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr  class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Maximum points per graph', WE_LS_SLUG ); ?></th>
														<td>
															<?php $chart_options = array(5,10,25,50,100,200); ?>
															<select id="ws-ls-max-points" name="ws-ls-max-points">
																<?php foreach ($chart_options as $option):?>
																	<option value="<?php echo $option; ?>" <?php selected( WE_LS_CHART_MAX_POINTS, $option ); ?>><?php echo $option; ?></option>
																<?php endforeach; ?>

															</select>
															<p><?php echo __('If enabled, "Allows points and labels to be displayed on graph.', WE_LS_SLUG); ?></p>
														</td>
													</tr >

												<tr>
													<th scope="row"><?php echo __( 'Weight colour?', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-line-colour" name="ws-ls-line-colour" type="color" value="<?php echo WE_LS_WEIGHT_LINE_COLOUR; ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for the Weight history line / bar border on graph.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr >
													<th scope="row"><?php echo __( 'Should y Axes start at 0?', WE_LS_SLUG ); ?></th>
													<td>
														<select id="ws-ls-axes-start-at-zero" name="ws-ls-axes-start-at-zero">
															<option value="no" <?php selected( get_option('ws-ls-axes-start-at-zero'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															<option value="yes" <?php selected( get_option('ws-ls-axes-start-at-zero'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>

														</select>
														<p><?php echo __('If enabled, y Axes shall start at 0. Otherwise, they are automatically calculated.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Text colour', WE_LS_SLUG ); ?></th>
                                                    <td>
                                                        <input id="ws-ls-text-colour" name="ws-ls-text-colour" type="color" value="<?php echo WE_LS_TEXT_COLOUR; ?>">
                                                        <p><?php echo __('Enter a HEX colour code to use for text displayed on the graph.', WE_LS_SLUG); ?></p>
                                                    </td>
                                                </tr>
                                                <tr >
                                                    <th scope="row"><?php echo __( 'Font Family', WE_LS_SLUG ); ?></th>
                                                    <td>
                                                        <input id="ws-ls-font-family" name="ws-ls-font-family" type="text" maxlength="80" class="large-text" value="<?php esc_attr_e(WE_LS_FONT_FAMILY); ?>">
                                                        <p><?php echo __('Specify one or more fonts that should be used when rendering text on the graph. Separate multiple fonts with a comma. Leave blank to use the default.', WE_LS_SLUG); ?></p>
                                                    </td>
                                                </tr>
												<tr>
													<th colspan="2">
														<h3><?php echo __( 'Line Chart Options', WE_LS_SLUG ); ?></h3>
													</th>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Bezier Curve?', WE_LS_SLUG ); ?></th>
													<td>
														<select id="ws-ls-bezier-curve" name="ws-ls-bezier-curve">
															<option value="yes" <?php selected( get_option('ws-ls-bezier-curve'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															<option value="no" <?php selected( get_option('ws-ls-bezier-curve'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>

														</select>
														<p><?php echo __('If enabled, lines between points on a line graph will be curved', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr>
													<th scope="row"><?php echo __( 'Target line colour?', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-target-colour" name="ws-ls-target-colour" type="color" value="<?php echo WE_LS_TARGET_LINE_COLOUR; ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for the Target line on graph.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Display points on graph?', WE_LS_SLUG ); ?></th>
													<td>
														<select id="ws-ls-allow-points" name="ws-ls-allow-points">
															<option value="yes" <?php selected( get_option('ws-ls-allow-points'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															<option value="no" <?php selected( get_option('ws-ls-allow-points'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>

														</select>
														<p><?php echo __('If enabled, "Allows points and labels to be displayed on graph.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row" class="<?php echo $disable_if_not_pro_class; ?>"><?php echo __( 'Point thickness', WE_LS_SLUG ); ?></th>
													<td>
														<?php $chart_options = array(1,2,3,4,5,6,7,8,9,10); ?>
														<select id="ws-ls-point-size" name="ws-ls-point-size">
															<?php foreach ($chart_options as $option):?>
																<option value="<?php echo $option; ?>" <?php selected( WE_LS_CHART_POINT_SIZE, $option ); ?>><?php echo $option; ?></option>
															<?php endforeach; ?>

														</select>
														<p><?php echo __('Specifies the point thickness on a line chart.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Add a fill under weight line?', WE_LS_SLUG ); ?></th>
													<td>
														<select id="ws-ls-fill-under-weight-line" name="ws-ls-fill-under-weight-line">
															<option value="no" <?php selected( get_option('ws-ls-fill-under-weight-line'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															<option value="yes" <?php selected( get_option('ws-ls-fill-under-weight-line'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
														</select>
														<p><?php echo __('If enabled, a fill colour will be added under the weight line.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Weight Fill Colour', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-fill-under-weight-line-colour" name="ws-ls-fill-under-weight-line-colour" type="color" value="<?php echo WE_LS_WEIGHT_FILL_LINE_COLOUR; ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for the fill colour under the weight line.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row" class="<?php echo $disable_if_not_pro_class; ?>"><?php echo __( 'Weight Fill Opacity', WE_LS_SLUG ); ?></th>
													<td>
														<?php $chart_options = array('0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1'); ?>
														<select id="ws-ls-fill-under-weight-line-opacity" name="ws-ls-fill-under-weight-line-opacity">
															<?php foreach ($chart_options as $option):?>
																<option value="<?php echo $option; ?>" <?php selected( WE_LS_WEIGHT_FILL_LINE_OPACITY, $option ); ?>><?php echo $option; ?></option>
															<?php endforeach; ?>

														</select>
														<p><?php echo __('Specifies the opacity of the fill colour under the weight line.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr>
													<th colspan="2">
														<h3><?php echo __( 'Bar Chart Options', WE_LS_SLUG ); ?></h3>
													</th>
												</tr>
												<tr>
													<th scope="row"><?php echo __( 'Weight fill colour?', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-line-fill-colour" name="ws-ls-line-fill-colour" type="color" value="<?php echo WE_LS_WEIGHT_FILL_COLOUR; ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for filling the Weight bars on the graph.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
											</table>
										</div>
											<div>
                                                <?php
                                                if ( false === WS_LS_IS_PRO ) {
                                                    ws_ls_display_pro_upgrade_notice();
                                                }
                                                ?>
												<table class="form-table">
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Enable email notifications', WE_LS_SLUG ); ?></th>
														<td>
															<select id="ws-ls-email-enable" name="ws-ls-email-enable">
																<option value="no" <?php selected( get_option('ws-ls-email-enable'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
																<option value="yes" <?php selected( get_option('ws-ls-email-enable'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															</select>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Email addresses to notify', WE_LS_SLUG ); ?></th>
														<td>
															<input id="ws-ls-email-addresses" name="ws-ls-email-addresses" type="text" maxlength="500" class="large-text" value="<?php esc_attr_e(WE_LS_EMAIL_ADDRESSES); ?>">
															<p><?php echo __('Specify one or more email addresses to be notified. Seperate multiple emails with a comma.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'New weight / measurement entries', WE_LS_SLUG ); ?></th>
														<td>
															<select id="ws-ls-email-notifications-new" name="ws-ls-email-notifications-new">
																<option value="yes" <?php selected( get_option('ws-ls-email-notifications-new'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( get_option('ws-ls-email-notifications-new'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Receive notifications when a member adds a new weight / measurement entry.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Edited weight / measurement entries', WE_LS_SLUG ); ?></th>
														<td>
															<select id="ws-ls-email-notifications-edit" name="ws-ls-email-notifications-edit">
																<option value="yes" <?php selected( get_option('ws-ls-email-notifications-edit'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( get_option('ws-ls-email-notifications-edit'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Receive notifications when a member edits an existing weight / measurement entry.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'New / updated targets', WE_LS_SLUG ); ?></th>
														<td>
															<select id="ws-ls-email-notifications-targets" name="ws-ls-email-notifications-targets">
																<option value="yes" <?php selected( get_option('ws-ls-email-notifications-targets'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( get_option('ws-ls-email-notifications-targets'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Receive notifications when a member adds / edits their target.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
												</table>
											</div>
                                        <div>
                                            <?php
                                            if ( false === WS_LS_IS_PRO ) {
                                                ws_ls_display_pro_upgrade_notice();
                                            }
                                            ?>
                                           <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                	<th scope="row"><?php echo __( 'Allow Measurements?' , WE_LS_SLUG); ?></th>
													<td>
														<select id="ws-ls-allow-measurements" name="ws-ls-allow-measurements">
															<option value="no" <?php selected( get_option('ws-ls-allow-measurements'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
															<option value="yes" <?php selected( get_option('ws-ls-allow-measurements'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
														</select>
														<p><?php echo __('If enabled, a user can also add body measurements along with their weights.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Measurement Units' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                        <select id="ws-ls-measurement-units" name="ws-ls-measurement-units">
                                                            <option value="cm" <?php selected( get_option('ws-ls-measurement-units'), 'cm' ); ?>><?php echo __('Centimetres', WE_LS_SLUG); ?></option>
                                                            <option value="inches" <?php selected( get_option('ws-ls-measurement-units'), 'inches' ); ?>><?php echo __('Inches', WE_LS_SLUG); ?></option>
                                                        </select>
                                                        <p><?php echo __('Default unit for recording measurements.', WE_LS_SLUG);?></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                	<th scope="row"><?php echo __( 'Measurements mandatory?' , WE_LS_SLUG); ?></th>
													<td>
														<select id="ws-ls-measurements-mandatory" name="ws-ls-measurements-mandatory">
															<option value="no" <?php selected( get_option('ws-ls-measurements-mandatory'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
															<option value="yes" <?php selected( get_option('ws-ls-measurements-mandatory'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
														</select>
														<p><?php echo __('If yes, a user will be forced to complete all measurement features.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Areas of measurements' , WE_LS_SLUG); ?></th>
                                                    <td>
                                                    <?php

                                                    $measurement_settings = (WS_LS_IS_PRO) ? ws_ls_get_measurement_settings() : false;

                                                        ?>
                                                    <table>
                                                        <?php

														if($measurement_settings) {
															foreach ($measurement_settings as $key => $body_part) {
	                                                            if (!$body_part['user_preference']) {
	                                                        ?>

	                                                                <tr>
	                                                                    <td colspan="2">
	                                                                        <label style="font-weight: bold;" for="ws-ls-<?php echo $key; ?>"><?php echo $body_part['title']; ?></label>
	                                                                    </td>
	                                                                </tr>
	                                                                <tr>
	                                                                    <td>
	                                                                        <?php echo __( 'Enable' , WE_LS_SLUG); ?>: <input type="checkbox" id="ws-ls-<?php echo $key; ?>" name="ws-ls-measurement[enabled][<?php echo $key; ?>]" value="on" <?php checked( $body_part['enabled'] ); ?> />
	                                                                    </td>
	                                                                    <td>
	                                                                        <?php echo __( 'Chart Colour' , WE_LS_SLUG); ?>: <input name="ws-ls-measurement[colors][<?php echo $key; ?>]" type="color" value="<?php echo $body_part['chart_colour']; ?>">

	                                                                    </td>
	                                                                </tr>
	                                                        <?php }
	                                                            }
														} else {
															ws_ls_display_default_measurements();
														}
													    ?>
                                                    </table>
                                                    </td>
                                                </tr>


                                            </table>

                                        </div>
                                        <div>
                                            <?php
                                            if ( false === WS_LS_IS_PRO ) {
                                                ws_ls_display_pro_upgrade_notice();
                                            }
                                            ?>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Enable Gravity Forms', WE_LS_SLUG ); ?></th>
                                                    <td>
                                                        <select id="ws-ls-gf-enable" name="ws-ls-gf-enable">
                                                            <option value="no" <?php selected( get_option('ws-ls-gf-enable'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( get_option('ws-ls-gf-enable'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p>
                                                            <?php echo __('Examine Gravity Form submissions for weight and measurement data. If found, create a Weight Entry for the user currently logged in.', WE_LS_SLUG); ?>
                                                            <a href="https://weight.yeken.uk/gravity-forms/" target="_blank" rel="noopener noreferrer"><?php echo __('Read more at ', WE_LS_SLUG); ?>weight.yeken.uk/gravity-forms/</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
									</div>
								</div>
								<?php submit_button(); ?>


							</form>
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

function ws_ls_register_settings(){

    register_setting( 'we-ls-options-group', 'ws-ls-units' );
    register_setting( 'we-ls-options-group', 'ws-ls-allow-targets' );
	register_setting( 'we-ls-options-group', 'ws-ls-caching' );
	register_setting( 'we-ls-options-group', 'ws-ls-allow-points' );
    register_setting( 'we-ls-options-group', 'ws-ls-target-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-line-fill-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-line-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-use-us-dates' );
    register_setting( 'we-ls-options-group', 'ws-ls-disable-css' );
	register_setting( 'we-ls-options-group', 'ws-ls-axes-start-at-zero' );
	register_setting( 'we-ls-options-group', 'ws-ls-disable-yeken-notifications' );
	register_setting( 'we-ls-options-group', 'ws-ls-edit-permissions' );
    register_setting( 'we-ls-options-group', 'ws-ls-text-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-font-family' );
	register_setting( 'we-ls-options-group', 'ws-ls-fill-under-weight-line' );
    register_setting( 'we-ls-options-group', 'ws-ls-fill-under-weight-line-opacity' );
    register_setting( 'we-ls-options-group', 'ws-ls-fill-under-weight-line-colour' );
	register_setting( 'we-ls-options-group', 'ws-ls-number-formatting-separator' );

    // Pro only open
    if( WS_LS_IS_PRO ){

        register_setting( 'we-ls-options-group', 'ws-ls-allow-user-preferences' );
		register_setting( 'we-ls-options-group', 'ws-ls-about-you-mandatory' );
        register_setting( 'we-ls-options-group', 'ws-ls-chart-type' );
        register_setting( 'we-ls-options-group', 'ws-ls-max-points' );
        register_setting( 'we-ls-options-group', 'ws-ls-bezier-curve' );
        register_setting( 'we-ls-options-group', 'ws-ls-point-size' );
        register_setting( 'we-ls-options-group', 'ws-ls-grid-lines' );

	    // Groups
	    register_setting( 'we-ls-options-group', 'ws-ls-enable-groups' );
	    register_setting( 'we-ls-options-group', 'ws-ls-enable-groups-user-edit' );

	    // Birthdays
	    register_setting( 'we-ls-options-group', 'ws-ls-enable-birthdays' );

        // Measurements
        register_setting( 'we-ls-options-group', 'ws-ls-allow-measurements' );
        register_setting( 'we-ls-options-group', 'ws-ls-measurement-units' );
        register_setting( 'we-ls-options-group', 'ws-ls-measurement' );
        register_setting( 'we-ls-options-group', 'ws-ls-measurements-mandatory' );

		// BMI
		register_setting( 'we-ls-options-group', 'ws-ls-display-bmi-in-tables' );

		// Stats
		register_setting( 'we-ls-options-group', 'ws-ls-disable-stats-cron' );

		// Emails
		register_setting( 'we-ls-options-group', 'ws-ls-email-enable' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-addresses' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-notifications-edit' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-notifications-new' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-notifications-targets' );

		// Third Party
        register_setting( 'we-ls-options-group', 'ws-ls-gf-enable' );

        // Photos
	    register_setting( 'we-ls-options-group', 'ws-ls-photos-max-size' );
    }

    // Pro Plus
    if ( WS_LS_IS_PRO_PLUS ) {

        register_setting( 'we-ls-options-group', 'ws-ls-challenges-enabled' );
        register_setting( 'we-ls-options-group', 'ws-ls-female-cal-cap' );
        register_setting( 'we-ls-options-group', 'ws-ls-male-cal-cap' );
        register_setting( 'we-ls-options-group', 'ws-ls-cal-subtract' );
	    register_setting( 'we-ls-options-group', 'ws-ls-cal-add' );
	    register_setting( 'we-ls-options-group', 'ws-ls-cal-show-loss' );
	    register_setting( 'we-ls-options-group', 'ws-ls-cal-show-gain' );
        register_setting( 'we-ls-options-group', 'ws-ls-macro-proteins' );
        register_setting( 'we-ls-options-group', 'ws-ls-macro-carbs' );
        register_setting( 'we-ls-options-group', 'ws-ls-macro-fats' );
		register_setting( 'we-ls-options-group', 'ws-ls-cal-add-unit' );
		register_setting( 'we-ls-options-group', 'ws-ls-cal-lose-unit' );

        foreach ( ws_ls_harris_benedict_meal_ratio_defaults() as $key => $default ) {
            register_setting( 'we-ls-options-group', sprintf( ' ws-ls-meal-ratio-%s', $key ) );
        }

        // Calories to subtract
		foreach ( ws_ls_harris_benedict_calorie_subtract_ranges_keys() as $key ) {
			register_setting( 'we-ls-options-group', $key );
		}
    }
}
add_action( 'admin_init', 'ws_ls_register_settings' );
