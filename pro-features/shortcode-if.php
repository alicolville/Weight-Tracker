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
function ws_ls_shortcode_if( $user_defined_arguments, $content, $shortcode, $level = 0 ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

    // Check if we have content between opening and closing [wlt-if] tags, if we don't then nothing to render so why bother proceeding?
    if( true === empty( $content ) ) {
        return sprintf( '<p>%s</p>',  __( 'To use this shortcode, you must specify content between opening and closing tag e.g. [wlt-if]something to show if IF is true[/wlt-if]', WE_LS_SLUG ) );
    }

    $arguments = shortcode_atts( [      'user-id'       => get_current_user_id(),
								        'operator'      => 'exists',				// exists, not-exists
								        'field'         => 'weight',				// weight, target, bmr, height, gender, activity-level, dob, is-logged-in, aim
                                        'value'         => NULL,
								        'strip-p-br'    => false,
                                        'unit'          => 'kg'                     // kg / pounds
    ], $user_defined_arguments );

    // Validate arguments
    $arguments[ 'user-id' ]         = ws_ls_force_numeric_argument( $arguments[ 'user-id' ], get_current_user_id());
    $arguments[ 'operator' ]        = ( true === in_array( $arguments[ 'operator' ], ws_ls_shortcode_if_allows_operators() ) ) ? $arguments[ 'operator' ] : 'exists';
    $arguments[ 'field' ]           = ( true === empty( $arguments[ 'field' ] ) ) ? 'weight' : $arguments[ 'field' ];

    // Strip out BR / P tags?
    if( true === ws_ls_to_bool( $arguments[ 'strip-p-br' ] ) ) {
        $content = ws_ls_shortcode_if_remove_p_br($content);
    }

    // Remove Pro Plus fields if they don't have a license
    if( false === WS_LS_IS_PRO_PLUS && true === ( $arguments['field'] == 'bmr' ) ) {
        return sprintf( '<p>%s</p>', __( 'Unfortunately the field you specified is for Pro Plus licenses only.', WE_LS_SLUG ) );
    }

    $else_content   = '';
    $else_tag       = ( $level > 0 ) ? sprintf('[else-%d]', $level ) : '[else]';

    // Is there an [else] within the content? If so, split the content into true condition and else.
    $else_location = stripos( $content, $else_tag );

    if( false !== $else_location ) {

        $else_content = substr( $content, $else_location + strlen( $else_tag ) );

        // Strip out [else] content from true condition
        $content = substr( $content, 0, $else_location );
    }

    $display_true_condition = false;

    if ( true === in_array( $arguments[ 'operator' ], [ 'exists', 'not-exists' ] ) )  {
        $does_all_values_exist  = ws_ls_shortcode_if_value_exist( $arguments[ 'user-id' ], $arguments[ 'field' ] );
        $display_true_condition = 	(   ( true === $does_all_values_exist && 'exists' === $arguments[ 'operator' ] ) ||		        // True if field exists
                                            ( false === $does_all_values_exist && 'not-exists' === $arguments[ 'operator' ] ) );	// True if field does not exist
    } else {

        if ( true === empty( $arguments[ 'value' ] ) ) {
            return sprintf( '<p>%s</p>', __( 'For comparisons, you must specify a value to compare against.', WE_LS_SLUG ) );
        }

        // comparison logic (i.e. greater-than, less-than, equals)
        $display_true_condition = ws_ls_shortcode_if_comparison( $arguments[ 'field' ], $arguments[ 'user-id' ], $arguments[ 'operator' ], $arguments[ 'value' ],  $arguments[ 'unit' ] );

    }

    // If we should display true content, then do so. IF not, and it was specified, display [else]
    if( true === $display_true_condition ) {
        return do_shortcode( $content );
    } else if ( false === $display_true_condition && false === empty( $else_content ) ) {
        return do_shortcode( $else_content );
    }

    return '';
}
add_shortcode( 'wlt-if', 'ws_ls_shortcode_if' );
add_shortcode( 'wt-if', 'ws_ls_shortcode_if' );


