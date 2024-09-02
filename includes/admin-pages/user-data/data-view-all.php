<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_view_all() {

    ws_ls_permission_check_message();

	$title = apply_filters( 'wlt-filter-view-all-title', esc_html__( 'View All Data', WE_LS_SLUG ) );
    ?>
<div class="wrap ws-ls-user-data ws-ls-admin-page">
	<h1><?php echo $title; ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
                    <?php
                    if ( true !== WS_LS_IS_PRO ) {
                        ws_ls_display_pro_upgrade_notice();
                    }
                    ?>
					<div class="postbox">

						<h2 class="hndle"><span><?php echo $title; ?></span></h2>

						<div class="inside">
                            <?php

                                    do_action( 'wlt-hook-view-all-table' );

                                    if( true=== (bool) apply_filters( 'wlt-filters-admin-view-all-enabled', true ) ) {

	                                    $entry_counts = ws_ls_db_entries_count();

                                        if ( false === empty( $entry_counts ) ) {

                                            echo sprintf( '
                                                                        <p>
                                                                            <strong>%s:</strong> %s | <strong>%s:</strong> %s | <strong>%s:</strong> %s |
                                                                            <a href="%s">%s</a> | <a href="%s">%s</a>
                                                                        </p>',
                                                esc_html__( 'Number of WordPress users', WE_LS_SLUG ),
                                                ws_ls_round_number( $entry_counts['number-of-users'] ),
                                                esc_html__( 'Number of weight entries', WE_LS_SLUG ),
                                                ws_ls_round_number( $entry_counts['number-of-entries'] ),
                                                esc_html__( 'Number of targets entered', WE_LS_SLUG ),
                                                ws_ls_round_number( $entry_counts['number-of-targets'] ),
                                                ws_ls_get_link_to_export(),
                                                esc_html__( 'Export to CSV', WE_LS_SLUG ),
                                                ws_ls_get_link_to_export( 'json' ),
                                                esc_html__( 'Export to JSON', WE_LS_SLUG )
                                            );
                                        }

                                        if ( $entry_counts['number-of-entries'] > 5000 ) {
                                            printf( '<p class="ws-ls-validation-error"><strong>%s</strong></p>', esc_html__( 'For performance reasons, the following table shall be restricted to a maximum of 5000 entries. For more data, please view individual user records.' ) );
                                        }

                                        // Show meta data?
                                        if ( false === empty( $_GET['show-meta'] ) ) {
                                            $value = 'y' === $_GET['show-meta'];
                                            update_option( 'ws-ls-show-meta', $value );
                                        }

                                        $show_meta = (bool) get_option( 'ws-ls-show-meta' );

                                        echo ws_ls_data_table_render( [ 'limit' => 5000, 'enable-meta-fields' => $show_meta, 'custom-field-col-size' => 'xs' ] );

                                        if ( ws_ls_meta_fields_number_of_enabled() > 0 ) {

                                            echo sprintf(
                                                '&nbsp;<a class="btn button-secondary" href="%s"><i class="fas fa-book-reader"></i> %s</a>',
                                                admin_url( 'admin.php?page=ws-ls-data-home&mode=all&show-meta=' ) . ( ( false === $show_meta ) ? 'y' : 'n' ),
                                                ( false === $show_meta ) ? esc_html__( 'Include Custom Fields (Slower)', WE_LS_SLUG ) : esc_html__( 'Hide Custom Fields (Quicker)', WE_LS_SLUG )
                                            );

                                        }
                            }
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php
}
