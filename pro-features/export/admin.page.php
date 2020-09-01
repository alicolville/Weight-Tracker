<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_export_admin_page() {

    ws_ls_permission_check_message();

    if ( true !== WS_LS_IS_PRO_PLUS ) {
		ws_ls_display_pro_upgrade_notice();
	}

	switch ( ws_ls_querystring_value( 'mode' ) ) {

		case 'new':
			ws_ls_export_admin_page_new();
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
						<h2 class="hndle"><span><?php echo __( 'Export Data', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<h4><?php echo __( 'Start a new export', WE_LS_SLUG ); ?></h4>
							<p>
								<a href="<?php echo ws_ls_export_link( 'new' ); ?>" class="btn btn-default button-primary">
									<i class="fa fa-plus"></i>
									<?php echo __( 'Start a new export', WE_LS_SLUG ); ?>
								</a>
							</p>
							<h4><?php echo __( 'Last 10 exports', WE_LS_SLUG ); ?></h4>
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

	if ( 'yes' === ws_ls_post_value( 'add-report' ) ) {

		$created = ws_ls_db_export_insert( $_POST );

	}

?>
	<form method="post" id="ws-ls-export-new-form">
		<input type="hidden" name="add-report" value="yes" />

		<div class="wrap ws-ls-challenges ws-ls-admin-page">
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h2 class="hndle"><span><?php echo __( 'Details', WE_LS_SLUG ); ?></span></h2>
							<div class="inside">
								<?php

									echo ws_ls_form_field_text( [ 'name' => 'we-ls-title', 'title' => __( 'Title', WE_LS_SLUG ), 'show-label' => true, 'required' => true ] );
								?>
							</div>
						</div>
						<div class="postbox">
							<h2 class="hndle"><span><?php echo __( 'Date range', WE_LS_SLUG ); ?></span></h2>
							<div class="inside">
								<p><?php echo __( 'Specifying a date range will filter the report to only include weight entries within that period of time.', WE_LS_SLUG ); ?></p>
								<?php

									echo ws_ls_form_field_select( [ 'key' => 'ws-ls-date-range', 'label' => __( 'Period', WE_LS_SLUG ), 'values' => ws_ls_export_date_ranges(), 'selected' => '' ] );

									echo '<div id="ws-ls-date-range-options" class="ws-ls-hide">';

									echo ws_ls_form_field_date( [ 'name' => 'we-ls-date-from', 'title' => __( 'From', WE_LS_SLUG ), 'show-label' => true ] );

									echo ws_ls_form_field_date( [ 'name' => 'we-ls-date-to', 'title' => __( 'To', WE_LS_SLUG ), 'show-label' => true ] );

									echo '</div>';

								?>
							</div>
						</div>
						<div class="postbox">
							<h2 class="hndle"><span><?php echo __( 'Columns', WE_LS_SLUG ); ?></span></h2>
							<div class="inside">
								<p><?php echo __( 'Select which data should be included for each weight entry.', WE_LS_SLUG ); ?></p>
								<p><a class="button ws-ls-export-check-all">Check All</a><a class="button ws-ls-export-uncheck-all">Un-check All</a></p>
								<?php

									echo ws_ls_form_field_checkbox( [ 'name' => 'we-ls-fields[]', 'title' => __( 'BMI Value', WE_LS_SLUG ), 'show-label' => true, 'value' => 'bmi-value', 'css-class' => 'report-column' ] );

									echo ws_ls_form_field_checkbox( [ 'name' => 'we-ls-fields[]', 'title' => __( 'BMI Label', WE_LS_SLUG ), 'show-label' => true, 'value' => 'bmi-label', 'css-class' => 'report-column' ] );

									echo ws_ls_form_field_checkbox( [ 'name' => 'we-ls-fields[]', 'title' => __( 'Difference between current and start weight', WE_LS_SLUG ), 'show-label' => true, 'value' => 'weight-diff-start', 'css-class' => 'report-column' ] );

									echo ws_ls_form_field_checkbox( [ 'name' => 'we-ls-fields[]', 'title' => __( 'Notes', WE_LS_SLUG ), 'show-label' => true, 'value' => 'notes', 'css-class' => 'report-column' ] );

									$enabled_meta_fields = ws_ls_meta_fields_enabled();

									if ( false === empty( $enabled_meta_fields ) ) {

										foreach ( $enabled_meta_fields as $meta_field ) {

											echo ws_ls_form_field_checkbox( [ 'name' => 'we-ls-fields-meta[]', 'title' => $meta_field[ 'field_name' ], 'show-label' => true, 'value' => $meta_field[ 'field_key' ], 'css-class' => 'report-column' ] );

										}
									}
								?>
							</div>
						</div>

					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html( 'Output', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php

								echo ws_ls_form_field_select( [ 'key' => 'ws-ls-format', 'label' => __( 'Format', WE_LS_SLUG ), 'values' => [ 'json' => __( 'Json', WE_LS_SLUG ), 'xml' => __( 'XML', WE_LS_SLUG ) ], 'selected' => '' ] );
							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo esc_html( 'Options', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<center>
								<input type="submit" class="button-primary" value="<?php echo __( 'Run Report', WE_LS_SLUG ); ?>" />
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
