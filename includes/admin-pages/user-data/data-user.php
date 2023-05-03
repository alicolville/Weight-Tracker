<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_user() {

    ws_ls_permission_check_message();

	$user_id = ws_get_user_id_from_qs();

    // Ensure this WP user ID exists!
    ws_ls_user_exist_check( $user_id );

    // DELETE ALL DATA FOR THIS USER!! AHH!!
    if ( true === isset( $_GET['removedata'] ) && 'y' == $_GET['removedata'] ) {
        ws_ls_delete_data_for_user( $user_id);
    }

    // Delete all awards for this user
	if ( true === isset( $_GET['remove-awards'] ) && 'y' == $_GET['remove-awards'] ) {
		ws_ls_awards_db_delete_awards_for_user( $user_id );
	}

    $user_data = get_userdata( $user_id );
?>
<?php if(!empty($_GET['user-preference-saved'])) : ?>
	<div class="notice notice-success"><p><?php echo __('The preferences for this user have been saved.', WE_LS_SLUG); ?></p></div>
<?php endif; ?>

<?php if(!empty($_GET['deletecache'])) :
		ws_ls_cache_user_delete( $user_id );
    ?>
    <div class="notice notice-success"><p><?php echo __('The cache for this user has been deleted.', WE_LS_SLUG); ?></p></div>
<?php endif; ?>

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

                    // Allow an additional admin section to be added in
                    $customised_section = apply_filters( 'wlt-filters-admin-custom-section', '' );

                    if ( false === empty( $customised_section ) ) {
                        echo wp_kses_post( $customised_section  );
                    }

                    $user_order = get_option( 'ws-ls-postbox-order-ws-ls-user-data-one', [ 'chart', 'photos', 'macros', 'daily-calories', 'awards', 'user-entries'] );

                    if ( false === (bool) apply_filters( 'wlt-filters-admin-show-standard-photos', true ) ) {
                    	unset( $user_order[ 'photos' ] );
                    }

					$user_order = apply_filters( 'wlt-filters-postbox-order-ws-ls-user-data-one', $user_order );

					foreach ( $user_order as $postbox ) {

						if ( 'chart' === $postbox ) {
							ws_ls_postbox_chart( $user_id );
						} elseif ( 'photos' === $postbox ) {
							ws_ls_postbox_photos( $user_id );
						} elseif ( 'macros' === $postbox ) {
							ws_ls_postbox_macros( $user_id );
						} elseif ( 'daily-calories' === $postbox ) {
							ws_ls_postbox_daily_calories( $user_id );
						} elseif ( 'awards' === $postbox ) {
							ws_ls_postbox_awards( $user_id );
						} elseif ( 'user-entries' === $postbox ) {
							ws_ls_postbox_user_entries( $user_id );
						}
					}

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

/**
 * Postbox for chart
* @param $user_id
*
* @throws Exception
 */
function ws_ls_postbox_chart( $user_id ) {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'chart', 'ws-ls-user-data-one' ); ?>" id="chart">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Chart', WE_LS_SLUG ), 'postbox-id' => 'chart', 'postbox-col' => 'ws-ls-user-data-one' ] ); ?>
		<div class="inside">
			<?php

				// Fetch last 25 weight entries
				$weight_data = ws_ls_entries_get( [ 'user-id' => $user_id, 'limit' => 25, 'prep' => true, 'sort' => 'desc', 'reverse' => true ] );

				if ( true !== WS_LS_IS_PRO ) {

					echo sprintf('<p><a href="%s">%s</a> %s.</p>',
						ws_ls_upgrade_link(),
						__( 'Upgrade to Pro', WE_LS_SLUG ),
						__( 'to view the a chart of the user\'s weight entries.' , WE_LS_SLUG )
					);

				} else {
					echo ws_ls_display_chart( $weight_data, [ 'type' => 'line', 'max-points' => 25, 'user-id' => $user_id ] );
				}

			?>
		</div>
	</div>
<?php
}

/**
 * Postbox for photos
* @param $user_id
 */
function ws_ls_postbox_photos( $user_id ) {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'photos', 'ws-ls-user-data-one' ); ?>" id="photos">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Photos', WE_LS_SLUG ), 'postbox-id' => 'photos', 'postbox-col' => 'ws-ls-user-data-one' ] ); ?>
		<div class="inside">
			<?php
			if( true === ws_ls_meta_fields_photo_any_enabled() ) {

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
					__( 'Add and enable a Photo Custom Field', WE_LS_SLUG ),
					__( 'to allow a users to upload photos of their progress' , WE_LS_SLUG )
				);

			} else {

				echo sprintf('<p><a href="%s">%s</a> %s.</p>',
					ws_ls_upgrade_link(),
					__( 'Upgrade to Pro', WE_LS_SLUG ),
					__( 'to allow a user to upload photos of their progress. Before a user can upload photos, you must add one or more custom fields' , WE_LS_SLUG )
				);
			}
			?>
		</div>
	</div>
<?php
}

