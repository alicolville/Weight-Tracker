<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render [wlt-target] shortcode
 * @param bool $user_id
 *
 * @return string
 */
function ws_ls_shortcode_target( $user_id = NULL ) {

	if( false === is_user_logged_in() ) {
		return '';
	}

	$user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	$target_weight = ws_ls_target_get( $user_id, 'display' );

	return esc_html( $target_weight );
}
add_shortcode( 'wlt-target', 'ws_ls_shortcode_target' );
add_shortcode( 'wt-target-weight', 'ws_ls_shortcode_target' );

/**
 * Render shortcode [wt-start-weight]
 * @param bool $user_id
 *
 * @return string
 */
function ws_ls_shortcode_start_weight( $user_id = NULL ) {

	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	$oldest_entry = ws_ls_entry_get_oldest( $arguments );

	if( true === empty( $oldest_entry ) ) {
		return '';
	}

	return $oldest_entry[ 'display' ];
}
add_shortcode( 'wlt-weight-start', 'ws_ls_shortcode_start_weight' );
add_shortcode( 'wt-start-weight', 'ws_ls_shortcode_start_weight' );

/**
 * Render shortcode [wt-latest-weight]
 * @param bool $user_id
 *
 * @return string
 */
function ws_ls_shortcode_recent_weight( $user_id = NULL ) {

	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-latest-weight' ) ) {
		return $cache;
	}

	$latest_entry = ws_ls_entry_get_latest( $arguments );

	if( true === empty( $latest_entry ) ) {
		return '';
	}

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-latest-weight', $latest_entry[ 'display' ] );

	return $latest_entry[ 'display' ];
}
add_shortcode( 'wlt-weight-most-recent', 'ws_ls_shortcode_recent_weight' );
add_shortcode( 'wt-latest-weight', 'ws_ls_shortcode_recent_weight' );

/**
 * Display shortcode for difference since start
 * @param null $user_id
 *
 * @return string
 */
function ws_ls_shortcode_difference_in_weight_from_oldest( $user_id = NULL ) {

	// If not logged in then return no value
	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-since-start' ) ) {
		return $cache;
	}

	$latest_entry = ws_ls_entry_get_latest( $arguments );

	if( true === empty( $latest_entry ) ) {
		return '';
	}

	$difference =  ws_ls_weight_display( $latest_entry[ 'difference_from_start_kg' ], $arguments[ 'user-id' ], false, false, true );

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-since-start', $difference[ 'display' ] );

	return $difference[ 'display' ];
}
add_shortcode( 'wlt-weight-diff', 'ws_ls_shortcode_difference_in_weight_from_oldest' );
add_shortcode( 'wt-difference-since-start', 'ws_ls_shortcode_difference_in_weight_from_oldest' );

/**
 * Shortcide [wt-difference-from-target] display weight difference from target
 * @param null $user_id
 *
 * @return string|null
 */
function ws_ls_shortcode_difference_in_weight_target( $user_id = NULL ){

	// If not logged in then return no value
	if( false === is_user_logged_in() ) {
		return '';
	}

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-target' ) ) {
		return $cache;
	}

	$latest_entry = ws_ls_entry_get_latest( $arguments );

	if ( true === empty( $latest_entry[ 'kg' ] ) ) {
		return '';
	}

	$target_weight = ws_ls_db_target_get( $arguments[ 'user-id' ] );

	if ( true === empty( $target_weight ) ) {
		return '';
	}

	$difference = $latest_entry[ 'kg' ] - $target_weight;
	$sign       = ( $difference > 0 ) ? '+' : '';
	$difference = ws_ls_weight_display( $difference, $arguments[ 'user-id' ], false, false, true );
	$output     = sprintf ('%s%s', $sign, $difference[ 'display' ] );

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-target', $output );

	return $output;
}
add_shortcode( 'wlt-weight-diff-from-target', 'ws_ls_shortcode_difference_in_weight_target' );
add_shortcode( 'wt-difference-from-target', 'ws_ls_shortcode_difference_in_weight_target' );


