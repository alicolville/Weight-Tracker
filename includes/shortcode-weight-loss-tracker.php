<?php
	defined('ABSPATH') or die('Jog on!');

	$ws_ls_tab_index = 1;
	$ws_ls_wlt_already_placed = false;

	function ws_ls_shortcode($user_defined_arguments)
	{
			global $save_response;
			global $ws_ls_wlt_already_placed;

            $shortcode_arguments = shortcode_atts(
            array(
                'min-chart-points' 			=> 2,						// Minimum number of data entries before chart is shown
				'hide-first-target-form' 	=> false,					// Hide first Target form
				'hide-second-target-form' 	=> false,					// Hide second Target form
				'show-add-button' 			=> false,					// Display a "Add weight" button above the chart.
                'allow-delete-data' 		=> true,                	// Show "Delete your data" section
                'hide-photos' 				=> false,                   // Hide photos part of form
                'hide-tab-photos' 			=> false,                 	// Hide Photos tab
                'hide-tab-advanced' 		=> false,               	// Hide Advanced tab (macroN, calories, etc)
				'hide-tab-descriptions' 	=> false,               	// Hide tab descriptions
                'hide-advanced-narrative' 	=> false,         			// Hide text describing BMR, MarcoN, etc
                'disable-advanced-tables' 	=> false,         			// Disable advanced data tables.
                'disable-tabs' 				=> false,                   // Disable using tabs.
				'disable-second-check' 		=> false,					// Disable check to see if [wlt] placed more than once
				'user-id'					=> get_current_user_id()
               ), $user_defined_arguments );

			// Validate arguments
			$shortcode_arguments['hide-first-target-form'] = ws_ls_force_bool_argument($shortcode_arguments['hide-first-target-form']);
			$shortcode_arguments['hide-second-target-form'] = ws_ls_force_bool_argument($shortcode_arguments['hide-second-target-form']);
			$shortcode_arguments['show-add-button'] = ws_ls_force_bool_argument($shortcode_arguments['show-add-button']);
			$shortcode_arguments['min-chart-points'] = ws_ls_force_numeric_argument($shortcode_arguments['min-chart-points'], 2);
            $shortcode_arguments['allow-delete-data'] = ws_ls_force_bool_argument($shortcode_arguments['allow-delete-data']);
            $shortcode_arguments['hide-photos'] = ws_ls_force_bool_argument($shortcode_arguments['hide-photos']);
            $shortcode_arguments['hide-tab-photos'] = ws_ls_force_bool_argument($shortcode_arguments['hide-tab-photos']);
            $shortcode_arguments['hide-tab-advanced'] = ws_ls_force_bool_argument($shortcode_arguments['hide-tab-advanced']);
            $shortcode_arguments['hide-advanced-narrative'] = ws_ls_force_bool_argument($shortcode_arguments['hide-advanced-narrative']);
            $shortcode_arguments['disable-advanced-tables'] = ws_ls_force_bool_argument($shortcode_arguments['disable-advanced-tables']);
            $shortcode_arguments['disable-tabs'] = ws_ls_force_bool_argument($shortcode_arguments['disable-tabs']);
			$shortcode_arguments['disable-second-check'] = ws_ls_force_bool_argument($shortcode_arguments['disable-second-check']);

			if ( true === $ws_ls_wlt_already_placed && false === $shortcode_arguments['disable-second-check'] ) {
				return '<p>' . __('This shortcode can only be placed once on a page / post.', WE_LS_SLUG) . '</p>';
			}

			ws_ls_enqueue_files();

			// Display error if user not logged in
			if (!is_user_logged_in())	{
				return ws_ls_display_blockquote(__('You need to be logged in to record your weight.', WE_LS_SLUG) , '', false, true);
			}

            $user_id 	= (int) $shortcode_arguments[ 'user-id' ];
            $use_tabs 	= (false === $shortcode_arguments['disable-tabs']);

	        // Decide whether to show Macro N / Calories tab
            $show_advanced_tab = (false === $shortcode_arguments['hide-tab-advanced'] && true === WS_LS_IS_PRO_PLUS);
			$show_photos_tab = ( false === $shortcode_arguments['hide-tab-photos'] && true === ws_ls_meta_fields_photo_any_enabled( true ) );

            $html_output = '';

			// If a form was previously submitted then display resulting message!
			if (!empty($save_response) && $save_response['form_number'] == false){
				$html_output .= $save_response['message'];
			}

			if(isset($_GET['user-preference-saved']) && 'true' == $_GET['user-preference-saved'])	{
				$html_output .= ws_ls_display_blockquote(__('Your settings have been saved!', WE_LS_SLUG), 'ws-ls-success');
			} elseif( true === ws_ls_user_preferences_is_enabled() && isset($_GET['user-delete-all']) && 'true' == $_GET['user-delete-all'])	{
				$html_output .= ws_ls_display_blockquote(__('Your weight history has been deleted!', WE_LS_SLUG), 'ws-ls-success');
			}
			// Has the user selected a particular week to look at?
			$selected_week_number = -1;
			if (isset($_POST["week_number"]) && is_numeric($_POST["week_number"])) {
				$selected_week_number = $_POST["week_number"];
			}

			// Load week ranges
			if (ws_ls_is_date_intervals_enabled()) {
				$week_ranges = ws_ls_get_week_ranges();
			}

			$weight_data = ws_ls_db_weights_get( [ 'week' => $selected_week_number, 'prep' => true ] );

			// If enabled, render tab header
			if ( $use_tabs )	{

				$hide_tab_descriptions = ws_ls_force_bool_argument( $shortcode_arguments['hide-tab-descriptions'] );

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

					$description = ( false === $hide_tab_descriptions ) ? '<span>' . $tab[ 'description' ] . '</span>' : '';

					$html_output .= sprintf( '<li>
														<a><i class="fa %1$s" aria-hidden="true"></i>%2$s%3$s</a>
													</li>',
													$tab[ 'icon' ],
													$tab[ 'title' ],
													$description
					);

				}

				$html_output .= '</ul>
				<div>';

			}

			// Start Chart Tab
			$html_output .= ws_ls_start_tab("wlt-chart", $use_tabs);

            if (($weight_data && count($weight_data) >= $shortcode_arguments['min-chart-points']) ||
					empty($weight_data) && 0 == $shortcode_arguments['min-chart-points']) {

				// Display "Add Weight" button?
				if(true == $shortcode_arguments['show-add-button']) {
					$html_output .= '	<div class="ws-ls-add-weight-button">
											<input type="button" onclick="location.href=\'#add-weight\';" value="' . __('Add a weight entry', WE_LS_SLUG) . '" />
										</div>';
				}

				// Great, we have some weight data. Chop it up so we only have (at most) 30 plot points for the graph
				$html_output .= ws_ls_title(__('In a chart', WE_LS_SLUG));

				$html_output .= ws_ls_display_chart( $weight_data );
			}
			else {
				$html_output .= ws_ls_display_blockquote( __('A graph will appear once several weights have been entered.', WE_LS_SLUG) );
			}

			// Include target form?
			if (WE_LS_ALLOW_TARGET_WEIGHTS && false == $shortcode_arguments['hide-first-target-form']) {
				$html_output .= ws_ls_display_weight_form( true, 'ws-ls-target-form', $user_id );
			}

			// Display "Add Weight" anchor?
			if(true == $shortcode_arguments['show-add-button']) {
				$html_output .= '<a name="add-weight"></a>';
			}

			$entry_id = ws_ls_querystring_value('ws-edit-entry', true);

			// Are we in front end and editing enabled, and of course we want to edit, then do so!
			if( false === empty($entry_id)) {

				if ($entry_id) {
				//	$data = ws_ls_get_weight( $user_id, $entry_id);

					$data = ws_ls_entry_get( [ 'user-id' => $user_id, 'id' => $entry_id ] );
				}

				//If we have a Redirect URL, base decode.
				$redirect_url = ws_ls_querystring_value('redirect');

				if (false === empty($redirect_url)) {
					$redirect_url = base64_decode($redirect_url);
				}

				$html_output .= ws_ls_display_weight_form(false, false,	$user_id, false, false, false,
					false, false, $redirect_url, $data, true, $shortcode_arguments['hide-photos']);
			} else {

				// Display input form in add mode
				$html_output .= ws_ls_display_weight_form(false, 'ws-ls-main-weight-form', $user_id, false, false, false, true, false, false, false, false, $shortcode_arguments['hide-photos']);
			}

			// Close first tab
			$html_output .= ws_ls_end_tab($use_tabs);

			// Start data table tab?
			if ($use_tabs)	{
				$html_output .= ws_ls_start_tab('wlt-weight-history', $use_tabs);
			}

            //If we have data, display data table
			if ( $weight_data && ( count( $weight_data ) > 0 || $selected_week_number != -1 ) )	{

					if ( WE_LS_ALLOW_TARGET_WEIGHTS && $use_tabs && false == $shortcode_arguments['hide-second-target-form'] ) {
						$html_output .= ws_ls_display_weight_form( true, 'ws-ls-target-form', $user_id );
					}

					// Display week filters and data tab
					$html_output .= ws_ls_title( __('Weight History', WE_LS_SLUG ) );
					if( count($week_ranges) <= WE_LS_TABLE_MAX_WEEK_FILTERS ) {
						$html_output .= ws_ls_display_week_filters( $week_ranges, $selected_week_number );
					}

					if ( WS_LS_IS_PRO && false === $shortcode_arguments['disable-advanced-tables'] ){
						$html_output .=  ws_ls_data_table_placeholder( $user_id );
					} else {
						$html_output .= ws_ls_display_table( $weight_data );
					}
			}
            elseif ($use_tabs && $selected_week_number != -1) {
				$html_output .= __('There is no data for this week, please try selecting another:', WE_LS_SLUG);
                if(count($week_ranges) <= WE_LS_TABLE_MAX_WEEK_FILTERS) {
                    $html_output .= ws_ls_display_week_filters($week_ranges, $selected_week_number);
                }
			}
			elseif ($use_tabs) {
				$html_output .= __('You haven\'t entered any weight data yet.', WE_LS_SLUG);
			}
			$html_output .= ws_ls_end_tab($use_tabs);

			// Photos tab?
			if ( true === $show_photos_tab ){
				$html_output .= ws_ls_start_tab('wlt-user-photod', $use_tabs);
				$html_output .= ws_ls_shortcode_wlt_display_photos_tab( $user_id );
				$html_output .= ws_ls_end_tab($use_tabs);
			}

            // Advanced Data? MacroN etc?
            if ( true === $show_advanced_tab ){
                $html_output .= ws_ls_start_tab('wlt-user-advanced', $use_tabs);
                $html_output .= ws_ls_shortcode_wlt_display_advanced_tab( $shortcode_arguments );
                $html_output .= ws_ls_end_tab($use_tabs);
            }

			// If enabled, have a third tab to allow users to manage their own settings!
			if( true === ws_ls_user_preferences_is_enabled() ){
				$html_output .= ws_ls_start_tab('wlt-user-preferences', $use_tabs);
				$html_output .= ws_ls_user_preferences_form( ['user-id' => $user_id,  'allow-delete-data' => $shortcode_arguments['allow-delete-data']]);
				$html_output .= ws_ls_end_tab($use_tabs);
			}

			$html_output .= ws_ls_end_tab($use_tabs);
			$html_output .= ws_ls_end_tab($use_tabs);

            $ws_ls_wlt_already_placed = true;

			return $html_output;

	}
add_shortcode( 'weight-loss-tracker', 'ws_ls_shortcode' );
add_shortcode( 'wlt', 'ws_ls_shortcode' );

function ws_ls_start_tab($tab_name, $use_tabs)	{
	if ($use_tabs) {
		return '<div' . (($tab_name) ? ' class="' . esc_attr( $tab_name ) . '"' : '') . '>';
	}
	return '';
}
function ws_ls_end_tab($use_tabs)	{
	if ($use_tabs) {
		return '</div>';
	}
	return '';
}
function ws_ls_title($title_text)
{
	return '<h3 class="ws_ls_title">' . esc_html( $title_text ) . '</h3>';
}
