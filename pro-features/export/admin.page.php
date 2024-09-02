<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_export_admin_page() {

    ws_ls_permission_check_message();

    $delete_id = ws_ls_querystring_value( 'delete', true );

    if ( false === empty( $delete_id ) ) {
		ws_ls_export_delete( $delete_id );
	}

	switch ( ws_ls_querystring_value( 'mode' ) ) {

		case 'new':
			ws_ls_export_admin_page_new();
			break;

		case 'process':
			ws_ls_export_admin_page_process();
			break;

		default:
			ws_ls_export_admin_page_summary();
	}

}

/**
* Initial summary page
 */
function ws_ls_export_admin_page_summary() {
?>
	<div class="wrap ws-ls-challenges ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
	              	<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Export Data', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php
								if ( true !== WS_LS_IS_PRO ) {
									ws_ls_display_pro_upgrade_notice();
								}
							?>
							<p>
								<a href="<?php echo ws_ls_export_link( 'new' ); ?>" class="btn btn-default button-primary">
									<i class="fa fa-plus"></i>
									<?php echo esc_html__( 'Start a new export', WE_LS_SLUG ); ?>
								</a>
							</p>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Previous exports', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php

							$previous_exports = ws_ls_db_export_criteria_all();

							if ( false === empty( $previous_exports ) ) {

								echo '<ul class="ws-ls-export-list">';

								foreach ( $previous_exports as $export ) {

									$title = sprintf( '%s &middot; %s',
										ws_ls_export_render_date( $export[ 'created' ] ),
										esc_html( $export[ 'options' ][ 'title' ] )
									);

									$title .= '<span>';

									$title .= sprintf( '<a href="%s">%s</a> &middot; ',
										ws_ls_export_link( 'view', [ 'delete' => $export[ 'id' ] ] ),
										esc_html__( 'Delete', WE_LS_SLUG )
									);

									if ( 100 === (int) $export[ 'step' ] ) {
										$title .= sprintf( '<a href="%s">%s %s</a>',
											ws_ls_export_file_url( $export[ 'id' ] ),
											esc_html__( 'Download', WE_LS_SLUG ),
											esc_html( $export[ 'file' ] )
										);
									} else {
										$title .= sprintf( '<a href="%s%d">%s ></a>',
											admin_url( 'admin.php?page=ws-ls-export-data&mode=process&id='),
											$export[ 'id' ],
											esc_html__( 'finish processing', WE_LS_SLUG )
										);
									}

									$title .= '</span>';

									printf( '<li>%s</li>', $title );

								}

								echo '</ul>';

							} else {
								printf( '<p>%s</p>', esc_html__( 'No data has been exported.' ) );
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

/**
* Create new report criteria
 */
function ws_ls_export_admin_page_new() {

	ws_ls_enqueue_files();

	ws_ls_enqueue_form_dependencies();

	$title = NULL;

?>
	<form method="post" id="ws-ls-export-new-form" action="<?php echo admin_url( 'admin.php?page=ws-ls-export-data&mode=process'); ?>">
		<input type="hidden" name="add-report" value="yes" />

		<div class="wrap ws-ls-challenges ws-ls-admin-page">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h2 class="hndle"><span><?php echo esc_html__( 'Details', WE_LS_SLUG ); ?></span></h2>
							<div class="inside">
								<?php
									if (true !== WS_LS_IS_PRO) {
										ws_ls_display_pro_upgrade_notice();
									}

									$title = ws_ls_querystring_value( 'title' );

									echo ws_ls_form_field_text( [ 'name' => 'title', 'title' => esc_html__( 'Title', WE_LS_SLUG ), 'show-label' => true, 'required' => true, 'css-class' => 'set-title widefat', 'value' => $title ] );
								?>
							</div>
						</div>
						<div class="postbox">
							<h2 class="hndle"><span><?php echo esc_html__( 'Which weight entries would you like to include?', WE_LS_SLUG ); ?></span></h2>
							<div class="inside">

								<p><?php echo esc_html__( 'Use the following options to specify which weight entries should appear in the export.', WE_LS_SLUG ); ?></p>

								<?php

									$user_id = ws_ls_querystring_value( 'user-id' );

									if ( false === empty( $user_id ) ) {

										printf( '<input type="hidden" name="user-id" value="%d" />', $user_id );

										printf( '<h4>%s</h4>', esc_html__( 'User', WE_LS_SLUG ) );

										$display_name = ws_ls_user_display_name( $user_id );

										printf( '<p>%s (ID: %d)</p>', $display_name, $user_id );

										$title = $display_name;

									} else {

										$user_group_id = ws_ls_querystring_value( 'user-group' );

										if ( false === empty( $user_group_id ) ) {

											$user_group = ws_ls_groups_get( $user_group_id);

											if ( false === empty( $user_group ) ) {

												printf( '<input type="hidden" name="user-group" value"%d" />', $user_group_id );

												printf( '<h4>%s</h4>', esc_html__( 'User Group', WE_LS_SLUG ) );

												printf( '<p>%s (ID: %d)</p>', $user_group[ 'name' ], $user_group_id );

												$title = $user_group[ 'name' ];
											}

										} else {

											$user_groups = ws_ls_groups( true, true );
											$user_groups = wp_list_pluck( $user_groups, 'name', 'id' );

											if ( false === empty( $user_groups ) ) {

												printf( '<h4>%s</h4>', esc_html__( 'User Group', WE_LS_SLUG ) );

												echo ws_ls_form_field_select( [ 'key' => 'user-group', 'label' => esc_html__( 'Group', WE_LS_SLUG ), 'values' => $user_groups, 'selected' => '', 'empty-option' => true, 'css-class' => 'widefat' ] );
											}

										}
									}
								?>
								<h4><?php echo esc_html__( 'Date Range', WE_LS_SLUG ); ?></h4>

								<p><?php echo esc_html__( 'Specifying a date range will filter the report to only include weight entries within that period of time.', WE_LS_SLUG ); ?></p>
								<?php

									echo ws_ls_form_field_select( [ 'key' => 'date-range', 'label' => esc_html__( 'Period', WE_LS_SLUG ), 'values' => ws_ls_export_date_ranges(), 'selected' => '', 'css-class' => 'widefat' ] );

									echo '<div id="ws-ls-date-range-options" class="ws-ls-hide">';

									echo ws_ls_form_field_date( [ 'name' => 'date-from', 'title' => esc_html__( 'From', WE_LS_SLUG ), 'show-label' => true, 'css-class' => 'we-ls-datepicker widefat', 'css-class-label' => 'ws-ls-block-it' ] );

									echo ws_ls_form_field_date( [ 'name' => 'date-to', 'title' => esc_html__( 'To', WE_LS_SLUG ), 'show-label' => true, 'css-class' => 'we-ls-datepicker widefat', 'css-class-label' => 'ws-ls-block-it' ] );

									echo '</div>';

								?>
							</div>
						</div>
						<div class="postbox">
							<h2 class="hndle"><span><?php echo esc_html__( 'Additional columns', WE_LS_SLUG ); ?></span></h2>
							<div class="inside">
								<p><?php echo esc_html__( 'Select additional data columns to be included for each weight entry. Please note, each additional column requires extra work and will therefore impact the time it takes to produce the export.', WE_LS_SLUG ); ?></p>
								<p><a class="button ws-ls-export-check-all">Check All</a><a class="button ws-ls-export-uncheck-all">Un-check All</a></p>
								<?php

									echo ws_ls_form_field_checkbox( [ 'name' => 'fields[]', 'title' => esc_html__( 'BMI Value (height required)', WE_LS_SLUG ), 'show-label' => true, 'value' => 'bmi-value', 'css-class' => 'report-column', 'checked' => false ] );

									echo ws_ls_form_field_checkbox( [ 'name' => 'fields[]', 'title' => esc_html__( 'BMI Label (height required)', WE_LS_SLUG ), 'show-label' => true, 'value' => 'bmi-label', 'css-class' => 'report-column', 'checked' => true ] );

									echo ws_ls_form_field_checkbox( [ 'name' => 'fields[]', 'title' => esc_html__( 'Difference between current and start weight', WE_LS_SLUG ), 'show-label' => true, 'value' => 'weight-diff-start', 'css-class' => 'report-column', 'checked' => true ] );

									echo ws_ls_form_field_checkbox( [ 'name' => 'fields[]', 'title' => esc_html__( 'Notes', WE_LS_SLUG ), 'show-label' => true, 'value' => 'notes', 'css-class' => 'report-column', 'checked' => true ] );

									$enabled_meta_fields = ws_ls_meta_fields_enabled();

									if ( false === empty( $enabled_meta_fields ) ) {

										foreach ( $enabled_meta_fields as $meta_field ) {

											echo ws_ls_form_field_checkbox( [ 'name' => 'fields-meta[]', 'title' => stripslashes( $meta_field[ 'field_name' ] ), 'show-label' => true, 'value' => $meta_field[ 'id' ], 'css-class' => 'report-column', 'checked' => false ] );

										}
									}
								?>
							</div>
						</div>

					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Output', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php

								$selected_format = ws_ls_querystring_value( 'format', false, 'csv' );

								echo ws_ls_form_field_select( [ 'key' => 'format', 'label' => esc_html__( 'Format', WE_LS_SLUG ), 'values' => [ 'csv' => esc_html__( 'CSV', WE_LS_SLUG ), 'json' => esc_html__( 'Json', WE_LS_SLUG ) ], 'selected' => $selected_format ] );
							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Options', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<center>
								<input type="submit" class="button-primary" value="<?php echo esc_html__( 'Run Report', WE_LS_SLUG ); ?>" />
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<br class="clear">

<?php
}

function ws_ls_export_admin_page_process() {

	$export_id = ws_ls_querystring_value( 'id' );

	// Are we adding a new export?
	if ( true === empty( $export_id ) &&
		 	'yes' === ws_ls_post_value( 'add-report' ) ) {
		$export_id = ws_ls_export_insert( $_POST );
	}

	// Fetch export criteria from DB
	$criteria = ws_ls_db_export_criteria_get( $export_id );

	?>
	<div class="wrap ws-ls-challenges ws-ls-admin-page">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html__( 'Processing your export', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php

								if ( true !== WS_LS_IS_PRO ) {
									ws_ls_display_pro_upgrade_notice();

									echo esc_html__( 'Please upgrade for your report to be generated.', WE_LS_SLUG );
								} else if ( true === empty( $criteria ) ) {
									echo esc_html__( 'There was an error loading the criteria for the export.', WE_LS_SLUG );
								} else if ( 100 === (int) $criteria[ 'step' ] ) {
									echo esc_html__( 'This report has already been processed.', WE_LS_SLUG );
								} else { ?>

									<div class="ws-ls-export-progress-bar" data-export-id="<?php echo (int) $export_id; ?>">
										<div class="ws-ls-export-progress-bar-inner" style="width:75%;"></div>
									</div>
									<p id="ws-ls-export-message"><?php echo esc_html__( 'Initialising...', WE_LS_SLUG ); ?></p>

								<?php
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

