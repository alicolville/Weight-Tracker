<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Display data in basic HTML table
 * @param $user_id
 * @param $weight_data
 *
 * @return string|null
 */
function ws_ls_display_table( $user_id, $weight_data ) {

	if ( true === empty( $weight_data ) ) {
		return '';
	}

	$cache_key = ws_ls_cache_generate_key_from_array( 'data-table', $weight_data );

	if ( $cache = ws_ls_cache_user_get( $user_id, $cache_key ) ) {
		return $cache;
	}

	$output = sprintf( '<table width="100%" class="ws-ls-data-table">
						  <thead>
						  <tr>
							<th width="25%%">%1$s</th>
							<th width="25%%">%2$s</th>
							<th>%3$s</th>
						  </tr>
						  </thead>
						<tbody>',
						__( 'Date', WE_LS_SLUG ),
						sprintf( '%s (%s)', __( 'Weight', WE_LS_SLUG ), ws_ls_get_unit() ),
						__( 'Notes', WE_LS_SLUG )
	);

	foreach ( $weight_data as $weight_object ) {

		$output .= sprintf( '<tr>
											  <td>%1$s</td>
											  <td>%2$s</td>
											  <td>%3$s</td>
											</tr>',
											esc_html( $weight_object['display-date'] ),
											esc_html( $weight_object['display'] ),
											esc_html( $weight_object['notes'] )
		);
	}

	$output .= '<tbody></table>';

	ws_ls_cache_user_set_and_return( $user_id, $cache_key, $output );

	return $output;
}

function ws_ls_get_existing_value($data, $key, $esc_attr = true) {

	if(true === isset($data[$key])) {
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
									__( 'Please enter a value between 0-13.99 for pounds', WE_LS_SLUG ) :
										__( 'Please enter a value between 1 and 5000 for pounds', WE_LS_SLUG );

	$use_us_date = ws_ls_get_config('WE_LS_US_DATE');

	$config = array (
		'us-date' => ($use_us_date) ? 'true' : 'false',
		'date-format' => ($use_us_date) ? 'mm/dd/yy' : 'dd/mm/yy',
    	'clear-target' => __('Are you sure you wish to clear your target weight?', WE_LS_SLUG),
		'validation-about-you-mandatory' => ( true === ws_ls_option_to_bool( 'ws-ls-about-you-mandatory', 'no', true ) ) ? 'true' : 'false',
		'validation-we-ls-weight-pounds' => $message_for_pounds,
		'validation-we-ls-weight-kg' => __('Please enter a value between 1 and 5000 for Kg', WE_LS_SLUG),
		'validation-we-ls-weight-stones' => __('Please enter a value between 1 and 5000 for Stones', WE_LS_SLUG),
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
	if( true === ws_ls_option_to_bool( 'ws-ls-about-you-mandatory', 'no', true ) ) {

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
    return apply_filters( 'wlt-filter-js-ws-ls-config', $config);

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

