<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_summary() {

    ws_ls_permission_check_message();

    // DELETE ALL DATA!! AHH!!
    if (is_admin() && isset($_GET['removedata']) && 'y' == $_GET['removedata']) {

	    ws_ls_delete_existing_data();

        // Let others know we cleared all user data
        do_action( 'wlt-hook-data-all-deleted' );
    }

    ws_ls_data_table_enqueue_scripts();
?>
<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<h1><?php echo __('Summary', WE_LS_SLUG); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable" id="ws-ls-user-summary-one">
                    <?php
                        if ( true !== WS_LS_IS_PRO ) {
                            ws_ls_display_pro_upgrade_notice();
                        }

                        $user_summary_order = get_option( 'ws-ls-postbox-order-ws-ls-user-summary-one', [ 'league-table', 'weight-change-by-group', 'summary-entries' ] );

                        $user_summary_order = apply_filters( 'wlt-filters-postbox-order-ws-ls-user-summary-one', $user_summary_order );

						foreach ( $user_summary_order as $postbox ) {

							if ( 'league-table' === $postbox ) {
								ws_ls_postbox_league_table();
							} elseif ( 'weight-change-by-group' === $postbox ) {
								ws_ls_postbox_change_by_groups();
							} elseif ( 'summary-entries' === $postbox ) {
								ws_ls_postbox_latest_entries();
							} else {
                                if ( true === function_exists( 'ws_ls_postbox_' . $postbox ) ) {
                                    call_user_func( 'ws_ls_postbox_' . $postbox ) ;
                                }
							}
						}
                    ?>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables" id="ws-ls-user-summary-two">
					<?php

						$user_summary_order = get_option( 'ws-ls-postbox-order-ws-ls-user-summary-two', [ 'user-search', 'quick-stats', 'view-all', 'export', 'delete-data' ] );

                        $user_summary_order = apply_filters( 'wlt-filters-postbox-order-ws-ls-user-summary-two', $user_summary_order );

						foreach ( $user_summary_order as $postbox ) {

							if ( 'user-search' === $postbox ) {
								ws_ls_postbox_user_search();
							} elseif ( 'quick-stats' === $postbox ) {
								ws_ls_postbox_quick_stats();
							} elseif ( 'view-all' === $postbox ) {
								ws_ls_postbox_view_all();
							} elseif ( 'export' === $postbox ) {
								ws_ls_postbox_export();
							} elseif ( 'delete-data' === $postbox ) {
								ws_ls_postbox_delete_data();
							}
						}

					 ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php

    ws_ls_create_dialog_jquery_code(__('Are you sure you?', WE_LS_SLUG),
        __('Are you sure you wish to remove all user data?', WE_LS_SLUG) . '<br /><br />',
        'delete-confirm');
}

function ws_ls_postbox_quick_stats() {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'quick-stats', 'ws-ls-user-summary-two' ); ?>" id="quick-stats">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Quick Stats', WE_LS_SLUG ), 'postbox-id' => 'quick-stats', 'postbox-col' => 'ws-ls-user-summary-two' ] ); ?>
		<div class="inside">
			<?php

				$entry_counts = ws_ls_db_entries_count();

				if(false === empty($entry_counts)) {

					echo sprintf('<table class="ws-ls-sidebar-stats">
										<tr>
											<th>%1$s</th>
											<td data-testid="wt-no-wp-users">%2$s</td>
										</tr>
										<tr>
											<th>%3$s</th>
											<td class="%9$s" data-testid="wt-no-weights">%4$s</td>
										</tr>
										<tr>
											<th>%5$s</th>
											<td class="%9$s" data-testid="wt-no-targets">%6$s</td>
										</tr>
								   </table>

									<p><small>(* %7$s %8$s)</small></p>',
									__('No. of WordPress users', WE_LS_SLUG),
									$entry_counts['number-of-users'],
									__('No. of Weight Entries', WE_LS_SLUG),
									ws_ls_blur_text( $entry_counts['number-of-entries'] ),
									__('No. of Target Entries', WE_LS_SLUG),
									ws_ls_blur_text( $entry_counts['number-of-targets'] ),
									__('refreshed every 15 minutes', WE_LS_SLUG),
									'<a href="' . admin_url( 'admin.php?page=ws-ls-data-home&regenerate-stats=y' ) . '"><small>Regenerate these stats</small></a>',
									ws_ls_blur( false, false )
					);
				}
			?>
		</div>
	</div>
<?php
}

function ws_ls_postbox_view_all() {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'view-all', 'ws-ls-user-summary-two' ); ?>" id="view-all">
		<?php ws_ls_postbox_header( [ 'title' => __( 'View all data', WE_LS_SLUG ), 'postbox-id' => 'view-all', 'postbox-col' => 'ws-ls-user-summary-two' ] ); ?>
		<div class="inside">
			<a class="button-primary" href="<?php echo ws_ls_get_link_to_user_data() . '&amp;mode=all'; ?>">
				<i class="fa fa-book"></i>
				<?php echo __('View all entries', WE_LS_SLUG); ?>
			</a>
		</div>
	</div>
