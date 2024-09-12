<?php

defined('ABSPATH') or die('Jog on!');

define( 'WE_LS_SETUP_WIZARD_DIMISS_OPTION', 'ws-ls-setup-wizard-dismiss' );

/**
 * Display HTML for admin notice
 */
function ws_ls_setup_wizard_notice() {

    printf('<div class="updated notice is-dismissible setup-wizard-dismiss">
                        <p>%1$s <strong>%2$s</strong>! %3$s.</p>
                        <p><a href="%4$s" class="button button-primary">Run wizard</a></p>
                    </div>',
                    esc_html__( 'Welcome to' , WE_LS_SLUG),
                    WE_LS_TITLE,
                    esc_html__( 'You\'re almost there, but a wizard might help you setup the plugin' , WE_LS_SLUG),
                    esc_url( ws_ls_setup_wizard_get_link() )
    );
}

/**
 * Return URL for setup wizard
 *
 * @return string|void
 */
function ws_ls_setup_wizard_get_link() {
	return admin_url( 'admin.php?page=ws-ls-data-setup-wizard');
}

/**
 * Show setup wizard
 *
 * @return bool
 */
function ws_ls_setup_wizard_show_notice() {
    return ( false === (bool) get_option( WE_LS_SETUP_WIZARD_DIMISS_OPTION ) );
}

/**
 * Show / hide setup wizard?
 */
function ws_ls_setup_wizard_help_page_show_links_again() {

    if ( true === isset( $_GET[ 'wlt-show-setup-wizard-links' ] ) ) {
        ws_ls_setup_wizard_show_notice_links_again();
    } else if ( true === isset( $_GET[ 'hide-setup-wizard' ] ) ) {
		ws_ls_setup_wizard_dismiss_notice();
	}

}
add_action( 'plugins_loaded', 'ws_ls_setup_wizard_help_page_show_links_again' );

/**
 * Show Wizard Links again
 */
function ws_ls_setup_wizard_show_notice_links_again() {
    delete_option( WE_LS_SETUP_WIZARD_DIMISS_OPTION );
}

/**
 * Show Admin Notice
 */
function ws_ls_setup_wizard_show_admin_notice() {
    if ( true === ws_ls_setup_wizard_show_notice() ) {
        ws_ls_setup_wizard_notice();
    }
}
add_action( 'admin_notices', 'ws_ls_setup_wizard_show_admin_notice' );

/**
 * Update option on whether to show wizard
 */
function ws_ls_setup_wizard_dismiss_notice() {
    update_option( WE_LS_SETUP_WIZARD_DIMISS_OPTION, true );
}
add_action( 'wp_ajax_ws_ls_setup_wizard_dismiss', 'ws_ls_setup_wizard_dismiss_notice' );

/**
 * HTML for mention of custom work
 */
function wl_ls_setup_wizard_custom_notification_html() {
	?>

		<p><img src="<?php ws_ls_echo( WS_LS_BASE_URL . 'includes/admin-pages/assets/images/yeken-logo.png' ); ?>" width="100" height="100" style="margin-right:20px" align="left" /><?php echo esc_html__( 'If require plugin modifications to Weight Tracker, or need a new plugin built, or perhaps you need a developer to help you with your website then please don\'t hesitiate get in touch!', WE_LS_SLUG ); ?></p>
		<p><strong><?php echo esc_html__( 'We provide fixed priced quotes.', WE_LS_SLUG); ?></strong></p>
		<p><a href="https://weighttracker.yeken.uk" rel="noopener noreferrer" target="_blank">Weight Tracker Plugin</a> /
        <a href="https://www.yeken.uk" rel="noopener noreferrer" target="_blank">YeKen.uk</a> /
			<a href="mailto:email@yeken.uk" >email@yeken.uk</a></p>
		<br clear="both"/>

	<?php
}

/**
 * HTML for mention of meal tracker
 */
function wl_ls_setup_wizard_meal_tracker_html() {

    return;

?>
    <p><img src="<?php ws_ls_echo( WS_LS_BASE_URL . 'includes/admin-pages/assets/images/meal-tracker-logo.png' ); ?>" width="100" height="100" style="margin-right:20px" align="left" />
        <?php echo esc_html__( 'Why not check out our sister plugin Meal Tracker. Allow your user\'s to track their meals and calorie intake too!', WE_LS_SLUG ); ?></p>
    <p><strong><?php echo esc_html__( 'Get Meal Tracker Now', WE_LS_SLUG); ?>.</strong></p>
    <p><a href="https://wordpress.org/plugins/meal-tracker/" rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'from WordPress.org', WE_LS_SLUG); ?></a> /
        <a href="https://shop.yeken.uk/product/meal-tracker-premium/" rel="noopener noreferrer" target="_blank"><?php echo esc_html__( 'Upgrade to Premium', WE_LS_SLUG); ?></a> /
        <a href="mailto:email@yeken.uk" ><?php echo esc_html__( 'Any questions, email@yeken.uk', WE_LS_SLUG); ?></a></p>
    <br clear="both"/>

    <?php
}