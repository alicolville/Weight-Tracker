<?php
defined('ABSPATH') or die("Jog on!");

/**
 * Are web hooks enabled?
 * @return bool
 */
function ws_ls_webhooks_enabled() {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	if ( false === ws_ls_webhooks_urls_any() ) {
		return false;
	}

	if ( 'yes' !== get_option( 'ws-ls-webhooks-enabled', 'no' ) ) {
		return false;
	}

	return true;
}

/**
 * Fetch URLs of webhook endpoints
 * @return array|string[]
 */
function ws_ls_webhooks_urls() {

	if ( $urls = ws_ls_cache_get( 'webhook-endpoints' ) ) {
		return $urls;
	}

	$urls       = [];
	$url_keys   = [ 'one', 'two', 'three' ];

	foreach ( $url_keys as $key ) {

		$url = get_option( 'ws-ls-webhook-endpoint-' . $key, '' );

		if ( false !== filter_var( $url, FILTER_VALIDATE_URL ) ) {
			$urls[] = $url;
		}
	}

	ws_ls_cache_set( 'webhook-endpoints', $urls );

	return $urls;
}

/**
 * Do we have an endpoints specified?
 * @return bool
 */
function ws_ls_webhooks_urls_any() {
	return ( false === empty( ws_ls_webhooks_urls() ) );
}

/**
 * Do we have any Slack endpoints?
 * @return bool
 */
