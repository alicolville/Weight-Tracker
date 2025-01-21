<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_settings_user() {

    ws_ls_permission_check_message();

	$user_id = ws_get_user_id_from_qs();

    // Ensure this WP user ID exists!
    ws_ls_user_exist_check( $user_id );
?>
<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<div id="poststuff">
		<?php ws_ls_user_header($user_id, ws_ls_get_link_to_user_profile($user_id)); ?>
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <?php
                        if ( true !== WS_LS_IS_PREMIUM ) {
                            ws_ls_display_pro_upgrade_notice();
                        }
                    ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__('Edit user preferences', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
                            <br />
							<?php
                                $disable_save = ( false === WS_LS_IS_PREMIUM );

								echo ws_ls_user_preferences_form(['user-id' => $user_id,  'allow-delete-data' => false, 'disable-save' => $disable_save ] );

                                if ( $disable_save ) {

                                    echo sprintf('<p><a href="%s">%s</a> %s.</p>',
                                        ws_ls_upgrade_link(),
                                        esc_html__('Upgrade to Pro', WE_LS_SLUG),
                                        esc_html__('to save changes to your user\'s settings' , WE_LS_SLUG)
                                    );

                                }
    						?>
						</div>
					</div>

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
