<?php

defined('ABSPATH') or die('Naw ya dinnie!');

// ------------------------------------------------------------------------------
// User search Search box
// ------------------------------------------------------------------------------

function ws_ls_box_user_search_form( $ajax_mode = false ) {

	?>	<p><?php echo __('Enter a user\'s email address, display name or username and click Search.', WE_LS_SLUG); ?></p>
		<form id="wlt-user-search"
				<?php if (false === $ajax_mode): ?>
					method="get" action="<?php echo ws_ls_get_link_to_user_data(); ?>"
				<?php else: ?>
					class="wlt-user-search-ajax"
				<?php endif; ?>
		>
			<input type="text" name="search" placeholder="" id="ws-ls-search-field" />
            <input type="hidden" name="page" value="ws-ls-data-home"  />
            <input type="hidden" name="mode" value="search-results"  />
			<input type="submit" class="button" value="Search" id="ws-ls-search-button" />
		</form>
	<?php
}

// ------------------------------------------------------------------------------
// User Side Bar
// ------------------------------------------------------------------------------

/**
 * @param $user_id
 */
function ws_ls_user_side_bar($user_id) {

	if( true === empty($user_id ) )  {
		return;
	}

	$settings_url = ws_ls_get_link_to_user_settings($user_id);

	?>
		<div class="postbox">
			<h2 class="hndle"><?php echo __('User Search', WE_LS_SLUG); ?></h2>
			<div class="inside">
				<?php ws_ls_box_user_search_form(); ?>
			</div>
		</div>

		<?php if ( true === ws_ls_meta_fields_photo_any_enabled() ) : ?>
			<div class="postbox">
				<h2 class="hndle"><?php echo __('Most Recent Photo', WE_LS_SLUG); ?></h2>
				<div class="inside">
					<center>
						<?php

							if( true === ws_ls_has_a_valid_pro_plus_license() ) {
								echo ws_ls_photos_shortcode_recent( [ 'user-id' => $user_id, 'width' => 200, 'height' => 200, 'hide-date' => true ] );

                                $photo_count = ws_ls_photos_db_count_photos( $user_id );

                                echo sprintf('<p>%s <strong>%s</strong>. <a href="%s">%s</a></p>',
                                    __( 'No. of photos: ', WE_LS_SLUG ),
                                    $photo_count,
                                    ws_ls_get_link_to_photos( $user_id),
                                    __( 'View all', WE_LS_SLUG )
                                );
							} else {
								echo sprintf('<a href="%s">%s</a>', ws_ls_upgrade_link(), __( 'Upgrade to Pro Plus', WE_LS_SLUG ) );
							}
					   ?>
					</center>
				</div>
			</div>
		<?php endif; ?>

		<div class="postbox ws-ls-user-data">
			<h2 class="hndle"><span><?php echo __('User Information', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
    			<table class="ws-ls-sidebar-stats">

					<?php echo ws_ls_side_bar_render_rows( apply_filters( 'wlt-filter-admin-user-sidebar-top', [], $user_id) ); ?>

                    <?php $stats = ws_ls_db_entries_count($user_id); ?>
                    <tr>
                        <th><?php echo __('No. of Entries', WE_LS_SLUG); ?></th>
                        <td><?php echo $stats['number-of-entries']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Start Weight', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_start_weight($user_id) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Latest Weight', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_recent_weight($user_id) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Diff. from Start', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_difference_in_weight_from_oldest($user_id) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Target Weight', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>">
                            <a href="<?php echo ws_ls_get_link_to_edit_target($user_id); ?>">
                                <?php

                                $target = ws_ls_target_get( $user_id );
                                echo ( true === empty( $target[ 'display' ] ) ) ? __( 'No target set', WE_LS_SLUG ) : ws_ls_blur_text( $target[ 'display' ] );
                                ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('Diff. from Target', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_difference_in_weight_target($user_id) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Current BMI', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_shortcode_bmi([ 'user-id' => $user_id, 'display' => 'both', 'no-height-text' => __('No height specified', WE_LS_SLUG)]); ?></td>
                    </tr>

					<?php echo ws_ls_side_bar_render_rows( apply_filters( 'wlt-filter-admin-user-sidebar-middle', [], $user_id) ); ?>

                    <tr>
                        <th><?php echo __('Aim', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><a href="<?php echo $settings_url; ?>"><?php echo ws_ls_blur_text( ws_ls_display_user_setting($user_id, 'aim') ); ?></a></td>
                    </tr>
					<tr>
                        <th><?php echo __('Height', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><a href="<?php echo $settings_url; ?>"><?php echo ws_ls_blur_text ( ws_ls_display_user_setting($user_id, 'height') ); ?></a></td>
                    </tr>
					<tr>
                        <th><?php echo __('Gender', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><a href="<?php echo $settings_url; ?>"><?php echo ws_ls_blur_text( ws_ls_display_user_setting($user_id, 'gender') ); ?></a></td>
                    </tr>
					<tr>
                        <th><?php echo __('Activity Level', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><a href="<?php echo $settings_url; ?>"><?php echo ws_ls_blur_text( ws_ls_display_user_setting($user_id, 'activity_level', false, true) ); ?></a></td>
                    </tr>
					<tr>
                        <th><?php echo __('Date of Birth', WE_LS_SLUG); ?></th>
                        <td class="<?php echo ws_ls_blur(); ?>"><a href="<?php echo $settings_url; ?>"><?php echo ws_ls_blur_text( ws_ls_get_dob_for_display($user_id, false, true) ); ?></a></td>
                    </tr>
					<tr class="last">
                        <th><?php echo __('BMR', WE_LS_SLUG); ?></th>
                        <td>
							<?php
                                    if(ws_ls_has_a_valid_pro_plus_license()) {
                                        $bmr = ws_ls_calculate_bmr($user_id, false);
				                        echo (false === empty($bmr)) ? esc_html($bmr) : __('Missing data', WE_LS_SLUG);
                                    } else {
                                        echo sprintf('<a href="%s">Upgrade to Pro Plus</a>', ws_ls_upgrade_link());
                                    }
							?>
						</td>
                    </tr>

					<?php echo ws_ls_side_bar_render_rows( apply_filters('wlt-filter-admin-user-sidebar-bottom', [], $user_id) ); ?>

                </table>
			</div>
		</div>
		<div class="postbox ws-ls-user-data">
			<h2 class="hndle"><span><?php echo __('Add Entry', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-primary" href="<?php echo ws_ls_get_link_to_edit_entry($user_id); ?>">
					<i class="fa fa-calendar-plus-o"></i>
					<?php echo __('Add Entry', WE_LS_SLUG); ?>
				</a>
				<a class="button-secondary" href="<?php echo ws_ls_get_link_to_edit_target($user_id); ?>">
					<i class="fa fa-bullseye"></i>
					<?php echo __('Edit Target', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>
		<div class="postbox ws-ls-user-data">
			<h2 class="hndle"><span><?php echo __('Export Data', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-secondary" href="<?php echo ws_ls_export_link('new', [ 'user-id' => $user_id, 'format' => 'csv' ] ); ?>">
					<i class="fa fa-file-excel-o"></i>
					<?php echo __('To CSV', WE_LS_SLUG); ?>
				</a>
				<a class="button-secondary" href="<?php echo ws_ls_export_link('new', [ 'user-id' => $user_id, 'format' => 'json' ] ); ?>">
					<i class="fa fa-file-code-o"></i>
					<?php echo __('To JSON', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>
		<div class="postbox ws-ls-user-data">
			<h2 class="hndle"><span><?php echo __('Settings', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-secondary" href="<?php echo $settings_url; ?>">
						<i class="fa fa-cog"></i>
					<?php echo __('Preferences', WE_LS_SLUG); ?>
				</a>
				<a href="<?php echo get_edit_user_link($user_id); ?>" class="button-secondary"><i class="fa fa-wordpress"></i> WordPress Record</a>
			</div>
		</div>
        <div class="postbox ws-ls-user-data">
            <h2 class="hndle"><span><?php echo __('Delete Cache', WE_LS_SLUG); ?></span></h2>
            <div class="inside">
                <a class="button-secondary" href="<?php echo esc_url(ws_ls_get_link_to_delete_user_cache($user_id )); ?>">
                    <i class="fa fa-refresh"></i>
                    <?php echo __('Delete Cache for this user', WE_LS_SLUG); ?>
                </a>
            </div>
        </div>
		<div class="postbox ws-ls-user-data">
			<h2 class="hndle"><span><?php echo __('Delete Data', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-secondary delete-confirm" href="<?php echo esc_url(admin_url( 'admin.php?page=ws-ls-data-home&mode=user&removedata=y&user-id=' . $user_id )); ?>">
					<i class="fa fa-trash-o"></i>
					<?php echo __('Delete ALL data for this user', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>

	<?php
        ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
            __('Are you sure you wish to remove the data for this user?', WE_LS_SLUG) . '<br /><br />',
            'delete-confirm');

}

/**
 * Render a sidebar row.
 *
 * @param $row
 * @return string
 */
function ws_ls_side_bar_row($row) {

	if (true === is_array($row) && 2 === count($row)) {

		return sprintf('<tr>
                        			<th>%s</th>
									<td>%s</td>
								</tr>',
								$row['th'],
								$row['td']
		);
	}

	return '';
}

/**
 * Render one or more sidebar rows.
 *
 * @param $rows
 * @return string
 */
function ws_ls_side_bar_render_rows( $rows ) {

	if ( true === empty( $rows ) ) {
	    return '';
    }

	$html = '';

	foreach ( $rows as $row ) {
		$html .= ws_ls_side_bar_row( $row );
	}

	return $html;

}

/**
 * Displays a navigational header at top of user data page
 *
 * @param $user_id
 * @param bool $previous_url
 */
function ws_ls_user_header( $user_id, $previous_url = false ) {

	if( true === empty( $user_id ) || false === ws_ls_user_exist_check( $user_id ) ) {
		return;
	}

    $previous_url = ( true === empty( $previous_url ) ) ? ws_ls_get_link_to_user_data() : $previous_url;

    $additional_links = apply_filters( 'wt_ls_user_profile_header_links', '', $user_id );

    echo sprintf('
        <h3>%s %s</h3>
        <div class="postbox ws-ls-user-data">
            <div class="inside">
                <a href="%s" class="button-secondary"><i class="fa fa-arrow-left"></i> <span>%s</span></a>
                <a href="%s" class="button-secondary"><i class="fa fa-wordpress"></i> <span>%s</span></a>
                <a href="%s" class="button-secondary"><i class="fa fa-line-chart"></i> <span>%s</span></a>
                %s
            </div>
        </div>',
        ws_ls_user_display_name( $user_id ),
        ws_ls_get_email_link( $user_id, true ),
        esc_url( $previous_url ),
        __( 'Back', WE_LS_SLUG ),
        get_edit_user_link( $user_id ),
        __('WordPress Record', WE_LS_SLUG ),
        ws_ls_get_link_to_user_profile( $user_id ),
        __('Weight Tracker Record', WE_LS_SLUG ),
        wp_kses_post( $additional_links )
    );
}

/**
 * Render Postbox header
 * @param array $args
 */
function ws_ls_postbox_header( $args = [] ) {

		$args = wp_parse_args( $args, [		'title'			=> __( 'Title', WE_LS_SLUG ),
											'show-controls' => true,
											'postbox-id'	=> NULL,
											'postbox-col'	=> 'ws-ls-col-one'
		]);

		echo '<div class="postbox-header">';

		printf( '<h2 class="hndle"><span>%1$s</span></h2>', esc_html( $args[ 'title' ] ) );

		if ( true === $args[ 'show-controls' ] &&
			 	false === empty( $args[ 'postbox-id' ] ) ) {

			printf( '<div class="handle-actions hide-if-no-js">
						<button type="button" class="handle-order-higher ws-ls-postbox-higher" data-postbox-id="%1$s" data-postbox-col="%2$s"><span class="order-higher-indicator"></span></button>
						<button type="button" class="handle-order-lower ws-ls-postbox-lower" data-postbox-id="%1$s" data-postbox-col="%2$s"><span class="order-lower-indicator"></span></button>
						<button type="button" class="handlediv" data-postbox-id="%1$s" data-postbox-col="%2$s"><span class="toggle-indicator"></span></button>
					</div>',
					esc_attr( $args[ 'postbox-id' ] ),
					esc_attr( $args[ 'postbox-col' ] )
			);
		}

		echo '</div>';
}

/**
 * Show / Hide postbox?
 * @param $id
 *
 * @return bool
 */
function ws_ls_postbox_show( $id ) {

	$key = sprintf( 'ws-ls-postbox-%s-display', $id );

	return (bool) get_option( $key, true );
}

/**
 * Render class to hide postbox if needed
 *
 * @param $id
 * @param string $column
 *
 * @return string|void
 */
function ws_ls_postbox_classes( $id, $column = 'ws-ls-col-one' ) {

	$classes = [ 'ws-ls-postbox', $column ];

	if ( false === ws_ls_postbox_show( $id ) ) {
		$classes[] = 'closed';
	}

	$classes = implode( ' ', $classes );

	echo esc_attr( $classes );
}

// ------------------------------------------------------------------------------
// Helper functions
// ------------------------------------------------------------------------------

/**
 * Fetch the user's ID from the querystring key user-id
 *
 * @return int
 */
function ws_get_user_id_from_qs(){
	return (int) ws_ls_querystring_value( 'user-id', true );
}
