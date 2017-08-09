<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_settings_user() {

	$user_id = ws_get_user_id_from_qs();

    $user_data = get_userdata( $user_id );
?>
<div class="wrap">
	<h1><?php echo $user_data->user_nicename; ?><?php echo ws_ls_get_email_link($user_id, true); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Edit user settings', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
                            <br />
							<?php
								echo ws_ls_user_preferences_form($user_id);
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
