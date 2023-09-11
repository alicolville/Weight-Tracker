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
function ws_ls_user_side_bar( $user_id ) {

	if( true === empty( $user_id ) )  {
		return;
	}

	echo '<div class="meta-box-sortables" id="ws-ls-user-data-two">';

	// Allow an additional admin section to be added in
	$customised_section = apply_filters( 'wlt-filters-admin-sidebar-custom-section', '' );

	if ( false === empty( $customised_section ) ) {
		echo wp_kses_post( $customised_section  );
	}

	$default_order		= [ 'user-search', 'most-recent', 'user-information', 'notes', 'add-entry', 'export-data', 'settings', 'delete-cache', 'delete-data' ];
	$user_sidebar_order = get_option( 'ws-ls-postbox-order-ws-ls-user-data-two', $default_order );

	// Notes not in the array (as a new feature)?
	if ( false === in_array( 'notes', $user_sidebar_order ) ) {
		$user_sidebar_order = $default_order;
	}

	// Most recent photo missing? i.e. have we saved the order when it was previously hidden?
	if ( true === ws_ls_meta_fields_photo_any_enabled() && false === in_array( 'most-recent', $user_sidebar_order ) ) {
		array_unshift($user_sidebar_order , 'most-recent' );
	}

	$user_sidebar_order = apply_filters( 'wlt-filters-postbox-order-ws-ls-user-data-sidebar', $user_sidebar_order );

	foreach ( $user_sidebar_order as $postbox ) {

		if ( 'user-search' === $postbox ) {
			ws_ls_postbox_user_search( 'ws-ls-user-data-one' );
		} elseif ( 'most-recent' === $postbox ) {
			ws_ls_postbox_sidebar_recent_photo( $user_id );
		} elseif ( 'user-information' === $postbox ) {
			ws_ls_postbox_sidebar_user_information( $user_id );
		} elseif ( 'add-entry' === $postbox ) {
			ws_ls_postbox_sidebar_add_entry( $user_id );
		} elseif ( 'export-data' === $postbox ) {
			ws_ls_postbox_sidebar_export_data( $user_id );
		} elseif ( 'settings' === $postbox ) {
			ws_ls_postbox_sidebar_settings( $user_id );
		} elseif ( 'delete-cache' === $postbox ) {
			ws_ls_postbox_sidebar_delete_data( $user_id );
		} elseif ( 'delete-data' === $postbox ) {
			ws_ls_postbox_sidebar_settings( $user_id );
		} elseif ( 'notes' === $postbox ) {
			ws_ls_postbox_user_notes( $user_id );
		} else {
            if ( true === function_exists( 'ws_ls_postbox_sidebar_' . $postbox ) ) {
	            call_user_func( 'ws_ls_postbox_sidebar_' . $postbox, $user_id) ;
            }
		}
	}

	echo '</div>';

}

/**
 * Postbox for user search
 *
 * @param string $class
 */
function ws_ls_postbox_user_search( $class = 'ws-ls-user-summary-two' ) {

    $title = apply_filters( 'wlt-filter-user-search-title', __( 'User Search', WE_LS_SLUG ) );
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'user-search', $class ); ?>" id="user-search">
		<?php ws_ls_postbox_header( [ 'title' => $title, 'postbox-id' => 'user-search', 'postbox-col' => $class ] ); ?>
		<div class="inside">
			<?php ws_ls_box_user_search_form(); ?>
		</div>
	</div>
<?php
}

/**
 * Postbox for user notes
 *
 * @param $user_id
 */
