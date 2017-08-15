<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_user() {

    ws_ls_user_data_permission_check();

	$user_id = ws_get_user_id_from_qs();

    // DELETE ALL DATA FOR THIS USER!! AHH!!
    if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {
        ws_ls_delete_data_for_user($user_id);
    }

    $user_data = get_userdata( $user_id );
?>
<?php if(!empty($_GET['user-preference-saved'])) : ?>
	<div class="notice notice-success"><p><?php echo __('The preferences for this user have been saved.', WE_LS_SLUG); ?></p></div>
<?php endif; ?>

<div class="wrap">
	<h1><?php echo $user_data->user_nicename; ?>
			<?php echo ws_ls_get_email_link($user_id, true); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Chart', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php

								// Fetch last 25 weight entries
								$weight_data = ws_ls_get_weights($user_id, 25, -1, 'desc');

								// Reverse array so in cron order
                                if($weight_data) {
                                    $weight_data = array_reverse($weight_data);
                                }

								echo ws_ls_display_chart($weight_data, ['type' => 'line', 'max-points' => 25, 'user-id' => $user_id]);

							?>
						</div>
					</div>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo __('Daily calorie needs', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <?php
								if(ws_ls_has_a_valid_pro_plus_license()) {

									echo ws_ls_harris_benedict_render_table($user_id);

								} else {

									echo sprintf('<p><a href="%s">%s</a> %s. %s.</p>',
													ws_ls_upgrade_link(),
													__('Upgrade to Pro Plus', WE_LS_SLUG),
													__('to view the user\'s daily calorie intake required to either maintain or lose weight (Harris Benedict formula)' , WE_LS_SLUG),
                                                    ws_ls_calculations_link()
												);
								}
                            ?>
                        </div>
                    </div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Entries for this user', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php ws_ls_data_table_placeholder($user_id, false, true); ?>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<?php echo ws_ls_user_side_bar($user_id); ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php

    echo ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
        __('Are you sure you wish to remove the data for this user?', WE_LS_SLUG) . '<br /><br />',
        'delete-confirm');

}
