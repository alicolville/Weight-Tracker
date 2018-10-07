<?php
	defined('ABSPATH') or die('Jog on!');

	/* Display weight data in a HTML table */
	function ws_ls_display_table($weight_data) {

		$html_output = '';

		$html_output .= '
		<table width="100%" class="ws-ls-data-table">
		  <thead>
		  <tr>
			<th width="25%">' . __('Date', WE_LS_SLUG) .'</th>
			<th width="25%">' . __('Weight', WE_LS_SLUG) . ' (' . ws_ls_get_unit() . ')</th>
			<th>' . __('Notes', WE_LS_SLUG) . '</th>
		  </tr>
		  </thead>
		<tbody>';

		foreach ($weight_data as $weight_object)
		{
			$html_output .= '<tr>
							  <td>' . esc_html(ws_ls_render_date($weight_object)) . '</td>
							  <td>' . esc_html($weight_object['display']) . '</td>
							  <td>' . esc_html($weight_object['notes']) . '</td>
							</tr>';
		}

		$html_output .= '<tbody></table>';

		return $html_output;
	}

  	/* Display Chart */
	function ws_ls_display_chart($weight_data, $options = false)
	{
        // Build the default arguments for a chart. This can then be overrided by what is being passed in (i.e. to support shortcode arguments)
		$chart_config = array(
			'user-id' => get_current_user_id(),
			'type' => WE_LS_CHART_TYPE,
			'height' => WE_LS_CHART_HEIGHT,
            'weight-line-color' => WE_LS_WEIGHT_LINE_COLOUR,
			'weight-fill-color' => WE_LS_WEIGHT_FILL_COLOUR,
			'weight-target-color' => WE_LS_TARGET_LINE_COLOUR,
			'show-gridlines' => WE_LS_CHART_SHOW_GRID_LINES,
			'bezier' => WE_LS_CHART_BEZIER_CURVE,
            'hide_login_message_if_needed' => true,
			'exclude-measurements' => false,
            'ignore-login-status' => false
		);

        // If we are PRO and the developer has specified options then override the default
        if($options && WS_LS_IS_PRO){
            $chart_config = wp_parse_args( $options, $chart_config );
        }

		$measurements_enabled = (false == $chart_config['exclude-measurements'] && WE_LS_MEASUREMENTS_ENABLED && ws_ls_any_active_measurement_fields()) ? true : false;

        // Make sure they are logged in
        if (false == $chart_config['ignore-login-status'] && !is_user_logged_in())	{
            if (false == $chart_config['hide_login_message_if_needed']) {
				return ws_ls_display_blockquote(__('You need to be logged in to record your weight.', WE_LS_SLUG) , '', false, true);
			} else {
                return;
            }
        }

		$chart_id = 'ws_ls_chart_' . rand(10,1000) . '_' . rand(10,1000);

		// If Pro disabled or Measurements to be displayed then force to line
		if(!WS_LS_IS_PRO || $measurements_enabled) {
			$chart_config['type'] = 'line';
		}

		$y_axis_unit = (ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS')) ? __('lbs', WE_LS_SLUG) : __('kg', WE_LS_SLUG) ;
		$y_axis_measurement_unit = ('inches' == ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT')) ? __('Inches', WE_LS_SLUG) : __('CM', WE_LS_SLUG) ;

		$point_size = (WE_LS_ALLOW_POINTS && WE_LS_CHART_POINT_SIZE > 0) ? WE_LS_CHART_POINT_SIZE : 0;
        $line_thickness = 2;

		// Build graph data
		$graph_data['labels'] = array();
		$graph_data['datasets'][0] = array( 'label' =>  __('Weight', WE_LS_SLUG),
											'borderColor' => $chart_config['weight-line-color'],
										    );

		// Determine fill based on chart type
		if ('line' == $chart_config['type']) {

			// Add a fill colour under weight line?
			if ( true === WE_LS_WEIGHT_FILL_LINE_ENABLED ) {
				$graph_data['datasets'][0]['fill'] = true;
				$graph_data['datasets'][0]['backgroundColor'] =  ws_ls_hex_to_rgb( WE_LS_WEIGHT_FILL_LINE_COLOUR, WE_LS_WEIGHT_FILL_LINE_OPACITY );
			} else {
				$graph_data['datasets'][0]['fill'] = false;
			}

			$graph_data['datasets'][0]['lineTension'] = ($chart_config['bezier']) ? 0.4 : 0;
			$graph_data['datasets'][0]['pointRadius'] = $point_size;
            $graph_data['datasets'][0]['borderWidth'] = $line_thickness;
		} else {
			$graph_data['datasets'][0]['fill'] = true;
			$graph_data['datasets'][0]['backgroundColor'] = $chart_config['weight-fill-color'];
			$graph_data['datasets'][0]['borderWidth'] = 2;

		}

		$graph_data['datasets'][0]['data'] = array();
		$graph_data['datasets'][0]['yAxisID'] = 0;

		$target_weight = ws_ls_get_user_target($chart_config['user-id']);

		$chart_type_supports_target_data = ('bar' == $chart_config['type']) ? false : true;

		$dataset_index = 1;
		$number_of_measurement_datasets_with_data = 0;

		// If target weights are enabled, then include into javascript data object
		if ($target_weight != false && WE_LS_ALLOW_TARGET_WEIGHTS && $chart_type_supports_target_data){

				$graph_data['datasets'][1] = array( 'label' =>  __('Target', WE_LS_SLUG),
												    'borderColor' => $chart_config['weight-target-color'],
													'borderWidth' => $line_thickness,
													'pointRadius' => 0,
													'borderDash' => array(5,5),
													'fill' => false,
													'type' => 'line'
												);
				$graph_data['datasets'][1]['data'] = array();
				$dataset_index = 2;
		}

		// ----------------------------------------------------------------------------
		// Measurements - add measurement sets if enabled!
		// ----------------------------------------------------------------------------

		if($measurements_enabled) {
			$active_measurement_fields = ws_ls_get_active_measurement_fields();
			$active_measurment_field_keys = ws_ls_get_keys_for_active_measurement_fields('', true);
			$measurement_graph_indexes = array();


			foreach ($active_measurement_fields as $key => $data) {

				$graph_data['datasets'][$dataset_index] = array( 'label' =>  __( $data['title'], WE_LS_SLUG),
													'borderColor' => $data['chart_colour'],
													'borderWidth' => $line_thickness,
													'pointRadius' => $point_size,
													'fill' => false,
													'spanGaps' => true,
													'yAxisID' => 'y-axis-measurements',
													'type' => 'line',
													'lineTension' => ($chart_config['bezier']) ? 0.4 : 0
												);
				$graph_data['datasets'][$dataset_index]['data'] = array();
				$graph_data['datasets'][$dataset_index]['data-count'] = 0;

				$measurement_graph_indexes[$key] = $dataset_index;

				$dataset_index++;
			}
		}

		if($weight_data) {
			foreach ($weight_data as $weight_object) {

				array_push($graph_data['labels'], $weight_object['date-graph']);
				array_push($graph_data['datasets'][0]['data'], $weight_object['graph_value']);

				// Set target weight if specified
				if ($target_weight != false && WE_LS_ALLOW_TARGET_WEIGHTS && $chart_type_supports_target_data){
					array_push($graph_data['datasets'][1]['data'], $target_weight['graph_value']);
				}

				// ----------------------------------------------------------------------------
				// Add data for all measurements
				// ----------------------------------------------------------------------------
				if($measurements_enabled) {
					foreach ($active_measurment_field_keys as $key) {

						// If we have a genuine measurement value then add to graph data - otherwise NULL
						if(!is_null($weight_object['measurements'][$key]) && 0 != $weight_object['measurements'][$key]) {
							$graph_data['datasets'][$measurement_graph_indexes[$key]]['data'][] = ws_ls_prep_measurement_for_display($weight_object['measurements'][$key]);
							$graph_data['datasets'][$measurement_graph_indexes[$key]]['data-count']++;
						} else {
							$graph_data['datasets'][$measurement_graph_indexes[$key]]['data'][] = NULL;
						}
					}
				}
			}

		}

		// Remove any empty measurements from graph
		if($measurements_enabled) {
			foreach ($active_measurment_field_keys as $key) {
				if(0 == $graph_data['datasets'][$measurement_graph_indexes[$key]]['data-count']) {
			//		unset($graph_data['datasets'][$measurement_graph_indexes[$key]]);
				} else {
					$number_of_measurement_datasets_with_data++;
				}
			}
		}

		// Embed JavaScript data object for this graph into page
		wp_localize_script( 'jquery-chart-ws-ls', $chart_id . '_data', $graph_data );

		$graph_line_options = array();

		// Set initial y axis for weight
		$graph_line_options = array(
			'scales' => array('yAxes' => array(array('scaleLabel' => array('display' => true, 'labelString' => __('Weight', WE_LS_SLUG) . ' (' . __($y_axis_unit, WE_LS_SLUG) . ')'), 'type' => "linear", 'ticks' => array('beginAtZero' => WE_LS_AXES_START_AT_ZERO), "display" => "true", "position" => "left", "id" => "y-axis-weight", '' , 'gridLines' => array('display' => $chart_config['show-gridlines']))))
		);

		if ('line' == $chart_config['type']) {

			// Add measurement Axis?
			if ($measurements_enabled ) {
				$graph_line_options['scales']['yAxes'] = array_merge($graph_line_options['scales']['yAxes'], array(array('scaleLabel' => array('display' => true, 'labelString' => __('Measurement', WE_LS_SLUG) . ' (' . __($y_axis_measurement_unit, WE_LS_SLUG) . ')'), 'ticks' => array('beginAtZero' => WE_LS_AXES_START_AT_ZERO), 'type' => "linear", "display" => (($number_of_measurement_datasets_with_data != 0) ? true : false), "position" => "right", "id" => "y-axis-measurements", 'gridLines' => array('display' => $chart_config['show-gridlines']))));
			}
		}

		// If gridlines are disabled, hide x axes too
		if(!$chart_config['show-gridlines']) {
			$graph_line_options['scales']['xAxes'] = array(array('gridLines' => array('display' => false)));
		}

		// Legend
		$graph_line_options['legend']['position'] = 'bottom';
		$graph_line_options['legend']['labels']['boxWidth'] = 10;
		$graph_line_options['legend']['labels']['fontSize'] = 10;

		// Font settings
        $graph_line_options['fontColor'] = WE_LS_TEXT_COLOUR;
        $graph_line_options['fontFamily'] = WE_LS_FONT_FAMILY;

		// Embed JavaScript options object for this graph into page
		wp_localize_script( 'jquery-chart-ws-ls', $chart_id . '_options', $graph_line_options );

		$html = '<div><canvas id="' . $chart_id . '" class="ws-ls-chart" ' . (($chart_config['height']) ? 'height="'.  esc_attr($chart_config['height']) . '" ' : '') . ' data-chart-type="' . esc_attr($chart_config['type'])  . '" data-target-weight="' . esc_attr($target_weight['graph_value']) . '" data-target-colour="' . esc_attr($chart_config['weight-target-color']) . '"></canvas>';
		$html .= '<div class="ws-ls-notice-of-refresh ws-ls-reload-page-if-clicked ws-ls-hide"><a href="#">' . __('You have modified data. Please refresh page.', WE_LS_SLUG) . '</a></div>';
		$html .= '</div>';
		return $html;
	}

/*
	Displays either a target or weight form
*/
function ws_ls_display_weight_form($target_form = false, $class_name = false, $user_id = false, $hide_titles = false,
                                        $form_number = false, $force_to_todays_date = false, $hide_login_message_if_needed = true,
                                            $hide_measurements_form = false, $redirect_url = false, $existing_data = false, $cancel_button = false,
                                                $hide_photos_form = false, $hide_meta_fields_form = false ) {
    global $save_response;
    $html_output  = '';

    $measurements_form_enabled = (WE_LS_MEASUREMENTS_ENABLED && ws_ls_any_active_measurement_fields() && false == $hide_measurements_form && !$target_form) ? true : false;
    $photo_form_enabled = ( false === $hide_photos_form && true === ws_ls_meta_fields_photo_any_enabled( true ) && false === $target_form);
    $meta_field_form_enabled = ( false === $hide_meta_fields_form && true === ws_ls_meta_fields_is_enabled() && ws_ls_meta_fields_number_of_enabled() > 0 && false === $target_form);
    $entry_id = NULL;

    // Make sure they are logged in
    if (!is_user_logged_in())	{
        if ($hide_login_message_if_needed) {

            $prompt = ( true === $target_form ) ? __('You need to be logged in to set your target.', WE_LS_SLUG) : __('You need to be logged in to record your weight.', WE_LS_SLUG);

            return ws_ls_display_blockquote($prompt, '', false, true);
        } else {
            return;
        }
    }

    if(true === empty($user_id)){
        $user_id = get_current_user_id();
    }

    $form_id = 'ws_ls_form_' . rand(10,1000) . '_' . rand(10,1000);

	// Set title / validator
    if (!$hide_titles) {

		$title = __('Add a new weight', WE_LS_SLUG);

		if ($target_form) {
			$title = __('Target Weight', WE_LS_SLUG);
		} else if( false === empty($existing_data) ) {
			$title = __('Edit weight', WE_LS_SLUG);
		}

        $html_output .= '<h3 class="ws_ls_title">' . esc_html($title) . '</h3>';
    }

	// If a form was previously submitted then display resulting message!
	if ($form_number && !empty($save_response) && $save_response['form_number'] == $form_number){
		$html_output .= $save_response['message'];
	}

	$html_output .= sprintf('
							<form action="%1$s" method="post" class="we-ls-weight-form we-ls-weight-form-validate ws_ls_display_form%2$s" id="%3$s"
							data-measurements-enabled="%4$s"
							data-measurements-all-required="%5$s"
							data-is-target-form="%6$s"
							data-metric-unit="%7$s",
							data-photos-enabled="%12$s",
							%11$s
							>
							<input type="hidden" value="%8$s" id="ws_ls_is_target" name="ws_ls_is_target" />
							<input type="hidden" value="true" id="ws_ls_is_weight_form" name="ws_ls_is_weight_form" />
							<input type="hidden" value="%9$s" id="ws_ls_user_id" name="ws_ls_user_id" />
							<input type="hidden" value="%10$s" id="ws_ls_security" name="ws_ls_security" />',
							esc_attr( get_permalink() ),
							(($class_name) ? ' ' . esc_attr( $class_name ) : ''),
							esc_attr( $form_id ),
							(($measurements_form_enabled) ? 'true' : 'false'),
							(($measurements_form_enabled && WE_LS_MEASUREMENTS_MANDATORY) ? 'true' : 'false'),
							(($target_form) ? 'true' : 'false'),
							esc_attr (ws_ls_get_chosen_weight_unit_as_string() ),
							(($target_form) ? 'true' : 'false'),
							esc_attr($user_id),
							esc_attr( wp_hash($user_id) ),
							( true === $photo_form_enabled) ? ' enctype="multipart/form-data"' : '',
							(($photo_form_enabled) ? 'true' : 'false')
	);

	// Do we have data? If so, embed existing row ID
	if(!empty($existing_data['db_row_id']) && is_numeric($existing_data['db_row_id'])) {
        $entry_id = intval( $existing_data['db_row_id'] );
		$html_output .= '<input type="hidden" value="' . $entry_id . '" id="db_row_id" name="db_row_id" />';
	}
 
	// Redirect form afterwards?
	if($redirect_url) {
		$html_output .= '<input type="hidden" value="' . esc_url($redirect_url) . '" id="ws_redirect" name="ws_redirect" />';
	}

	if($form_number){
			$html_output .= '	<input type="hidden" value="' . esc_attr($form_number) . '" id="ws_ls_form_number" name="ws_ls_form_number" />';
	}

	$html_output .= '<div class="ws-ls-inner-form comment-input">
		
	';

	// If not a target form include date
	if (!$target_form) {

		$default_date = date("d/m/Y");

		// Do we have an existing value?
		if($existing_date = ws_ls_get_existing_value($existing_data, 'date-display')) {
			$default_date = $existing_date;
		} else if (ws_ls_get_config('WE_LS_US_DATE')) { // Override if US
			$default_date = date("m/d/Y");
		}

		if(false == $force_to_todays_date){
			$html_output .= '<input type="text" name="we-ls-date" tabindex="' . ws_ls_get_next_tab_index() . '" id="we-ls-date-' . esc_attr($form_id) . '" value="' . esc_attr($default_date) . '" placeholder="' . esc_attr($default_date) . '" size="22" class="we-ls-datepicker">';
		} else {
			$html_output .= '<input type="hidden" name="we-ls-date" value="' . esc_attr($default_date) . '">';
		}

	} else {

		$target_weight = ws_ls_get_user_target($user_id);

		if ($target_weight['display'] != '') {

			$pre_text = (false === is_admin()) ? __('Your target weight is', WE_LS_SLUG) : __('The user\'s target weight is currently', WE_LS_SLUG);

			$html_output .= '<p>' . esc_html( $pre_text ) . ' <strong>' . esc_html( $target_weight['display'] ) . '</strong>.</p>';
		}
	}

	// Display the relevant weight fields depending on weight unit selected
	if(ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS'))
	{
		if (ws_ls_get_config('WE_LS_DATA_UNITS') == 'stones_pounds') {
			$html_output .= '<input  type="number"  tabindex="' . ws_ls_get_next_tab_index() . '" step="any" min="0" name="we-ls-weight-stones" id="we-ls-weight-stones" value="' . ws_ls_get_existing_value($existing_data, 'stones') . '" placeholder="' . __('Stones', WE_LS_SLUG) . '" size="11" >';
			$html_output .= '<input  type="number" tabindex="' . ws_ls_get_next_tab_index() . '" step="any" min="0" max="13.99" name="we-ls-weight-pounds" id="we-ls-weight-pounds" value="' . ws_ls_get_existing_value($existing_data, 'pounds') . '" placeholder="' . __('Pounds', WE_LS_SLUG) . '" size="11"  >';
		}
		else {
			$html_output .= '<input  type="number" tabindex="' . ws_ls_get_next_tab_index() . '" step="any" min="1" name="we-ls-weight-pounds" id="we-ls-weight-pounds" value="' . ws_ls_get_existing_value($existing_data, 'only_pounds') . '" placeholder="' . __('Pounds', WE_LS_SLUG) . '" size="11"  >';
		}
	}
	else {
		$html_output .= '<input  type="number" tabindex="' . ws_ls_get_next_tab_index() . '" step="any" min="1" name="we-ls-weight-kg" id="we-ls-weight-kg" value="' . ws_ls_get_existing_value($existing_data, 'kg') . '" placeholder="' . __('Weight', WE_LS_SLUG) . ' (' . __('kg', WE_LS_SLUG) . ')" size="22" >';
	}

	$html_output .= '</div>';

	// Display notes section if not target form
	if (false === $target_form) {

		$html_output .= '<div id="comment-textarea">
							<textarea name="we-ls-notes" tabindex="' . ws_ls_get_next_tab_index() . '" id="we-ls-notes" cols="39" rows="4" tabindex="4" class="textarea-comment" placeholder="' . __('Notes', WE_LS_SLUG) . '">' . esc_textarea(ws_ls_get_existing_value($existing_data, 'notes', false)) . '</textarea>
						</div>';
	}

	// Include
	if( false === $target_form && $measurements_form_enabled) {
		$html_output .= sprintf('<h3 class="ws_ls_title">%s (%s)</h3>',
										( false === empty($existing_data) )	? __('Edit measurements', WE_LS_SLUG) : __('Add measurements', WE_LS_SLUG),
								(WE_LS_MEASUREMENTS_MANDATORY) ? __('Mandatory', WE_LS_SLUG) : __('Optional', WE_LS_SLUG));
		$html_output .= ws_ls_load_measurement_form($existing_data);
	}

	// Render Meta Fields
    if ( false === $target_form && true === $meta_field_form_enabled ) {
	    $html_output .= ws_ls_meta_fields_form( $entry_id );
    }

	$button_text = ($target_form) ?  __('Set Target', WE_LS_SLUG) :  __('Save Entry', WE_LS_SLUG);

	$html_output .= '<div class="ws-ls-form-buttons">
						<div>
						    <div class="ws-ls-error-summary">
						        <p>' . __('Please correct the following:', WE_LS_SLUG) . '</p>
                                <ul></ul>
                            </div>
                            <div class="ws-ls-form-processing-throbber ws-ls-loading ws-ls-hide"></div>
							<input name="submit_button" type="submit" id="we-ls-submit"  tabindex="' . ws_ls_get_next_tab_index() . '" value="' . $button_text . '" class="comment-submit button ws-ls-remove-on-submit" />';

							// If we want a cancel button then add one
							if ( false === empty( $cancel_button ) && false === $target_form && false === empty( $redirect_url ) ) {
								$html_output .= '&nbsp;<button id="ws-ls-cancel" type="button" tabindex="' . ws_ls_get_next_tab_index() . '" class="ws-ls-cancel-form button ws-ls-remove-on-submit" data-form-id="' . esc_attr($form_id) . '" >' . __('Cancel', WE_LS_SLUG) . '</button>';
							}

							//If a target form, display "Clear Target" button
							if ($target_form && false === is_admin()){
								$html_output .= '&nbsp;<button name="ws-ls-clear-target" id="ws-ls-clear-target" type="button" tabindex="' . ws_ls_get_next_tab_index() . '" class="ws-ls-clear-target button ws-ls-remove-on-submit" >' . __('Clear Target', WE_LS_SLUG) . '</button>';
							}
	$html_output .= '	</div>
					</div>
	</form>';

	return $html_output;
}

function ws_ls_get_existing_value($data, $key, $esc_attr = true) {

	if(false === empty($data[$key])) {
		return ($esc_attr) ? esc_attr($data[$key]) : $data[$key];
	}

	return '';
}


function ws_ls_convert_date_to_iso($date, $user_id = false)
{
    if (ws_ls_get_config('WE_LS_US_DATE', $user_id)) {
		list($month,$day,$year) = sscanf($date, "%d/%d/%d");
		$date = "$year-$month-$day";
	} else {
		list($day,$month,$year) = sscanf($date, "%d/%d/%d");
		$date = "$year-$month-$day";
	}

	return $date;
}

function ws_ls_capture_form_validate_and_save($user_id = false)
{
	if(false == $user_id){
		$user_id = get_current_user_id();
	}

    $meta_fields_enabled = ( true === ws_ls_meta_fields_is_enabled() && ws_ls_meta_fields_number_of_enabled() > 0 );

	$allowed_post_keys = array('ws_ls_is_target', 'we-ls-date', 'we-ls-weight-pounds',
								'we-ls-weight-stones', 'we-ls-weight-kg', 'we-ls-notes' );

 	if ( true === $meta_fields_enabled ) {
        $allowed_post_keys = array_merge( $allowed_post_keys, ws_ls_meta_fields_form_field_ids() );
    }

	$weight_keys = false;

	// Strip slashes from post object
	$form_values = stripslashes_deep($_POST);

	// Target form?
	$is_target_form = ('true' == $form_values['ws_ls_is_target']) ? true : false;

	// If measurements enabled and PRO add enabled fields to the above list
	if (WE_LS_MEASUREMENTS_ENABLED && !$is_target_form) {
		$weight_keys = ws_ls_get_keys_for_active_measurement_fields('ws-ls-');
		$allowed_post_keys = array_merge($allowed_post_keys, $weight_keys);
	}

	// Remove invalid post keys
	foreach ($form_values as $key => $value) {
		if(!in_array($key, $allowed_post_keys)) {
			unset($form_values[$key]);
		}	elseif ('we-ls-date' == $key)	{
			// Convert date to ISO
			$form_values[$key] = ws_ls_convert_date_to_iso($form_values[$key]);
		}
	}

	$weight_object = false;
	$weight_notes = (!$is_target_form) ? $form_values['we-ls-notes'] : '' ;
	$weight_date = (!$is_target_form) ? $form_values['we-ls-date'] : false ;
	$measurements = array();

	// Build measurement fields up and convert to CM if needed
	if (WE_LS_MEASUREMENTS_ENABLED && is_array($weight_keys) && !empty($weight_keys) && !$is_target_form) {
		foreach ($weight_keys as $key) {
			if(array_key_exists($key, $form_values)) {
				// Convert to CM?
				if('cm' != ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT')) {
					$measurements[$key] = ws_ls_convert_to_cm(0, $form_values[$key]);
				} elseif (isset($form_values[$key])) {
					$measurements[$key] = round($form_values[$key], 2);
				}
			}
		}
		unset($measurements['ws-ls-height']);	// Remove height key from this form save
	}

	switch (ws_ls_get_config('WE_LS_DATA_UNITS')) {
		case 'pounds_only':
				$weight_object = ws_ls_weight_object($user_id, 0, 0, 0, $form_values['we-ls-weight-pounds'], $weight_notes,	$weight_date, true, false, '', $measurements);
			break;
		case 'kg':
				$weight_object = ws_ls_weight_object($user_id, $form_values['we-ls-weight-kg'], 0, 0, 0, $weight_notes,	$weight_date, true, false, '', $measurements);
			break;
		default:
				$weight_object = ws_ls_weight_object($user_id, 0, $form_values['we-ls-weight-pounds'], $form_values['we-ls-weight-stones'], 0, $weight_notes, $weight_date, true, false, '', $measurements);
			break;
	}

	// Do we have a row ID embedded in the form (i.e. are we in admin and editing an entry)?
	$existing_db_id = (false === empty($_POST['db_row_id'])) ? intval($_POST['db_row_id']) : false;

    // ---------------------------------------------
    // Process Meta Fields
    // ---------------------------------------------

    if ( true === ws_ls_meta_fields_is_enabled() && ws_ls_meta_fields_number_of_enabled() > 0 ) {

	    // Loop through each enabled meta field. If the field exists in the $_POST object then update the database.
        foreach ( ws_ls_meta_fields_enabled() as $field ) {

            $field_key = ws_ls_meta_fields_form_field_generate_id( $field['id'] );

            // If photo, we need to process the upload
            if ( true === WS_LS_IS_PRO && 3 === intval( $field[ 'field_type' ] ) ) {

            	$photo_upload = ws_ls_meta_fields_photos_process_upload( $field_key, $weight_object[ 'date-display' ] , $user_id, $existing_db_id, $field['id'] );

                if ( false === empty( $photo_upload ) ) {
                    $weight_object[ 'meta-keys' ][ $field['id'] ] = $photo_upload;
                }

            } else if ( true === isset( $form_values[  $field_key ] ) ) {

                $weight_object[ 'meta-keys' ][ $field['id'] ] = $form_values[ $field_key ];

            }
        }

    }

	$result = ws_ls_save_data($user_id, $weight_object, $is_target_form, $existing_db_id);

    return $result;
}

function ws_ls_validate_weight_data($weight_object, $is_target_form = false) {
    if(is_numeric($weight_object['only_pounds']) &&
        is_numeric($weight_object['kg']) &&
          is_numeric($weight_object['stones']) &&
            is_numeric($weight_object['pounds']))
            {
              return true;
            }
		return false;
}

function ws_ls_get_chosen_weight_unit_as_string() {

	$use_imperial_weights = ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS');

	if($use_imperial_weights && 'stones_pounds' == ws_ls_get_config('WE_LS_DATA_UNITS'))	{
		return 'imperial-both';
	} elseif ($use_imperial_weights && 'pounds_only' == ws_ls_get_config('WE_LS_DATA_UNITS'))	{
		return 'imperial-pounds';
	} else {
		 return 'metric';
	}
}

function ws_ls_get_js_config() {

	$message_for_pounds = (ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS') && 'stones_pounds' == ws_ls_get_config('WE_LS_DATA_UNITS')) ? __('Please enter a value between 0-13 for pounds', WE_LS_SLUG) : __('Please enter a valid figure for pounds', WE_LS_SLUG);

	$use_us_date = ws_ls_get_config('WE_LS_US_DATE');

	$config = array (
		'us-date' => ($use_us_date) ? 'true' : 'false',
		'date-format' => ($use_us_date) ? 'mm/dd/yy' : 'dd/mm/yy',
    	'clear-target' => __('Are you sure you wish to clear your target weight?', WE_LS_SLUG),
		'validation-about-you-mandatory' => (WE_LS_ABOUT_YOU_MANDATORY) ? 'true' : 'false',
		'validation-we-ls-weight-pounds' => $message_for_pounds,
		'validation-we-ls-weight-kg' => __('Please enter a valid figure for Kg', WE_LS_SLUG),
		'validation-we-ls-weight-stones' => __('Please enter a valid figure for Stones', WE_LS_SLUG),
		'validation-we-ls-date' => __('Please enter a valid date', WE_LS_SLUG),
		'validation-we-ls-history' => __('Please confirm you wish to delete ALL your weight history', WE_LS_SLUG),
		'validation-we-ls-photo' => __('Your photo must be less than ', WE_LS_SLUG) . ws_ls_photo_display_max_upload_size(),
    	'confirmation-delete' => __('Are you sure you wish to delete this entry? If so, press OK.', WE_LS_SLUG),
		'ajax-url' => admin_url('admin-ajax.php'),
		'ajax-security-nonce' => wp_create_nonce( 'ws-ls-nonce' ),
		'is-pro' => (WS_LS_IS_PRO) ? 'true' : 'false',
		'user-id' => get_current_user_id(),
		'current-url' => get_permalink(),
		'measurements-enabled' => ( WE_LS_MEASUREMENTS_ENABLED && true === ws_ls_any_active_measurement_fields() ) ? 'true' : 'false',
		'photos-enabled' => ( ws_ls_meta_fields_photo_any_enabled( true ) ) ? 'true' : 'false',
		'measurements-unit' => ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT'),
		'validation-we-ls-measurements' => __('Please enter a valid measurement (' . WE_LS_MEASUREMENTS_UNIT . ') which is less that 1000.', WE_LS_SLUG),
		'date-picker-locale' => ws_ls_get_js_datapicker_locale(),
		'in-admin' => (is_admin()) ? 'true' : 'false',
		'max-photo-upload' => ws_ls_photo_max_upload_size(),


	);

	// If About You fields mandatory, add extra translations
	if( WE_LS_ABOUT_YOU_MANDATORY ) {

	    $config['validation-user-pref-messages'] = [
            'we-ls-height' => __('Please select or enter a value for height.', WE_LS_SLUG),
            'ws-ls-activity-level' => __('Please select or enter a value for activity level.', WE_LS_SLUG),
            'ws-ls-gender' => __('Please select or enter a value for gender.', WE_LS_SLUG),
            'we-ls-dob' => __('Please enter a valid date.', WE_LS_SLUG),
            'ws-ls-aim' => __('Please select your aim.', WE_LS_SLUG)
        ];

        $config['validation-user-pref-rules'] = [
            'ws-ls-gender' => ['required' => true, 'min' => 1],
            'we-ls-height' => ['required' => true, 'min' => 1],
            'ws-ls-aim' => ['required' => true, 'min' => 1],
            'ws-ls-activity-level' => ['required' => true, 'min' => 1]
        ];

        $config['validation-required'] = __('This field is required.', WE_LS_SLUG);
	}

	// Allow others to filter config object
    return apply_filters(WE_LS_FILTER_JS_WS_LS_CONFIG, $config);

}

/*
	Use a combination of WP Locale and MO file to translate datepicker
	Based on: https://gist.github.com/clubdeuce/4053820
 */
function ws_ls_get_js_datapicker_locale()
{
	global $wp_locale;

	return array(
	        'closeText'         => __( 'Done', WE_LS_SLUG ),
	        'currentText'       => __( 'Today', WE_LS_SLUG ),
	        'monthNames'        => ws_ls_strip_array_indices( $wp_locale->month ),
	        'monthNamesShort'   => ws_ls_strip_array_indices( $wp_locale->month_abbrev ),
	        'dayNames'          => ws_ls_strip_array_indices( $wp_locale->weekday ),
	        'dayNamesShort'     => ws_ls_strip_array_indices( $wp_locale->weekday_abbrev ),
	        'dayNamesMin'       => ws_ls_strip_array_indices( $wp_locale->weekday_initial ),
	    	// get the start of week from WP general setting
	        'firstDay'          => get_option( 'start_of_week' ),
	    );
}

function ws_ls_strip_array_indices( $ArrayToStrip ) {
    foreach( $ArrayToStrip as $objArrayItem) {
        $NewArray[] =  $objArrayItem;
    }

    return( $NewArray );
}

function ws_ls_get_next_tab_index() {
	global $ws_ls_tab_index;

	$current_index = $ws_ls_tab_index;
	$ws_ls_tab_index++;

	return $current_index;
}
