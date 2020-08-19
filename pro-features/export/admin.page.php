<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_export_admin_page() {

    ws_ls_permission_check_message();

    ?>
    <div class="wrap ws-ls-challenges ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
	                <?php
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
	                ?>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}

/**
* Initial summary page
 */
function ws_ls_export_admin_page_summary() {
?>
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
<?php
}

/**
* Create new report criteria
 */
function ws_ls_export_admin_page_new() {

	ws_ls_enqueue_files();

	ws_ls_enqueue_form_dependencies();
?>
	<div class="postbox">
		<h2 class="hndle"><span><?php echo __( 'Export Data', WE_LS_SLUG ); ?></span></h2>
		<div class="inside">
			<h4><?php echo __( 'Criteria', WE_LS_SLUG ); ?></h4>
			<?php

				echo ws_ls_form_field_date( [ 'name' => 'we-ls-date', 'title' => __( 'Date', WE_LS_SLUG ), 'show-label' => true ] );

			 ?>
		</div>
	</div>
<?php
}
