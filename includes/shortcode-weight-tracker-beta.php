<?php

defined('ABSPATH') or die('Jog on!');

//TODO: Tidy this up - only want this included when doing uikit related stuff
add_filter( 'body_class', function( $classes ) {
	$classes[]  = 'uk-scope';
	$classes[]  = 'ykuk-scope';
	return $classes;
 });

/**
 * Shortcode for [wt-beta]
 * @param $user_defined_arguments
 *
 * @return string
 * @throws Exception
 */
function ws_ls_shortcode_beta( $user_defined_arguments ) {

	$shortcode_arguments = shortcode_atts( [    'accordian-multiple-open'   => true,                    // NEW: Allow more than one accordian tab to be open
												'active-tab'                => 'home',                      // Initial active tab
												'min-chart-points' 			=> 2,	                        // Minimum number of data entries before chart is shown
												'custom-field-groups'       => '',                          // If specified, only show custom fields that are within these groups
												'custom-field-slugs'        => '',                          // If specified, only show the custom fields that are specified
												'bmi-format'                => 'both',                      // Format for display BMI
												'show-add-button' 			=> false,					    // Display a "Add weight" button above the chart.
												'allow-delete-data' 		=> true,                	    // Show "Delete your data" section
												'hide-notes' 				=> ws_ls_setting_hide_notes(),  // Hide notes field
												'hide-photos' 				=> false,                       // Hide photos part of form
												'hide-chart-overview' 		=> false,               	    // Hide chart on the overview tab
												//		'hide-tab-photos' 			=> false,                 	    // Hide Photos tab
												//		'hide-tab-advanced' 		=> false,               	    // Hide Advanced tab (macroN, calories, etc)
												//		'hide-tab-descriptions' 	=> ws_ls_option_to_bool( 'ws-ls-tab-hide-descriptions', 'yes' ), // Hide tab descriptions
												'hide-advanced-narrative' 	=> false,         			    // Hide text describing BMR, MarcoN, etc
												//		'disable-advanced-tables' 	=> false,         			    // Disable advanced data tables.
												//		'disable-tabs' 				=> false,                       // Disable using tabs.
												//		'disable-second-check' 		=> false,					    // Disable check to see if [wlt] placed more than once
												'enable-week-ranges'        => false,                       // Enable Week Ranges?
												'user-id'					=> get_current_user_id(),
												'weight-mandatory'			=> true,						// Is weight mandatory?
	], $user_defined_arguments );

	if ( null !== ws_ls_querystring_value( 'ws-edit-entry' ) ) {
		$shortcode_arguments[ 'active-tab' ] = 'history';
	}

	if ( $active_tab = ws_ls_querystring_value( 'tab' ) ) {
		$shortcode_arguments[ 'active-tab' ] = $active_tab;
	}

	ws_ls_enqueue_uikit();

	$shortcode_arguments[ 'user-id' ]               = (int) $shortcode_arguments[ 'user-id' ];
	//$show_advanced_tab                          = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-advanced' ] ) && true === WS_LS_IS_PRO_PLUS );
	//$show_photos_tab                            = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-photos' ] ) && true === ws_ls_meta_fields_photo_any_enabled( true ) );
	$shortcode_arguments[ 'enable-week-ranges' ]	= ws_ls_to_bool( $shortcode_arguments[ 'enable-week-ranges' ] );
	$shortcode_arguments[ 'min-chart-points' ]      = (int) $shortcode_arguments[ 'min-chart-points' ];

	$html                                           = '<div class="ws-ls-tracker">';

	$shortcode_arguments[ 'selected-week-number' ]   = ( true === $shortcode_arguments[ 'enable-week-ranges' ] ) ? ws_ls_post_value_numeric( 'week-number' ) : NULL;
	$shortcode_arguments[ 'weight-data' ]            = ws_ls_entries_get( [     'week'      => $shortcode_arguments[ 'selected-week-number' ] ,
								                                                'prep'      => true,
								                                                'week'      => $shortcode_arguments[ 'selected-week-number' ] ,
								                                                'reverse'   => true,
								                                                'sort'      => 'desc' ] );

	// Display error if user not logged in
	if ( false === is_user_logged_in() )	{

		$html .= ws_ls_component_alert( __( 'You need to be logged in to record your weight.', WE_LS_SLUG ), 'primary', false, true );

	} else {

		if( 'true' === ws_ls_querystring_value( 'user-preference-saved', 'true' ) ) {
			$html .= ws_ls_component_alert( __( 'Your settings have been successfully saved!', WE_LS_SLUG ) );
		} elseif( 'true' === ws_ls_querystring_value( 'user-delete-all', 'true' ) ) {
			$html .= ws_ls_component_alert( __( 'Your data has successfully been deleted.', WE_LS_SLUG ) );
		}

		// Tab menu
		$html .= ws_ls_wt_tab_menu( $shortcode_arguments );

		// Tabs
		$html .= ws_ls_wt_tab_panes( $shortcode_arguments );

	}

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
									'uikit'                 => true
	] );

	return $html;
}

/**
 * Return summary info for home tab
 * @param array $arguments
 * @return string
 */
function ws_ls_wt_home_summary( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id() ] );

	return sprintf('<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-child-width-1-4@m ykuk-grid-match ykuk-text-small" ykuk-grid>
								%s
								%s
								%s
								%s
							</div>',
							ws_ls_component_latest_weight( [ 'user-id' => $arguments[ 'user-id' ] ] ),
							ws_ls_component_previous_weight( [ 'user-id' => $arguments[ 'user-id' ] ] ),
							ws_ls_component_latest_versus_target( [ 'user-id' => $arguments[ 'user-id' ] ] ),
							ws_ls_component_target_weight( [ 'user-id' => $arguments[ 'user-id' ] ] )
	);
}