function ws_ls_shortcode_if_comparison( $field, $user_id, $operator, $shortcode_value, $unit = 'kg' ) {
   
    if ( 'pounds' === $unit ) {
        $shortcode_value = ws_ls_convert_pounds_to_kg( $shortcode_value );
    }

    // Fetch the value to compare against
    $db_value           = ws_ls_shortcode_if_comparison_get_value( $field, $user_id );
    $shortcode_value    = (float) $shortcode_value;

    switch ( $operator ) {

        case 'equals':
            return $shortcode_value === $db_value; 
            break;
        case 'greater-than':
            return $db_value > $shortcode_value; 
            break;    
        case 'greater-than-or-equal-to':
            return $db_value >= $shortcode_value; 
            break;
        case 'less-than':
            return $db_value < $shortcode_value; 
            break;    
        case 'less-than-or-equal-to':
            return $db_value <= $shortcode_value;
            break;  
        default:    // invalid operator
            return false;    
    }

    return false;
}

/**
 * Fetch the value we wish to compare against
 * @param $field
 * @param $user_id
 * @param $unit
 * @return array|string|null
 */
function ws_ls_shortcode_if_comparison_get_value( $field, $user_id ) {

    $value = NULL;

    switch( $field ) {
        case 'difference-from-start':
            $latest_entry = ws_ls_entry_get_latest(  [ 'user-id' => $user_id ] );
            $value = ( false === empty( $latest_entry[ 'difference_from_start_kg' ] ) ) ? $latest_entry[ 'difference_from_start_kg' ] : NULL;
            break;
        case 'weight':
            $value = ws_ls_entry_get_latest_kg( $user_id );
            break;
        case 'target':
            $value = ws_ls_target_get( $user_id, 'kg' );
            break;
        case 'previous-weight':
            $value = ws_ls_entry_get_previous( [ 'id' => $user_id ] );
            break; 
        case 'no-days':
            $value = ws_ls_shortcode_days_between_start_and_latest( [ 'user-id' => $user_id ], true );
            break;       
        case 'no-entries':

            $counts = ws_ls_db_entries_count( $user_id );

            $value = ( false === empty( $counts[ 'number-of-weight-entries' ] ) ) ?
                        (int) $counts[ 'number-of-weight-entries' ] :
                            NULL ;
            break; 
    }

    return NULL !== $value ? (float) $value : NULL;
}
/**
 * Return allowed operators for [if] shortcode
 * @return array
 */
function ws_ls_shortcode_if_allows_operators() {
    return [ 'exists', 'not-exists', 'equals', 'greater-than','greater-than-or-equal-to', 'less-than', 'less-than-or-equal-to' ];
}

/**
 * Remove <br> and <p> tags from text
 * @param $text
 * @return mixed
 */
function ws_ls_shortcode_if_remove_p_br($text) {

    if( false === empty( $text ) ) {

        $find = [ '<br>', '<br />', '<p>', '</p>' ];

        foreach ( $find as $value ) {
            $text = str_ireplace( $value, '', $text );
        }
    }

    return $text;
}

/**
 *
 * Given a shortcode IF field, check it is populated (wrap around ws_ls_shortcode_if_value_exist() )
 *
 * @param $user_id
 * @param $fields
 * @return bool
 */
function ws_ls_if( $fields, $user_id = false ) {

	$user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	return ws_ls_shortcode_if_value_exist( $user_id, $fields );
}

/**
 *
 * Given a shortcode IF field, check it is populated
 *
 * @param $user_id
 * @param $fields
 * @return bool
 */
