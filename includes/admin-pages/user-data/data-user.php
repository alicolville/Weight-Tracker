<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_user() {

    ws_ls_user_data_permission_check();

	$user_id = ws_get_user_id_from_qs();

    // Ensure this WP user ID exists!
    ws_user_exist_check( $user_id );

    // DELETE ALL DATA FOR THIS USER!! AHH!!
    if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {
        ws_ls_delete_data_for_user($user_id);
    }

    $user_data = get_userdata( $user_id );
?>
<?php if(!empty($_GET['user-preference-saved'])) : ?>
	<div class="notice notice-success"><p><?php echo __('The preferences for this user have been saved.', WE_LS_SLUG); ?></p></div>
<?php endif; ?>

<?php if(!empty($_GET['deletecache'])) :
        ws_ls_delete_cache_for_given_user($user_id);
    ?>
    <div class="notice notice-success"><p><?php echo __('The cache for this user has been deleted.', WE_LS_SLUG); ?></p></div>
<?php endif; ?>

<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<div id="poststuff">
		<?php ws_ls_user_header($user_id); ?>
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <?php
                        if ( true !== WS_LS_IS_PRO ) {
                            ws_ls_display_pro_upgrade_notice();
                        }
                    ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Chart', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php

								// Fetch last 25 weight entries
								$weight_data = ws_ls_get_weights($user_id, 25, -1, 'desc');

								// Reverse array so in cron order
                                if($weight_data) {
                                    $weight_data = array_reverse($weight_data);
                                }

                                if ( true !== WS_LS_IS_PRO ) {

                                    echo sprintf('<p><a href="%s">%s</a> %s.</p>',
                                        ws_ls_upgrade_link(),
                                        __('Upgrade to Pro', WE_LS_SLUG),
                                        __('to view the a chart of the user\'s weight entries.' , WE_LS_SLUG)
                                    );

                                } else {
                                    echo ws_ls_display_chart($weight_data, ['type' => 'line', 'max-points' => 25, 'user-id' => $user_id]);
                                }

							?>
						</div>
					</div>
                    <?php

                    // Allow an additional admin section to be added in
                    $customised_section = apply_filters( 'wlt-filters-admin-custom-section', '' );

                    if ( false === empty( $customised_section ) ) {
                        echo wp_kses_post( $customised_section  );
                    }

                    $show_standard_photos = (bool) apply_filters( 'wlt-filters-admin-show-standard-photos', true );

                    if ( true === $show_standard_photos ) :
                    ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo __('Photos', WE_LS_SLUG); ?></span></h2>
                            <div class="inside">
                                <?php
                                if( ws_ls_meta_fields_photo_any_enabled() ) {

                                    $photo_count = ws_ls_photos_db_count_photos( $user_id );

                                    echo sprintf( '<p>%s <strong>%s %s</strong>. <a href="%s">%s</a>.</p>',
                                        __( 'This user has uploaded ', WE_LS_SLUG ),
                                        $photo_count,
                                        _n( 'photo', 'photos', $photo_count, WE_LS_SLUG ),
                                        ws_ls_get_link_to_photos( $user_id ),
                                        __( 'View all photos', WE_LS_SLUG )
                                    );

                                    if ( $photo_count >= 1 ) {
                                        echo ws_ls_photos_shortcode_gallery( [
                                            'error-message'        => __( 'No photos could be found for this user.', WE_LS_SLUG ),
                                            'mode'                 => 'tilesgrid',
                                            'limit'                => 20,
                                            'direction'            => 'desc',
                                            'user-id'              => $user_id,
                                            'hide-from-shortcodes' => false
                                        ] );
                                    }
                                } else if ( true === WS_LS_IS_PRO ) {

                                    echo sprintf('<p><a href="%s">%s</a> %s.</p>',
                                        ws_ls_meta_fields_base_url(),
                                        __('Add and enable a Photo Custom Field', WE_LS_SLUG),
                                        __('to allow a users to upload photos of their progress' , WE_LS_SLUG)
                                    );

                                } else {

                                    echo sprintf('<p><a href="%s">%s</a> %s.</p>',
                                        ws_ls_upgrade_link(),
                                        __('Upgrade to Pro', WE_LS_SLUG),
                                        __('to allow a user to upload photos of their progress' , WE_LS_SLUG)
                                    );
                                }
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php	if( ws_ls_has_a_valid_pro_plus_license()) :

                            $awards = ws_ls_awards_previous_awards( $user_id );

                            if ( false === empty( $awards ) ): ?>
                                 <div class="postbox">
                                      <h2 class="hndle"><span><?php echo __('Awards', WE_LS_SLUG); ?></span></h2>
                                      <div class="inside">
                                          <?php echo ws_ls_awards_render_badges( [ 'user-id' => $user_id ] ); ?>
                                      </div>
                                  </div>
                            <?php endif; ?>
                            <div class="postbox">
                                <h2 class="hndle"><span><?php echo __('Daily calorie needs', WE_LS_SLUG); ?></span></h2>
                                <div class="inside">
                                    <?php echo ws_ls_harris_benedict_render_table( $user_id, false, 'ws-ls-footable' ); ?>
                                </div>
                            </div>
                            <div class="postbox">
                                <h2 class="hndle"><span><?php echo __('Macronutrient Calculator', WE_LS_SLUG); ?></span></h2>
                                <div class="inside">
                                    <?php

                                        if( false === ws_ls_macro_validate_percentages() ) {
                                            echo printf( '%s <a href="%s">%s</a>%s',
                                                    __('Please ensure the values for Proteins, Carbohydrates and Fats (within ', WE_LS_SLUG),
                                                    ws_ls_get_link_to_settings(),
                                                    __('settings', WE_LS_SLUG),
                                                    __(') have been specified and total 100%.', WE_LS_SLUG)
                                            );
                                        } else {
                                           echo ws_ls_macro_render_table($user_id, false, 'ws-ls-footable');
                                        }

                                    ?>
                                </div>
                            </div>
                    <?php else: ?>
                          <div class="postbox">
                              <h2 class="hndle"><span><?php echo __('Get More', WE_LS_SLUG); ?>: <?php echo __('Macronutrient Calculator', WE_LS_SLUG); ?>, <?php echo __('Daily calorie needs', WE_LS_SLUG); ?> and <?php echo __('Awards', WE_LS_SLUG); ?></span></h2>
                              <div class="inside">
                                  <?php
				                      echo sprintf('<p>
				                                        <a href="%s">%s</a> %s:
				                                    </p>     
                                                    <ul>
                                                        <li><strong>- %s</strong> - %s. %s.</li>
                                                        <li><strong>- %s</strong> - %s. %s.</li>
                                                        <li><strong>- %s</strong> - %s. <a href="%s">Modify awards</a>.</li>
                                                    </ul>',
					                      ws_ls_upgrade_link(),
					                      __('Upgrade to Pro Plus', WE_LS_SLUG),
					                      __('and get the following', WE_LS_SLUG),
					                      __('Macronutrient Calculator', WE_LS_SLUG),
					                      __('view the user\'s suggested Macronutrient intake based on their recommended calorie intake' , WE_LS_SLUG),
                                          ws_ls_calculations_link(),
					                      __('Daily calorie needs', WE_LS_SLUG),
					                      __('view the user\'s daily calorie intake required to either maintain or lose weight (Harris Benedict formula)' , WE_LS_SLUG),
					                      ws_ls_calculations_link(),
					                      __('Awards', WE_LS_SLUG),
					                      __('view and issue awards based upon users meeting your certain goals' , WE_LS_SLUG),
                                          ws_ls_awards_base_url()
				                      );
			                      ?>
                              </div>
                          </div>
                    <?php endif; ?>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Entries for this user', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php echo ws_ls_data_table_placeholder($user_id, false, true); ?>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<?php ws_ls_user_side_bar( $user_id ); ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php

}