/**
 * Postbox for awards
* @param $user_id
 */
function ws_ls_postbox_macros( $user_id ) {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'macros', 'ws-ls-user-data-one' ); ?>" id="macros">
		  <?php ws_ls_postbox_header( [ 'title' => __('Macronutrient Calculator', WE_LS_SLUG), 'postbox-id' => 'macros', 'postbox-col' => 'ws-ls-user-data-one' ] ); ?>
		  <div class="inside">
		<?php

			if( true === ws_ls_has_a_valid_pro_plus_license() ) {

			 	echo ws_ls_macro_render_table( $user_id, false, 'ws-ls-footable' );

			} else {
				  printf('<p><a href="%s">%s</a> <strong>- %s</strong> - %s. %s.</p>',
					  ws_ls_upgrade_link(),
					  __( 'Upgrade to Pro Plus', WE_LS_SLUG ),
					  __( 'Macronutrient Calculator', WE_LS_SLUG ),
					  __( 'view the user\'s suggested Macronutrient intake based on their recommended calorie intake' , WE_LS_SLUG ),
					  ws_ls_calculations_link()
				  );
			 } ?>
		  </div>
  	</div>
<?php
}

/**
 * Postbox for awards
* @param $user_id
 */
function ws_ls_postbox_daily_calories( $user_id ) {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'daily-calories', 'ws-ls-user-data-one' ); ?>" id="daily-calories">
		  <?php ws_ls_postbox_header( [ 'title' => __('Daily calorie needs', WE_LS_SLUG), 'postbox-id' => 'daily-calories', 'postbox-col' => 'ws-ls-user-data-one' ] ); ?>
		  <div class="inside">
		<?php

			if( true === ws_ls_has_a_valid_pro_plus_license() ) {

			 	echo ws_ls_harris_benedict_render_table( $user_id, false, 'ws-ls-footable' );

			} else {
				  printf('<p><a href="%s">%s</a> <strong>- %s</strong> - %s. %s.</p>',
					  ws_ls_upgrade_link(),
					  __( 'Upgrade to Pro Plus', WE_LS_SLUG ),
					  __( 'Daily calorie needs', WE_LS_SLUG ),
					  __( 'view the user\'s daily calorie intake required to either maintain or lose weight (Harris Benedict formula)' , WE_LS_SLUG ),
					  ws_ls_calculations_link()
				  );
			 } ?>
		  </div>
  	</div>
<?php
}

/**
 * Postbox for awards
* @param $user_id
 */
function ws_ls_postbox_awards( $user_id ) {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'awards', 'ws-ls-user-data-one' ); ?>" id="awards">
		  <?php ws_ls_postbox_header( [ 'title' => __('Awards', WE_LS_SLUG), 'postbox-id' => 'awards', 'postbox-col' => 'ws-ls-user-data-one' ] ); ?>
		  <div class="inside">
		<?php

			if( true === ws_ls_has_a_valid_pro_plus_license() ) {

			 	$awards = ws_ls_awards_previous_awards( $user_id );

				if ( false === empty( $awards ) ) {

					echo ws_ls_awards_render_badges( [ 'user-id' => $user_id ] );

					printf( '<a class="button-secondary delete-awards-confirm" href="%1$s">
								  <i class="fa fa-exclamation-circle"></i>
								  %2$s
							</a>',
							esc_url( admin_url( 'admin.php?page=ws-ls-data-home&mode=user&remove-awards=y&user-id=' . (int) $user_id ) ),
							__( 'Delete ALL existing awards', WE_LS_SLUG )
					);

					ws_ls_create_dialog_jquery_code( 	__('Are you sure you?', WE_LS_SLUG ),
												__('Are you sure you wish to remove all awards for this user? Note, when the user next enters a weight the awards will be re-calculated.', WE_LS_SLUG) . '<br /><br />',
								'delete-awards-confirm'
					);
				}

			} else {
				  printf('<p><a href="%s">%s</a> <strong>- %s</strong> - %s. <a href="%s">%s</a>.</p>',
					  ws_ls_upgrade_link(),
					  __( 'Upgrade to Pro Plus', WE_LS_SLUG ),
					  __( 'Awards', WE_LS_SLUG ),
					  __( 'view and issue awards based upon users meeting your certain goals' , WE_LS_SLUG ),
					  ws_ls_awards_base_url(),
					  __( 'Modify your awards', WE_LS_SLUG )
				  );
			 } ?>
		  </div>
  	</div>
<?php
}

/*
 * User entries postbox
 */
function ws_ls_postbox_user_entries( $user_id ) {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'user-entries', 'ws-ls-user-data-one' ); ?>" id="user-entries">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Entries for this user', WE_LS_SLUG ), 'postbox-id' => 'user-entries', 'postbox-col' => 'ws-ls-user-data-one' ] ); ?>
		<div class="inside">
			<?php echo ws_ls_data_table_render( [ 'smaller-width' => true, 'user-id' => $user_id, 'bmi-format' => 'both' ] ); ?>
		</div>
	</div>
<?php
}
