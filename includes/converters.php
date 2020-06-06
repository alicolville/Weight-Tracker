<?php
defined('ABSPATH') or die("Jog on!");

// -------------------------------------------------------------
// Weight
// -------------------------------------------------------------

function ws_ls_stones_pounds_to_pounds_only($stones, $pounds) {
  return ($stones * 14) + $pounds;
}

function ws_ls_to_kg($stones, $pounds) {
	$pounds += $stones * 14;
	return round($pounds / 2.20462, 3);
}

function ws_ls_pounds_to_kg($pounds) {
	return round($pounds / 2.20462, 3);
}

function ws_ls_to_lb($kg) {
	$pounds = $kg * 2.20462;
	return round($pounds, 2);
}

function ws_ls_to_stones($pounds) {
	$pounds = $pounds / 14;
	return round($pounds, 2);
}

function ws_ls_pounds_to_stone_pounds($lb) {

	$weight = array ("stones" => 0, "pounds" => 0);
	$weight["stones"] = $lb < 0 ? -1 * floor(-1 * $lb / 14) : floor($lb / 14);
 	$weight["pounds"] = Round(fmod($lb, 14), 1);
    return $weight;
}

function ws_ls_to_stone_pounds($kg) {
	$weight = array ("stones" => 0, "pounds" => 0);
    $totalPounds = Round($kg * 2.20462, 3);
    $weight["stones"] = $totalPounds < 0 ? -1 * floor(-1 * $totalPounds / 14) : floor($totalPounds / 14);
    $weight["pounds"] = Round(fmod($totalPounds, 14), 2);
    return $weight;
}

/**
 * Take Kg and convert into the relevant formats for unit and graph
 * @param $kg
 * @param null $user_id
 * @param bool $key
 * @return array|null
 */
function ws_ls_weight_display( $kg, $user_id = NULL, $key = false ) {

	$weight 	= [];

	// Are we wanting to format the weight for admin UI?
	if ( true === is_admin() ) {
		$weight[ 'format' ] = ws_ls_get_config('WE_LS_DATA_UNITS' );

	// Or, format for front end for the user?
	} else {

		$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;
		$weight[ 'user-id' ] 	= $user_id;
		$weight[ 'format' ] 	= ws_ls_get_config('WE_LS_DATA_UNITS', $user_id );
	}

	$cache_key = sprintf( 'weight-%s-%s', $kg, $weight[ 'format' ] );

	if ( $cache = ws_ls_cache_user_get( $user_id, $cache_key ) ) {
		return $cache;
	}

	$weight[ 'kg' ] = $kg;

	switch ( $weight[ 'format' ] ) {

		case 'pounds_only':
			$weight[ 'pounds' ] 		= ws_ls_to_lb( $kg );
			$weight[ 'display' ] 		= sprintf( '%s%s', $weight[ 'pounds' ], __( 'lbs', WE_LS_SLUG ) );
			$weight[ 'graph-value' ] 	= $weight[ 'pounds' ];
			break;

		case 'kg':
			$weight[ 'display' ] 		= sprintf( '%s%s', $kg, __( 'kg', WE_LS_SLUG ) );
			$weight[ 'graph-value' ] 	= $weight['kg'];
			break;

		default:

			$imperial = ws_ls_to_stone_pounds( $kg );

			// If pounds at 14, then round up stones!
			if( 14 == $imperial[ 'pounds' ] ) {
				$imperial[ 'pounds' ] = 0;
				$imperial[ 'stones' ]++;
			}

			$weight['display'] 		= sprintf( '%s%s %s%s', $imperial[ 'stones' ],__( 'st' , WE_LS_SLUG), $imperial[ 'pounds' ], __( 'lbs' , WE_LS_SLUG) );
			$weight['graph-value'] 	= ( $imperial['stones'] * 14 ) + $imperial['pounds'];
			break;
	}

	ws_ls_cache_user_set( $user_id, $cache_key, $weight );

	return ( false !== $key && false === empty( $weight[ $key ] ) ) ?
		$weight[ $key ] :
		$weight;
}

function ws_ls_convert_kg_into_relevant_weight_String($kg, $comparison_value = false, $user_id = false) {

	if ($kg) {

		switch (ws_ls_get_config('WE_LS_DATA_UNITS', $user_id)) {
			case 'pounds_only':
				return ws_ls_to_lb($kg) . __('lbs', WE_LS_SLUG);
			break;
			case 'kg':
				return round($kg, 2) . __('kg', WE_LS_SLUG);
			break;
			default:
				$weight = ws_ls_to_stone_pounds($kg);

				if ($comparison_value) {
					return ws_ls_format_stones_pound_for_comparison_display($weight);
				}

				// If pounds at 14, then round up stones!
                if(14 == $weight['pounds']) {
                    $weight['pounds'] = 0;
                    $weight['stones']++;
                }

				return $weight['stones'] . __("St", WE_LS_SLUG) . ' ' . $weight['pounds'] . __("lbs", WE_LS_SLUG);
			break;
		}

	}

	return '';
}

// -------------------------------------------------------------
// Others
// -------------------------------------------------------------

function ws_ls_round_decimals($value) {

	if (is_numeric($value)) {
		$value = round($value, 2);
	}
	return $value;
}

function ws_ls_hex_to_rgb( $colour = '', $alpha = null ) {

	if ( $colour[0] == '#' ) {
		$colour = substr( $colour, 1 );
	}
	if ( strlen( $colour ) == 6 ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	} elseif ( strlen( $colour ) == 3 ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	} else {
		return false;
	}
	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );
	$value = $r . ',' . $g . ',' . $b;
	if ( empty($alpha) === false ) {
		return 'rgba(' . $value . ',' . $alpha . ')';
	} else {
		return 'rgb(' . $value . ')';
	}

}
