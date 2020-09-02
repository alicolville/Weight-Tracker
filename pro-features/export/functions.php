<?php

defined('ABSPATH') or die('Jog on.');

/**
 * Link to export page
 *
 * @param string $mode
 *
 * @return string|void
 */
function ws_ls_export_link( $mode = 'view' ) {

	$url = sprintf( 'admin.php?page=ws-ls-export-data&mode=%1$s', $mode );

	$url = admin_url( $url );

	return $url;
}

/**
 * Export date range options
 * @return array
 */
function ws_ls_export_date_ranges() {

	return [
				'today' 	=> __( 'Today', WE_LS_SLUG ),
				'last-7' 	=> __( 'Last 7 Days', WE_LS_SLUG ),
				'last-31' 	=> __( 'Last 31 Days', WE_LS_SLUG ),
				'custom'	=> __( 'Custom date range', WE_LS_SLUG )
	];
}
