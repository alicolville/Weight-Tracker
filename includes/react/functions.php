<?php
defined('ABSPATH') or die('Jog on!');

/**
 * Enqueued files for react UI
 */
function ws_ls_react_enqueue() {

	$version = ( defined( 'SCRIPT_DEBUG' ) && true === SCRIPT_DEBUG ) ? mt_rand() : WE_LS_CURRENT_VERSION;

	wp_enqueue_script( 'yk-wt-react', plugins_url( 'react/frontend/assets/js/public.min.js', __DIR__ ), [ 'jquery' ], $version, true );

	wp_localize_script( 'yk-wt-react', 'ws_ls_react', ws_ls_react_config() );
}

/**
 * Return an array of settings for React App
 * @return mixed|void
 */
function ws_ls_react_config() {

	$user_id = get_current_user_id();

	$config = [	'nonce'		=> wp_create_nonce( 'wp_rest' ),
				'locale'    => ws_ls_react_config_locale(),
				'data'      => ws_ls_entries_get( [] ),
				'target'	=> ws_ls_target_get( $user_id, NULL, true )
	];

	return apply_filters( 'wlt-filter-react-config', $config );
}

/**
 * Return a array of strings to use in React App
 * @return mixed|void
 */
function ws_ls_react_config_locale() {

	$strings = [    'kg'        => __( 'Kg', WE_LS_SLUG ),
					'save'      => __( 'Save', WE_LS_SLUG ),
					'target'    => __( 'Target', WE_LS_SLUG )

	];

	return apply_filters( 'wlt-filter-react-config-locale', $strings );
}
