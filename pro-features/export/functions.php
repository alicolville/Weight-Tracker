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