function ws_ls_postbox_user_notes( $user_id ) {

	$stats 			= ws_ls_messages_db_stats( $user_id );
	$notes_link 	= ws_ls_get_link_to_notes( $user_id );
	$component_id 	= ws_ls_component_id();

	$title = sprintf( __( ': <span id="%2$s_count">%1$d</span>', WE_LS_SLUG ),
							$stats[ 'notes-count' ],
							$component_id );

	?>
	<div class="postbox ws-ls-notes-add-postbox <?php ws_ls_postbox_classes( 'notes', 'ws-ls-user-data-two' ); ?>" id="notes">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Notes', WE_LS_SLUG ) . $title , 'postbox-id' => 'notes', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
			<p id="<?php echo $component_id; ?>_errormessage" class="ws-ls-validation-error ws-ls-hide">
				<?php echo __( 'There was an error adding the note. Please try again.', WE_LS_SLUG ); ?>
			</p>
			<p id="<?php echo $component_id; ?>_successmessage" class="ws-ls-good ws-ls-hide">
				<?php echo __( 'The note has been saved successfully.', WE_LS_SLUG ); ?>
			</p>
			<p>
			<?php

				printf( 	__( '<a href="%1$s">View All</a> /', WE_LS_SLUG ), esc_url( $notes_link ) );

				printf( 	__( ' <a href="#" id="%1$s_view_most_read">View most recent</a><a href="#" class="ws-ls-hide" id="%1$s_hide_most_read">Hide most recent</a>', WE_LS_SLUG ), $component_id );
				?>
			</p>
			<div id="<?php echo $component_id; ?>_most_recent_comment_div" class="ws-ls-hide">
				<hr />
				<p><?php echo __( 'Most recent comment', WE_LS_SLUG ); ?>:</p>
				<?php
				echo ws_ls_form_field_textarea( [ 	'name' 			=> $component_id . '_most_recent',
													'cols' 			=> 30,
													'rows'			=> 6,
													'disabled'		=> true,
													'placeholder'	=>  __( 'There are no notes for this user', WE_LS_SLUG ),
													'value'			=> esc_html( $stats[ 'notes-latest-text' ] )

				]);
				?>
				<br />
			</div>
			<div id="<?php echo $component_id; ?>_add_new_div">
				<?php
					echo ws_ls_form_field_textarea( [ 	'name' 			=> $component_id . '_textarea',
														'cols' 			=> 30,
														'placeholder' 	=> __( 'Add a note for this user...', WE_LS_SLUG )
					]);

					echo ws_ls_form_field_checkbox( [ 	'id' 				=> $component_id . '_send_email',
														'title'				=> __( 'Send note to user via email', WE_LS_SLUG ),
														'show-label'		=> true,
														'css-class-row' 	=> 'ws-ls-note-checkbox'
					]);

					echo ws_ls_form_field_checkbox( [ 	'id' 				=> $component_id . '_visible_to_user',
														 'title'			=> __( 'Visible to the user?', WE_LS_SLUG ),
														 'show-label'		=> true,
														 'css-class-row' 	=> 'ws-ls-note-checkbox'
					]);

					if ( ws_ls_note_is_enabled() ) {
						printf( '<button id="%1$s_button" class="button">%2$s</button>',
								$component_id,
								__( 'Add note', WE_LS_SLUG )
						);
					} else {
						printf( '<a href="%s">Upgrade to Pro to save notes</a>', ws_ls_upgrade_link() );
					}
				?>
			</div>
			<script>
				jQuery( document ).ready( function ( $ ) {

					let button_id 			= '#<?php echo $component_id; ?>_button';
					let textarea_id 		= '#<?php echo $component_id; ?>_textarea';
					let most_recent_id 		= '#<?php echo $component_id; ?>_most_recent';
					let errormessage_id 	= '#<?php echo $component_id; ?>_errormessage';
					let successmessage_id 	= '#<?php echo $component_id; ?>_successmessage';

					$( button_id ).click( function( event ) {

						event.preventDefault();

						let note = $( textarea_id ).val();

						$( button_id ).addClass( 'ws-ls-loading-button');
						$( errormessage_id ).addClass( 'ws-ls-hide');
						$( successmessage_id ).addClass( 'ws-ls-hide' );

						let data = { 	'action' : 			'ws_ls_add_note',
										'security' : 		'<?php echo wp_create_nonce( 'ws-ls-add-note' ) ?>',
										'user-id' :			<?php echo (int) $user_id; ?>,
										'note' :			note,
										'send-email' :		$( '#<?php echo $component_id; ?>_send_email' ).is(':checked'),
										'visible-to-user' :	$( '#<?php echo $component_id; ?>_visible_to_user' ).is(':checked')
						};

						jQuery.post( "<?php echo admin_url('admin-ajax.php'); ?>", data, function ( response ) {

							if ( parseInt( response ) === 0 ) {
								$( errormessage_id ).removeClass( 'ws-ls-hide' );
								return;
							}

							$( most_recent_id ).val( $( textarea_id ).val() );

							$( textarea_id ).val( '' );

							$( "#<?php echo $component_id; ?>_count" ).text( response );
							$( successmessage_id ).removeClass( 'ws-ls-hide' );

						}).fail(function() {
							$( errormessage_id ).removeClass( 'ws-ls-hide' );
						})
						.always(function() {
							$( button_id ).removeClass( 'ws-ls-loading-button');
						});;
					});

					let hide_most_recent_id 	= '#<?php echo $component_id; ?>_hide_most_read';
					let view_most_recent_id 	= '#<?php echo $component_id; ?>_view_most_read';
					let view_most_recent_div_id = '#<?php echo $component_id; ?>_most_recent_comment_div';
					let view_add_new_div_id 	= '#<?php echo $component_id; ?>_add_new_div';

					$( hide_most_recent_id ).click( function( event ) {

						event.preventDefault();

						$( view_most_recent_id ).removeClass( 'ws-ls-hide' );
						$( hide_most_recent_id ).addClass( 'ws-ls-hide' );
						$( view_most_recent_div_id ).addClass( 'ws-ls-hide' );
						$( view_add_new_div_id ).removeClass( 'ws-ls-hide' );
					});

					$( view_most_recent_id ).click( function( event ) {

						event.preventDefault();

						$( hide_most_recent_id ).removeClass( 'ws-ls-hide' );
						$( view_most_recent_id ).addClass( 'ws-ls-hide' );
						$( view_most_recent_div_id ).removeClass( 'ws-ls-hide' );
						$( view_add_new_div_id ).addClass( 'ws-ls-hide' );
					});

				});

			</script>
		</div>
	</div>
	<?php
}

