<?php
defined('ABSPATH') or die('Jog on!');

/**
 * Enqueued files for react UI
 */
function ws_ls_react_enqueue() {

	$version = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? mt_rand() : WE_LS_CURRENT_VERSION;

	wp_enqueue_script( 'yk-wt-react', plugins_url( 'react/frontend/assets/js/public.min.js', __DIR__ ), [ 'jquery' ], $version, true );
}