/**
 * Return summary info for data tab
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_uikit_data_summary( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id() ] );

	return sprintf( '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-child-width-1-4@m ykuk-grid-match ykuk-text-small" ykuk-grid>
						%s
						%s
						%s
						%s
					</div>',
					ws_ls_component_number_of_entries( [ 'user-id' => $arguments[ 'user-id' ] ] ),
					ws_ls_component_number_of_days_tracking( [ 'user-id' => $arguments[ 'user-id' ] ] ),
					ws_ls_component_latest_weight( [ 'user-id' => $arguments[ 'user-id' ] ] ),
					ws_ls_component_start_weight( [ 'user-id' => $arguments[ 'user-id' ] ] )
	);
}

/**
 * Tabs menu
 *
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_wt_tab_menu( $arguments = [] ) {

	$tabs = [
				[ 'name' => 'home', 'icon' => 'home' ],
				[ 'name' => 'add-edit', 'icon' => 'plus' ],
				[ 'name' => 'history', 'icon' => 'history' ],
	];

	$tabs[] = [ 'name' => 'advanced', 'icon' => 'heart' ];
	$tabs[] = [ 'name' => 'gallery', 'icon' => 'image' ];
	$tabs[] = [ 'name' => 'messages', 'icon' => 'mail' ];

	if( true === ws_ls_user_preferences_is_enabled() ) {
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

	/*
	 * Todo:
	 * Check ws_ls_note_is_enabled() for notes
	 */

	$html = '<ul class="ykuk-switcher switcher-container ykuk-margin">';
	$html .=		'<li>' . ws_ls_wt_tab_home( $arguments ) . '</li>
					<li>' . ws_ls_tab_add_entry( $arguments ) . '</li>
					<li>' . ws_ls_wt_tab_table( $arguments ) . '</li>
					<li>' . ws_ls_wt_tab_advanced( $arguments ) .'</li>
					<li>' . ws_ls_tab_gallery(  $arguments ) . '</li>';

	if( true === ws_ls_note_is_enabled() ) {
		$html .= '<li>' . ws_ls_tab_notes( $arguments ) . '</li>';
	}

	if( true === ws_ls_user_preferences_is_enabled() ) {
		$html .= '<li>' . ws_ls_tab_settings( $arguments ) . '</li>';
	}

	$html .= '</ul>';

	return $html;
}

/**
 * Return home tab content
 * @param array $shortcode_arguments
 * @return string
 */
function ws_ls_wt_tab_home( $shortcode_arguments = [] ) {

	$args = wp_parse_args( $shortcode_arguments, [ 'enable-week-ranges' => false, 'uikit' => true ] );

	$html = ws_ls_wt_home_summary( $args );

	// Display chart?
	if ( false === ws_ls_to_bool( $args[ 'hide-chart-overview' ] ) ) {

		// Configure chart
		$args[ 'hide-title' ] 		= true;
		$args[ 'legend-position' ]	= 'bottom';

		$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header'        => __( 'Weight entries', WE_LS_SLUG ),
																'body'          => ws_ls_shortcode_embed_chart( $args[ 'weight-data' ]  , $args ),
																'footer-link'   => '#',
																'footer-text'   => __('View in tabular format', WE_LS_SLUG),
																'tab-changer'   => 'history'
		]);
	}

	return $html;
}

function ws_ls_tab_settings( $arguments = [] ) {

	$html = 'Add target';

	$html .= ws_ls_user_preferences_form( [ 'user-id' => $arguments[ 'user-id' ],
	                                        'allow-delete-data' => ws_ls_to_bool( $arguments[ 'allow-delete-data' ] ) ] );

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

	$html = ws_ls_uikit_data_summary();

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header' 		=> __( 'Your entries', WE_LS_SLUG ),
															'body-class'	=> 'ykuk-text-small',
															'body' 			=> ws_ls_shortcode_table( [ 	'user-id' 				=> $arguments[ 'user-id' ],
																											'enable-add-edit' 		=> true,
																											'enable-meta-fields'	=> true,
																											'week' 					=> $arguments[ 'selected-week-number' ] ,
																											'bmi-format' 			=> $arguments[ 'bmi-format' ],
																											'custom-field-groups'   => $arguments[ 'custom-field-groups' ],
																											'custom-field-slugs'    => $arguments[ 'custom-field-slugs' ],
																											'uikit'                => true
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
																	'body' 			=> ws_ls_photos_shortcode_recent( [] )
					] ) . '
				</div>
				<div>
					' . ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Oldest Photo', WE_LS_SLUG ),
																	'body-class'	=> 'ykuk-text-small ykuk-text-center',
																	'body' 			=> ws_ls_photos_shortcode_oldest( [] )
					] ) . '
				</div>
			</div>

			';

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [    'header' 		=> __( 'All of your photos', WE_LS_SLUG ),
															'body-class'	=> 'ykuk-text-small ykuk-text-center',
															'body' 			=> ws_ls_photos_shortcode_gallery( [] )
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

	$html = '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
				' . ws_ls_component_bmi( $arguments ) . '
				' . ws_ls_component_bmr( $arguments ) . '
			</div>';

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
