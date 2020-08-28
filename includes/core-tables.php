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
