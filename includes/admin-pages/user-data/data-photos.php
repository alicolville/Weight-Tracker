<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_photos() {

    ws_ls_permission_check_message();

    // Determine user id
    $user_id = ws_ls_querystring_value('user-id', true);

    // Ensure this WP user ID exists!
    ws_ls_user_exist_check($user_id);

    // We need to ensure we either have a user id (to add a new entry to) OR an existing entry ID so we can load / edit it.
    if( empty($user_id) ) {
        echo esc_html__('There was an issue loading this page', WE_LS_SLUG);
        return;
    }

    ?>
    <div class="wrap ws-ls-user-data ws-ls-admin-page">
        <div id="poststuff">
            <?php ws_ls_user_header($user_id, ws_ls_get_link_to_user_profile($user_id)); ?>
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            <h2><span><?php echo esc_html__('User\'s photos', WE_LS_SLUG); ?></span></h2>
                            <div class="inside">
                                <?php

                                    if( ws_ls_meta_fields_photo_any_enabled() ) {

	                                    $photo_count = ws_ls_photos_db_count_photos($user_id);

	                                    echo sprintf('<p>%s <strong>%s %s</strong>.</p>',
		                                    esc_html__('This user has uploaded ', WE_LS_SLUG),
		                                    $photo_count,
		                                    _n( 'photo', 'photos', $photo_count, WE_LS_SLUG )
	                                    );

	                                    echo ws_ls_photos_shortcode_gallery([   'error-message' => esc_html__('No photos could be found for this user.', WE_LS_SLUG),
	                                                                            'user-id' => $user_id,
	                                                                            'width' => '1200',
	                                                                            'direction' => 'desc',
	                                                                            'limit' => 50,
	                                                                            'hide-from-shortcodes' => false
	                                    ]);

                                    } else if ( true === WS_LS_IS_PREMIUM ) {

                                        echo sprintf('<p><a href="%s">%s</a> %s.</p>',
                                            ws_ls_meta_fields_base_url(),
                                            esc_html__('Add and enable a Photo Custom Field', WE_LS_SLUG),
                                            esc_html__('to allow a users to upload photos of their progress' , WE_LS_SLUG)
                                        );

                                    } else {

                                        echo sprintf('<p><a href="%s">%s</a> %s.</p>',
                                            ws_ls_upgrade_link(),
                                            esc_html__('Upgrade to Pro', WE_LS_SLUG),
                                            esc_html__('to allow a user to upload photos of their progress' , WE_LS_SLUG)
                                        );
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