function ws_ls_webhooks_endpoints_contain_slack() {

	$endpoints = ws_ls_webhooks_urls();

	if ( true === empty( $endpoints ) ) {
		return false;
	}

	foreach ( $endpoints as $endpoint ) {
		if ( 'slack' === ws_ls_webhooks_url_type( $endpoint ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Do we want to send this?
 * @param $type
 *
 * @return bool
 */
function ws_ls_webhooks_process_this( $type ) {

	// If this is from an admin screen, do we want to process it?
	if ( true === is_admin() &&
	     'yes' !== get_option( 'ws-ls-webhooks-admin-changes-enabled', 'no' ) ) {
		return false;
	}

	// Do we want to process weight entries?
	if ( 'weight' === $type &&
	     'yes' !== get_option( 'ws-ls-webhooks-weight-entries-enabled', 'no' ) ) {
		return false;
	}

	// Do we want to process notes?
	if ( 'new-note' === $type &&
	     'yes' !== get_option( 'ws-ls-webhooks-new-note-enabled', 'no' ) ) {
		return false;
	}

	// Do we want to process targets?
	if ( 'target' === $type &&
	     'yes' !== get_option( 'ws-ls-webhooks-targets-enabled', 'no' ) ) {
		return false;
	}

	return true;
}

/**
 * Listen out for weight entry add/edit hooks
 * @param $type
 * @param $entry
 */
function ws_ls_webhooks_weight_target( $type, $entry ) {

	if ( false === ws_ls_webhooks_enabled() ) {
		return;
	}

	// Want to process this?
	if ( false === ws_ls_webhooks_process_this( $type[ 'type' ] ) ) {
		return;
	}

	$endpoints = ws_ls_webhooks_urls();

	$entry[ 'event' ]   = $type;

	$entry = ( 'target' === $type[ 'type' ] ) ?
		ws_ls_webhooks_data_prep_target( $entry ) :
			ws_ls_webhooks_data_prep_weight( $entry );

	foreach ( $endpoints as $endpoint ) {
		ws_ls_webhooks_send( $endpoint, $entry );
	}

}
add_action( 'wlt-hook-data-added-edited', 'ws_ls_webhooks_weight_target', 10, 2 );

/**
 * Manually fire an a web hook for this weight entry
 */
function ws_ls_webhooks_manually_fire_weight( $user_id, $entry_id ) {

	if ( true === empty( $user_id ) ) {
		return;
	}

	if ( true === empty( $entry_id ) ) {
		return;
	}

	$entry = ws_ls_entry_get( [ 'user-id' => $user_id, 'id' => $entry_id, 'meta' => true ] );

	ws_ls_webhooks_weight_target( [ 'type' => 'weight', 'mode' => 'resend' ], $entry );
}

/**
 * Listen out for new note hooks
 * @param $note
 */
function ws_ls_webhooks_new_note( $note ) {
	
	if ( false === ws_ls_webhooks_enabled() ) {
		return;
	}

	// Want to process this?
	if ( false === ws_ls_webhooks_process_this( 'new-note' ) ) {
		return;
	}

	$note[ 'user-from' ] 	= ws_ls_simple_user_object( $note[ 'from' ] );
	$note[ 'user-to' ] 		= ws_ls_simple_user_object( $note[ 'to' ] );
	$note[ 'timestamp' ]	= date( "Y-m-d H:i:s" );
	
	$endpoints = ws_ls_webhooks_urls();

	foreach ( ws_ls_webhooks_urls() as $endpoint ) {
		ws_ls_webhooks_send( $endpoint, $note );
	}

}
add_action( 'wlt-hook-data-new-note', 'ws_ls_webhooks_new_note' );

/**
 * Fire data to webhook endpoint
 * @param $endpoint_url
 * @param $data
 */
function ws_ls_webhooks_send( $endpoint_url, $data ) {

	$endpoint_type = ws_ls_webhooks_url_type( $endpoint_url );

	/**
	 * If firing to a slack endpoint, only send the "blocks" data. Everything else can be dropped!
	 *
	 * If firing to a non slack endpoint, drop the "blocks" element as only used by Slack
	 */
	if ( 'slack' != $endpoint_type ) {
		unset( $data[ 'blocks' ] );
	} else {
		$data = [ 'blocks' => $data[ 'blocks' ] ];
	}

	$response = wp_remote_post( $endpoint_url, [ 'body' => json_encode( $data ), 'headers' => [ 'Content-Type' => 'application/json; charset=utf-8' ] ] );
}

/**
 * Prepare weight data for sending
 * @param $entry
 * @param string $endpoint_type
 *
 * @return array
 */
function ws_ls_webhooks_data_prep_weight( $entry ) {

	$header = [ '_event' => [ 'type' => 'weight', 'mode' => $entry[ 'event' ][ 'mode' ] ] ];

	$data = $header;

	$data[ 'entry-id' ]                                 = $entry[ 'id' ];
	$data[ 'user-id' ]                                  = $entry[ 'user_id' ];
	$data[ 'user-display-name' ]                        = ws_ls_user_display_name( $entry[ 'user_id' ] );
	$data[ 'date-iso' ]                                 = $entry[ 'weight_date' ];
	$data[ 'date-display' ]                             = $entry[ 'display-date' ];
	$data[ 'weight-kg' ]                                = $entry[ 'kg' ];
	$data[ 'weight-display' ]                           = $entry[ 'display' ];
	$data[ 'weight-first-kg' ]                          = $entry[ 'first_weight' ];
	$data[ 'weight-first-display' ]                     = ws_ls_weight_display( $entry[ 'first_weight' ], $data[ 'user-id' ], 'display', true );
	$data[ 'weight-difference-from-start-kg' ]          = $entry[ 'difference_from_start_kg' ];
	$data[ 'weight-difference-from-start-display' ]     = ws_ls_weight_display( $entry[ 'difference_from_start_kg' ], $data[ 'user-id' ], 'display', true, true );
	$data[ 'weight-previous-kg' ]                       = NULL;

	$previous_weight = ws_ls_entry_get_previous( [ 'user-id' => $entry[ 'user_id' ], 'meta' => false ] );

	if ( false === empty( $previous_weight[ 'kg' ] ) ) {
		$data[ 'weight-previous-kg' ]                           = $previous_weight[ 'kg' ];
		$data[ 'weight-previous-display']                       = ws_ls_weight_display( $previous_weight[ 'kg' ], $data[ 'user-id' ], 'display', true, true );
		$data[ 'weight-difference-latest-previous-kg' ]         = $data[ 'weight-kg' ] - $data[ 'weight-previous-kg' ];
		$data[ 'weight-difference-latest-previous-display' ]    = ws_ls_weight_display( $data[ 'weight-difference-latest-previous-kg' ], $data[ 'user-id' ], 'display', true, true );
	}

	$data[ 'notes' ]                                    = $entry[ 'notes' ];
	$data[ 'url-user-profile' ]                         = ws_ls_get_link_to_user_profile( $entry[ 'user_id' ], NULL, false );
	$data[ 'url-entry-edit' ]                           = ws_ls_get_link_to_edit_entry( $entry[ 'user_id' ], $entry[ 'id' ], false, $data[ 'url-user-profile' ] );


	if ( false === empty( $entry[ 'meta' ] ) ) {

		$data[ 'custom-fields' ] = [];

		foreach ( $entry[ 'meta' ] as $id => $value ) {
			$index                              = ws_ls_meta_fields_get_column( $id );
			$data[ 'custom-fields' ][ $index ]  = ws_ls_fields_display_field_value( $value, $id, true );
		}
	}

	// If Slack, add a summary "text" line.
	if ( true === ws_ls_webhooks_endpoints_contain_slack() ) {

		$data[ 'blocks' ]   = [];

		// Header
		$data[ 'blocks' ][] =  [
									'type'  => 'header',
									'text'  => [
										'type'  => 'plain_text',
										'text'  => esc_html__( 'A weight entry has been added/updated', WE_LS_SLUG ),
										'emoji' => false
									]
		];

		// Name / Date
		$data[ 'blocks' ][] =  [
			'type'  => 'section',
			'fields'  => [
				[
					'type'  => 'mrkdwn',
					'text'  => sprintf( '*%1$s:*%2$s %3$s', esc_html__( 'Name', WE_LS_SLUG ), PHP_EOL, $data[ 'user-display-name' ] )
				],
				[
					'type'  => 'mrkdwn',
					'text'  => sprintf( '*%1$s:*%2$s %3$s', esc_html__( 'Date', WE_LS_SLUG ), PHP_EOL, $data[ 'weight-display' ] )
				]
			]
		];

		// Weight / Starting Weight
		$data[ 'blocks' ][] =  [
			'type'  => 'section',
			'fields'  => [
				[
					'type'  => 'mrkdwn',
					'text'  => sprintf( '*%1$s:*%2$s %3$s %2$s (%4$s)', esc_html__( 'Weight', WE_LS_SLUG ), PHP_EOL, $data[ 'weight-display' ], $data[ 'weight-difference-from-start-display' ] )
				],
				[
					'type'  => 'mrkdwn',
					'text'  => sprintf( '*%1$s:*%2$s %3$s', esc_html__( 'Starting Weight', WE_LS_SLUG ), PHP_EOL, $data[ 'weight-first-display' ] )
				]
			]

		];

		// Custom fields
		if ( false === empty( $entry[ 'meta' ] ) ) {

			foreach ( $entry[ 'meta' ] as $id => $value ) {

				$data[ 'blocks' ][] =  [
					'type'  => 'section',
					'fields'  => [
						[
							'type'  => 'mrkdwn',
							'text'  => sprintf( '*%1$s:*%2$s %3$s %2$s', ws_ls_meta_fields_get_column( $id, 'field_name' ), PHP_EOL, ws_ls_fields_display_field_value( $value, $id, true ) )
						]
					]

				];

			}
		}

		if ( false === empty( $data[ 'notes' ] ) ) {

			$data[ 'blocks' ][] =  [
				'type'  => 'section',
				'fields'  => [
					[
						'type'  => 'mrkdwn',
						'text'  => sprintf( '*%1$s:*%2$s %3$s %2$s', esc_html__( 'Notes', WE_LS_SLUG ), PHP_EOL, wp_strip_all_tags( $data[ 'notes' ] ) )
					]
				]
			];
		}

		// Buttons
		$data[ 'blocks' ][] =  [
			'type'  => 'actions',
			'elements'  => [
				[
					'type'  => 'button',
					'text'  => [ 'type' => 'plain_text', 'text' => esc_html__( 'View Entry', WE_LS_SLUG ) ],
					'url'   => $data[ 'url-entry-edit' ]
				],
				[
					'type'  => 'button',
					'text'  => [ 'type' => 'plain_text', 'text' => esc_html__( 'View Profile', WE_LS_SLUG ) ],
					'url'   => $data[ 'url-user-profile' ]
				]
			]

		];

	}

	return $data;
}

/**
 * Prep data for target
 * @param $target
 *
 * @return array[]
 */
function ws_ls_webhooks_data_prep_target( $target ) {

	$header = [ '_event' => [ 'type' => 'target', 'mode' => $target[ 'event' ][ 'mode' ] ] ];

	$data = $header;

	$data[ 'user-id' ]              = $target[ 'event' ][ 'user-id' ];
	$data[ 'user-display-name' ]    = ws_ls_user_display_name( $data[ 'user-id' ] );
	$data[ 'weight-kg' ]            = $target[ 'kg' ];
	$data[ 'weight-display' ]       = ws_ls_weight_display( $target[ 'kg' ], $data[ 'user-id' ], 'display', true );
	$data[ 'url-user-profile' ]     = ws_ls_get_link_to_user_profile( $data[ 'user-id' ], NULL, false );

	// If Slack, add a summary "text" line.
	if ( true === ws_ls_webhooks_endpoints_contain_slack() ) {

		$data[ 'blocks' ]   = [];

		// Header
		$data[ 'blocks' ][] =  [
			'type'  => 'header',
			'text'  => [
				'type'  => 'plain_text',
				'text'  => esc_html__( 'A new target has been set', WE_LS_SLUG ),
				'emoji' => false
			]
		];

		// Name / Date
		$data[ 'blocks' ][] =  [
			'type'  => 'section',
			'fields'  => [
				[
					'type'  => 'mrkdwn',
					'text'  => sprintf( '*%1$s:*%2$s %3$s', esc_html__( 'Name', WE_LS_SLUG ), PHP_EOL, $data[ 'user-display-name' ] )
				],
				[
					'type'  => 'mrkdwn',
					'text'  => sprintf( '*%1$s:*%2$s %3$s', esc_html__( 'Target Weight', WE_LS_SLUG ), PHP_EOL, $data[ 'weight-display' ] )
				]
			]
		];

		// Button
		$data[ 'blocks' ][] =  [
			'type'  => 'actions',
			'elements'  => [
				[
					'type'  => 'button',
					'text'  => [ 'type' => 'plain_text', 'text' => esc_html__( 'View Profile', WE_LS_SLUG ) ],
					'url'   => $data[ 'url-user-profile' ]
				]
			]

		];
	}

	return $data;
}

/**
 * Given the webhook URL, determine the service
 *
 * @param $url
 *
 * @return string|null
 */
function ws_ls_webhooks_url_type( $url ) {

	if ( true === empty( $url ) ) {
		return NULL;
	}

	$services = [ 'slack', 'zapier' ];

	foreach ( $services as $service )  {
		if ( false !== strpos( $url, $service . '.' ) ) {
			return $service;
		}
	}

	return 'default';
}