<?php
}

function ws_ls_postbox_export() {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'export', 'ws-ls-user-summary-two' ); ?>" id="export">
		<?php ws_ls_postbox_header( [ 'title' => __( 'Export all data', WE_LS_SLUG ), 'postbox-id' => 'export', 'postbox-col' => 'ws-ls-user-summary-two' ] ); ?>
		<div class="inside">
		    <?php if ( ! ws_ls_permission_check_export_delete() ) : ?>
				<?php printf( '<p>%s</p>',  __('You do not have permission to do this.', WE_LS_SLUG ) ); ?>
			<?php else : ?>
                <a class="button-secondary button-wt-to-excel" href="<?php echo ws_ls_export_link('new', [ 'format' => 'csv', 'title' => __( 'All Data', WE_LS_SLUG ) ] ); ?>">
                    <i class="fa fa-file-excel-o"></i>
                    <?php echo __('To CSV', WE_LS_SLUG); ?>
                </a>
                <a class="button-secondary button-wt-to-json" href="<?php echo ws_ls_export_link('new', [ 'format' => 'json', 'title' => __( 'All Data', WE_LS_SLUG ) ] ); ?>">
                    <i class="fa fa-file-code-o"></i>
                    <?php echo __('To JSON', WE_LS_SLUG); ?>
                </a>
			<?php endif; ?>
		</div>
	</div>
<?php
}

function ws_ls_postbox_delete_data() {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'delete-data', 'ws-ls-user-summary-two' ); ?>" id="delete-data" >
		<?php ws_ls_postbox_header( [ 'title' => __( 'Delete Data', WE_LS_SLUG ), 'postbox-id' => 'delete-data', 'postbox-col' => 'ws-ls-user-summary-two' ] ); ?>
		<div class="inside">
		<?php if ( ! ws_ls_permission_check_export_delete() ) : ?>
				<?php printf( '<p>%s</p>',  __('You do not have permission to do this.', WE_LS_SLUG ) ); ?>
			<?php else : ?>
                <a class="button-secondary delete-confirm" href="<?php echo admin_url( 'admin.php?page=ws-ls-data-home&removedata=y' ); ?>">
                    <i class="fa fa-exclamation-circle"></i>
                    <?php echo __('Delete data for ALL users', WE_LS_SLUG); ?>
                </a>
			<?php endif; ?>
		</div>
	</div>
<?php
}

function ws_ls_postbox_league_table() {
?>
	<div class="postbox <?php ws_ls_postbox_classes( 'league-table' ); ?>" id="league-table">
		<?php

			// If changing gain / loss set options
			if( false === empty($_GET['show-gain'])) {

				$value = 'y' === $_GET['show-gain'];
				update_option('ws-ls-show-gains', $value);
			}

			// Are we wanting to see who has lost the most? Or gained?
			$show_gain 	= (bool) get_option( 'ws-ls-show-gains' );
			$title 		= ( false === $show_gain ) ? __( 'League table for those that have lost the most', WE_LS_SLUG ) : __( 'League Table for those that have gained the most', WE_LS_SLUG );

			ws_ls_postbox_header( [ 'title' => $title, 'postbox-id' => 'league-table' ] );
		?>
		<div class="inside">
			<?php
			$ignore_cache = false;

			// Run stats if plugin version number has changed!
			if( true === WS_LS_IS_PRO && update_option('ws-ls-version-number-stats', WE_LS_CURRENT_VERSION) || (false === empty($_GET['regenerate-stats']) && 'y' == $_GET['regenerate-stats'])) {
				ws_ls_db_stats_clear_last_updated_date();
				ws_ls_stats_run_cron();
				ws_ls_tidy_cache_on_delete();
				$ignore_cache = true;
			}

			echo ws_ls_shortcode_stats_league_total(['ignore_cache' => $ignore_cache, 'order' => (false === $show_gain) ? 'asc' : 'desc']);

			if( true === WS_LS_IS_PRO ) {
				?>
				<p>
					<small><?php echo __( 'Please note: For performance reasons, this table only will update every hour. Click the following button to manually update.', WE_LS_SLUG ); ?></small>
				</p>
				<a class="btn button-secondary"
				   href="<?php echo admin_url( 'admin.php?page=ws-ls-data-home&regenerate-stats=y' ); ?>"><i
						class="fa fa-refresh"></i> <?php echo __( 'Regenerate these stats', WE_LS_SLUG ); ?>
				</a>
				<?php

				echo sprintf(
					'<a class="btn button-secondary" href="%s"><i class="fa fa-arrows-v"></i> %s</a>',
					admin_url( 'admin.php?page=ws-ls-data-home&show-gain=' ) . ( ( false === $show_gain ) ? 'y' : 'n' ),
					( false === $show_gain ) ? __( 'Show who has gained the most', WE_LS_SLUG ) : __( 'Show who has lost the most', WE_LS_SLUG )
				);
			}
			?>
		</div>
	</div>
<?php
}

