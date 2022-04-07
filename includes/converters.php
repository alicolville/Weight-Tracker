<?php
defined('ABSPATH') or die("Jog on!");

// -------------------------------------------------------------
// Weight
// -------------------------------------------------------------

/**
 * Convert Stones / Pounds to pounds
 * @param $stones
 * @param $pounds
 *
 * @return float|int
 */
function ws_ls_convert_stones_pounds_to_pounds( $stones, $pounds ) {
  return ( $stones * 14 ) + $pounds;
}

/**
 * Convert Stones / Pounds to Kg
 * @param $stones
 * @param $pounds
 *
 * @return float
 */
function ws_ls_convert_stones_pounds_to_kg( $stones, $pounds ) {
	$pounds += $stones * 14;
	return round($pounds / 2.20462, 3);
}

/**
 * Convert pounds to Kg
 * @param $pounds
 *
 * @return float
 */
function ws_ls_convert_pounds_to_kg( $pounds ) {
	return round($pounds / 2.20462, 3);
}

/**
 * Convert stones to pounds
 * @param $pounds
 *
 * @return float
 */
function ws_ls_convert_pounds_to_stones( $pounds ) {
	$pounds = $pounds / 14;
	return round($pounds, 2 );
}

/**
 * Convert pounds to stone / pounds
 * @param $lb
 *
 * @return array
 */
function ws_ls_convert_pounds_to_stone_pounds( $lb ) {
	$weight = [ 'stones' => 0, 'pounds' => 0 ];
	$weight[ 'stones' ] = $lb < 0 ? -1 * floor(-1 * $lb / 14 ) : floor($lb / 14 );
	$weight[ 'pounds' ] = Round( fmod( $lb, 14 ), 1 );
	return $weight;
}

/**
 * Convert Kg to pounds
 * @param $kg
 *
 * @return float
 */
function ws_ls_convert_kg_to_lb( $kg ) {
	$pounds = $kg * 2.20462;
	return round( $pounds, 2 );
}

/**
 * Convert Kg into Stone / Pounds
 * @param $kg
 *
 * @return array
 */
function ws_ls_convert_kg_to_stone_pounds( $kg ) {
	$weight = [ 'stones' => 0, 'pounds' => 0 ];
	$totalPounds = Round($kg * 2.20462, 3 );
	$weight[ 'stones' ] = $totalPounds < 0 ? -1 * floor(-1 * $totalPounds / 14 ) : floor($totalPounds / 14 );
	$weight[ 'pounds' ] = Round ( fmod( $totalPounds, 14 ), 2 );
	return $weight;
}

/**
 * Take Kg and convert into the relevant formats for unit and graph
 *
 * @param $kg
 * @param null $user_id
 * @param bool $key
 * @param bool $force_admin
 *
 * @param bool $comparison_value
 *
 * @return array|null
 */
function ws_ls_weight_display( $kg, $user_id = NULL, $key = false, $force_admin = false, $comparison_value = false ) {

	$weight 	            = [];
	$weight[ 'user-id' ]    = ( NULL === $user_id ) ? get_current_user_id() : $user_id;
	$weight[ 'format' ]     = ws_ls_setting( 'weight-unit', $weight[ 'user-id' ], $force_admin );
	$weight[ 'kg' ]         = $kg;

	switch ( $weight[ 'format' ] ) {

		case 'pounds_only':
			$weight[ 'pounds' ] 		= ws_ls_convert_kg_to_lb( $kg );
			$weight[ 'display' ] 		= sprintf( '%s%s', $weight[ 'pounds' ], __( 'lbs', WE_LS_SLUG ) );
			$weight[ 'graph-value' ] 	= $weight[ 'pounds' ];
			$weight[ 'pounds' ]         = $weight['pounds'];
			$weight[ 'imperial' ]       = true;
			break;

		case 'kg':
			$weight[ 'display' ] 		= sprintf( '%s%s', ws_ls_round_decimals( $kg ), __( 'kg', WE_LS_SLUG ) );
			$weight[ 'graph-value' ] 	= $weight['kg'];
			$weight[ 'imperial' ]       = false;
			break;

		default:

			$imperial = ws_ls_convert_kg_to_stone_pounds( $kg );

			// If pounds at 14, then round up stones!
			if( 14 == $imperial[ 'pounds' ] ) {
				$imperial[ 'pounds' ] = 0;
				$imperial[ 'stones' ]++;
			}

			$weight[ 'display' ] 		= sprintf( '%s%s %s%s', $imperial[ 'stones' ],__( 'st' , WE_LS_SLUG), $imperial[ 'pounds' ], __( 'lbs' , WE_LS_SLUG) );
			$weight[ 'graph-value' ] 	= ( $imperial['stones'] * 14 ) + $imperial['pounds'];
			$weight[ 'stones' ]         = $imperial['stones'];
			$weight[ 'pounds' ]         = $imperial['pounds'];
			$weight[ 'imperial' ]       = true;

			// Comparison value?
			if ( true === $comparison_value ) {
				$weight[ 'display' ] = ws_ls_format_stones_pound_for_comparison_display( $weight );
			}

			break;
	}

	return ( false !== $key && false === empty( $weight[ $key ] ) ) ?
		$weight[ $key ] :
		$weight;
}


