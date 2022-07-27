<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Shortcode for [wt-beta]
 * @param $user_defined_arguments
 *
 * @return string
 * @throws Exception
 */
function ws_ls_shortcode_beta( $user_defined_arguments ) {

	$shortcode_arguments = shortcode_atts( [    'active-tab'                => 'home',                      // Initial active tab
												'min-chart-points' 			=> 2,	                        // Minimum number of data entries before chart is shown
												'custom-field-groups'       => '',                          // If specified, only show custom fields that are within these groups
												'custom-field-slugs'        => '',                          // If specified, only show the custom fields that are specified
												'bmi-format'                => 'both',                      // Format for display BMI
												'disable-main-font'         => false,                       // If set to true, don't include the main font
												'disable-theme-css'         => false,                       // If set to true, don't include the additional theme CSS used
												'enable-week-ranges'        => false,                       // Enable Week Ranges?
	                                            'hide-notes' 				=> ws_ls_setting_hide_notes(),  // Hide notes field
												'hide-notifications' 		=> false,                        // Hide notifications part of form
												'hide-photos' 				=> false,                       // Hide photos part of form
												'hide-chart-overview' 		=> false,               	    // Hide chart on the overview tab
												'hide-tab-awards' 		    => false,               	    // Hide Awards tab
												'hide-tab-photos' 			=> false,                 	    // Hide Photos tab
												'hide-tab-advanced' 		=> false,               	    // Hide Advanced tab (macroN, calories, etc)
												'hide-advanced-narrative' 	=> false,         			    // Hide text describing BMR, MarcoN, etc
												'hide-custom-fields-form'   => false,                       // Hide custom fields from form
												'hide-custom-fields-chart'  => false,                       // Hide custom fields from chart
												'hide-custom-fields-table'  => false,                       // Hide custom fields from table
												'kiosk-mode'                => false,                       // If in Kiosk mode, allow this UI to be used for multiple isers
												'show-delete-data' 		    => true,                	    // Show "Delete your data" section
												'show-tab-info' 		    => false,
	                                            'summary-boxes-data'        => 'number-of-entries,number-of-days-tracking,latest-weight,start-weight', // Summary boxes to display at top of data tab
												'summary-boxes-home'        => 'latest-weight,previous-weight,latest-versus-target,target-weight', // Summary boxes to display at top of data tab
												'summary-boxes-awards'      => 'latest-award,number-of-awards',
												'summary-boxes-advanced'    => 'bmi,bmr',                   // Summary boxes to display at top of advanced tab
												'summary-boxes-kiosk'       => 'weight-difference-since-previous,latest-weight,latest-versus-target,latest-versus-start,latest-award,bmi,calories-lose,calories-maintain,divider,start-weight,aim,target-weight,start-bmi,previous-weight,divider,name-and-email,gender,age-dob,height,activity-level,group', // Summary boxes to display at top of data tab
	                                            'user-id'					=> get_current_user_id(),
												'weight-mandatory'			=> true,						// Is weight mandatory?
	], $user_defined_arguments );

	$html = '<div class="ws-ls-tracker">';

	if ( false === is_user_logged_in() ) {
		return ws_ls_component_alert( [ 'message' => __( 'You need to be logged in to record your weight.', WE_LS_SLUG ), 'type' => 'primary', 'closable' => false, 'include-login-link' => true ] ) . '</div>';
	}

	$shortcode_arguments[ 'kiosk-mode' ] = ws_ls_to_bool( $shortcode_arguments[ 'kiosk-mode' ] );

	if ( true === $shortcode_arguments[ 'kiosk-mode' ] ) {

		global $kiosk_mode;

		$kiosk_mode = true;

		$shortcode_arguments[ 'show-tab-info' ] = true;
		$shortcode_arguments[ 'active-tab' ]    = 'summary';
		$shortcode_arguments[ 'user-loaded' ]   = false;
		$user_id_to_load                        = ws_ls_querystring_value( 'wt-user-id', true );

		if ( $user_id_to_load && ws_ls_user_exist( $user_id_to_load ) ) {
			$shortcode_arguments[ 'user-id' ]       = $user_id_to_load;
			$shortcode_arguments[ 'user-loaded' ]   = true;
		}

		$shortcode_arguments[ 'todays-entry' ] = ws_ls_entry_get_todays( [ 'user-id' => $shortcode_arguments[ 'user-id' ] ] );

		if ( false === empty( $shortcode_arguments[ 'todays-entry' ] ) ) {
			$html = '<div class="ws-ls-tracker ws-ls-has-an-entry-for-today">';
		} else {
			$html = '<div class="ws-ls-tracker ws-ls-has-no-entry-for-today">';
		}

		$html .= ws_ls_uikit_data_exposed_notice();

		$shortcode_arguments[ 'disable-not-logged-in' ] = true;

		$html .= ws_ls_component_user_search( $shortcode_arguments );

		// Have we got a selected user? If not, only display drop down box
		if( false === $shortcode_arguments[ 'user-loaded' ] ) {
			return $html . '</div>';
		}
	}

	if(  true === ws_ls_to_bool( $shortcode_arguments['show-delete-data'] ) )	{
		ws_ls_delete_user_data( $shortcode_arguments[ 'user-id' ], $shortcode_arguments['kiosk-mode'] );
	}

	if ( null !== ws_ls_querystring_value( 'ws-edit-entry' ) ) {
		$shortcode_arguments[ 'active-tab' ] = 'history';
	} elseif ( $active_tab = ws_ls_querystring_value( 'tab' ) ) {
		$shortcode_arguments[ 'active-tab' ] = $active_tab;
	}

	ws_ls_enqueue_uikit( ! $shortcode_arguments[ 'disable-theme-css' ], ! $shortcode_arguments[ 'disable-main-font' ], 'wt' );

	$shortcode_arguments[ 'user-id' ]               = (int) $shortcode_arguments[ 'user-id' ];
	$shortcode_arguments[ 'show-tab-awards' ]       = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-awards' ] ) && true === WS_LS_IS_PRO_PLUS );
	$shortcode_arguments[ 'show-tab-advanced' ]     = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-advanced' ] ) && true === WS_LS_IS_PRO_PLUS );
	$shortcode_arguments[ 'show-tab-photos' ]       = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-photos' ] ) && true === ws_ls_meta_fields_photo_any_enabled( true ) );
	$shortcode_arguments[ 'enable-week-ranges' ]	= ws_ls_to_bool( $shortcode_arguments[ 'enable-week-ranges' ] );
	$shortcode_arguments[ 'min-chart-points' ]      = (int) $shortcode_arguments[ 'min-chart-points' ];
	$shortcode_arguments[ 'selected-week-number' ]  = ( true === $shortcode_arguments[ 'enable-week-ranges' ] ) ? ws_ls_post_value_numeric( 'week-number' ) : NULL;

	// If any of the arguments are hiding to custom fields, then ensure all DB entries we fetch have a weight
	$ensure_we_have_weights = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-custom-fields-form' ] ) ||
		                            true === ws_ls_to_bool( $shortcode_arguments[ 'hide-custom-fields-chart' ] ) ||
										true === ws_ls_to_bool( $shortcode_arguments[ 'hide-custom-fields-table' ] ) );

	$shortcode_arguments[ 'weight-data' ]   = ws_ls_entries_get( [     'user-id'           => $shortcode_arguments[ 'user-id' ],
																	   'week'              => $shortcode_arguments[ 'selected-week-number' ],
	                                                                   'prep'              => true,
	                                                                   'must-have-weight'  => $ensure_we_have_weights,
	                                                                   'reverse'           => true,
	                                                                   'sort'              => 'desc' ] );

	$html .= ws_ls_uikit_beta_notice();

	if( 'true' === ws_ls_querystring_value( 'user-preference-saved', 'true' ) ) {
		$html .= ws_ls_component_alert( [ 'message' => __( 'Your settings have been successfully saved!', WE_LS_SLUG ) ] );
	} elseif( 'true' === ws_ls_querystring_value( 'user-delete-all', 'true' ) ) {
		$html .= ws_ls_component_alert( [ 'message' => __( 'Your data has successfully been deleted.', WE_LS_SLUG ) ] );
	}

	if ( true !== ws_ls_to_bool( $shortcode_arguments[ 'hide-notifications' ] ) && true === WS_LS_IS_PRO_PLUS ) {
		$html .= ws_ls_notifications_shortcode( $shortcode_arguments, true );
	}

	// Tab menu
	$html .= ws_ls_wt_tab_menu( $shortcode_arguments );

	// Tabs
	$html .= ws_ls_wt_tab_panes( $shortcode_arguments );

	return $html;
}
add_shortcode( 'wt-beta', 'ws_ls_shortcode_beta' );

