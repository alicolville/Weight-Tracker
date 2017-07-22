<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_add_edit() {

	$data = false;

	// Determine user id / entry id
	$user_id = ws_ls_querystring_value('user-id', true);
	$entry_id = ws_ls_querystring_value('entry-id', true);

	// We need to ensure we either have a user id (to add a new entry to) OR an existing entry ID so we can load / edit it.
	if(empty($user_id) && empty($entry_id)) {
		echo __('There was an issue loading this page', WE_LS_SLUG);
		return;
	}

	// If we have an entry ID, then load data
	if ($entry_id) {
		$data = ws_ls_get_weight($user_id, $entry_id);
	}

	//If we have a Redirect URL, base decode.
	$redirect_url = ws_ls_querystring_value('redirect');

	if(false === empty($redirect_url)) {
		$redirect_url = base64_decode($redirect_url);
	}
?>
	<div class="wrap">

				<h1><?php echo __('Add / Edit an entry', WE_LS_SLUG); ?></h1>

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-1">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<div class="postbox">
									<h2><span><?php echo __('All entries', WE_LS_SLUG); ?></span></h2>
									<div class="inside">
										<?php echo ws_ls_display_weight_form(false, false, $user_id, false, false, false, false, false, $redirect_url, $data); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br class="clear">
	</div>

<?php

}
