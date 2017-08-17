<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_edit_target() {

    ws_ls_user_data_permission_check();

    // Determine user id
	$user_id = ws_ls_querystring_value('user-id', true);

    // Ensure this WP user ID exists!
    ws_user_exist_check($user_id);

	// We need to ensure we either have a user id (to add a new entry to) OR an existing entry ID so we can load / edit it.
	if(empty($user_id) && empty($entry_id)) {
		echo __('There was an issue loading this page', WE_LS_SLUG);
		return;
	}

	//If we have a Redirect URL, base decode.
	$redirect_url = ws_ls_get_link_to_user_profile($user_id);

?>
	<div class="wrap ws-ls-user-data">
		<div id="poststuff">
			<?php ws_ls_user_header($user_id, ws_ls_get_link_to_user_profile($user_id)); ?>
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h2><span><?php echo __('Edit user\'s target', WE_LS_SLUG); ?></span></h2>
							<div class="inside">
                                <?php echo ws_ls_display_weight_form(true, false, $user_id, true, false, false, false, false, $redirect_url); ?>
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
		</div>
		<br class="clear">
	</div>

<?php

}
