<?php

defined('ABSPATH') or die('Jog on!');

/**
 * JS config used for localise script
 * @return mixed|void
 */
function ws_ls_config_js() {

	$user_id            = get_current_user_id();
	$user_id            = apply_filters( 'wlt-filter-js-ws-ls-config-user-id', $user_id );
	$message_for_pounds = ( 'stones_pounds' == ws_ls_setting('weight-unit', $user_id ) ) ?
									__( 'Please enter a value between 0-13.99 for pounds', WE_LS_SLUG ) :
										__( 'Please enter a value between 1 and 5000 for pounds', WE_LS_SLUG );

	$use_us_date        = ws_ls_setting('use-us-dates', $user_id );

	$config = [     'us-date'                           => ( $use_us_date ) ? 'true' : 'false',
					'date-format'                       => ( $use_us_date ) ? 'mm/dd/yy' : 'dd/mm/yy',
    	            'clear-target'                      => __( 'Are you sure you wish to clear your target weight?', WE_LS_SLUG ),
					'validation-about-you-mandatory'    => ( true === ws_ls_option_to_bool( 'ws-ls-about-you-mandatory', 'no', true ) ) ? 'true' : 'false',
					'validation-we-ls-weight-pounds'    => $message_for_pounds,
					'validation-we-ls-weight-kg'        => __( 'Please enter a value between 1 and 5000 for Kg', WE_LS_SLUG ),
					'validation-we-ls-weight-stones'    => __( 'Please enter a value between 1 and 5000 for Stones', WE_LS_SLUG ),
					'validation-we-ls-date'             => __( 'Please enter a valid date', WE_LS_SLUG ),
					'validation-we-ls-history'          => __( 'Please confirm that you wish to delete ALL of your user data', WE_LS_SLUG ),
					'validation-we-ls-photo'            => __( 'Your photo must be less than ', WE_LS_SLUG ) . ws_ls_photo_display_max_upload_size(),
    	            'confirmation-delete'               => __( 'Are you sure you wish to delete this entry? If so, press OK.', WE_LS_SLUG ),
					'ajax-url'                          => admin_url( 'admin-ajax.php' ),
					'ajax-security-nonce'               => wp_create_nonce( 'ws-ls-nonce' ),
					'is-pro'                            => ( WS_LS_IS_PRO ) ? 'true' : 'false',
					'user-id'                           => $user_id,
					'current-url'                       => apply_filters( 'wlt_current_url', get_permalink() ),
					'photos-enabled'                    => ( ws_ls_meta_fields_photo_any_enabled( true ) ) ? 'true' : 'false',
					'date-picker-locale'                => ws_ls_config_js_datapicker_locale(),
					'in-admin'                          => ( is_admin() ) ? 'true' : 'false',
					'max-photo-upload'                  => ws_ls_photo_max_upload_size(),
					'tab-config'                        => ws_ls_config_js_tab_config(),
					'form-load-previous'                => true // TODO! Add a new setting to admin panel - also set to false if in admin?? Or should admin see previous values?
	];

	// If About You fields mandatory, add extra translations
	if( true === ws_ls_option_to_bool( 'ws-ls-about-you-mandatory', 'no', true ) ) {

	    $config['validation-user-pref-messages'] = [
											            'ws-ls-height'          => __( 'Please select or enter a value for height.', WE_LS_SLUG ),
											            'ws-ls-activity-level'  => __( 'Please select or enter a value for activity level.', WE_LS_SLUG ),
											            'ws-ls-gender'          => __( 'Please select or enter a value for gender.', WE_LS_SLUG ),
											            'we-ls-dob'             => __( 'Please enter a valid date.', WE_LS_SLUG ),
											            'ws-ls-aim'             => __( 'Please select your aim.', WE_LS_SLUG )
        ];

        $config['validation-user-pref-rules']   = [
										            'ws-ls-gender'          => [ 'required' => true, 'min' => 1 ],
										            'ws-ls-height'          => [ 'required' => true, 'min' => 1 ],
										            'ws-ls-aim'             => [ 'required' => true, 'min' => 1 ],
										            'ws-ls-activity-level'  => [ 'required' => true, 'min' => 1 ]
        ];

        $config['validation-required']          = __( 'This field is required.', WE_LS_SLUG );
	}

	// Allow others to filter config object
    return apply_filters( 'wlt-filter-js-ws-ls-config', $config );
}

/*
	Use a combination of WP Locale and MO file to translate datepicker
	Based on: https://gist.github.com/clubdeuce/4053820
 */
function ws_ls_config_js_datapicker_locale() {

	global $wp_locale;

	return [
	        'closeText'         => __( 'Done', WE_LS_SLUG ),
	        'currentText'       => __( 'Today', WE_LS_SLUG ),
	        'monthNames'        => array_values( $wp_locale->month ),
	        'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
	        'dayNames'          => array_values( $wp_locale->weekday ),
	        'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
	        'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),
	    	// get the start of week from WP general setting
	        'firstDay'          => get_option( 'start_of_week' ),
			'entry-found'       => __( 'An entry has been found for this date. Would you like to load the existing values?', WE_LS_SLUG ) . PHP_EOL . PHP_EOL . __( 'Note: Any unsaved data shall be lost!', WE_LS_SLUG )
	];
}

/**
 * JS Config for Tabs
 * @return array
 */
function ws_ls_config_js_tab_config() {

	return [    'rounded'           => false,
				'multiline'         => true,
				'theme'             => get_option( 'ws-ls-tab-theme', 'silver' ), // white, crystal, silver, gray, black, orange, red, green, blue, deepblue
                'size'              => 'small',
				'minWindowWidth'    => (int) get_option( 'ws-ls-tab-window-resize', '1200' ),
				'mobileNav'         => true,
				'responsive'        => true,
				'animation'         => [ 'effects' => 'slideH', 'easing' => 'easeInOutCirc', 'type' => 'jquery' ]
	];
}
