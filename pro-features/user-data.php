<?php
	defined('ABSPATH') or die('Jog on!');

	

// -------------------------------------------------------------------------------------------
// TODO: Remove this code with new user management. Code below is for old User Data page
// -------------------------------------------------------------------------------------------

function ws_ls_manage_user_data_page() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
	}

	$disable_if_not_pro_class = (WS_LS_IS_PRO) ? '' : 'ws-ls-disabled';

	wp_enqueue_script('jquery-tabs',plugins_url( '../js/tabs.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('wlt-tabs', plugins_url( '../css/tabs.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('wlt-tabs-flat', plugins_url( '../css/tabs.flat.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
	wp_enqueue_script('ws-ls-admin',plugins_url( '../js/admin.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);
	wp_enqueue_style('ws-ls-admin-style', plugins_url( '../css/admin.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);


	wp_localize_script( 'ws-ls-admin', 'ws_ls_user_data', array('ajax-url' => admin_url('admin-ajax.php') . '?action=ws_ls_user_data&security=' . wp_create_nonce( 'ajax-security-nonce' ) ) );

    $clear_cache = false;

    // If remove existing data
	if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {
		ws_ls_delete_existing_data();
		$clear_cache = true;

		// Let others know we cleared all user data
		do_action( WE_LS_HOOK_DATA_ALL_DELETED );
	}

	if($clear_cache) {
		ws_ls_delete_all_cache();
	}

	$measurement_unit = ('inches' == WE_LS_MEASUREMENTS_UNIT) ? __('Inches', WE_LS_SLUG) : __('Cm', WE_LS_SLUG);

?>


	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">


						<h3 class="hndle"><span><?php echo __( WE_LS_TITLE . ' User Data', WE_LS_SLUG); ?></span></h3>

						<div class="inside">

								<div id="ws-ls-tabs">
									<ul>
											<li><a><?php echo __( 'View / Edit', WE_LS_SLUG); ?><span><?php echo __( 'View and edit existing user data', WE_LS_SLUG); ?></span></a></li>
											<li><a><?php echo __( 'Delete All', WE_LS_SLUG); ?><span><?php echo __( 'Delete all user data!', WE_LS_SLUG); ?></span></a></li>
									</ul>
									<div>
										<div>

											 <table id="ws-ls-user-data" class="display ws-ls-advanced-data-table" cellspacing="0" width="100%">
								        <thead>
								            <tr>
												<th><?php echo __('User', WE_LS_SLUG); ?></th>
								                <th><?php echo __('Date', WE_LS_SLUG); ?></th>
								                <th><?php echo __('Weight', WE_LS_SLUG); ?></th>
												<th><?php echo __('Notes', WE_LS_SLUG); ?></th>

												<?php
													$measurement_columns_html = '';
													// Weight Columns?
													$measurement_columns = ws_ls_get_active_measurement_fields();
													if ($measurement_columns) {
														foreach ($measurement_columns as $key => $data) {
															$measurement_columns_html .= '<th class="none">' . __($data['title'], WE_LS_SLUG) . ' (' . $measurement_unit . ')</th>';
														}
														echo $measurement_columns_html;
													}
												?>
												<th></th>

								              </tr>
								        </thead>
								        <tfoot>
								            <tr>
												<th><?php echo __('User', WE_LS_SLUG); ?></th>
												<th><?php echo __('Date', WE_LS_SLUG); ?></th>
												<th><?php echo __('Weight', WE_LS_SLUG); ?></th>
												<th><?php echo __('Notes', WE_LS_SLUG); ?></th>
								                <?php echo $measurement_columns_html; ?>
												<th></th>
								            </tr>
								        </tfoot>
								    </table>


										</div>
										<div>

											 <p><?php echo __( 'You can use the following button to remove all user data currently stored by the plugin. <strong>All weight entries for every user will be lost!</strong>', WE_LS_SLUG ); ?></p>
					                       <a class="button-secondary delete-confirm" href="<?php echo get_permalink() . '?page=ws-ls-weight-loss-tracker-pro';  ?>&amp;removedata=y"><?php echo __( 'Remove ALL user data', WE_LS_SLUG); ?></a>


										</div>
									</div>
								</div>


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

	</div>
	<!-- #poststuff -->
<!-- .wrap -->
<?php

    	echo ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
		__('Are you sure you wish to remove all user data?', WE_LS_SLUG) . '<br /><br />',
			'delete-confirm');

}
?>