function ws_ls_postbox_latest_entries() {?>
	<div class="postbox <?php ws_ls_postbox_classes( 'summary-entries' ); ?>" id="summary-entries">
		<?php

			// Show 100 most recent entries? Or show 500?
			if( true === isset( $_GET[ 'entries-limit' ] ) ) {
				$value = ws_ls_querystring_value( 'entries-limit', true );
				update_option( 'ws-ls-entries-limit', $value );
			}

			// Show meta data?
			if( false === empty( $_GET['show-meta'] ) ) {
				$value = 'y' === $_GET['show-meta'];
				update_option('ws-ls-show-meta', $value );
			}

			$entries_limit  = (int) get_option( 'ws-ls-entries-limit', 100 );
			$show_meta  	= (bool) get_option( 'ws-ls-show-meta' );

			$title = ( false === empty( $entries_limit ) ) ? sprintf( 'Last %d entries', $entries_limit ) : __( 'All entries', WE_LS_SLUG );

			ws_ls_postbox_header( [ 'title' => $title, 'postbox-id' => 'summary-entries' ] );
		?>
		<div class="inside">
			<?php

				if ( true === empty( $entries_limit ) ) {
					$entries_limit = NULL;
				}

				echo ws_ls_data_table_render( [ 'limit' => $entries_limit, 'smaller-width' => true, 'enable-meta-fields' => $show_meta, 'page-size' => 20, 'bmi-format' => 'both' ] );

				if ( 100 !== $entries_limit ) {
					echo sprintf(
								'<a class="btn button-secondary" href="%s"><i class="fa fa-book"></i> %s</a>&nbsp;',
								admin_url( 'admin.php?page=ws-ls-data-home&entries-limit=100' ),
								__( 'Show 100 recent entries', WE_LS_SLUG )
							);
				}

				if ( 500 !== $entries_limit ) {
					echo sprintf(
								'<a class="btn button-secondary" href="%s"><i class="fa fa-book"></i> %s</a>&nbsp;',
								admin_url( 'admin.php?page=ws-ls-data-home&entries-limit=500' ),
								__( 'Show 500 recent entries', WE_LS_SLUG )
							);
				}

				if ( false === empty( $entries_limit ) ) {
					echo sprintf('<a class="btn button-secondary" href="%s"><i class="fa fa-book"></i> %s (%s)</a>&nbsp;',
									admin_url( 'admin.php?page=ws-ls-data-home&entries-limit=0' ),
									__( 'Show all entries', WE_LS_SLUG ),
									__( 'slow!', WE_LS_SLUG )
								);
				}

				if ( ws_ls_meta_fields_number_of_enabled() > 0 ) {
					echo sprintf(
						'<a class="btn button-secondary" href="%s"><i class="fas fa-book-reader"></i> %s</a>',
						admin_url( 'admin.php?page=ws-ls-data-home&show-meta=' ) . ( ( false === $show_meta ) ? 'y' : 'n'),
						( false === $show_meta ) ? __( 'Include Custom Fields (Slower)', WE_LS_SLUG ) : __( 'Hide Custom Fields (Quicker)', WE_LS_SLUG )
					);
				}

			?>
		</div>
	</div>

<?php
}

function ws_ls_postbox_change_by_groups() {

	if ( false === ws_ls_groups_do_we_have_any() ) {
		return;
	}

	?>
	<div class="postbox <?php ws_ls_postbox_classes( 'weight-change-by-group' ); ?>" id="weight-change-by-group">
		<?php
			ws_ls_postbox_header( [ 'title' => __( 'Weight change by group', WE_LS_SLUG ), 'postbox-id' => 'weight-change-by-group' ] );
		?>
		<div class="inside">
			<table class="ws-ls-settings-groups-list-ajax table ws-ls-loading-table" id="groups-list-stats"
				   data-paging="true"
				   data-filtering="false"
				   data-sorting="true"
				   data-editing-allow-add="false"
				   data-editing-allow-delete="false"
				   data-editing-allow-edit="false"
				   data-cascade="true"
				   data-paging-size="10"
				   data-toggle="true"
				   data-use-parent-width="true">
			</table>
			<a class="btn button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-data-home&regenerate-stats=y' ); ?>"><i class="fa fa-refresh"></i> <?php echo __('Regenerate these stats', WE_LS_SLUG); ?></a>
			<a class="btn button-secondary" href="<?php echo admin_url( 'admin.php?page=ws-ls-settings&mode=groups' ); ?>"><i class="fa fa-eye"></i> <?php echo __('View / Edit', WE_LS_SLUG); ?></a>
		</div>
	</div>

	<?php
}
