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
?>
	<div class="wrap ws-ls-challenges ws-ls-admin-page">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __( 'Export Data', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php

								echo ws_ls_form_field_text( [ 'name' => 'we-ls-date-title', 'title' => __( 'Title', WE_LS_SLUG ), 'show-label' => true ] );
							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __( 'Specify the date range for weight entries to be included in the report', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php

								echo ws_ls_form_field_select( [ 'key' => 'ws-ls-date-range', 'label' => __( 'Period', WE_LS_SLUG ), 'values' => ws_ls_export_date_ranges(), 'selected' => '' ] );

								echo ws_ls_form_field_date( [ 'name' => 'we-ls-date-from', 'title' => __( 'From', WE_LS_SLUG ), 'show-label' => true ] );

								echo ws_ls_form_field_date( [ 'name' => 'we-ls-date-to', 'title' => __( 'To', WE_LS_SLUG ), 'show-label' => true ] );

							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __( 'Specify the data that should be included in the report', WE_LS_SLUG ); ?></span></h2>
						<div class="inside">
							<?php
							echo ws_ls_form_field_select( [ 'key' => 'ws-ls-format', 'label' => __( 'Format', WE_LS_SLUG ), 'values' => [ 'json' => __( 'Json', WE_LS_SLUG ), 'xml' => __( 'XML', WE_LS_SLUG ) ], 'selected' => '' ] );

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
					<h2 class="hndle"><span><?php echo esc_html( 'Output', WE_LS_SLUG ); ?></span></h2>
					<div class="inside">
						<a href="" class="button btn-primary">Run</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br class="clear">

<?php
}