function ws_ls_get_weight_previous( $user_id ) {

    $user_id = $user_id ?: get_current_user_id();

	global $wpdb;

	// Return cache if found!
	if ( $cache = ws_ls_cache_user_get( $user_id, WE_LS_CACHE_KEY_WEIGHT_PREVIOUS ) )   {
		return $cache;
	}

	$table_name = $wpdb->prefix . WE_LS_TABLENAME;
	$sql = $wpdb->prepare( "SELECT weight_weight FROM $table_name where weight_user_id = %d order by weight_date desc limit 1, 1", $user_id );

	$result = $wpdb->get_var( $sql );

	if ( false === empty( $result ) ) {

		$result = floatval( $result );

        ws_ls_cache_user_set( $user_id, WE_LS_CACHE_KEY_WEIGHT_PREVIOUS, $result );

		return $result;
	}

	return NULL;
}

/**
 *
 * Render the shortcode for previous weight [wlt-weight-previous]
 *
 * @param null $user_id
 *
 * @return string
 */
function ws_ls_shortcode_previous_weight( $user_id = NULL ) {

    if ( false === WS_LS_IS_PRO ) {
        return '';
    }

	$arguments[ 'user-id' ] = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], 'shortcode-previous-weight' ) ) {
		return $cache;
	}

    $previous_entry = ws_ls_entry_get_previous( $arguments );

    $output = ( false === empty( $previous_entry[ 'display' ] ) ) ?
	            $previous_entry[ 'display' ] :
	                '';

	ws_ls_cache_user_set( $arguments[ 'user-id' ], 'shortcode-previous-weight', $output );

    return $output;
}
add_shortcode('wlt-weight-previous', 'ws_ls_shortcode_previous_weight');
add_shortcode('wt-previous-weight', 'ws_ls_shortcode_previous_weight');

function ws_ls_get_target_weight_in_kg($user_id = false){

	$user_id = (true === empty($user_id)) ? get_current_user_id() : $user_id;

	return ws_ls_get_weight_target($user_id);
}

function ws_ls_get_weight_target($user_id, $unit = "target_weight_weight")
{
	global $wpdb;

	$cache_key = $user_id . '-' . WE_LS_CACHE_KEY_TARGET_WEIGHT . $unit;
  	$cache = ws_ls_get_cache($cache_key);

      // Return cache if found!
      if ($cache)   {
         return  $cache;
      }

	$table_name = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;
	$sql =  $wpdb->prepare("SELECT " . $unit . " as weight_value FROM $table_name where weight_user_id = %d", $user_id);
	$rows = $wpdb->get_row($sql);

	if ( false === empty( $rows->weight_value ) ) {
		ws_ls_set_cache($cache_key, $rows->weight_value);
		return $rows->weight_value;
	}

	return false;

}

/**
 * Format weight into correct string
 * @param $weight
 * @param bool $comparison
 * @return string
 */
function we_ls_format_weight_into_correct_string_format( $weight, $comparison = false ) {

    // Don't bother converting the value if there isn't one!
    if ( false === $weight) {
        return '';
    }

	if( true === ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS') ) {

		if ( 'pounds_only' === ws_ls_get_config('WE_LS_DATA_UNITS' ) ) {

            return sprintf( '%1$s%2$s',
				ws_ls_round_number( $weight, 2 ),
                __( 'lbs', WE_LS_SLUG )
            );

        } else {

		    $weight_data = ws_ls_convert_kg_to_stone_pounds( $weight );

			if ( $comparison ) {
				return ws_ls_format_stones_pound_for_comparison_display( $weight_data );
			} else {

				if ( $weight_data[ 'pounds' ] < 0 ) {
					$weight_data[ 'pounds'] = abs( $weight_data[ 'pounds' ] );
				}

				// If Lbs is 14, then set to 0 and increment stones!
				if ( 14 === (int) $weight_data[ 'pounds' ] ) {
					$weight_data[ 'pounds' ] = 0;
					$weight_data[ 'stones' ]++;
				}

				return sprintf( '%1$d%2$s %3$s%4$s',
                    (int) $weight_data[ 'stones' ],
                    __( 'st', WE_LS_SLUG ),
					ws_ls_round_number( $weight_data["pounds"], 2 ),
                    __( 'lbs', WE_LS_SLUG )
                );
			}
		}
	} else {
	    return sprintf( '%1$s%2$s',
						ws_ls_round_number( $weight, 2 ),
                                __( 'Kg', WE_LS_SLUG )
        );
	}
}
