<?php

	defined('ABSPATH') or die("Jog on!");

function ws_ls_get_measurement_settings()
{
    if(defined('WE_LS_MEASUREMENTS')) {

        $settings = json_decode(WE_LS_MEASUREMENTS, true);

        // Settings specified in admin?
        if (get_option('ws-ls-measurement') != false) {

            $user_defined = get_option('ws-ls-measurement');
            if(isset($user_defined['colors'])) {
                foreach($user_defined['colors'] as $slug => $colour) {
                    $settings[$slug]['chart_colour'] = $colour;
                }
            }
            if(isset($user_defined['enabled'])) {
                foreach($user_defined['enabled'] as $slug => $enabled) {
                    if ('on' == $enabled) {
                        $settings[$slug]['enabled'] = true;
                    } else {
                        $settings[$slug]['enabled'] = false;
                    }
                }
            }
        }

        return $settings;
    }
    return false;
}
function ws_ls_any_active_measurement_fields(){
  $measurement_fields = ws_ls_get_measurement_settings();
  foreach($measurement_fields as $key => $data) {
      if($data['enabled']) {
          return true;
      }
  }
  return false;
}
function ws_ls_get_keys_for_active_measurement_fields($prefix = ''){
  $measurement_fields = ws_ls_get_measurement_settings();
	$keys = [];
  foreach($measurement_fields as $key => $data) {
      if($data['enabled']) {
          $keys[] = $prefix . $key;
      }
  }
  return $keys;
}

function ws_ls_load_measurement_form()
{
    $public_html = '';
    $measurement_fields = ws_ls_get_measurement_settings();

	  foreach($measurement_fields as $key => $data) {
        if($data['enabled'] && false == $data['user_preference']) {
            $public_html .= ws_ls_measurement_field($key, $data['title'] . ' (' . WE_LS_MEASUREMENTS_UNIT . ')');
        }
    }

    return $public_html;
}

function ws_ls_measurement_field($field_id, $display_text, $value = false)
{
		$value = ($value) ? $value : '';
		$field_id = 'ws-ls-' . $field_id;

    $html_output = '<label for="' . $field_id . '">' . $display_text . ':</label>';
    $html_output .= '<input  type="number"' . ((WE_LS_MEASUREMENTS_MANDATORY) ? ' required' : '' ) . ' tabindex="' . ws_ls_get_next_tab_index() . '" step="any" min="0" max="1000" name="' . $field_id . '" id="' . $field_id . '" value="' . $value . '" size="11" class="ws-ls-measurement ws-ls-measurement-required"  />';
    return $html_output;
}
