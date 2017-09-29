<?php

defined('ABSPATH') or die("Jog on");

/**
 * Render IF shortcode
 *
 * @param $user_defined_arguments
 * @param null $content - content between WP tags
 * @param $level - used to determine level of IF nesting
 * @return string
 */
function ws_ls_shortcode_if($user_defined_arguments, $content = null, $level = 0) {

    // Check if we have content between opening and closing [wlt-if] tags, if we don't then nothing to render so why bother proceeding?
    if(true === empty($content)) {
        return '<p>' . __('To use this shortcode, you must specify content between opening and closing tags<br /><br />e.g. [wlt-if]something to show if IF is true[/wlt-if]', WE_LS_SLUG) . '</p>';
    }

    $arguments = shortcode_atts(array(
        'user-id' => get_current_user_id(),
        'operator' => 'exists',				// exists, not-exists
        'field' => 'weight',				// weight, target, bmr, height, gender, activity-level, dob, is-logged-in, aim
        'strip-p-br' => false
    ), $user_defined_arguments );

    // Validate arguments
    $arguments['user-id'] = ws_ls_force_numeric_argument($arguments['user-id'], get_current_user_id());
    $arguments['operator'] = (true === in_array($arguments['operator'], ['exists', 'not-exists'])) ? $arguments['operator'] : 'exists';
    $arguments['field'] = (true === empty($arguments['field'])) ? 'weight' : $arguments['field'];
    $level = ws_ls_force_numeric_argument($level, '');
    $arguments['strip-p-br'] = ws_ls_force_bool_argument($arguments['strip-p-br']);

    // Strip out BR / P tags?
    if( true === $arguments['strip-p-br']) {
        $content = ws_ls_shortcode_if_remove_p_br($content);
    }

    // Remove Pro Plus fields if they don't have a license
    if( false === WS_LS_IS_PRO_PLUS && true === in_array($arguments['field'], ['bmr'])) {
        return '<p>' . __('Unfortunately the field you specified is for Pro Plus licenses only.', WE_LS_SLUG) . '</p>';
    }

    $else_content = '';

    $else_tag = ($level > 0) ? '[else-' . $level . ']' : '[else]';

    // Is there an [else] within the content? If so, split the content into true condition and else.
    $else_location = stripos($content, $else_tag);

    if(false !== $else_location) {

        $else_content = substr($content, $else_location + strlen($else_tag));

        // Strip out [else] content from true condition
        $content = substr($content, 0, $else_location);
    }

    $does_all_values_exist = ws_ls_shortcode_if_value_exist($arguments['user-id'], $arguments['field']);

    $display_true_condition = 	(
        (true === $does_all_values_exist && 'exists' === $arguments['operator']) ||		// True if field exists
        (false === $does_all_values_exist && 'not-exists' === $arguments['operator'])	// True if field does not exist
    ) ? true : false;

    // If we should display true content, then do so. IF not, and it was specified, display [else]
    if($display_true_condition) {
        return do_shortcode($content);
    } else if (false === $display_true_condition && false === empty($else_content)) {
        return do_shortcode($else_content);
    }

    return '';
}

/**
 * Remove <br> and <p> tags from text
 * @param $text
 * @return mixed
 */
function ws_ls_shortcode_if_remove_p_br($text) {

    if(false === empty($text)) {

        $find = ['<br>', '<br />', '<p>', '</p>'];

        foreach ($find as $value) {
            $text = str_ireplace($value, '', $text);
        }
    }

    return $text;
}

/**
 *
 * Given a shortcode IF field, check it is populated
 *
 * @param $user_id
 * @param $fields
 * @return bool
 */
function ws_ls_shortcode_if_value_exist($user_id, $fields) {

    // If we have a field, try exploding in case it is more than one value!
    if(false === empty($fields) && true === is_numeric($user_id)) {

        if(false === is_array($fields)) {
            $fields = explode(',', $fields);
        }

        // Loop through each field. If any are invalid then return calse
        foreach ($fields as $field) {

            $field = trim($field);

            // Check a valid field
            if(false === ws_ls_shortcode_if_valid_field_name($field)) {
                return false;
            }

            $value = '';

            switch ($field) {
                case 'is-logged-in':
                    $value = is_user_logged_in();
                    break;
                case 'photo':

                    if (false !== WS_LS_IS_PRO_PLUS) {
                        $value = ws_ls_photos_db_get_recent_or_latest($user_id);
                    }

                    break;
                case 'weight':
                    $value = ws_ls_get_recent_weight_in_kg($user_id);
                    break;
                case 'target':
                    $value = ws_ls_get_target_weight_in_kg($user_id);
                    break;
                case 'bmr':
                    $value = ws_ls_calculate_bmr($user_id);
                    $value = (false === is_numeric($value)) ? '' : $value;
                    break;
                case 'height':
                case 'gender':
                case 'activity_level':
                case 'dob':
                case 'aim':
                    $value = ws_ls_get_user_setting($field, $user_id);

                    if ( 'dob' === $field && '0000-00-00 00:00:00' === $value ) {
                        $value = NULL;
                    }

                    break;
            }

            if (true === empty($value)) {
                return false;
            }

        }

        return true;
    }
    return false;
}

/**
 * Validate a IF shortcode field name
 * @param $field
 * @return bool
 */
function ws_ls_shortcode_if_valid_field_name($field) {
    return (true === in_array($field, ['weight', 'target', 'bmr', 'height', 'gender', 'activity_level', 'dob', 'is-logged-in', 'photo', 'aim']));
}

/**
 * Shortcode to allow nesting of [wlt-if]. This is for [wlt-if-1]
 *
 * @param $user_defined_arguments
 * @param null $content
 * @return string
 */
function ws_ls_shortcode_if_level_one($user_defined_arguments, $content = null) {
    return ws_ls_shortcode_if($user_defined_arguments, $content, 1);
}
add_shortcode( 'wlt-if-1', 'ws_ls_shortcode_if_level_one' );

/**
 * Shortcode to allow nesting of [wlt-if]. This is for [wlt-if-2]
 *
 * @param $user_defined_arguments
 * @param null $content
 * @return string
 */
function ws_ls_shortcode_if_level_two($user_defined_arguments, $content = null) {
    return ws_ls_shortcode_if($user_defined_arguments, $content, 2);
}
add_shortcode( 'wlt-if-2', 'ws_ls_shortcode_if_level_two' );

/**
 * Shortcode to allow nesting of [wlt-if]. This is for [wlt-if-3]
 *
 * @param $user_defined_arguments
 * @param null $content
 * @return string
 */
function ws_ls_shortcode_if_level_three($user_defined_arguments, $content = null) {
    return ws_ls_shortcode_if($user_defined_arguments, $content, 3);
}
add_shortcode( 'wlt-if-3', 'ws_ls_shortcode_if_level_three' );