function ws_ls_shortcode_if_value_exist( $user_id, $fields ) {

    // If we have a field, try exploding in case it is more than one value!
    if( false === empty( $fields ) && true === is_numeric( $user_id ) ) {

        if( false === is_array( $fields ) ) {
            $fields = explode( ',', $fields );
        }

        // Loop through each field. If any are invalid then return calse
        foreach ( $fields as $field ) {

            $field = trim( $field );

            // Check a valid field
            if( false === ws_ls_shortcode_if_valid_field_name( $field ) ) {
                return false;
            }

            $value = '';

            switch ( $field ) {
                case 'is-logged-in':
                    $value = is_user_logged_in();
                    break;
	            case 'photo':

                    if (false !== WS_LS_IS_PRO ) {
                        $value = ws_ls_photos_db_get_recent_or_latest( $user_id );
                    }

                    break;
				case 'challenges-opted-in':
						$value = ws_ls_user_preferences_get( 'challenge_opt_in', $user_id );
					break;
                case 'weight':
                    $value = ws_ls_entry_get_latest_kg( $user_id );
                    break;
	            case 'previous-weight':
		            $value = ws_ls_entry_get_previous( [ 'id' => $user_id ] );
		            break;
                case 'target':
                    $value = ws_ls_target_get( $user_id, 'kg' );
                    break;
                case 'bmr':

                    if ( true === WS_LS_IS_PRO_PLUS ) {
                        $value = ws_ls_calculate_bmr( $user_id );
                        $value = ( false === is_numeric( $value ) ) ? '' : $value;
                    } else {
                        $value = 'IGNORE';
                    }

                    break;
                case 'height':
                case 'gender':
                case 'activity_level':
                case 'dob':
                case 'aim':
                    $value = ws_ls_user_preferences_get( $field, $user_id );

                    if ( 'dob' === $field && '0000-00-00 00:00:00' === $value ) {
                        $value = NULL;
                    }

                    break;
            }

            // Allow other developers to insert
            $value = apply_filters( 'wlt-filter-if-condition-' . $field, $value, $user_id );

            if ( true === empty( $value ) ) {
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
function ws_ls_shortcode_if_valid_field_name( $field ) {

    $fields = [ 'weight', 'target', 'bmr', 'height', 'gender', 'activity_level', 'dob', 'previous-weight', 'is-logged-in', 'photo', 'aim', 'challenges-opted-in' ];

    // Allow others to override accepted fields.
    $fields = apply_filters( 'wlt-filter-if-allowed-fields', $fields );

    return ( true === in_array( $field, $fields ) );
}

/**
 * Shortcode to allow nesting of [wlt-if]. This is for [wlt-if-1]
 *
 * @param $user_defined_arguments
 * @param null $content
 * @return string
 */
function ws_ls_shortcode_if_level_one( $user_defined_arguments, $content, $shortcode ) {
    return ws_ls_shortcode_if( $user_defined_arguments, $content, $shortcode, 1 );
}
add_shortcode( 'wlt-if-1', 'ws_ls_shortcode_if_level_one' );
add_shortcode( 'wt-if-1', 'ws_ls_shortcode_if_level_one' );

/**
 * Shortcode to allow nesting of [wlt-if]. This is for [wlt-if-2]
 *
 * @param $user_defined_arguments
 * @param null $content
 * @return string
 */
function ws_ls_shortcode_if_level_two( $user_defined_arguments, $content, $shortcode ) {
    return ws_ls_shortcode_if( $user_defined_arguments, $content, $shortcode, 2 );
}
add_shortcode( 'wlt-if-2', 'ws_ls_shortcode_if_level_two' );
add_shortcode( 'wt-if-2', 'ws_ls_shortcode_if_level_two' );

/**
 * Shortcode to allow nesting of [wlt-if]. This is for [wlt-if-3]
 *
 * @param $user_defined_arguments
 * @param null $content
 * @return string
 */
function ws_ls_shortcode_if_level_three( $user_defined_arguments, $content, $shortcode ) {
    return ws_ls_shortcode_if( $user_defined_arguments, $content, $shortcode, 3 );
}
add_shortcode( 'wlt-if-3', 'ws_ls_shortcode_if_level_three' );
add_shortcode( 'wt-if-3', 'ws_ls_shortcode_if_level_three' );
