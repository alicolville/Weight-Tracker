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
						$notes = ws_ls_notes_fetch( $user_id, NULL );

						if ( false === empty( $notes ) ) {
						    array_map( 'ws_ls_notes_render', $notes );
						} else {
							printf( '	<div class="postbox ws-ls-postbox">
											<div class="postbox-header ws-ls-note-header">
												<h2 class="hndle"><span>%s</span></h2>
												</div>
											<div class="ws-ls-note-content inside">
												<p>%s</p>
											</div>
										</div>',
										__( 'Notes', WE_LS_SLUG ),
										__( 'There are currently no notes for this user.', WE_LS_SLUG )
							);
						}
 					?>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<?php ws_ls_user_side_bar( $user_id ); ?>
			</div>
		</div>
		<?php 

			if ( true === is_admin() && false === empty( $notes ) ) {
			
				wp_enqueue_script( 'wt-admin-notes', plugins_url( '../../../assets/js/admin.data-notes.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION, true );

				wp_localize_script( 'wt-admin-notes', 'ws_notes_config', [ 	'nonce' 		=> wp_create_nonce( 'ws-ls-delete-note' ),
																			'url'			=> admin_url( 'admin-ajax.php' ),
																			'error-message'	=> __( 'An error occurred when attempting to delete the note.', WE_LS_SLUG ) ]);
			}

		?>
		<style>
			.ws-note-delete {
				margin: 2px;
			}

			<?php if ( false === is_admin() ): ?>
					.ws-note-delete-div {
						display: none !important;
					}
			<?php endif; ?>
		</style>

		<br class="clear">
	</div>
<?php

}
