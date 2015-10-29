<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_settings_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
	}

	$clear_cache = (isset($_GET['settings-updated']) && 'true' == $_GET['settings-updated']) ? true : false;

	// If remove existing data
	if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {
		ws_ls_delete_existing_data();
		$clear_cache = true;
	}

	if (is_admin() && isset($_GET['recreatetables']) && 'y' == $_GET['recreatetables']) {
		ws_ls_activate();
		$clear_cache = true;
	}

	if($clear_cache) {
			ws_ls_delete_all_cache();
	}


		?>
		<div class="wrap">

<?php

	$mysql_table_check = ws_ls_admin_check_mysql_tables_exist();

	if ($mysql_table_check != false): ?>
		<div class="error">
			<p><?php echo $mysql_table_check; ?></p>
 			<p><a href="<?php echo get_permalink() . '?page=ws-ls-weight-loss-tracker-main-menu';  ?>&amp;recreatetables=y"><?php echo __('Rebuild them now', WE_LS_SLUG); ?></a></p>
		</div>
	<?php
	endif;


?>


	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( WE_LS_TITLE . ' Instructions', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 0px 15px 0px 15px">
							<p><?php echo __( 'Place the tag [weight-loss-tracker] on a given page and the user is presented with a form to enter a date, weight and notes for that entry. When the person saves their entry the data table and graph is refreshed.', WE_LS_SLUG ); ?></p>
						</div>
					</div>

					<div class="postbox">


						<h3 class="hndle"><span><?php echo __( WE_LS_TITLE . ' Settings', WE_LS_SLUG); ?></span></h3>

						<div class="inside">

							<form method="post" action="options.php">
								<?php

									settings_fields( 'we-ls-options-group' );
									do_settings_sections( 'we-ls-options-group' );

								?>

									<table class="form-table">
										<tr>
											<th scope="row"><?php echo __( 'Weight Units' , WE_LS_SLUG); ?></th>
											<td>
												<select id="ws-ls-units" name="ws-ls-units">
													<option value="kg" <?php selected( get_option('ws-ls-units'), 'kg' ); ?>><?php echo __('Kg', WE_LS_SLUG); ?></option>
													<option value="stones_pounds" <?php selected( get_option('ws-ls-units'), 'stones_pounds' ); ?>><?php echo __('Stones & Pounds', WE_LS_SLUG); ?></option>
													<option value="pounds_only" <?php selected( get_option('ws-ls-units'), 'pounds_only' ); ?>><?php echo __('Pounds', WE_LS_SLUG); ?></option>
												</select>
												<p><?php echo __('You can specify whether to display weights in Kg, Stones & Pounds or just Pounds. Please note: The graph will be displayed in Pounds if "Stones & Pounds" is selected.', WE_LS_SLUG);?></p>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo __( 'Allow target weights?' , WE_LS_SLUG); ?></th>
											<td>
												<select id="ws-ls-allow-targets" name="ws-ls-allow-targets">
													<option value="no" <?php selected( get_option('ws-ls-allow-targets'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
													<option value="yes" <?php selected( get_option('ws-ls-allow-targets'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
												</select>
												<p><?php echo __('If enabled, a user is allowed to enter a target weight. This will be displayed as a horizontal bar on the line chart.', WE_LS_SLUG); ?></p>
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
										<tr>
											<th scope="row"><?php echo __( 'Display points on graph?', WE_LS_SLUG ); ?></th>
											<td>
												<select id="ws-ls-allow-points" name="ws-ls-allow-points">
													<option value="yes" <?php selected( get_option('ws-ls-allow-points'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
													<option value="no" <?php selected( get_option('ws-ls-allow-points'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>

												</select>
												<p><?php echo __('If enabled, "Allows points and labels to be displayed on graph.', WE_LS_SLUG); ?></p>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo __( 'Graph: Weight colour?', WE_LS_SLUG ); ?></th>
											<td>
												<input id="ws-ls-line-colour" name="ws-ls-line-colour" type="color" value="<?php echo WE_LS_WEIGHT_LINE_COLOUR; ?>">
												<p><?php echo __('If enabled, enter a HEX colour code to use for the Weight history line on graph.', WE_LS_SLUG); ?></p>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo __( 'Graph: Weight fill colour?', WE_LS_SLUG ); ?></th>
											<td>
												<input id="ws-ls-line-fill-colour" name="ws-ls-line-fill-colour" type="color" value="<?php echo WE_LS_WEIGHT_FILL_COLOUR; ?>">
												<p><?php echo __('If enabled, enter a HEX colour code to use forthe shading underneath the Weight history line on graph. Line charts only.', WE_LS_SLUG); ?></p>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php echo __( 'Graph: Target line colour?', WE_LS_SLUG ); ?></th>
											<td>
												<input id="ws-ls-target-colour" name="ws-ls-target-colour" type="color" value="<?php echo WE_LS_TARGET_LINE_COLOUR; ?>">
												<p><?php echo __('If enabled, enter a HEX colour code to use for the Target line on graph.', WE_LS_SLUG); ?></p>
											</td>
										</tr>

										<tr>
											<th scope="row"><?php echo __( 'Display data in tabs?' , WE_LS_SLUG); ?></th>
											<td>
												<select id="ws-ls-use-tabs" name="ws-ls-use-tabs">
													<option value="no" <?php selected( get_option('ws-ls-use-tabs'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
													<option value="yes" <?php selected( get_option('ws-ls-use-tabs'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
												</select>
												<p><?php echo __('If enabled, "Weight History" and "Target Weight" will be displayed on sepearate tabs.', WE_LS_SLUG)?></p>
											</td>
										</tr>
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
											<th scope="row"><?php echo __( 'Enable support for Avada theme?' , WE_LS_SLUG) ?>:</th>
											<td>
												<select id="ws-ls-support-avada" name="ws-ls-support-avada">
													<option value="no" <?php selected( get_option('ws-ls-support-avada'), 'no' ); ?>><?php echo __('No', WE_LS_SLUG)?></option>
													<option value="yes" <?php selected( get_option('ws-ls-support-avada'), 'yes' ); ?>><?php echo __('Yes', WE_LS_SLUG)?></option>
												</select>
												<p><?php echo __('<strong style="color:red">Deprecated. This feature will very shortly be removed from the plugin and is currently no longer supported</strong>. Enables additional styling to support theAvada theme</a>.', WE_LS_SLUG)?></p>
											</td>
										</tr>
									</table>


								<?php submit_button(); ?>



							</form>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

					<div class="postbox">
						<h3 class="hndle"><span><?php echo __( 'Delete existing data', WE_LS_SLUG); ?> </span></h3>
						<div style="padding: 15px 15px 0px 15px">
							<a class="button-secondary delete-confirm" href="<?php echo get_permalink() . '?page=ws-ls-weight-loss-tracker-main-menu';  ?>&amp;removedata=y"><?php echo __( 'Remove ALL user data', WE_LS_SLUG); ?></a>
							<p><?php echo __( 'You can use the following button to remove all user data currently stored by the plugin. <strong>All weight entries for every user will be lost!</strong>', WE_LS_SLUG ); ?></p>
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

	echo ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
		__('Are you sure you wish to remove all user data?', WE_LS_SLUG) . '<br /><br />',
			'delete-confirm');


}

function ws_ls_register_settings()
{
		register_setting( 'we-ls-options-group', 'ws-ls-units' );
  	register_setting( 'we-ls-options-group', 'ws-ls-allow-targets' );
  	register_setting( 'we-ls-options-group', 'ws-ls-allow-points' );
  	register_setting( 'we-ls-options-group', 'ws-ls-use-tabs' );
  	register_setting( 'we-ls-options-group', 'ws-ls-support-avada' );
  	register_setting( 'we-ls-options-group', 'ws-ls-target-colour' );
  	register_setting( 'we-ls-options-group', 'ws-ls-line-fill-colour' );
  	register_setting( 'we-ls-options-group', 'ws-ls-line-colour' );
		register_setting( 'we-ls-options-group', 'ws-ls-use-us-dates' );
		register_setting( 'we-ls-options-group', 'ws-ls-disable-css' );


}
add_action( 'admin_init', 'ws_ls_register_settings' );