/**
 * Prepare Stones / Pounds for display
 * @param $weight
 *
 * @return string
 */
function ws_ls_format_stones_pound_for_comparison_display( $weight ) {

	if( true === isset( $weight[ 'stones' ] ) &&
	    true === isset( $weight['pounds'] ) ) {

		$text           = [];
		$show_stones    = true;

		// Round up figures that hit 14lb
		if( 14 == $weight[ 'pounds' ] ) {
			$weight[ 'pounds' ] = 0;
			$weight[ 'stones' ]++;
		} else if (-14 == $weight["pounds"]) {
			$weight[ 'pounds' ] = 0;
			$weight[ 'stones' ]--;
		}

		// Is stones equal to zero?
		if( -0 == $weight['stones'] || 0 == $weight['stones'] ) {
			$show_stones = false;
		}

		if ($show_stones) {
			$text[] = $weight['stones'] . __( 'st', WE_LS_SLUG );
		}

		if ( true === is_numeric( $weight['pounds'] ) ) {
var_dump( $weight );
			// If both stones and pounds negative then invert pounds.
			// e.g.
			// -1 stone -10 pounds will get displayed as -1 stone 10 pounds
			if ( $show_stones && ( -0 == $weight['stones'] || $weight['stones'] < 0 ) && ( $weight['pounds'] < 0 || -0 == $weight['pounds'] ) ) {
				$weight['pounds'] = abs($weight['pounds']);
			}

			$text[] = $weight['pounds'] . __('lbs', WE_LS_SLUG);
		}

		return implode(' ', $text);
	}

	return '';
}


/**
 * Convert an ISO date into a date object
 * @param $iso_date
 * @param null $key
 *
 * @param bool $force_admin
 * @return array|mixed
 */
function ws_ls_convert_ISO_date_into_locale( $iso_date, $key = NULL, $force_admin = false ) {

	$convert = [ 'raw' => $iso_date, 'chart' => '', 'display-date' => '', 'admin' => '', 'uk' => '', 'us' => '' ];

	if ( false === empty( $iso_date ) ) {

		$convert[ 'time' ]          = strtotime( $iso_date );
		$convert[ 'chart-date' ]    = date_i18n('d M', $convert[ 'time' ] );
		// $convert[ 'uk' ]            = date('d/m/Y', $convert[ 'time' ] );
		// $convert[ 'us' ]            = date('m/d/Y', $convert[ 'time' ] );
		$format  = ( true === ws_ls_setting('use-us-dates', get_current_user_id(), $force_admin ) ) ? 'm/d/Y' : 'd/m/Y';
		$convert[ 'display-date' ] = date( $format, $convert[ 'time' ] );
	}

	if ( NULL !== $key && false === empty( $convert[ $key ] ) ) {
		return $convert[ $key ];

	} elseif ( NULL !== $key ) {
		return '';
	}

	return $convert;
}

/**
 * DEPRECATED: REFACTOR: WITH ws_ls_convert_ISO_date_into_locale
 *
 * @param $date
 * @param bool $user_id
 *
 * @return string|null
 */
function ws_ls_convert_date_to_iso( $date, $user_id = false ) {

	if ( true === empty( $date ) ) {
		return NULL;
	}

	$user_id = ( true === empty( $user_id ) ) ? get_current_user_id() : $user_id;

	if ( true === ws_ls_setting('use-us-dates', $user_id ) ) {
		list( $month,$day,$year ) = sscanf( $date, "%d/%d/%d" );
		$date = "$year-$month-$day";
	} else {
		list( $day,$month,$year ) = sscanf ( $date, "%d/%d/%d" );
		$date = "$year-$month-$day";
	}

	return $date;
}

// -------------------------------------------------------------
// Others
// -------------------------------------------------------------

/**
 * Round a number to two decimal places
 * @param $value
 *
 * @return float
 */
function ws_ls_round_decimals( $value ) {

	return ( is_numeric( $value ) ) ?
			round( $value, 2) :
				$value ;
}

/**
 * Convert a HEx colour to RGB
 * @param string $colour
 * @param null $alpha
 *
 * @return bool|string
 */
function ws_ls_convert_hex_to_rgb( $colour = '', $alpha = null ) {

	if ( '#' === $colour[0] ) {
		$colour = substr( $colour, 1 );
	}
	if ( 6 === strlen( $colour ) ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
	} elseif ( 3 === strlen( $colour ) ) {
		list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
	} else {
		return false;
	}

	$r = hexdec( $r );
	$g = hexdec( $g );
	$b = hexdec( $b );

	$value = $r . ',' . $g . ',' . $b;

	return ( false === empty( $alpha )  ) ?
		 'rgba(' . $value . ',' . $alpha . ')' :
			 'rgb(' . $value . ')';
}