/**
 * Display form for entry
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_form( $arguments = [] ) {

	$html       = '';

	//If we have a Redirect URL, base decode.
	$redirect_url = ws_ls_querystring_value( 'redirect' );

	if ( false === empty( $redirect_url ) ) {
		$redirect_url = base64_decode( $redirect_url );
	}

	$html .= ws_ls_form_weight( [   'css-class-form'        => 'ws-ls-main-weight-form',
	                                'user-id'               => $arguments[ 'user-id' ],
	                                'redirect-url'          => $redirect_url,
	                                'entry-id'              => ws_ls_querystring_value('ws-edit-entry', true, NULL ),
	                                'hide-fields-photos'    => ws_ls_to_bool( $arguments[ 'hide-photos' ] ),
	                                'hide-notes'            => ws_ls_to_bool( $arguments[ 'hide-notes' ] ),
	                                'hide-title'            => true,
	                                'hide-confirmation'     => true,
	                                'custom-field-groups'   => $arguments[ 'custom-field-groups' ],
	                                'custom-field-slugs'    => $arguments[ 'custom-field-slugs' ],
	                                'weight-mandatory'		=> $arguments[ 'weight-mandatory' ],
									'uikit'                 => true,
									'hide-fields-meta'      => $arguments[ 'hide-custom-fields-form' ],
									'kiosk-mode'            => $arguments[ 'kiosk-mode' ]
	] );

	return $html;
}

/**
 * Tabs menu
 *
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_menu( $arguments = [] ) {

	$tabs = [];

	if ( true === ws_ls_to_bool( $arguments[ 'show-tab-info'] ) ) {
		$tabs[] = [ 'name' => 'summary', 'icon' => 'info' ];
	}

	$tabs[] = [ 'name' => 'home', 'icon' => 'home' ];
	$tabs[] = [ 'name' => 'add-edit', 'icon' => 'plus' ];
	$tabs[] = [ 'name' => 'history', 'icon' => 'history' ];

	if ( true === $arguments[ 'show-tab-awards' ] ) {
		$tabs[] = [ 'name' => 'awards', 'icon' => 'star' ];
	}

	if ( true === $arguments[ 'show-tab-advanced' ] ) {
		$tabs[] = [ 'name' => 'advanced', 'icon' => 'heart' ];
	}

	if ( true === $arguments[ 'show-tab-photos' ] ) {
		$tabs[] = [ 'name' => 'gallery', 'icon' => 'image' ];
	}

	$tabs[] = [ 'name' => 'messages', 'icon' => 'mail' ];

	if( true === ws_ls_user_preferences_is_enabled() ||
			true === $arguments[ 'kiosk-mode' ] ) {
		$tabs[] = [ 'name' => 'settings', 'icon' => 'settings' ];
	}

	// Store tab names / position in a JS object so JS scripts can look determine their position when
	// switching tabs
	$tab_names = wp_list_pluck( $tabs, 'name' );
	wp_localize_script( 'yk-uikit-wt', 'ws_ls_tab_positions', $tab_names );

	$html = '<ul ykuk-tab class="ykuk-tab-menu ykuk-flex-center ykuk-flex-right@s" ykuk-switcher>';

	foreach( $tabs as $tab ) {
		$html .= sprintf( '	<li class="ykuk-padding-remove-left%s">
								<a href="#">
									<span ykuk-icon="icon: %s"></span>
								</a>
							</li>',
							( false === empty( $arguments[ 'active-tab' ] ) && $tab[ 'name' ] === $arguments[ 'active-tab' ] ) ? ' ykuk-active' : '',
							$tab[ 'icon' ]
		);
	}

	$html .= '</ul>';

	return $html;
}

/**
 * Tabs content
 *
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_panes( $arguments = [] ) {

	$html = '<ul class="ykuk-switcher switcher-container ykuk-margin">';

	if ( true === ws_ls_to_bool( $arguments[ 'show-tab-info'] ) ) {
		$html .= '<li class="ws-ls-kiosk-tab">' . ws_ls_wt_tab_summary( $arguments ) .'</li>';
	}

	$html .=		'	<li>' . ws_ls_wt_tab_home( $arguments ) . '</li>
						<li>' . ws_ls_tab_add_entry( $arguments ) . '</li>
						<li>' . ws_ls_wt_tab_table( $arguments ) . '</li>';

	if ( true === $arguments[ 'show-tab-awards' ] ) {
		$html .= '<li>' . ws_ls_wt_tab_awards( $arguments ) .'</li>';
	}

	if ( true === $arguments[ 'show-tab-advanced' ] ) {
		$html .= '<li>' . ws_ls_wt_tab_advanced( $arguments ) .'</li>';
	}

	if ( true === $arguments[ 'show-tab-photos' ] ) {
		$html .= '<li>' . ws_ls_tab_gallery(  $arguments ) . '</li>';
	}

	if( true === ws_ls_note_is_enabled() ) {
		$html .= '<li>' . ws_ls_tab_notes( $arguments ) . '</li>';
	}

	if( true === ws_ls_user_preferences_is_enabled() ||
	        true === $arguments[ 'kiosk-mode' ] ) {
		$html .= '<li>' . ws_ls_tab_settings( $arguments ) . '</li>';
	}

	$html .= '</ul>';

	return $html;
}

/**
 * Return Summary tab content
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_summary( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [ 'breakpoint_s' => '4' ] );

	return ws_ls_uikit_data_summary_boxes_display( 'summary-boxes-kiosk', $arguments );
}

/**
 * Return home tab content
 *
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_home( $arguments = [] ) {

	$args = wp_parse_args( $arguments, [ 'enable-week-ranges' => false, 'uikit' => true ] );

	$html = ws_ls_uikit_data_summary_boxes_display( 'summary-boxes-home', $arguments );

	// Display chart?
	if ( false === ws_ls_to_bool( $args[ 'hide-chart-overview' ] ) ) {

		// Configure chart
		$args[ 'hide-title' ] 		        = true;
		$args[ 'legend-position' ]	        = 'bottom';
		$args[ 'hide-custom-fields' ]       = $arguments[ 'hide-custom-fields-chart' ];

		$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header'        => __( 'Weight entries', WE_LS_SLUG ),
																'body'          => ws_ls_shortcode_embed_chart( $args[ 'weight-data' ]  , $args ),
																'footer-link'   => '#',
																'footer-text'   => __('View in tabular format', WE_LS_SLUG),
																'tab-changer'   => 'history'
		]);
	}

	return $html;
}

/**
 * Display settings tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_tab_settings( $arguments = [] ) {

	$redirect_url =  ( true === $arguments[ 'kiosk-mode' ] ) ?
						add_query_arg( [    'wt-user-id'            => $arguments[ 'user-id' ],
											'user-preference-saved' => 'true'
						], get_permalink() ) : '';

	// Include target form?
	if ( true === ws_ls_targets_enabled() ) {

		$html = ws_ls_ui_kit_info_box_with_header_footer( [ 'header'    => __( 'Your target', WE_LS_SLUG ),
		                                                    'body'      => ws_ls_form_weight( [ 'type'              => 'target',
		                                                                                        'css-class-form'    => 'ws-ls-target-form',
		                                                                                        'user-id'           => $arguments[ 'user-id' ],
		                                                                                        'kiosk-mode'        => $arguments[ 'kiosk-mode' ],
		                                                                                        'hide-confirmation' => false,
			                                                                                    'hide-title'        => true,
			                                                                                    'uikit'             => true
		                                                    ] )
		]);
	}

	$settings = ws_ls_user_preferences_form( [  'user-id'           => $arguments[ 'user-id' ],
	                                            'uikit'             => true,
												'show-delete-data'  => false,
												'kiosk-mode'        => $arguments[ 'kiosk-mode' ],
												'redirect-url'      => $redirect_url
	]);

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [    'header'    => __( 'Settings', WE_LS_SLUG ),
	                                                        'body'      => $settings
	]);

	if( false === empty( $arguments[ 'show-delete-data' ] ) ) {
		$settings = ws_ls_user_preferences_form( [  'user-id'               => $arguments[ 'user-id' ],
		                                            'hide-titles'           => true,
		                                            'uikit'                 => true,
		                                            'show-user-preferences' => false,
		                                            'kiosk-mode'            => $arguments[ 'kiosk-mode' ],
													'redirect-url'          => $redirect_url
		]);

		$html .= ws_ls_ui_kit_info_box_with_header_footer( [    'header'    => __( 'Delete your existing data', WE_LS_SLUG ),
		                                                        'body'      => $settings
		]);
	}

	return $html;
}

/**
 * Display notes tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_tab_notes( $arguments = [] ) {

	$content = ws_ls_note_shortcode( [ 'user-id' => $arguments[ 'user-id' ], 'notes-per-page' => 5, 'uikit' => true ]);

	return ws_ls_ui_kit_info_box_with_header_footer( [ 	'header' 		=> __( 'Messages', WE_LS_SLUG ),
														'body-class'	=> 'ykuk-text-small',
														'body' 			=> $content
	]);
}

/**
 * Display form tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_tab_add_entry( $arguments = [] ) {

	return ws_ls_ui_kit_info_box_with_header_footer( [	'header' 		=> __( 'Add a new entry', WE_LS_SLUG ),
														'body' 			=> ws_ls_wt_form( $arguments )
	] );
}

/**
 * Data table tab
 * @param array $arguments
 * @return string
 */
