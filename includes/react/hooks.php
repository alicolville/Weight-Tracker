<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Enqueued files
 */
function ws_ls_react_enqueue() {

	// Minify assets
	$version = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? mt_rand() : WE_LS_CURRENT_VERSION;

	// wp_enqueue_script('jquery-tabs', plugins_url( '../assets/js/libraries/tabs.min.js', __FILE__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION );

	// Styles
	//wp_enqueue_style( 'lookup-style', LOOKUP_ROOT_URI . '/assets/css/lookup-style' . $minified . '.css', [], LOOKUP_VERSION );

	// Scripts
	wp_enqueue_script( 'yk-wt-react', plugins_url( 'react/frontend/public.js', __DIR__ ), [ 'jquery' ], $version, true );

	// Localized scripts
//	wp_localize_script( 'lookup-react', 'lookup_react_config', lookup_config() );
//	wp_localize_script( 'lookup-react', 'lookup_react_rest', lookup_rest() );
//	wp_localize_script( 'lookup-react', 'lookup_react_strings', lookup_strings() );
}
add_action( 'wp_enqueue_scripts', 'ws_ls_react_enqueue', 1 );
