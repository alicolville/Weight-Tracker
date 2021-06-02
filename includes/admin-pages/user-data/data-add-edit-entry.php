<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_add_edit() {

    ws_ls_permission_check_message();

	// Determine user id / entry id
	$user_id = ws_ls_querystring_value('user-id', true);
	$entry_id = ws_ls_querystring_value('entry-id', true);

	// We need to ensure we either have a user id (to add a new entry to) OR an existing entry ID so we can load / edit it.
	if(empty($user_id) && empty($entry_id)) {
		echo __('There was an issue loading this page', WE_LS_SLUG);
		return;
	}

	// Ensure this WP user ID exists!
    ws_ls_user_exist_check( $user_id );

	//If we have a Redirect URL, base decode.
	$redirect_url = ws_ls_querystring_value('redirect');

	if( false === empty( $redirect_url ) ) {
		$redirect_url = base64_decode( $redirect_url );
	}
?>
	<div class="wrap ws-ls-user-data ws-ls-admin-page">
		<div id="poststuff">
			<?php ws_ls_user_header( $user_id, ws_ls_get_link_to_user_profile( $user_id ) ); ?>
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
                        <?php
                        if ( true !== WS_LS_IS_PRO ) {
                            ws_ls_display_pro_upgrade_notice();
                        }
                        ?>
						<div class="postbox">
							<h2><span><?php echo __('Add / Edit an entry', WE_LS_SLUG); ?></span></h2>
							<div class="inside">
								<?php
	                                if ( true === WS_LS_IS_PRO ) {

		                                echo ws_ls_form_weight( [    'user-id'              => $user_id,
		                                                             'entry-id'             => $entry_id,
		                                                             'redirect-url'         => $redirect_url,
		                                                             'hide-login-message'   => true,
																	 'weight-mandatory'		=> false
		                                ] );

	                                } else {
	                                    echo sprintf( '<p>%s</p>', __('A Pro license is required to add / edit a weight entry.', WE_LS_SLUG) );
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