function ws_ls_wt_tab_table( $arguments = [] ) {

	$html = ws_ls_uikit_data_summary_boxes_display( 'summary-boxes-data', $arguments );

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header' 		=> __( 'Your entries', WE_LS_SLUG ),
															'body-class'	=> 'ykuk-text-small',
															'body' 			=> ws_ls_shortcode_table( [ 	'user-id' 				=> $arguments[ 'user-id' ],
																											'enable-add-edit' 		=> true,
																											'enable-meta-fields'	=> ! $arguments[ 'hide-custom-fields-table' ],
																											'week' 					=> $arguments[ 'selected-week-number' ] ,
																											'bmi-format' 			=> $arguments[ 'bmi-format' ],
																											'custom-field-groups'   => $arguments[ 'custom-field-groups' ],
																										    'kiosk-mode'            => $arguments[ 'kiosk-mode' ],
																											'custom-field-slugs'    => $arguments[ 'custom-field-slugs' ],
																											'uikit'                 => true,
																											'show-refresh-button'   => true
															] )

	]);

	return $html;
}

/**
 * Render gallery tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_tab_gallery( $arguments = [] ) {
	$html = '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
				<div>
					' . ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Latest Photo', WE_LS_SLUG ),
																	'body-class'	=> 'ykuk-text-small ykuk-text-center',
																	'body' 			=> ws_ls_photos_shortcode_recent( [ 'user-id' => $arguments[ 'user-id' ] ] )
					] ) . '
				</div>
				<div>
					' . ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Oldest Photo', WE_LS_SLUG ),
																	'body-class'	=> 'ykuk-text-small ykuk-text-center',
																	'body' 			=> ws_ls_photos_shortcode_oldest( [ 'user-id' => $arguments[ 'user-id' ] ] )
					] ) . '
				</div>
			</div>

			';

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [    'header' 		=> __( 'All of your photos', WE_LS_SLUG ),
															'body-class'	=> 'ykuk-text-small ykuk-text-center',
															'body' 			=> ws_ls_photos_shortcode_gallery( [ 'user-id' => $arguments[ 'user-id' ] ] )
			] );

	return $html;
}

/**
 * Render awards data tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_awards( $arguments = [] ) {

	$html = ws_ls_uikit_data_summary_boxes_display( 'summary-boxes-awards', $arguments );

	$awards = ws_ls_awards_shortcode_gallery( $arguments );


	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Awards', WE_LS_SLUG ),
	                                                     'body-class'	=> 'ykuk-text-small',
	                                                     'body' 		=> $awards
	] );

	return $html;
}

/**
 * Render Advanced data tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_advanced( $arguments = [] ) {

	$html = ws_ls_uikit_data_summary_boxes_display( 'summary-boxes-advanced', $arguments );

	$nested_html = '';

	// --------------------
	// Calorie intake
	// --------------------

	if( true === empty( $arguments[ 'hide-advanced-narrative' ] ) ) {
		$nested_html .= ws_ls_component_modal_with_text_link(   __( 'Learn more about suggested calorie intakes', WE_LS_SLUG ),
																__( 'Once we know your BMR (the number of calories to keep you functioning at rest), we can go on to give you suggestions on how to spread your calorie intake across the day. Firstly we split the figures into daily calorie intake to maintain weight and daily calorie intake to lose weight. Daily calorie intake to lose weight is calculated based on NHS advice – they suggest to lose 1 – 2lbs a week you should subtract 600 calories from your BMR. The two daily figures can be further broken down by recommending how to split calorie intake across the day i.e. breakfast, lunch, dinner and snacks.', WE_LS_SLUG ) );
	}

	$calorie_html = ws_ls_harris_benedict_render_table( $arguments[ 'user-id' ], false,  'ws-ls-footable ykuk-table ykuk-table-striped ykuk-table-small' );

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Suggested Calorie Intake', WE_LS_SLUG ),
	                                                     'body-class'	=> 'ykuk-text-small',
	                                                     'body' 		=> $calorie_html . $nested_html
	] );

	// --------------------
	// Macronutrients
	// --------------------

	if( true === empty( $arguments[ 'hide-advanced-narrative' ] ) ) {
		$nested_html = ws_ls_component_modal_with_text_link(    __( 'Learn more about macronutrients', WE_LS_SLUG ),
																__( 'With calories calculated, the we can recommend how those calories should be split into Fats, Carbohydrates and Proteins.', WE_LS_SLUG )
		);
	}

	$calorie_html = ws_ls_macro_render_table( $arguments[ 'user-id' ], false,  'ws-ls-footable ykuk-table ykuk-table-striped ykuk-table-small' );

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Macronutrients', WE_LS_SLUG ),
	                                                     'body-class'	=> 'ykuk-text-small',
	                                                     'body' 		=> $calorie_html . $nested_html
	] );

	return $html;
}

/**
 * Return a link for editing an entry
 * @param $id
 *
 * @return string
 */
function ws_ls_wt_link_edit_entry( $id ) {

	$edit_link  = remove_query_arg( ['ws-edit-entry', 'ws-edit-cancel', 'ws-edit-saved'], ws_ls_get_url() );

	return esc_url( add_query_arg( 'ws-edit-entry', $id, $edit_link ) );
}

/**
 * Goto certain tab
 * @param $tab
 *
 * @return string
 */
function ws_ls_wt_link_goto_tab( $tab ) {

	$link  = remove_query_arg( ['ws-edit-entry', 'ws-edit-cancel', 'ws-edit-saved'], ws_ls_get_url() );

	return esc_url( add_query_arg( 'tab', $tab, $link ) );
}
