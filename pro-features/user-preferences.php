<?php
defined('ABSPATH') or die("Jog on!");

function ws_ls_user_preferences_form($user_id = false)
{
    $html_output = '';

    // Have user preferences been allowed in Settings?
    if (false === WE_LS_ALLOW_USER_PREFERENCES && false === is_admin()) {
        return $html_output;
    }

    $user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

    // Decide which set of labels to render
	$labels = [
                'title-about' => __('About You:', WE_LS_SLUG),
				'height' => __('Your height:', WE_LS_SLUG),
				'weight' => __('Which unit would you like to record your weight in:', WE_LS_SLUG),
				'measurements' => __('Which unit would you like to record your measurements in:', WE_LS_SLUG),
				'date' => __('Display dates in the following formats:', WE_LS_SLUG),
                'gender' => __('Your Gender (needed for BMR):', WE_LS_SLUG),
                'dob' => __('Your Date of Birth (needed for BMR):', WE_LS_SLUG),
                'activitylevel' => __('Your Activity Level (needed for BMR):', WE_LS_SLUG)
	];

	// If admin, add notice and override labels
	if(is_admin()) {
		$html_output .= '<div class="notice ws-ls-hide" id="ws-ls-notice"><p></p></div>';

		$labels = [ 'title-about' => __('About User:', WE_LS_SLUG),
					'height' => __('Height:', WE_LS_SLUG),
					'weight' => __('Weight unit:', WE_LS_SLUG),
					'measurements' => __('Measurements unit:', WE_LS_SLUG),
					'date' => __('Date format:', WE_LS_SLUG),
                    'gender' => __('Gender (needed for BMR):', WE_LS_SLUG),
                    'dob' => __('Date of Birth (needed for BMR):', WE_LS_SLUG),
                    'activitylevel' => __('Activity Level (needed for BMR):', WE_LS_SLUG)
		];
	} else {
	    // Enqueue front end scripts if needed (mainly for datepicker)
        ws_ls_enqueue_files();
    }

    $html_output = ws_ls_title($labels['title-about']);

	$html_output .= '

	<form action="' .  get_permalink() . '" class="ws-ls-user-pref-form" method="post">
  	<input type="hidden" name="ws-ls-user-pref" value="true" />
	<input type="hidden" id="ws-ls-user-id" value="' . (($user_id) ? esc_attr($user_id) : '0')  . '" />
	<input type="hidden" name="ws-ls-user-pref-redirect" value="' . get_the_ID() . '" />';

	// If BMI enabled, record allow height to be soecified
	if(WE_LS_DISPLAY_BMI_IN_TABLES) {

		$html_output .= '
		<label>' . $labels['height'] . '</label>
		<select id="we-ls-height" name="we-ls-height"  tabindex="' . ws_ls_get_next_tab_index() . '">';
		$heights = ws_ls_heights();
		$existing_height = ws_ls_get_user_height($user_id, false);

		foreach ($heights as $key => $value) {
		    $html_output .= sprintf('<option value="%s" %s>%s</option>', $key, selected($key, $existing_height, false), $value);
		}

		$html_output .= '</select>';

	}

	//-------------------------------------------------------
    // Gender
    //-------------------------------------------------------
    $html_output .= '
		<label>' . $labels['gender'] . '</label>
		<select id="ws-ls-gender" name="ws-ls-gender"  tabindex="' . ws_ls_get_next_tab_index() . '">';

        $existing_gender = ws_ls_get_user_setting('gender', $user_id);
        $existing_gender = (true === empty($existing_gender)) ? '0' : $existing_gender;

        foreach (ws_ls_genders() as $key => $value) {
            $html_output .= sprintf('<option value="%s" %s>%s</option>', $key, selected($key, $existing_gender, false), $value);
        }

    $html_output .= '</select>';

    //-------------------------------------------------------
    // Activity Level
    //-------------------------------------------------------
    $html_output .= '
		<label>' . $labels['activitylevel'] . '</label>
		<select id="ws-ls-activity-level" name="ws-ls-activity-level"  tabindex="' . ws_ls_get_next_tab_index() . '">';

    $activity_level = ws_ls_get_user_setting('activity_level', $user_id);
    $activity_level = (true === empty($activity_level)) ? '0' : $activity_level;

    foreach (ws_ls_activity_levels() as $key => $value) {
        $html_output .= sprintf('<option value="%s" %s>%s</option>', $key, selected($key, $activity_level, false), $value);
    }

    $html_output .= '</select>';

    //-------------------------------------------------------
    // Date of Birth
    //-------------------------------------------------------

    $dob = ws_ls_get_dob_for_display($user_id);

    $html_output .= '<label>' . $labels['dob'] . '</label>
                    <input type="text" name="ws-ls-dob" tabindex="' . ws_ls_get_next_tab_index() . '" id="ws-ls-dob" value="' . ws_ls_get_dob_for_display($user_id) . '" size="22" class="we-ls-datepicker">
                    ';

    //-------------------------------------------------------
    // Preferences
    //-------------------------------------------------------

    $html_output .= ws_ls_title(__('Preferences', WE_LS_SLUG));

  	$html_output .= '
	<label>' . $labels['weight'] . '</label>
    <select id="WE_LS_DATA_UNITS" name="WE_LS_DATA_UNITS"  tabindex="' . ws_ls_get_next_tab_index() . '">
      <option value="kg" ' . selected( ws_ls_get_config('WE_LS_DATA_UNITS', $user_id), 'kg', false ) . '>' . __('Kg', WE_LS_SLUG) . '</option>
      <option value="stones_pounds" ' . selected( ws_ls_get_config('WE_LS_DATA_UNITS', $user_id), 'stones_pounds', false ) . '>' . __('Stones & Pounds', WE_LS_SLUG) . '</option>
      <option value="pounds_only" ' . selected( ws_ls_get_config('WE_LS_DATA_UNITS', $user_id), 'pounds_only', false ) . '>' . __('Pounds', WE_LS_SLUG) . '</option>
    </select>';

	if(WE_LS_MEASUREMENTS_ENABLED) {
		$html_output .= '
			<label>' . $labels['measurements'] . '</label>
		    <select id="WE_LS_MEASUREMENTS_UNIT" name="WE_LS_MEASUREMENTS_UNIT"  tabindex="' . ws_ls_get_next_tab_index() . '">
		    	<option value="cm" ' . selected( ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT', $user_id), 'cm', false ) . '>' . __('Centimetres', WE_LS_SLUG) . '</option>
		    	<option value="inches" ' . selected( ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT', $user_id), 'inches', false ) . '>' . __('Inches', WE_LS_SLUG) . '</option>
		    </select>
		';
	}

    $html_output .= '

	<label>' . $labels['date'] . '</label>
    <select id="WE_LS_US_DATE" name="WE_LS_US_DATE"  tabindex="' . ws_ls_get_next_tab_index() . '">
      <option value="false" ' . selected( ws_ls_get_config('WE_LS_US_DATE', $user_id), false, false ) . '>' . __('UK (DD/MM/YYYY)', WE_LS_SLUG) . '</option>
      <option value="true" ' . selected( ws_ls_get_config('WE_LS_US_DATE', $user_id), true, false ) . '>' . __('US (MM/DD/YYYY)', WE_LS_SLUG) . '</option>
    </select>
  <input name="submit_button" type="submit" id="we-ls-user-pref-submit"  tabindex="' . ws_ls_get_next_tab_index() . '" value="' .  __('Save Settings', WE_LS_SLUG) . '" class="comment-submit btn btn-default button default small fusion-button button-small button-default button-round button-flat">
</form><br />';

// Hide delete data form if on the admin screen
if(false === is_admin()) {

	$html_output .= ws_ls_title(__('Delete existing data', WE_LS_SLUG)) . '
		<form action="' .  get_permalink() . '?user-delete-all=true" class="ws-ls-user-delete-all" method="post">
		<div class="ws-ls-error-summary">
			<ul></ul>
		</div>
			<input type="hidden" name="ws-ls-user-delete-all" value="true" />
			<label for="ws-ls-delete-all">' . __('The button below allows you to clear your existing weight history. Confirm:', WE_LS_SLUG) . '</label>
			<select id="ws-ls-delete-all" name="ws-ls-delete-all"  tabindex="' . ws_ls_get_next_tab_index() . '" required>
				<option value=""></option>
				<option value="true">' . __('DELETE ALL DATA', WE_LS_SLUG) . '</option>
			</select>
			<input name="submit_button" type="submit" tabindex="' . ws_ls_get_next_tab_index() . '" value="' .  __('Delete', WE_LS_SLUG) . '" class="comment-submit btn btn-default button default small fusion-button button-small button-default button-round button-flat">
		</form>';
}

	return $html_output;
}
