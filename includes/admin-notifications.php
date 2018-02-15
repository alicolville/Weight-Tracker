<?php

	defined('ABSPATH') or die("Jog on!");

	/**
	 *  Display admin notification
	 */
	function ws_ls_display_admin_notice() {

		if(WE_LS_DISABLE_YEKEN_NOTIFICATIONS) {
			return;
		}

		$yeken_data = ws_ls_get_data_from_yeken();

		// Get md5 of latest message
		$yeken_md5 = (isset($yeken_data->notice) && !empty($yeken_data->notice)) ? md5($yeken_data->notice) : false;

		// Don't display empty notifications!!
		if ( false === $yeken_md5 ) {
		    return;
        }

		// Get md5 of last dismissed message
		$local_md5 = get_option(WE_LS_KEY_YEKEN_ADMIN_NOTIFICATION);

		if( $yeken_md5 != $local_md5 && true === isset($yeken_data->notice) ) {
    ?>
		    <div class="notice notice-info is-dismissible" id="ws-ls-admin-notice" data-wsmd5="<?php esc_html_e($yeken_md5); ?>">
		        <p><strong><?php echo __( 'Weight Loss Tracker', WE_LS_SLUG); ?></strong> - <?php esc_html_e($yeken_data->notice) ?></p>
		    </div>
	    <?php
		}
	}
	add_action( 'admin_notices', 'ws_ls_display_admin_notice' );

	/**
	 * 	Handle dismiss notification
	 */
	function ws_ls_dismiss_notice()
	{
		if(!empty($_POST['md5'])) {
			update_option(WE_LS_KEY_YEKEN_ADMIN_NOTIFICATION, $_POST['md5']);
			echo 1;
		}
	}
	add_action( 'wp_ajax_ws_ls_dismiss_notice', 'ws_ls_dismiss_notice' );
