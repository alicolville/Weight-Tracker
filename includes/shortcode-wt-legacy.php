<?php
defined('ABSPATH') or die('Jog on!');

$ws_ls_tab_index            = 1;
$ws_ls_wlt_already_placed   = false;

/**
 * Main Weight Tracker shortcode
 * @param $user_defined_arguments
 *
 * @return string
 * @throws Exception
 */
function ws_ls_shortcode_wt_legacy( $user_defined_arguments ) {

	global $save_response;
	global $ws_ls_wlt_already_placed;

    $shortcode_arguments = shortcode_atts( [
								                'min-chart-points' 			=> 2,	                        // Minimum number of data entries before chart is shown
												'hide-first-target-form' 	=> false,					    // Hide first Target form
												'hide-second-target-form' 	=> false,					    // Hide second Target form
								                'custom-field-groups'       => '',                          // If specified, only show custom fields that are within these groups
								                'custom-field-slugs'        => '',                          // If specified, only show the custom fields that are specified
								                'bmi-format'                => 'label',                     // Format for display BMI
												'show-add-button' 			=> false,					    // Display a "Add weight" button above the chart.
								                'show-chart-history' 		=> false,					    // Display a chart on the History tab.
								                'allow-delete-data' 		=> true,                	    // Show "Delete your data" section
								                'hide-notes' 				=> ws_ls_setting_hide_notes(),  // Hide notes field
								                'hide-photos' 				=> false,                       // Hide photos part of form
								                'hide-chart-overview' 		=> false,               	    // Hide chart on the overview tab
								                'hide-tab-photos' 			=> false,                 	    // Hide Photos tab
								                'hide-tab-advanced' 		=> false,               	    // Hide Advanced tab (macroN, calories, etc)
												'hide-tab-descriptions' 	=> ws_ls_option_to_bool( 'ws-ls-tab-hide-descriptions', 'yes' ), // Hide tab descriptions
								                'hide-advanced-narrative' 	=> false,         			    // Hide text describing BMR, MarcoN, etc
								                'disable-advanced-tables' 	=> false,         			    // Disable advanced data tables.
								                'disable-tabs' 				=> false,                       // Disable using tabs.
												'disable-second-check' 		=> false,					    // Disable check to see if [wlt] placed more than once
                                                'enable-week-ranges'        => false,                       // Enable Week Ranges?
												'user-id'					=> get_current_user_id(),
												'weight-mandatory'			=> true,						// Is weight mandatory?
    ], $user_defined_arguments );

	if ( true === $ws_ls_wlt_already_placed && false === ws_ls_to_bool( $shortcode_arguments['disable-second-check'] ) ) {
		return sprintf('<p>%s</p>', __( 'This shortcode can only be placed once on a page / post. To disable this check, try: [wt disable-second-check=true]', WE_LS_SLUG ) );
	}

	ws_ls_enqueue_files();

	// Display error if user not logged in
	if ( false === is_user_logged_in() )	{
		return ws_ls_display_blockquote( __( 'You need to be logged in to record your weight.', WE_LS_SLUG ) , '', false, true );
	}

    $user_id 	                                = (int) $shortcode_arguments[ 'user-id' ];
    $use_tabs 	                                = ( false === ws_ls_to_bool( $shortcode_arguments[ 'disable-tabs' ] ) );
    $show_advanced_tab                          = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-advanced' ] ) && true === WS_LS_IS_PRO_PLUS );
	$show_photos_tab                            = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-photos' ] ) && true === ws_ls_meta_fields_photo_any_enabled( true ) );
	$week_ranges_enabled                        = ws_ls_to_bool( $shortcode_arguments[ 'enable-week-ranges' ] );
	$shortcode_arguments[ 'min-chart-points' ]  = (int) $shortcode_arguments[ 'min-chart-points' ];
    $html_output                                = '';

	$html_output .= ws_ls_wt_beta_upgrade_notice();

	// If a form was previously submitted then display resulting message!
	if ( false === empty( $save_response[ 'message' ] ) ){
		$html_output .= $save_response[ 'message' ];
	}

	if( 'true' === ws_ls_querystring_value( 'user-preference-saved', 'true' ) ) {
		$html_output .= ws_ls_blockquote_success( __( 'Your settings have been successfully saved!', WE_LS_SLUG ) );
	} elseif( 'true' === ws_ls_querystring_value( 'user-delete-all', 'true' ) ) {
		$html_output .= ws_ls_blockquote_success( __( 'Your data has successfully been deleted.', WE_LS_SLUG ) );
	}

	$selected_week_number   = ( true === $week_ranges_enabled ) ? ws_ls_post_value_numeric( 'week-number' ) : NULL;
	$weight_data            = ws_ls_entries_get( [  'week'      => $selected_week_number,
				                                    'prep'      => true,
				                                    'week'      => $selected_week_number,
				                                    'reverse'   => true,
				                                    'sort'      => 'desc' ] );

	// If enabled, render tab header
	if ( $use_tabs ) {

		$hide_tab_descriptions = ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-descriptions' ] );

		$html_output .= '<div id="ws-ls-tabs-loading" class="ws-ls-loading"></div>
								<div id="ws-ls-tabs" class="ws-ls-hide">
								<ul>';

		$tabs = [
					[
						'icon' 			=> 'fa-line-chart',
						'title'			=> __( 'Overview', WE_LS_SLUG ),
						'description'	=> __( 'Chart and add a new entry', WE_LS_SLUG )
					],
					[
						'icon' 			=> 'fa-table',
						'title'			=> __( 'History', WE_LS_SLUG ),
						'description'	=> ( true === WS_LS_IS_PRO ) ? __( 'View all of your entries', WE_LS_SLUG ) : __( 'View your latest entries', WE_LS_SLUG )
					]

		];

		// Show Photos Tab?
		if ( true === $show_photos_tab ) {

			$tabs[] = 	[
							'icon' 			=> 'fa-picture-o',
							'title'			=> __( 'Photos', WE_LS_SLUG ),
							'description'	=> __( 'View a gallery of your photos', WE_LS_SLUG )
						];
		}

		// Show Advanced Tab?
		if ( true === $show_advanced_tab ) {

			$tabs[] = 	[
				'icon' 			=> 'fa-university',
				'title'			=> __( 'Advanced', WE_LS_SLUG ),
				'description'	=> __( 'BMI, BMR, Calories and Macronutrients', WE_LS_SLUG )
			];
		}

		// If enabled, have a third tab to allow users to manage their own settings!
		if ( true === ws_ls_user_preferences_is_enabled() ) {

			$tabs[] = 	[
				'icon' 			=> 'fa-cog',
				'title'			=> __( 'Preferences', WE_LS_SLUG ),
				'description'	=> __( 'Customise this tool for you', WE_LS_SLUG )
			];
		}

		foreach ( $tabs as $tab ) {

			$html_output .= sprintf( '<li>
												<a><i class="fa %1$s" aria-hidden="true"></i>%2$s%3$s</a>
											</li>',
											$tab[ 'icon' ],
											$tab[ 'title' ],
											( false === $hide_tab_descriptions ) ? sprintf( '<span>%s</span>', $tab[ 'description' ] ) : '' );
		}

		$html_output .= '</ul>
		<div>';
	}

	// Start Chart Tab
	$html_output .= ws_ls_start_tab("wlt-chart", $use_tabs);

	// Display chart?
	if ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-chart-overview' ] ) ) {
		$html_output .= ws_ls_shortcode_embed_chart( $weight_data, $shortcode_arguments );
	}

	// Include target form?
	if ( true === ws_ls_targets_enabled() && false === ws_ls_to_bool( $shortcode_arguments[ 'hide-first-target-form' ] ) ) {
		$html_output .= ws_ls_form_weight( [ 'type' => 'target', 'css-class-form' => 'ws-ls-target-form', 'user-id' => $user_id, 'hide-confirmation' => true ] ) . ' <br />';
	}

	// Display "Add Weight" anchor?
	if(true == $shortcode_arguments['show-add-button']) {
		$html_output .= '<a name="add-weight"></a>';
	}

	$entry_id = ws_ls_querystring_value('ws-edit-entry', true);

	// Are we in front end and editing enabled, and of course we want to edit, then do so!
	if( false === empty( $entry_id ) ) {

		//If we have a Redirect URL, base decode.
		$redirect_url = ws_ls_querystring_value( 'redirect' );

		if ( false === empty( $redirect_url ) ) {
			$redirect_url = base64_decode( $redirect_url );
		}

		$html_output .= ws_ls_form_weight( [    'css-class-form'        => 'ws-ls-main-weight-form',
		                                        'user-id'               => $user_id,
		                                        'entry-id'              => $entry_id,
		                                        'hide-fields-photos'    => ws_ls_to_bool( $shortcode_arguments[ 'hide-photos' ] ),
												'redirect-url'          => $redirect_url,
												'hide-notes'            => ws_ls_to_bool( $shortcode_arguments[ 'hide-notes' ] ),
												'hide-confirmation'     => true,
												'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
												'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
												'weight-mandatory'		=> $shortcode_arguments[ 'weight-mandatory' ]
		] );

	} else {

		$html_output .= ws_ls_form_weight( [    'css-class-form'        => 'ws-ls-main-weight-form',
		                                        'user-id'               => $user_id,
		                                        'hide-fields-photos'    => ws_ls_to_bool( $shortcode_arguments[ 'hide-photos' ] ),
		                                        'hide-notes'            => ws_ls_to_bool( $shortcode_arguments[ 'hide-notes' ] ),
		                                        'hide-confirmation'     => true,
		                                        'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
		                                        'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
												'weight-mandatory'		=> $shortcode_arguments[ 'weight-mandatory' ]
		] );
	}

	// Close first tab
	$html_output .= ws_ls_end_tab( $use_tabs );

	// Start data table tab?
	if ( true === $use_tabs )	{
		$html_output .= ws_ls_start_tab( 'wlt-weight-history', $use_tabs );
	}

	$week_ranges = ( true === $week_ranges_enabled ) ? ws_ls_week_ranges_get( $user_id ) : NULL;

    // If we have data, display data table
	if ( false === empty( $weight_data ) )	{

		if ( true === ws_ls_to_bool( $shortcode_arguments[ 'show-chart-history' ] ) ) {
			$html_output .= ws_ls_shortcode_embed_chart( $weight_data, $shortcode_arguments );
		}

		if ( true === ws_ls_targets_enabled() && $use_tabs &&
		        false === ws_ls_to_bool( $shortcode_arguments[ 'hide-second-target-form' ] ) ) {
			$html_output .= ws_ls_form_weight( [ 'type' => 'target', 'css-class-form' => 'ws-ls-target-form', 'user-id' => $user_id, 'hide-confirmation' => true ] ) . ' <br />';
		}

		// Display week filters and data tab
		$html_output .= ws_ls_title( __( 'Weight History', WE_LS_SLUG ) );

		if( true === $week_ranges_enabled ) {
			$html_output .= ws_ls_week_ranges_display( $week_ranges, $selected_week_number );
		}

		if ( true === WS_LS_IS_PRO && false === ws_ls_to_bool( $shortcode_arguments[ 'disable-advanced-tables' ] ) ){
			$html_output .=  ws_ls_shortcode_table( [ 'user-id' => $user_id, 'enable-add-edit' => true, 'enable-meta-fields' => true,
			                                            'week' => $selected_week_number, 'bmi-format' => $shortcode_arguments[ 'bmi-format' ],
			                                                'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
			                                                'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ]
			] );
		} else {
			$html_output .= ws_ls_display_table( $user_id, $weight_data );
		}
	}
    elseif ( $use_tabs && false === empty( $selected_week_number ) ) {
		$html_output .= __( 'No data could be found for this week, please try selecting another:', WE_LS_SLUG );
        if( true === $week_ranges_enabled ) {
            $html_output .= ws_ls_week_ranges_display( $week_ranges, $selected_week_number );
        }
	}
	elseif ( $use_tabs ) {
		$html_output .= __('You haven\'t entered any weight data yet.', WE_LS_SLUG);
	}

	$html_output .= ws_ls_end_tab($use_tabs);

	// Photos tab?
	if ( true === $show_photos_tab ){
		$html_output .= ws_ls_start_tab( 'wlt-user-photos', $use_tabs );
		$html_output .= ws_ls_shortcode_wlt_display_photos_tab( $user_id );
		$html_output .= ws_ls_end_tab( $use_tabs );
	}

    // Advanced Data? MacroN etc?
    if ( true === $show_advanced_tab ){
        $html_output .= ws_ls_start_tab( 'wlt-user-advanced', $use_tabs );
        $html_output .= ws_ls_shortcode_wlt_display_advanced_tab( $shortcode_arguments );
        $html_output .= ws_ls_end_tab( $use_tabs );
    }

	// If enabled, have a third tab to allow users to manage their own settings!
	if( true === ws_ls_user_preferences_is_enabled() ){
		$html_output .= ws_ls_start_tab( 'wlt-user-preferences', $use_tabs );
		$html_output .= ws_ls_user_preferences_form( [ 'user-id' => $user_id,  'allow-delete-data' => ws_ls_to_bool( $shortcode_arguments[ 'allow-delete-data' ] ) ] );
		$html_output .= ws_ls_end_tab( $use_tabs );
	}

	$html_output .= ws_ls_end_tab( $use_tabs );
	$html_output .= ws_ls_end_tab( $use_tabs );

    $ws_ls_wlt_already_placed = true;

	return $html_output;

}
add_shortcode( 'wlt', 'ws_ls_shortcode_wt_legacy' );
add_shortcode( 'wt-legacy', 'ws_ls_shortcode_wt_legacy' );