/**
 * Postbox for recent photo
 *
 * @param $user_id
 */
function ws_ls_postbox_sidebar_recent_photo( $user_id ) {

	if ( false === ws_ls_meta_fields_photo_any_enabled() ) {
		return;
	}

?>
	<div class="postbox <?php ws_ls_postbox_classes( 'most-recent', 'ws-ls-user-data-two' ); ?>" id="most-recent">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Most Recent Photo', WE_LS_SLUG ), 'postbox-id' => 'most-recent', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
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
<?php
}

/**
 * Sidebar for user information
 * @param $user_id
 *
 * @throws Exception
 */
function ws_ls_postbox_sidebar_user_information( $user_id ) {

	$settings_url = ws_ls_get_link_to_user_settings( $user_id );

?>
	<div class="postbox ws-ls-user-data <?php ws_ls_postbox_classes( 'user-information', 'ws-ls-user-data-two' ); ?>" id="user-information">
		<?php ws_ls_postbox_header( [ 'title' => __( 'User summary', WE_LS_SLUG ), 'postbox-id' => 'user-information', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
			<table class="ws-ls-sidebar-stats">

				<?php echo ws_ls_side_bar_render_rows( apply_filters( 'wlt-filter-admin-user-sidebar-top', [], $user_id ) ); ?>

				<?php $stats = ws_ls_db_entries_count($user_id); ?>
				<tr>
					<th><?php echo __('No. of entries', WE_LS_SLUG); ?></th>
					<td><?php echo $stats['number-of-entries']; ?></td>
				</tr>
				<tr>
					<th><?php echo __( 'Starting entry', WE_LS_SLUG ); ?></th>
					<td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_start_date( $user_id ) ); ?></td>
				</tr>
				<tr>
					<th></th>
					<td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_start_weight( $user_id ) ); ?></td>
				</tr>
				<?php if ( (int) $stats['number-of-entries'] > 1 ) : ?>
					<tr>
						<th><?php echo __( 'Latest entry', WE_LS_SLUG ); ?></th>
						<td class="<?php echo ws_ls_blur(); ?>">
							<?php

								echo ws_ls_blur_text( ws_ls_shortcode_recent_date( $user_id ) );
								echo ' ';
								echo ws_ls_blur_text( ws_ls_shortcode_days_between_start_and_latest( [ 'user-id' => $user_id, 'include-brackets' => true, 'include-days' => true ] ) );
							?>
						</td>
					</tr>
					<tr>
						<th></th>
						<td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_recent_weight( $user_id ) ); ?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<th><?php echo __('Diff. from start', WE_LS_SLUG); ?></th>
					<td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_difference_in_weight_from_oldest($user_id) ); ?></td>
				</tr>
				<tr>
					<th><?php echo __('Target weight', WE_LS_SLUG); ?></th>
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
					<th><?php echo __('Diff. from target', WE_LS_SLUG); ?></th>
					<td class="<?php echo ws_ls_blur(); ?>"><?php echo ws_ls_blur_text( ws_ls_shortcode_difference_in_weight_target( [ 'user-id' => $user_id ] ) ); ?></td>
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
					<th><?php echo __('Activity level', WE_LS_SLUG); ?></th>
					<td class="<?php echo ws_ls_blur(); ?>"><a href="<?php echo $settings_url; ?>"><?php echo ws_ls_blur_text( ws_ls_display_user_setting($user_id, 'activity_level', false, true) ); ?></a></td>
				</tr>
				<tr>
					<th><?php echo __('Date of birth', WE_LS_SLUG); ?></th>
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
<?php
}

