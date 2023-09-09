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
		ws_ls_cache_delete_all();
	}

	if ( true === isset( $_GET[ 'settings-updated' ] ) ) {
		do_action( 'ws_ls_settings_saved' );
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
                                        <li><a><?php echo __( 'Macros & Calories', WE_LS_SLUG); ?><span><?php echo __( 'Adjust settings for macronutrients and calories', WE_LS_SLUG); ?></span></a></li>
                                        <li><a><?php echo __( 'Chart', WE_LS_SLUG); ?><span><?php echo __( 'Chart styling and config', WE_LS_SLUG); ?></span></a></li>
										<li><a><?php echo __( 'Emails & Notifications', WE_LS_SLUG); ?><span><?php echo __( 'Configure email notifications and templates', WE_LS_SLUG); ?></span></a></li>
                                        <li><a><?php echo __( 'Integrations', WE_LS_SLUG); ?><span><?php echo __( '3rd party integrations and webhooks', WE_LS_SLUG); ?></span></a></li>
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
															<p><?php echo __('You can specify whether to display weights in Kg, Stones & Pounds or just Pounds. Please note: The chart will be displayed in Pounds if "Stones & Pounds" is selected.', WE_LS_SLUG);?></p>
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
                                                <h3><?php echo __( 'User experience' , WE_LS_SLUG); ?></h3>
                                                <table class="form-table">
													<tr>
														<th scope="row"><?php echo __( 'Set default aim?' , WE_LS_SLUG); ?></th>
														<td>
															<?php
																echo ws_ls_form_field_select( [ 'key' 			=> 'ws-ls-default-aim',
																								'show-label' 	=> false,
																								'values' 		=> ws_ls_aims(),
																								'selected' 		=> get_option( 'ws-ls-default-aim', NULL ) ] );
															?>
															<p><?php echo __('If enabled, you can specify the default aim when a user has not selected one.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr>
                                                        <th scope="row"><?php echo __( 'Allow target weights?' , WE_LS_SLUG); ?></th>
                                                        <td>
	                                                        <?php
	                                                            $target_weights = get_option( 'ws-ls-allow-targets', 'yes' );
	                                                        ?>
                                                            <select id="ws-ls-allow-targets" name="ws-ls-allow-targets">
                                                                <option value="yes" <?php selected( $target_weights, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                                <option value="no" <?php selected( $target_weights, 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                            </select>
                                                            <p><?php echo __('If enabled, a user is allowed to enter a target weight. This will be displayed as a horizontal bar on the line chart.', WE_LS_SLUG); ?></p>
                                                        </td>
                                                    </tr>
                                                    <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                        <th scope="row"><?php echo __( 'Display BMI in tables?' , WE_LS_SLUG); ?></th>
														<?php
															$display_bmi_in_tables = get_option( 'ws-ls-display-bmi-in-tables', 'yes' );
														?>
                                                        <td>
                                                            <select id="ws-ls-display-bmi-in-tables" name="ws-ls-display-bmi-in-tables">
                                                                <option value="yes" <?php selected( $display_bmi_in_tables, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                                <option value="no" <?php selected( $display_bmi_in_tables, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            </select>
                                                            <p><?php echo __('If enabled, BMI values will be displayed alongside weight entries in data tables.', WE_LS_SLUG)?></p>
                                                        </td>
                                                    </tr>
                                                    <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                        <th scope="row"><?php echo __( 'Allow user settings' , WE_LS_SLUG); ?></th>
                                                        <td>
	                                                        <?php
	                                                            $user_preferences = get_option( 'ws-ls-allow-user-preferences', 'no' );
	                                                        ?>
                                                            <select id="ws-ls-allow-user-preferences" name="ws-ls-allow-user-preferences">
                                                                <option value="no" <?php selected( $user_preferences, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG )?></option>
	                                                            <option value="yes" <?php selected( $user_preferences, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG )?></option>

                                                            </select>
                                                            <p><?php echo __('Allow your users to select their own data units, complete their "About You" fields and remove all their data.', WE_LS_SLUG)?></p>
                                                        </td>
                                                    </tr>
                                                    <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                        <th scope="row"><?php echo __( '"About You" fields mandatory?' , WE_LS_SLUG); ?></th>
                                                        <td>
	                                                        <?php
	                                                            $about_you = get_option( 'ws-ls-about-you-mandatory', 'no' );
	                                                        ?>
                                                            <select id="ws-ls-about-you-mandatory" name="ws-ls-about-you-mandatory">
                                                                <option value="no" <?php selected( $about_you, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG )?></option>
                                                                <option value="yes" <?php selected( $about_you, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG )?></option>
                                                            </select>
                                                            <p><?php echo __( 'If User Settings is enabled, should all the "About You" (height, activity level, etc) be mandatory?', WE_LS_SLUG )?></p>
                                                        </td>
                                                    </tr>
	                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
		                                                <th scope="row"><?php echo __( 'Allow users to add notes for weight entries?' , WE_LS_SLUG); ?></th>
		                                                <td>
			                                                <?php
			                                                $user_notes = get_option( 'ws-ls-allow-user-notes', 'yes' );
			                                                ?>
			                                                <select id="ws-ls-allow-user-notes" name="ws-ls-allow-user-notes">
				                                                <option value="yes" <?php selected( $user_notes, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
				                                                <option value="no" <?php selected( $user_notes, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
			                                                </select>
			                                                <p><?php echo __('If enabled, users can add notes against weight entries.', WE_LS_SLUG); ?></p>
		                                                </td>
	                                                </tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Display previous entry on form?' , WE_LS_SLUG); ?></th>
														<td>
															<?php
															$enabled = get_option( 'ws-ls-populate-placeholders-with-previous-values', 'yes' );
															?>
															<select id="ws-ls-populate-placeholders-with-previous-values" name="ws-ls-populate-placeholders-with-previous-values">
																<option value="yes" <?php selected( $enabled, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
																<option value="no" <?php selected( $enabled, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
															</select>
															<p><?php echo __('If enabled, when adding a new weight entry, the previous weight entries values will be added as field placeholders to show the user the values previously entered.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Load previous entry upon date selection?' , WE_LS_SLUG); ?></th>
														<td>
															<?php
															$enabled = get_option( 'ws-ls-populate-form-with-values-on-date', 'yes' );
															?>
															<select id="ws-ls-populate-form-with-values-on-date" name="ws-ls-populate-form-with-values-on-date">
																<option value="yes" <?php selected( $enabled, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
																<option value="no" <?php selected( $enabled, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
															</select>
															<p><?php echo __('If enabled, and data exists, then the user will be asked whether they wish to load the data for the selected date chosen on the entry form.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
											</table>
                                            <h3><?php echo __( 'Awards' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Delete awards when weight entry deleted?' , WE_LS_SLUG); ?></th>
                                                    <td>
														<?php
														    $awards_enabled = get_option( 'ws-ls-awards-delete-when-entry-deleted-enabled', 'no' );
														?>
                                                        <select id="ws-ls-awards-delete-when-entry-deleted-enabled" name="ws-ls-awards-delete-when-entry-deleted-enabled">
                                                            <option value="yes" <?php selected( $awards_enabled, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
                                                            <option value="no" <?php selected( $awards_enabled, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
                                                        </select>
                                                        <p> <?php echo __( 'If set to yes, when a weight entry is deleted (for awards given in versions 10.5 and above) any awards that were given at that time are also deleted. Please note, awards will automatically be re-added when a new entry is added if the entry meets the award criteria. ', WE_LS_SLUG ); ?></p>
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
                                            <h3><?php echo __( 'Permissions' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Who can view and modify user data?' , WE_LS_SLUG); ?></th>
	                                                <?php
	                                                    $edit_permissions = ws_ls_permission_role();
	                                                ?>
	                                                <td>
                                                        <select id="ws-ls-edit-permissions" name="ws-ls-edit-permissions">
                                                            <option value="manage_options" <?php selected( $edit_permissions, 'manage_options' ); ?>><?php echo __( 'Administrators Only', WE_LS_SLUG ); ?></option>
                                                            <option value="read_private_posts" <?php selected( $edit_permissions, 'read_private_posts' ); ?>><?php echo __( 'Editors and above', WE_LS_SLUG ); ?></option>
                                                            <option value="publish_posts" <?php selected( $edit_permissions, 'publish_posts' ); ?>><?php echo __( 'Authors and above', WE_LS_SLUG ); ?></option>
                                                        </select>
                                                        <p><?php echo __('Specify the minimum level of user role that can view or edit user data.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Who can export and delete user data?' , WE_LS_SLUG); ?></th>
		                                            <?php
		                                            $delete_export_permissions = ws_ls_permission_export_delete_role();
		                                            ?>
                                                    <td>
                                                        <select id="ws-ls-export-delete-permissions" name="ws-ls-export-delete-permissions">
                                                            <option value="manage_options" <?php selected( $delete_export_permissions, 'manage_options' ); ?>><?php echo __( 'Administrators Only', WE_LS_SLUG ); ?></option>
                                                            <option value="read_private_posts" <?php selected( $delete_export_permissions, 'read_private_posts' ); ?>><?php echo __( 'Editors and above', WE_LS_SLUG ); ?></option>
                                                        </select>
                                                        <p><?php echo __('Specify the minimum level of user role that can export or delete user data.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
                                            </table>
                                            <h3><?php echo __( 'Groups' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
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
														<?php
															$disable_css = get_option( 'ws-ls-disable-css', 'no' );
														?>
                                                        <select id="ws-ls-disable-css" name="ws-ls-disable-css">
                                                            <option value="no" <?php selected( $disable_css, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( $disable_css, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('If you wish to style the forms in your own way, you can use this option to disable WLT\'s style sheets.', WE_LS_SLUG)?></p>
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

                                            <h3><?php echo __( 'Calculating daily calorie intake to lose weight' , WE_LS_SLUG); ?></h3>

                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Show Loss figures?' , WE_LS_SLUG); ?></th>
                                                    <td>
	                                                    <?php
	                                                    $show_loss_figures = get_option('ws-ls-cal-show-loss', 'yes' );
	                                                    ?>
                                                        <select id="ws-ls-cal-show-loss" name="ws-ls-cal-show-loss">
                                                            <option value="yes" <?php selected( $show_loss_figures, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                            <option value="no" <?php selected( $show_loss_figures, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p><?php echo __('Show loss figures to your users? For example, if your site is aimed at muscle building, you may wish not to.', WE_LS_SLUG)?></p>
                                                    </td>
                                                </tr>
												<tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
													<th scope="row"><?php echo __( 'Female Minimum Calorie Cap' , WE_LS_SLUG); ?></th>
													<?php
														$female_min_calorie_cap = ws_ls_harris_benedict_setting( 'ws-ls-female-min-cal-cap' );

														if ( true === empty( $female_min_calorie_cap ) ) {
															$female_min_calorie_cap = '';
														}
													?>
													<td>
														<input  type="number" step="any" min="800" max="5000" name="ws-ls-female-min-cal-cap" id="ws-ls-female-min-cal-cap" value="<?php echo esc_attr( $female_min_calorie_cap ); ?>" size="11" />
														<p><?php echo __('If specified, any calorie intake suggestions below this value shall be replaced by it e.g. if Weight Tracker calculates the recommended calorie intake to lose weight at 940kcal and the safety is set to 1200kcal, then Weight Tracker has calculated a value below your limits. The safety value will replace the calculated value and set it to 1200kcal.', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
													</td>
												</tr>
                                                 <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Female Maximum Calorie Cap' , WE_LS_SLUG); ?></th>
													 <?php
													 	$female_calorie_cap =  ws_ls_harris_benedict_setting( 'ws-ls-female-cal-cap' );
													 ?>
                                                    <td>
                                                        <input  type="number"  step="any" min="0" max="5000" name="ws-ls-female-cal-cap" id="ws-ls-female-cal-cap" value="<?php echo esc_attr( $female_calorie_cap ); ?>" size="11" />
                                                        <p><?php echo __('Specify a maximum value for number of daily calories allowed to achieve weight loss. As per NHS guidelines, females are set to 1400kcal by default', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
                                                    </td>
                                                </tr>
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Male Minimum Calorie Cap' , WE_LS_SLUG); ?></th>
                                                    <td>
														<?php
															$male_min_calorie_cap = ws_ls_harris_benedict_setting( 'ws-ls-male-min-cal-cap' );

															if ( true === empty( $male_min_calorie_cap ) ) {
																$male_min_calorie_cap = '';
															}
														?>
                                                        <input  type="number"  step="any" min="800" max="5000" name="ws-ls-male-min-cal-cap" id="ws-ls-male-min-cal-cap" value="<?php echo esc_attr( $male_min_calorie_cap ); ?>" size="11" />
														<p><?php echo __('If specified, any calorie intake suggestions below this value shall be replaced by it e.g. if Weight Tracker calculates the recommended calorie intake to lose weight at 940kcal and the safety is set to 1200kcal, then Weight Tracker has calculated a value below your limits. The safety value will replace the calculated value and set it to 1200kcal.', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
													<th scope="row"><?php echo __( 'Male Maximum Calorie Cap' , WE_LS_SLUG); ?></th>
													<td>
														<?php
															$male_calorie_cap = ws_ls_harris_benedict_setting( 'ws-ls-male-cal-cap' );
														?>
														<input  type="number"  step="any" min="0" max="5000" name="ws-ls-male-cal-cap" id="ws-ls-male-cal-cap" value="<?php echo esc_attr( $male_calorie_cap ); ?>" size="11" />
														<p><?php echo __('Specify a maximum value for number of daily calories allowed to achieve weight loss. As per NHS guidelines, males are set to 1900kcal by default', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p></td>
												</tr>
											    <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
                                                    <th scope="row"><?php echo __( 'Calories to subtract' , WE_LS_SLUG); ?></th>
                                                    <?php

														$subtract_ranges = ws_ls_harris_benedict_calorie_subtract_ranges();
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
                                                <tr class="<?php echo $disable_if_not_pro_plus_class; ?>">
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

													$add_ranges = ws_ls_harris_benedict_calorie_add_ranges();
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
													<th scope="row"><?php echo __( 'Percentages per aim' , WE_LS_SLUG); ?></th>
													<td>
														<p><?php echo __('For each aim, specify how Macronutrients should be split when calculated.', WE_LS_SLUG);?>. <?php echo ws_ls_calculations_link(); ?>. <em><?php echo __( 'Please note, it may take up to 15 minutes for calculations to change (due to caching).' , WE_LS_SLUG); ?></em></p>

														<table class="widefat ws-ls-calories-modify-table">
															<thead>
															<tr>
																<th class="row-title"></th>
																<th><?php echo __( 'Proteins' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'Carbohydrates' , WE_LS_SLUG); ?></th>
																<th><?php echo __( 'Fats' , WE_LS_SLUG); ?></th>
															</tr>
															</thead>
															<tbody>
															<?php foreach ( ['maintain', 'lose', 'gain' ] as $key ): ?>
																<tr>
																	<th><?php echo ws_ls_get_macro_name( $key ); ?></th>
																	<td>
																		<input  type="number"  step="any" min="0" max="100" name="ws-ls-macro-proteins-<?php echo $key; ?>"  class="ws-ls-macro ws-ls-macro-<?php echo $key; ?>" data-type="<?php echo $key; ?>" value="<?php echo esc_attr( ws_ls_harris_benedict_setting( 'ws-ls-macro-proteins-' . $key ) ); ?>" size=3" />%
																	</td>
																	<td>
																		<input  type="number"  step="any" min="0" max="100" name="ws-ls-macro-carbs-<?php echo $key; ?>" class="ws-ls-macro ws-ls-macro-<?php echo $key; ?>" data-type="<?php echo $key; ?>" value="<?php echo esc_attr( ws_ls_harris_benedict_setting( 'ws-ls-macro-carbs-' . $key ) ); ?>" size="3" />%
																	</td>
																	<td>
																		<input  type="number"  step="any" min="0" max="100" name="ws-ls-macro-fats-<?php echo $key; ?>"  class="ws-ls-macro ws-ls-macro-<?php echo $key; ?>" data-type="<?php echo $key; ?>" value="<?php echo esc_attr( ws_ls_harris_benedict_setting( 'ws-ls-macro-fats-' . $key ) ); ?>" size="3" />%
																	</td>
																</tr>
															<?php endforeach; ?>
															</tbody>
														</table>
													</td>
												</tr>
											</table>

                                            <h3><?php echo __( 'Macronutrient Calculator: Meals' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <?php
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
													<?php
														$chart_type = get_option( 'ws-ls-chart-type', 'line' );
													?>
													<td>
														<select id="ws-ls-chart-type" name="ws-ls-chart-type">
															<option value="line" <?php selected( $chart_type, 'line' ); ?>><?php echo __('Line Chart', WE_LS_SLUG)?> - (<?php echo __('Recommended', WE_LS_SLUG)?>)</option>
															<option value="bar" <?php selected( $chart_type, 'bar' ); ?>><?php echo __('Bar Chart', WE_LS_SLUG)?></option>
														</select>
													</td>
												</tr>

													<tr  class="<?php echo $disable_if_not_pro_class; ?>" >
														<th scope="row"><?php echo __( 'Display gridlines?', WE_LS_SLUG ); ?></th>
														<?php
															$show_gridlines = get_option( 'ws-ls-grid-lines', 'yes' );
														?>
														<td>
															<select id="ws-ls-grid-lines" name="ws-ls-grid-lines">
																<option value="yes" <?php selected( $show_gridlines, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
																<option value="no" <?php selected( $show_gridlines, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
															</select>
															<p><?php echo __('If enabled, grid lines will be displayed on charts.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr  class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Maximum points per chart', WE_LS_SLUG ); ?></th>
														<td>
															<?php

																$chart_options  = [ 5, 10, 25, 50, 100, 200 ];
																$max_points     = get_option( 'ws-ls-max-points', '25' );
															?>
															<select id="ws-ls-max-points" name="ws-ls-max-points">
																<?php foreach ( $chart_options as $option):?>
																	<option value="<?php echo $option; ?>" <?php selected( $max_points, $option ); ?>><?php echo $option; ?></option>
																<?php endforeach; ?>

															</select>
															<p><?php echo __( 'Specifies the maximum number of data points displayed on charts (this does not effect admin).', WE_LS_SLUG); ?></p>
														</td>
													</tr >

												<tr>
													<th scope="row"><?php echo __( 'Weight colour', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-line-colour" name="ws-ls-line-colour" type="color" value="<?php echo esc_attr( get_option( 'ws-ls-line-colour', '#aeaeae' ) ); ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for the Weight history line / bar border on chart.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr>
													<th scope="row"><?php echo __( 'Target line colour', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-target-colour" name="ws-ls-target-colour" type="color" value="<?php echo esc_attr( get_option( 'ws-ls-target-colour', '#76bada' ) ); ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for the Target line on chart.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<?php

												$text_colour = get_option( 'ws-ls-text-colour', '#AEAEAE' );

												?>
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Text colour', WE_LS_SLUG ); ?></th>
                                                    <td>
                                                        <input id="ws-ls-text-colour" name="ws-ls-text-colour" type="color" value="<?php echo esc_attr( $text_colour ); ?>">
                                                        <p><?php echo __('Enter a HEX colour code to use for text displayed on the chart.', WE_LS_SLUG); ?></p>
                                                    </td>
                                                </tr>
												<?php

													$font_family = get_option( 'ws-ls-font-family', '' );
												?>
                                                <tr>
                                                    <th scope="row"><?php echo __( 'Font Family', WE_LS_SLUG ); ?></th>
                                                    <td>
                                                        <input id="ws-ls-font-family" name="ws-ls-font-family" type="text" maxlength="80" class="large-text" value="<?php echo esc_attr( $font_family ); ?>">
                                                        <p><?php echo __('Specify one or more fonts that should be used when rendering text on the chart. Separate multiple fonts with a comma. Leave blank to use the default.', WE_LS_SLUG); ?></p>
                                                    </td>
                                                </tr>
												<tr>
													<th colspan="2">
														<h3><?php echo __( 'Custom Field Options', WE_LS_SLUG ); ?></h3>
													</th>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Field / Question or Abbreviation', WE_LS_SLUG ); ?></th>
													<td>
														<?php

														$abbv_or_question = get_option( 'ws-ls-abbv-or-question', 'abbv' );

														?>
														<select id="ws-ls-abbv-or-question" name="ws-ls-abbv-or-question">
															<option value="abbv" <?php selected( $abbv_or_question, 'abbv' ); ?>><?php echo __( 'Abbreviation', WE_LS_SLUG ); ?></option>
															<option value="question" <?php selected( $abbv_or_question, 'question' ); ?>><?php echo __( 'Field / Question', WE_LS_SLUG ); ?></option>
														</select>
														<p><?php echo __('When displaying a custom field on a chart, which value should be displayed in the chart\'s legend? The field question or abbreviation.', WE_LS_SLUG); ?></p>
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
														<?php

															$bezier_curve = get_option( 'ws-ls-bezier-curve', 'yes' );

														?>
														<select id="ws-ls-bezier-curve" name="ws-ls-bezier-curve">
															<option value="yes" <?php selected( $bezier_curve, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
															<option value="no" <?php selected( $bezier_curve, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
														</select>
														<p><?php echo __('If enabled, lines between points on a line chart will be curved', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row" class="<?php echo $disable_if_not_pro_class; ?>"><?php echo __( 'Point thickness', WE_LS_SLUG ); ?></th>
													<td>
														<?php
															$point_size = ws_ls_option_to_int( 'ws-ls-point-size', 3 );
														?>
														<select id="ws-ls-point-size" name="ws-ls-point-size">
															<?php
																for ( $i = 0; $i <= 10; $i++ ) {

																		printf( '<option value="%1$d" %2$s>%1$d</option>',
																						$i,
																						selected( $point_size, $i )
																		);
																	}
																?>
														</select>
														<p><?php echo __('Specifies the point thickness on a line chart. Set to 0 to hide points.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Add a fill under weight line?', WE_LS_SLUG ); ?></th>
													<td>
														<?php

															$fill_under_line = get_option( 'ws-ls-fill-under-weight-line', 'no' );

														?>
														<select id="ws-ls-fill-under-weight-line" name="ws-ls-fill-under-weight-line">
															<option value="no" <?php selected( $fill_under_line, 'no' ); ?>><?php echo __('No', WE_LS_SLUG )?></option>
															<option value="yes" <?php selected( $fill_under_line, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG )?></option>
														</select>
														<p><?php echo __( 'If enabled, a fill colour will be added under the weight line.', WE_LS_SLUG ); ?></p>
													</td>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Weight Fill Colour', WE_LS_SLUG ); ?></th>
													<td>
														<input id="ws-ls-fill-under-weight-line-colour" name="ws-ls-fill-under-weight-line-colour" type="color" value="<?php echo esc_attr( get_option( 'ws-ls-fill-under-weight-line-colour', '#aeaeae' ) ); ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for the fill colour under the weight line.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr  class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row" class="<?php echo $disable_if_not_pro_class; ?>"><?php echo __( 'Weight Fill Opacity', WE_LS_SLUG ); ?></th>
													<td>
														<?php
															$chart_options 		= [ '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1' ];
															$current_opacity 	= get_option( 'ws-ls-fill-under-weight-line-opacity', '0.5' );
														?>
														<select id="ws-ls-fill-under-weight-line-opacity" name="ws-ls-fill-under-weight-line-opacity">
															<?php foreach ( $chart_options as $option ): ?>
																<option value="<?php echo $option; ?>" <?php selected( $current_opacity, $option ); ?>><?php echo $option; ?></option>
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
														<input id="ws-ls-line-fill-colour" name="ws-ls-line-fill-colour" type="color" value="<?php echo esc_attr( get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ) ); ?>">
														<p><?php echo __('If enabled, enter a HEX colour code to use for filling the Weight bars on the chart.', WE_LS_SLUG); ?></p>
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
												<h3><?php echo __( 'Settings' , WE_LS_SLUG); ?></h3>
												<table class="form-table">
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Enable email notifications', WE_LS_SLUG ); ?></th>
														<td>
															<?php
																$email_enabled = get_option( 'ws-ls-email-enable', 'no' );
															?>
															<select id="ws-ls-email-enable" name="ws-ls-email-enable">
																<option value="no" <?php selected( $email_enabled, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
																<option value="yes" <?php selected( $email_enabled, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															</select>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Email addresses to notify', WE_LS_SLUG ); ?></th>
														<?php
															$email_addresses = get_option( 'ws-ls-email-addresses', '' );
														?>
														<td>
															<input id="ws-ls-email-addresses" name="ws-ls-email-addresses" type="text" maxlength="500" class="large-text" value="<?php echo esc_attr( $email_addresses ); ?>">
															<p><?php echo __('Specify one or more email addresses to be notified. Seperate multiple emails with a comma.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'New weight / custom field entries', WE_LS_SLUG ); ?></th>
														<td>
															<?php
																$email_notification_new = get_option( 'ws-ls-email-notifications-new', 'yes' );
															?>
															<select id="ws-ls-email-notifications-new" name="ws-ls-email-notifications-new">
																<option value="yes" <?php selected( $email_notification_new, 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG )?></option>
																<option value="no" <?php selected( $email_notification_new, 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG )?></option>
															</select>
															<p><?php echo __( 'Receive notifications when a member adds a new weight / custom field entry.', WE_LS_SLUG ); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Edited weight / custom field entries', WE_LS_SLUG ); ?></th>
														<td>
															<?php
																$email_notification_edit = get_option( 'ws-ls-email-notifications-edit', 'yes' );
															?>
															<select id="ws-ls-email-notifications-edit" name="ws-ls-email-notifications-edit">
																<option value="yes" <?php selected( $email_notification_edit, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( $email_notification_edit, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Receive notifications when a member edits an existing weight / custom field entry.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'New / updated targets', WE_LS_SLUG ); ?></th>
														<td>
															<?php
																$email_notification_targets = get_option( 'ws-ls-email-notifications-targets', 'yes' );
															?>
															<select id="ws-ls-email-notifications-targets" name="ws-ls-email-notifications-targets">
																<option value="yes" <?php selected( $email_notification_targets, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( $email_notification_targets, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Receive notifications when a member adds / edits their target.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Include user\'s email address?', WE_LS_SLUG ); ?></th>
														<td>
															<?php
															$include_email_address = get_option( 'ws-ls-email-include-email-address', 'yes' );
															?>
															<select id="ws-ls-email-include-email-address" name="ws-ls-email-include-email-address">
																<option value="yes" <?php selected( $include_email_address, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( $include_email_address, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Include the user\'s email address within the email body.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
													<tr class="<?php echo $disable_if_not_pro_class; ?>">
														<th scope="row"><?php echo __( 'Include Weight Summary?', WE_LS_SLUG ); ?></th>
														<td>
															<?php
															$include_weight_summary = get_option( 'ws-ls-email-include-weight-summary', 'yes' );
															?>
															<select id="ws-ls-email-include-weight-summary" name="ws-ls-email-include-weight-summary">
																<option value="yes" <?php selected( $include_weight_summary, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
																<option value="no" <?php selected( $include_weight_summary, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
															</select>
															<p><?php echo __('Add additional weight summary information into the email e.g. start weight, previous weight, difference between both, difference between current weight and start weight, etc.', WE_LS_SLUG); ?></p>
														</td>
													</tr>
												</table>
												<h3><?php echo __( 'Templates' , WE_LS_SLUG); ?></h3>
												<p><?php printf( '<p>%s</p><a href="%s" target="_blank" rel="noopener">%s</a>',
																	__( 'Email templates are stored within the database and can be edited in the Email Template manager', WE_LS_SLUG ),
																	ws_ls_emailer_edit_link(),
																	__( 'Email Template Manager', WE_LS_SLUG ) ) ; ?>
												</p>
											</div>
                                        <div>
                                            <?php
                                            if ( false === WS_LS_IS_PRO ) {
                                                ws_ls_display_pro_upgrade_notice();
                                            }
                                            ?>
											<h3><?php echo __( 'Webhooks/Slack/Zapier' , WE_LS_SLUG); ?></h3>
											<p>
												<?php echo __( 'Push data to third party applications when a user adds/updates weight entries or updates their target.' , WE_LS_SLUG ); ?>
												<a href="https://docs.yeken.uk/web-hooks.html" target="_blank" rel="noopener noreferrer"><?php echo __( 'Read more about Webhooks/Slack/Zapier' , WE_LS_SLUG); ?></a>
											</p>
											<table class="form-table">
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Enabled', WE_LS_SLUG ); ?></th>
													<td>
														<?php
														$is_enabled = get_option( 'ws-ls-webhooks-enabled', 'no' );
														?>
														<select id="ws-ls-webhooks-enabled" name="ws-ls-webhooks-enabled">
															<option value="yes" <?php selected( $is_enabled, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															<option value="no" <?php selected( $is_enabled, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
														</select>
														<p><?php echo __( 'If set to Yes, data will be fired to the specified endpoints.', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Endpoint URLs', WE_LS_SLUG ); ?></th>
													<td>
														<?php
															$endpoint_one 	= get_option( 'ws-ls-webhook-endpoint-one', '' );
															$endpoint_two 	= get_option( 'ws-ls-webhook-endpoint-two', '' );
															$endpoint_three = get_option( 'ws-ls-webhook-endpoint-three', '' );
														?>
															<input id="ws-ls-webhook-endpoint-one" name="ws-ls-webhook-endpoint-one" type="url" maxlength="250" class="large-text" value="<?php echo esc_url( $endpoint_one ); ?>">
															<input id="ws-ls-webhook-endpoint-two" name="ws-ls-webhook-endpoint-two" type="url" maxlength="250" class="large-text" value="<?php echo esc_url( $endpoint_two ); ?>">
															<input id="ws-ls-webhook-endpoint-three" name="ws-ls-webhook-endpoint-three" type="url" maxlength="250" class="large-text" value="<?php echo esc_url( $endpoint_three ); ?>">
															<p>
																<?php echo __( 'Specify one or more endpoints that data should be pushed to. If the endpoint is a determined to be Slack URL then a message shall be posted within the given Slack channel. All other endpoints will receive a JSON object representing the data.', WE_LS_SLUG); ?>
																<a href="https://docs.yeken.uk/web-hooks.html" target="_blank" rel="noopener noreferrer"><?php echo __('Read more at ', WE_LS_SLUG); ?>https://docs.yeken.uk/web-hooks.html</a>
															</p>
														</td>
													</td>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Send data for weight entries?', WE_LS_SLUG ); ?></th>
													<td>
														<?php
															$is_enabled = get_option( 'ws-ls-webhooks-weight-entries-enabled', 'yes' );
														?>
														<select id="ws-ls-webhooks-weight-entries-enabled" name="ws-ls-webhooks-weight-entries-enabled">
															<option value="yes" <?php selected( $is_enabled, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															<option value="no" <?php selected( $is_enabled, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
														</select>
														<p><?php echo __( 'When a user adds/updates a weight entry, should data be fired to the endpoint(s)?', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Send data for target updates?', WE_LS_SLUG ); ?></th>
													<td>
														<?php
														$is_enabled = get_option( 'ws-ls-webhooks-targets-enabled', 'no' );
														?>
														<select id="ws-ls-webhooks-targets-enabled" name="ws-ls-webhooks-targets-enabled">
															<option value="yes" <?php selected( $is_enabled, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															<option value="no" <?php selected( $is_enabled, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
														</select>
														<p><?php echo __( 'When a user updates their target, should data be fired to the endpoint(s)?', WE_LS_SLUG); ?></p>
													</td>
												</tr>
												<tr class="<?php echo $disable_if_not_pro_class; ?>">
													<th scope="row"><?php echo __( 'Include admin updates?', WE_LS_SLUG ); ?></th>
													<td>
														<?php
														$is_enabled = get_option( 'ws-ls-webhooks-admin-changes-enabled', 'no' );
														?>
														<select id="ws-ls-webhooks-admin-changes-enabled" name="ws-ls-webhooks-admin-changes-enabled">
															<option value="yes" <?php selected( $is_enabled, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
															<option value="no" <?php selected( $is_enabled, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
														</select>
														<p><?php echo __( 'Should changes made by admins (via the admin screens) cause data to be fired to the endpoints?', WE_LS_SLUG); ?></p>
													</td>
												</tr>
											</table>


											<h3><?php echo __( 'Form Handlers' , WE_LS_SLUG); ?></h3>
                                            <table class="form-table">
                                                <tr class="<?php echo $disable_if_not_pro_class; ?>">
                                                    <th scope="row"><?php echo __( 'Process Gravity Forms', WE_LS_SLUG ); ?></th>
                                                    <td>
														<?php
															$gf_enabled = get_option( 'ws-ls-gf-enable', 'no' );
														?>
                                                        <select id="ws-ls-gf-enable" name="ws-ls-gf-enable">
                                                            <option value="no" <?php selected( $gf_enabled, 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
                                                            <option value="yes" <?php selected( $gf_enabled, 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
                                                        </select>
                                                        <p>
                                                            <?php echo __('Examine Gravity Form submissions for weight and custom fields. If found, create a Weight Entry for the user currently logged in.', WE_LS_SLUG); ?>
                                                            <a href="https://docs.yeken.uk/gravity-forms.html" target="_blank" rel="noopener noreferrer"><?php echo __('Read more at ', WE_LS_SLUG); ?>https://docs.yeken.uk/gravity-forms.html</a>
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
	register_setting( 'we-ls-options-group', 'ws-ls-target-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-line-fill-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-line-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-use-us-dates' );
    register_setting( 'we-ls-options-group', 'ws-ls-disable-css' );
	register_setting( 'we-ls-options-group', 'ws-ls-edit-permissions' );
	register_setting( 'we-ls-options-group', 'ws-ls-export-delete-permissions' );
    register_setting( 'we-ls-options-group', 'ws-ls-text-colour' );
    register_setting( 'we-ls-options-group', 'ws-ls-font-family' );
	register_setting( 'we-ls-options-group', 'ws-ls-fill-under-weight-line' );
    register_setting( 'we-ls-options-group', 'ws-ls-fill-under-weight-line-opacity' );
    register_setting( 'we-ls-options-group', 'ws-ls-fill-under-weight-line-colour' );
	register_setting( 'we-ls-options-group', 'ws-ls-default-aim' );

    // Pro only open
    if( WS_LS_IS_PRO ){

        register_setting( 'we-ls-options-group', 'ws-ls-allow-user-preferences' );
		register_setting( 'we-ls-options-group', 'ws-ls-about-you-mandatory' );
        register_setting( 'we-ls-options-group', 'ws-ls-chart-type' );
        register_setting( 'we-ls-options-group', 'ws-ls-max-points' );
        register_setting( 'we-ls-options-group', 'ws-ls-bezier-curve' );
		register_setting( 'we-ls-options-group', 'ws-ls-abbv-or-question' );
        register_setting( 'we-ls-options-group', 'ws-ls-point-size' );
        register_setting( 'we-ls-options-group', 'ws-ls-grid-lines' );
		register_setting( 'we-ls-options-group', 'ws-ls-populate-placeholders-with-previous-values' );
		register_setting( 'we-ls-options-group', 'ws-ls-populate-form-with-values-on-date' );

	    // Groups
	    register_setting( 'we-ls-options-group', 'ws-ls-enable-groups-user-edit' );

	    // Birthdays
	    register_setting( 'we-ls-options-group', 'ws-ls-enable-birthdays' );

        // Measurements
        register_setting( 'we-ls-options-group', 'ws-ls-allow-measurements' );
        register_setting( 'we-ls-options-group', 'ws-ls-measurement-units' );
        register_setting( 'we-ls-options-group', 'ws-ls-measurement' );
        register_setting( 'we-ls-options-group', 'ws-ls-measurements-mandatory' );
	    register_setting( 'we-ls-options-group', 'ws-ls-allow-user-notes' );

		// BMI
		register_setting( 'we-ls-options-group', 'ws-ls-display-bmi-in-tables' );

		// Emails
		register_setting( 'we-ls-options-group', 'ws-ls-email-enable' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-addresses' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-notifications-edit' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-notifications-new' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-notifications-targets' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-include-weight-summary' );
		register_setting( 'we-ls-options-group', 'ws-ls-email-include-email-address' );

		// Third Party / Web hooks
        register_setting( 'we-ls-options-group', 'ws-ls-gf-enable' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhooks-enabled' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhook-endpoint-one' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhook-endpoint-two' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhook-endpoint-three' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhooks-admin-changes-enabled' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhooks-weight-entries-enabled' );
		register_setting( 'we-ls-options-group', 'ws-ls-webhooks-targets-enabled' );

        // Photos
	    register_setting( 'we-ls-options-group', 'ws-ls-photos-max-size' );
    }

    // Pro Plus
    if ( WS_LS_IS_PRO_PLUS ) {

        register_setting( 'we-ls-options-group', 'ws-ls-female-cal-cap' );
		register_setting( 'we-ls-options-group', 'ws-ls-female-min-cal-cap' );
		register_setting( 'we-ls-options-group', 'ws-ls-male-cal-cap' );
		register_setting( 'we-ls-options-group', 'ws-ls-male-min-cal-cap' );
        register_setting( 'we-ls-options-group', 'ws-ls-cal-subtract' );
	    register_setting( 'we-ls-options-group', 'ws-ls-cal-add' );
	    register_setting( 'we-ls-options-group', 'ws-ls-cal-show-loss' );
	    register_setting( 'we-ls-options-group', 'ws-ls-cal-show-gain' );
		register_setting( 'we-ls-options-group', 'ws-ls-cal-add-unit' );
		register_setting( 'we-ls-options-group', 'ws-ls-cal-lose-unit' );
		register_setting( 'we-ls-options-group', 'ws-ls-awards-delete-when-entry-deleted-enabled' );

		register_setting( 'we-ls-options-group', 'ws-ls-macro-proteins-maintain' );
		register_setting( 'we-ls-options-group', 'ws-ls-macro-carbs-maintain' );
		register_setting( 'we-ls-options-group', 'ws-ls-macro-fats-maintain' );

		register_setting( 'we-ls-options-group', 'ws-ls-macro-proteins-lose' );
		register_setting( 'we-ls-options-group', 'ws-ls-macro-carbs-lose' );
		register_setting( 'we-ls-options-group', 'ws-ls-macro-fats-lose' );

		register_setting( 'we-ls-options-group', 'ws-ls-macro-proteins-gain' );
		register_setting( 'we-ls-options-group', 'ws-ls-macro-carbs-gain' );
		register_setting( 'we-ls-options-group', 'ws-ls-macro-fats-gain' );

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
