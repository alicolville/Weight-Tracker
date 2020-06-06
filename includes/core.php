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


/*
	Displays either a target or weight form
*/
function ws_ls_display_weight_form($target_form = false, $class_name = false, $user_id = false, $hide_titles = false,
                                        $form_number = false, $force_to_todays_date = false, $hide_login_message_if_needed = true,
                                            $hide_measurements_form = false, $redirect_url = false, $existing_data = false, $cancel_button = false,
                                                $hide_photos_form = false, $hide_meta_fields_form = false ) {
    global $save_response;
    $html_output  = '';

    $photo_form_enabled = ( false === $hide_photos_form && true === ws_ls_meta_fields_photo_any_enabled( true ) && false === $target_form);
    $meta_field_form_enabled = ( false === $hide_meta_fields_form && true === ws_ls_meta_fields_is_enabled() && ws_ls_meta_fields_number_of_enabled() > 0 && false === $target_form);
    $entry_id = NULL;

    // Make sure they are logged in
    if (!is_user_logged_in())	{
        if ($hide_login_message_if_needed) {

            $prompt = ( true === $target_form ) ? __('You need to be logged in to set your target.', WE_LS_SLUG) : __('You need to be logged in to record your weight.', WE_LS_SLUG);

            return ws_ls_display_blockquote($prompt, '', false, true);
        } else {
            return '';
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

	$post_url = apply_filters( 'wlt_form_url', get_permalink() );

	$html_output .= sprintf('
							<form action="%1$s" method="post" class="we-ls-weight-form we-ls-weight-form-validate ws_ls_display_form%2$s" id="%3$s"
							data-is-target-form="%4$s"
							data-metric-unit="%5$s",
							data-photos-enabled="%9$s",
							%8$s
							>
							<input type="hidden" value="%4$s" id="ws_ls_is_target" name="ws_ls_is_target" />
							<input type="hidden" value="true" id="ws_ls_is_weight_form" name="ws_ls_is_weight_form" />
							<input type="hidden" value="%6$s" id="ws_ls_user_id" name="ws_ls_user_id" />
							<input type="hidden" value="%7$s" id="ws_ls_security" name="ws_ls_security" />',
							esc_url( $post_url ),
							(($class_name) ? ' ' . esc_attr( $class_name ) : ''),
							esc_attr( $form_id ),
							( ( true === $target_form ) ? 'true' : 'false'),
							esc_attr (ws_ls_get_chosen_weight_unit_as_string( $user_id ) ),
							esc_attr($user_id),
							esc_attr( wp_hash($user_id) ),
							( true === $photo_form_enabled) ? ' enctype="multipart/form-data"' : '',
							(($photo_form_enabled) ? 'true' : 'false')
	);

	// Do we have data? If so, embed existing row ID
	if(!empty($existing_data['db_row_id']) && is_numeric($existing_data['db_row_id'])) {
        $entry_id = (int) $existing_data['db_row_id'];
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
	if( ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS', $user_id ) )
	{
		if ( 'stones_pounds' === ws_ls_get_config('WE_LS_DATA_UNITS' , $user_id ) ) {
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

	// Render Meta Fields
    if ( false === $target_form && true === $meta_field_form_enabled ) {
	    $html_output .= ws_ls_meta_fields_form( $entry_id );
    }

	$button_text = ($target_form) ?  __('Set Target', WE_LS_SLUG) :  __('Save Entry', WE_LS_SLUG);

	$html_output .= '<div class="ws-ls-form-buttons">
						<div>
						    <div class="ws-ls-error-summary ws-ls-hide-if-admin">
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


function ws_ls_convert_date_to_iso($date, $user_id = false) {

    if ( true === empty( $date ) ) {
        return NULL;
    }

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
	if( false == $user_id){
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

	switch ( ws_ls_get_config('WE_LS_DATA_UNITS', $user_id ) ) {
		case 'pounds_only':
				$weight_object = ws_ls_weight_object($user_id, 0, 0, 0, $form_values['we-ls-weight-pounds'], $weight_notes,	$weight_date, true, false, '');
			break;
		case 'kg':
				$weight_object = ws_ls_weight_object($user_id, $form_values['we-ls-weight-kg'], 0, 0, 0, $weight_notes,	$weight_date, true, false, '');
			break;
		default:
				$weight_object = ws_ls_weight_object($user_id, 0, $form_values['we-ls-weight-pounds'], $form_values['we-ls-weight-stones'], 0, $weight_notes, $weight_date, true, false, '');
			break;
	}

	// Do we have a row ID embedded in the form (i.e. are we in admin and editing an entry)?
	$existing_db_id = (false === empty($_POST['db_row_id'])) ? (int) $_POST['db_row_id'] : false;

    // ---------------------------------------------
    // Process Meta Fields
    // ---------------------------------------------

    if ( false === $is_target_form &&
			true === ws_ls_meta_fields_is_enabled() &&
				ws_ls_meta_fields_number_of_enabled() > 0 ) {

	    // Loop through each enabled meta field. If the field exists in the $_POST object then update the database.
        foreach ( ws_ls_meta_fields_enabled() as $field ) {

            $field_key = ws_ls_meta_fields_form_field_generate_id( $field['id'] );

            // If photo, we need to process the upload
            if ( true === WS_LS_IS_PRO && 3 === (int) $field[ 'field_type' ] ) {

            	$photo_upload = ws_ls_meta_fields_photos_process_upload( $field_key, $weight_object[ 'date-display' ] , $user_id, $existing_db_id, $field['id'] );

                if ( false === empty( $photo_upload ) ) {
                    $weight_object[ 'meta-keys' ][ $field['id'] ] = $photo_upload;
                }

            } else if ( true === isset( $form_values[  $field_key ] ) ) {

                $weight_object[ 'meta-keys' ][ $field['id'] ] = $form_values[ $field_key ];

            }
        }

    }

    // If nothing was entered for a target weight, then delete existing.
    if ( true === $is_target_form && true === empty( $weight_object['kg'] ) ) {
        ws_ls_delete_target( $user_id );
    }

	$result = ws_ls_save_data( $user_id, $weight_object, $is_target_form, $existing_db_id );

    return $result;
}

function ws_ls_validate_weight_data( $weight_object ) {
    if(is_numeric($weight_object['only_pounds']) &&
        is_numeric($weight_object['kg']) &&
          is_numeric($weight_object['stones']) &&
            is_numeric($weight_object['pounds']))
            {
              return true;
            }
		return false;
}

function ws_ls_get_chosen_weight_unit_as_string( $user_id = NULL ) {

	$user_id = ( null !== $user_id ) ? (int) $user_id : get_current_user_id();

	$use_imperial_weights = ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS', $user_id );

	$data_unit =  ws_ls_get_config('WE_LS_DATA_UNITS', $user_id );

	if( $use_imperial_weights && 'stones_pounds' == $data_unit )	{
		return 'imperial-both';
	} elseif ( $use_imperial_weights && 'pounds_only' == $data_unit )	{
		return 'imperial-pounds';
	} else {
		 return 'metric';
	}
}

function ws_ls_get_js_config() {

	$user_id = get_current_user_id();
	$user_id = apply_filters( 'wlt-filter-js-ws-ls-config-user-id', $user_id );

	$message_for_pounds = ( ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS', $user_id )
								&& 'stones_pounds' == ws_ls_get_config('WE_LS_DATA_UNITS', $user_id ) ) ?
									__( 'Please enter a value between 0-13 for pounds', WE_LS_SLUG ) :
										__( 'Please enter a valid figure for pounds', WE_LS_SLUG );

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
		'is-pro' => ( WS_LS_IS_PRO ) ? 'true' : 'false',
		'user-id' => $user_id,
		'current-url' => apply_filters( 'wlt_current_url', get_permalink() ),
		'photos-enabled' => ( ws_ls_meta_fields_photo_any_enabled( true ) ) ? 'true' : 'false',
		'date-picker-locale' => ws_ls_get_js_datapicker_locale(),
		'in-admin' => ( is_admin() ) ? 'true' : 'false',
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
