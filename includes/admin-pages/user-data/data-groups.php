<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_group_view() {

    ws_ls_permission_check_message();

    ws_ls_data_table_enqueue_scripts();

    $group_id = ws_ls_querystring_value( 'id', true, 0 );

    // Are we attempting to update the group name?
	if ( $new_name = ws_ls_post_value( 'new_group_name' ) ) {
		ws_ls_groups_update_name( $group_id, $new_name );
	}

    ?>
    <div class="wrap ws-ls-admin-page">
        <div id="icon-options-general" class="icon32"></div>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-3">

                <!-- main content -->
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">
                        <?php
                            if ( true !== WS_LS_IS_PRO ) {
                                ws_ls_display_pro_upgrade_notice();
                            }

                            $group = ws_ls_groups_get( $group_id );
                        ?>
                        <div class="postbox">
                            <h3 class="hndle">
                                    <span><?php echo __('View Group', WE_LS_SLUG); ?>
                                    <?php printf('%s', false === empty( $group['name'] ) ? ': ' . esc_html( $group['name'] ) : '' ); ?></span>
                                    <?php printf(' ( %d %s )', ws_ls_groups_count( $group_id ), __('user(s)', WE_LS_SLUG) ); ?>
                            </h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <?php if ( false === empty( $group ) ) : ?>

									<h4><?php echo __('Edit group name', WE_LS_SLUG); ?></h4>
									<form method="post">
										<input type="text" name="new_group_name" size="30" maxlength="40" value="<?php echo  esc_html( $group['name'] )?>" />
										<input type="hidden" name="id" value="<?php echo $group_id; ?>" />
										<input type="submit" value="<?php echo __('Edit', WE_LS_SLUG); ?>" class="button" <?php if ( false === WS_LS_IS_PRO ) { echo ' disabled'; } ?> />
									</form>
									<br />
									<?php echo ws_ls_component_group_view_entries( [ 'group-id' => $group_id ]); ;?>
                                    <p>
                                        <a class="button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-settings&mode=groups' ); ?>">
                                            <i class="fa fa-arrow-left"></i>
                                            <?php echo __('All Groups', WE_LS_SLUG); ?>
                                        </a>
                                    </p>

                                <?php else: ?>

                                    <p><?php echo __('The group could not be found', WE_LS_SLUG); ?>.</p>

                                <?php endif; ?>
                                <br clear="both"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

}