/**
 * Postbox for add entry buttons
 * @param $user_id
 */
function ws_ls_postbox_sidebar_add_entry( $user_id ) {
?>
	<div class="postbox ws-ls-user-data <?php ws_ls_postbox_classes( 'add-entry', 'ws-ls-user-data-two' ); ?>" id="add-entry">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Add Entry', WE_LS_SLUG ), 'postbox-id' => 'add-entry', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
			<a class="button-primary" href="<?php echo ws_ls_get_link_to_edit_entry( $user_id ); ?>">
				<i class="fa fa-calendar-plus-o"></i>
				<?php

					$text = apply_filters( 'wlt-filter-admin-user-sidebar-add-entry-text', 'Add Entry' );
					echo __( $text, WE_LS_SLUG); 
				?>
			</a>
			<a class="button-secondary" href="<?php echo ws_ls_get_link_to_edit_target( $user_id ); ?>">
				<i class="fa fa-bullseye"></i>
				<?php echo __('Edit Target', WE_LS_SLUG); ?>
			</a>
		</div>
	</div>
<?php
}

/**
 * Postbox for export data
 * @param $user_id
 */
function ws_ls_postbox_sidebar_export_data( $user_id ) {

?>
	<div class="postbox ws-ls-user-data <?php ws_ls_postbox_classes( 'export-data', 'ws-ls-user-data-two' ); ?>" id="export-data">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Export data', WE_LS_SLUG ), 'postbox-id' => 'export-data', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
            <?php if ( ! ws_ls_permission_check_export_delete() ) : ?>
	        	<?php printf( '<p>%s</p>',  __('You do not have permission to do this.', WE_LS_SLUG ) ); ?>
            <?php else : ?>
                <a class="button-secondary button-wt-to-excel" href="<?php echo ws_ls_export_link('new', [ 'user-id' => $user_id, 'format' => 'csv' ] ); ?>">
                    <i class="fa fa-file-excel-o"></i>
                    <?php echo __('To CSV', WE_LS_SLUG); ?>
                </a>
                <a class="button-secondary button-wt-to-json" href="<?php echo ws_ls_export_link('new', [ 'user-id' => $user_id, 'format' => 'json' ] ); ?>">
                    <i class="fa fa-file-code-o"></i>
                    <?php echo __('To JSON', WE_LS_SLUG); ?>
                </a>
		    <?php endif; ?>
		</div>
	</div>
<?php
}

