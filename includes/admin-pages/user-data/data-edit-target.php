<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_edit_target() {

    ws_ls_permission_check_message();

    // Determine user id
	$user_id = ws_ls_querystring_value('user-id', true);

    // Ensure this WP user ID exists!
    ws_ls_user_exist_check( $user_id) ;

	// We need to ensure we either have a user id (to add a new entry to) OR an existing entry ID so we can load / edit it.
	if( empty( $user_id ) ) {
		echo esc_html__('There was an issue loading this page', WE_LS_SLUG);
		return;
	}

	//If we have a Redirect URL, base decode.
	$redirect_url = ws_ls_get_link_to_user_profile($user_id);

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
							<h2><span><?php echo esc_html__('Edit user\'s target', WE_LS_SLUG); ?></span></h2>
							<div class="inside">
                                <?php
                                    if ( true === WS_LS_IS_PREMIUM ) {

	                                    echo ws_ls_form_weight( [    'user-id'              => $user_id,
	                                                                 'type'       			=> 'target',
	                                                                 'redirect-url'         => $redirect_url,
	                                                                 'hide-login-message'   => true,
	                                    ] );

										echo sprintf( '<p><em>%s</em></p>', esc_html__('A user\'s target weight can be cleared by setting the value to 0 or leaving blank.', WE_LS_SLUG) );

                                    } else {
                                        echo sprintf( '<p>%s</p>', esc_html__('A Pro license is required to set a user\'s target weight.', WE_LS_SLUG) );
                                    }
                                ?>
							</div>
						</div>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<?php ws_ls_user_side_bar($user_id); ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>

<?php

}