/**
 * Embed Chart
 *
 * @param $weight_data
 * @param $shortcode_arguments
 *
 * @return string
 */
function ws_ls_shortcode_embed_chart( $weight_data, $shortcode_arguments ) {

	$shortcode_arguments = wp_parse_args( $shortcode_arguments, [ 	    'custom-field-groups' 	=> [],
	                                                                    'custom-field-slugs'	=> [],
																	    'hide-title' 			=> false,
																	    'legend-position' 		=> 'top',
																	    'uikit'                 => false,
																	    'hide-custom-fields'    => false,
																		'user-id'               => get_current_user_id()

	] );

	$html_output = '';

	// Do we have enough data points to display a chart?
	if ( ( false === empty( $weight_data ) && count( $weight_data ) >= $shortcode_arguments[ 'min-chart-points' ] ) ||
	     ( true === empty( $weight_data ) && 0 === $shortcode_arguments[ 'min-chart-points' ] ) ) {

		// Display "Add Weight" button?
		if( false === empty(  $shortcode_arguments[ 'show-add-button' ] ) &&
				true === ws_ls_to_bool( $shortcode_arguments[ 'show-add-button' ] ) ) {
			$html_output .= sprintf('	<div class="ws-ls-add-weight-button">
														<input type="button" onclick="location.href=\'#add-weight\';" value="%s" />
													</div>',
				__( 'Add a weight entry', WE_LS_SLUG )
			);
		}

		if ( false === $shortcode_arguments[ 'hide-title'] ) {
			$html_output .= ws_ls_title( __( 'In a chart', WE_LS_SLUG ) );
		}

		$html_output .= ws_ls_display_chart( $weight_data, [    'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
		                                                        'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
																'legend-position'       => $shortcode_arguments[ 'legend-position' ],
																'user-id'               => $shortcode_arguments[ 'user-id' ],
																'show-meta-fields'      => ! $shortcode_arguments[ 'hide-custom-fields' ]
		] );

	} else {

		$message = sprintf( __( 'A graph shall appear when %d or more weight entries have been entered.', WE_LS_SLUG ),
			$shortcode_arguments[ 'min-chart-points' ] );

		$html_output .= ( true === empty( $shortcode_arguments[ 'uikit' ] ) ) ?
								ws_ls_display_blockquote( $message ) :
									ws_ls_component_alert( [ 'message' =>  $message, 'type' => 'warning', 'closable' => false ] );

	}

	return $html_output;
}

