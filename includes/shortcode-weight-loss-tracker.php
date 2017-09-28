<?php
	defined('ABSPATH') or die('Jog on!');

	$ws_ls_tab_index = 1;
	$ws_ls_wlt_already_placed = false;

	function ws_ls_shortcode($user_defined_arguments)
	{
			global $save_response;
			global $ws_ls_wlt_already_placed;

			if ( true === $ws_ls_wlt_already_placed ) {
			   return '<p>' . __('This shortcode can only be placed once on a page / post.', WE_LS_SLUG) . '</p>';
            }

			ws_ls_enqueue_files();

			// Display error if user not logged in
			if (!is_user_logged_in())	{
				return ws_ls_display_blockquote(__('You need to be logged in to record your weight.', WE_LS_SLUG) , '', false, true);
			}

            $shortcode_arguments = shortcode_atts(
            array(
                'min-chart-points' => 2,					// Minimum number of data entries before chart is shown
				'hide-first-target-form' => false,			// Hide first Target form
				'hide-second-target-form' => false,			// Hide second Target form
				'show-add-button' => false,					// Display a "Add weight" button above the chart.
                'allow-delete-data' => true,                // Show "Delete your data" section
                'hide-photos' => false,                     // Hide photos part of form
                'hide-tab-photos' => false,                 // Hide Photos tab
                'hide-tab-advanced' => false,               // Hide Advanced tab (macroN, calories, etc)
                'hide-advanced-narrative' => false          // Hide text describing BMR, MarcoN, etc
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

            $user_id = get_current_user_id();

            // Decide whether to show Macro N / Calories tab
            $show_advanced_tab = (false === $shortcode_arguments['hide-tab-advanced'] && true === WS_LS_IS_PRO_PLUS);
			$show_photos_tab = (false === $shortcode_arguments['hide-tab-photos'] && true === WE_LS_PHOTOS_ENABLED);

            $html_output = '';

			// If a form was previously submitted then display resulting message!
			if (!empty($save_response) && $save_response['form_number'] == false){
				$html_output .= $save_response['message'];
			}

			if(isset($_GET['user-preference-saved']) && 'true' == $_GET['user-preference-saved'])	{
				$html_output .= ws_ls_display_blockquote(__('Your settings have been saved!', WE_LS_SLUG), 'ws-ls-success');
			} elseif(WE_LS_ALLOW_USER_PREFERENCES && isset($_GET['user-delete-all']) && 'true' == $_GET['user-delete-all'])	{
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

			// Load user's weight dta (taking into account selected week)
			$weight_data = ws_ls_get_weights($user_id, 1000, $selected_week_number);

			// If enabled, render tab header
			if (WE_LS_USE_TABS)	{

				$html_output .= '
                        <div id="ws-ls-tabs-loading" class="ws-ls-loading"></div>
						<div id="ws-ls-tabs" style="display:none;">
							<ul>
									<li><a><i class="fa fa-line-chart" aria-hidden="true"></i>' . __('Overview', WE_LS_SLUG) . '<span>' . __('Chart and add a new entry', WE_LS_SLUG) . '</span></a></li>
									<li><a><i class="fa fa-table" aria-hidden="true"></i>' . __('All Entries', WE_LS_SLUG) . '<span>' . __('View all of your entries', WE_LS_SLUG) . '</span></a></li>';

									 // Show Advanced Tab?
                                    if ( true === $show_photos_tab ) {
										$html_output .= '<li><a><i class="fa fa-picture-o" aria-hidden="true"></i>' . __('Photos', WE_LS_SLUG) . '<span>' . __('View a gallery of your photos', WE_LS_SLUG) . '</span></a></li>';
									}

                                    // Show Advanced Tab?
                                    if ( true === $show_advanced_tab ) {
                                        $html_output .= '<li><a><i class="fa fa-university" aria-hidden="true"></i>' . __('Advanced', WE_LS_SLUG) . '<span>' . __('View BMI, BMR and suggested Calorie and Macronutrient intake', WE_LS_SLUG) . '</span></a></li>';
                                    }

									// If enabled, have a third tab to allow users to manage their own settings!
									if(WE_LS_ALLOW_USER_PREFERENCES){
										$html_output .= '<li><a><i class="fa fa-cog" aria-hidden="true"></i>' . __('Preferences', WE_LS_SLUG) . '<span>' . __('Customise this tool and tell us a little more about you', WE_LS_SLUG) . '</span></a></li>';
									}

							$html_output .= '</ul>
							<div>';
			}

			// Start Chart Tab
			$html_output .= ws_ls_start_tab("wlt-chart");

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

                $weight_data_for_graph = ws_ls_fetch_elements_from_end_of_array($weight_data, WE_LS_CHART_MAX_POINTS);

				//$weight_data_for_graph = array_slice($weight_data, 0, WE_LS_CHART_MAX_POINTS);
				$html_output .= ws_ls_display_chart($weight_data_for_graph);
			}
			else {
				$html_output .= ws_ls_display_blockquote( __('A graph will appear once several weights have been entered.', WE_LS_SLUG) );
			}

			// Include target form?
			if (WE_LS_ALLOW_TARGET_WEIGHTS && false == $shortcode_arguments['hide-first-target-form']) {
				$html_output .= ws_ls_display_weight_form(true, 'ws-ls-target-form', false, false);
			}

			// Display "Add Weight" anchor?
			if(true == $shortcode_arguments['show-add-button']) {
				$html_output .= '<a name="add-weight"></a>';
			}

			$entry_id = ws_ls_querystring_value('ws-edit-entry', true);

			// Are we in front end and editing enabled, and of course we want to edit, then do so!
			if( false === empty($entry_id)) {

				if ($entry_id) {
					$data = ws_ls_get_weight(get_current_user_id(), $entry_id);
				}

				//If we have a Redirect URL, base decode.
				$redirect_url = ws_ls_querystring_value('redirect');

				if (false === empty($redirect_url)) {
					$redirect_url = base64_decode($redirect_url);
				}

				$html_output .= ws_ls_display_weight_form(false, false,	false, false, false, false,
					false, false, $redirect_url, $data, true, $shortcode_arguments['hide-photos']);
			} else {

				// Display input form in add mode
				$html_output .= ws_ls_display_weight_form(false, 'ws-ls-main-weight-form', false, false, false, false, true, false, false, false, false, $shortcode_arguments['hide-photos']);
			}

			// Close first tab
			$html_output .= ws_ls_end_tab();

			// Start data table tab?
			if (WE_LS_USE_TABS)	{
				$html_output .= ws_ls_start_tab('wlt-weight-history');
			}

            //If we have data, display data table
			if ($weight_data && (count($weight_data) > 0 || $selected_week_number != -1))	{

					if (WE_LS_ALLOW_TARGET_WEIGHTS && WE_LS_USE_TABS && false == $shortcode_arguments['hide-second-target-form']) {
						$html_output .= ws_ls_display_weight_form(true, 'ws-ls-target-form', false, false);
					}

					// Display week filters and data tab
					$html_output .= ws_ls_title(__('Weight History', WE_LS_SLUG));
					if(count($week_ranges) <= WE_LS_TABLE_MAX_WEEK_FILTERS) {
						$html_output .= ws_ls_display_week_filters($week_ranges, $selected_week_number);
					}

					if (WS_LS_ADVANCED_TABLES && WS_LS_IS_PRO){
						$html_output .=  ws_ls_data_table_placeholder($user_id, false, false, true);
					} else {
						$html_output .= ws_ls_display_table($weight_data);
					}
			}
            elseif (WE_LS_USE_TABS && $selected_week_number != -1) {
				$html_output .= __('There is no data for this week, please try selecting another:', WE_LS_SLUG);
                if(count($week_ranges) <= WE_LS_TABLE_MAX_WEEK_FILTERS) {
                    $html_output .= ws_ls_display_week_filters($week_ranges, $selected_week_number);
                }
			}
			elseif (WE_LS_USE_TABS) {
				$html_output .= __('You haven\'t entered any weight data yet.', WE_LS_SLUG);
			}
			$html_output .= ws_ls_end_tab();

			// Advanced Data? MacroN etc?
			if ( true === $show_photos_tab ){
				$html_output .= ws_ls_start_tab('wlt-user-photod');
				$html_output .= ws_ls_shortcode_wlt_display_photos_tab();
				$html_output .= ws_ls_end_tab();
			}

            // Advanced Data? MacroN etc?
            if ( true === $show_advanced_tab ){
                $html_output .= ws_ls_start_tab('wlt-user-advanced');
                $html_output .= ws_ls_shortcode_wlt_display_advanced_tab($shortcode_arguments);
                $html_output .= ws_ls_end_tab();
            }

			// If enabled, have a third tab to allow users to manage their own settings!
			if(WE_LS_ALLOW_USER_PREFERENCES){
				$html_output .= ws_ls_start_tab('wlt-user-preferences');
				$html_output .= ws_ls_user_preferences_form(['user-id' => false,  'allow-delete-data' => $shortcode_arguments['allow-delete-data']]);
				$html_output .= ws_ls_end_tab();
			}

			$html_output .= ws_ls_end_tab();
			$html_output .= ws_ls_end_tab();

            $ws_ls_wlt_already_placed = true;

			return $html_output;

	}

function ws_ls_start_tab($tab_name)	{
	if (WE_LS_USE_TABS) {
		return '<div' . (($tab_name) ? ' class="' . $tab_name . '"' : '') . '>';
	}
	return '';
}
function ws_ls_end_tab()	{
	if (WE_LS_USE_TABS) {
		return '</div>';
	}
	return '';
}
function ws_ls_title($title_text)
{
	return '<h3 class="ws_ls_title">' . $title_text . '</h3>';
}