/**
 * Postbox for settings
 * @param $user_id
 */
function ws_ls_postbox_sidebar_settings( $user_id ) {

	$settings_url = ws_ls_get_link_to_user_settings( $user_id );
?>
	<div class="postbox ws-ls-user-data <?php ws_ls_postbox_classes( 'settings', 'ws-ls-user-data-two' ); ?>" id="settings">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Settings', WE_LS_SLUG ), 'postbox-id' => 'settings', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
			<a class="button-secondary" href="<?php echo $settings_url; ?>">
				<i class="fa fa-cog"></i>
				<?php echo __('Preferences', WE_LS_SLUG); ?>
			</a>
			<a href="<?php echo get_edit_user_link( $user_id ); ?>" class="button-secondary"><i class="fa fa-wordpress"></i> WordPress Record</a>
		</div>
	</div>
<?php
}

/**
 * Postbox for deleting cache
 * @param $user_id
 */
function ws_ls_postbox_sidebar_delete_cache( $user_id ) {
?>
	<div class="postbox ws-ls-user-data <?php ws_ls_postbox_classes( 'delete-cache', 'ws-ls-user-data-two' ); ?>" id="delete-cache">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Delete cache', WE_LS_SLUG ), 'postbox-id' => 'delete-cache', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
			<a class="button-secondary" href="<?php echo esc_url( ws_ls_get_link_to_delete_user_cache( $user_id ) ); ?>">
				<i class="fa fa-refresh"></i>
				<?php echo __( 'Delete Cache for this user', WE_LS_SLUG ); ?>
			</a>
		</div>
	</div>
<?php
}

/**
 * Delete data postbox
 *
 * @param $user_id
 */
function ws_ls_postbox_sidebar_delete_data( $user_id ) {
?>
	<div class="postbox ws-ls-user-data <?php ws_ls_postbox_classes( 'delete-data', 'ws-ls-user-data-two' ); ?>" id="delete-data">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Delete data', WE_LS_SLUG ), 'postbox-id' => 'delete-data', 'postbox-col' => 'ws-ls-user-data-two' ] ); ?>
		<div class="inside">
			<?php if ( ! ws_ls_permission_check_export_delete() ) : ?>
				<?php printf( '<p>%s</p>',  __('You do not have permission to do this.', WE_LS_SLUG ) ); ?>
			<?php else : ?>
                <a class="button-secondary delete-confirm" href="<?php echo esc_url( admin_url( 'admin.php?page=ws-ls-data-home&mode=user&removedata=y&user-id=' . $user_id ) ); ?>">
                    <i class="fa fa-trash-o"></i>
					<?php echo __('Delete ALL data for this user', WE_LS_SLUG); ?>
                </a>
			<?php endif; ?>
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
                <a href="%s" class="button-secondary button-wt-back"><i class="fa fa-arrow-left"></i> <span>%s</span></a>
                <a href="%s" class="button-secondary button-wt-wp-record"><i class="fa fa-wordpress"></i> <span>%s</span></a>
                <a href="%s" class="button-secondary button-wt-record"><i class="fa fa-line-chart"></i> <span>%s</span></a>
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
											'postbox-col'	=> 'ws-ls-user-summary-one'
		]);

		echo '<div class="postbox-header">';

		printf( '<h2 class="hndle"><span>%1$s</span></h2>', wp_kses_post( $args[ 'title' ] ) );

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
function ws_ls_postbox_classes( $id, $column = 'ws-ls-user-summary-one' ) {

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