/**
* HTML for opening tab
* @param $tab_name
* @param $use_tabs
*
* @return string
*/
function ws_ls_start_tab( $tab_name, $use_tabs )	{
	return ( true === $use_tabs ) ? sprintf( '<div class="ws-ls-tab %s">', $tab_name ) : '';
}

/**
* HTML for closing tab
* @param $use_tabs
*
* @return string
*/
function ws_ls_end_tab( $use_tabs )	{
	return ( true === $use_tabs ) ? '</div>' : '';
}

/**
* @param $title_text
*
* @return string
*/
function ws_ls_title( $title_text ) {
	return sprintf( '<h3 class="ws_ls_title">%s</h3>', esc_html( $title_text ) );
}


/**
 * Display a notice about it being beta
 * @return string
 */
function ws_ls_wt_beta_upgrade_notice() {

	if ( !current_user_can( 'manage_options' ) )  {
		return '';
	}

	$key = 'ws-ls-wt-upgrade-notice';

	if ( 'y' === ws_ls_querystring_value( $key ) ) {
		update_option( $key, 'n' );
	}

	if ( 'n' === get_option( $key, 'y' ) ) {
		return '';
	}

	$link = ws_ls_get_url();

	$link = add_query_arg($key, 'y', $link );

	$message = '<p>
					<strong>
						Note: Only administrators can see this message.
					</strong>
				</p>
			    <p>This shortcode, [wt] will be replaced in the near future with a modern replacement. To test that the newer version works smoothly with your website, we have released a beta. You can test this beta by placing the shortcode [wt-beta]. Read more:</p>
			    <p><a href="https://docs.yeken.uk/shortcodes/wt-beta.html" target="_blank" rel="noopener">https://docs.yeken.uk/shortcodes/wt-beta.html</a>.</p>
';

	return ws_ls_blockquote_success( $message );
}
