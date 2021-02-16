<?php
defined('ABSPATH') or die("Jog on!");

/**
 * Are web hooks enabled?
 * @return bool
 */
function ws_ls_webhooks_enabled() {

	return WS_LS_IS_PRO;
}

/**
 * Fetch URLs of webhook endpoints
 * @return array|string[]
 */
function ws_ls_webhooks_urls() {

	if ( false === ws_ls_webhooks_enabled() ) {
		return false;
	}

	// https://hooks.slack.com/services/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
	// https://hooks.zapier.com/hooks/catch/9539368/opanb2u
	return [ 'https://hooks.slack.com/services/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' ];
	//return [ 'https://enor9e1ovwxver3.m.pipedream.net' ];
}

/**
 * Listen out for weight entry add/edit hooks
 * @param $type
 * @param $entry
 */
function ws_ls_webhooks_weight_entry( $type, $entry ) {

	$endpoints = ws_ls_webhooks_urls();

	// If we have no endpoints, then stop here.
	if ( true === empty( $endpoints ) ) {
		return;
	}

	$entry[ 'event' ]   = $type;

	//print_r($type);
	print_r($entry);
	$entry[ 'text' ] = json_encode( $entry );

	$r = wp_remote_post( $endpoints[ 0 ], [ 'body' => json_encode( $entry ) ] );
var_dump($r);
	die;
}
add_action( 'wlt-hook-data-added-edited', 'ws_ls_webhooks_weight_entry', 10, 2 );


/*
 * Array
(
    [user-id] => 2
    [type] => weight-measurements
    [mode] => update
)
Array
(
    [id] => 15
    [weight_date] => 2021-02-21 00:00:00
    [kg] => 345
    [notes] => gerger erg erg
    [user_id] => 2
    [first_weight] => 90.719
    [difference_from_start_kg] => 254.281
    [meta] => Array
        (
            [3] => 23
            [5] => 44
            [7] => 66
            [6] => 33
            [4] => 11
        )

    [raw] => 2021-02-21 00:00:00
    [chart] =>
    [display-date] => 21/02/2021
    [admin] =>
    [uk] =>
    [us] =>
    [time] => 1613865600
    [chart-date] => 21 Feb
    [user-id] => 1
    [format] => kg
    [display] => 345kg
    [graph-value] => 345
    [imperial] =>
)
 */
