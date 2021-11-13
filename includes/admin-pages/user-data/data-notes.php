<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_notes_for_user() {

    ws_ls_permission_check_message();

	$user_id = ws_get_user_id_from_qs();

    // Ensure this WP user ID exists!
    ws_ls_user_exist_check( $user_id );

?>
<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<div id="poststuff">
		<?php 	ws_ls_user_header( $user_id );

				if ( true !== WS_LS_IS_PRO ) {
					ws_ls_display_pro_upgrade_notice();
				}
        ?>
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable" id="ws-ls-user-data-one">
                    <?php



 					?>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<?php ws_ls_user_side_bar( $user_id ); ?>
			</div>
		</div>
		<br class="clear">
	</div>
<?php

}
