<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_settings_user() {

    ws_ls_user_data_permission_check();

	$user_id = ws_get_user_id_from_qs();

    // Ensure this WP user ID exists!
    ws_user_exist_check($user_id);
?>
<div class="wrap ws-ls-user-data">
	<div id="poststuff">
		<?php ws_ls_user_header($user_id, ws_ls_get_link_to_user_profile($user_id)); ?>
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Edit user preferences', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
                            <br />
							<?php
								echo ws_ls_user_preferences_form(['user-id' => $user_id,  'allow-delete-data' => false]);
							?>
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

}
