<?php

defined( 'ABSPATH' ) or die( 'Jog on!' );

define( 'DATA_WEIGHT', 0 );
define( 'DATA_TARGET', 1 );
define( 'AXIS_WEIGHT_AND_TARGET', 'y-axis-weight' );
define( 'AXIS_META_FIELDS', 'y-axis-meta' );

/**
 * Render a Chart
 *
 * @param $weight_data        array[ $weight_obj ]
 * @param bool $options
 *
 * @return string
 */
function ws_ls_display_chart( $weight_data, $options = [] ) {

	$chart_config = wp_parse_args( $options, [
												'bezier'                => ws_ls_option_to_bool( 'ws-ls-bezier-curve', 'yes', true ),
												'height'                => 250,
												'show-gridlines'        => ws_ls_option_to_bool( 'ws-ls-grid-lines', 'yes', true ),
												'show-target'           => true,
												'show-meta-fields'      => true,
												'type'                  => get_option( 'ws-ls-chart-type', 'line' ),
												'user-id'               => get_current_user_id(),
												'weight-line-color'     => get_option( 'ws-ls-line-colour', '#aeaeae' ),
												'bar-weight-fill-color' => get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ),
												'target-fill-color'     => get_option( 'ws-ls-target-colour', '#76bada' ),
												'begin-y-axis-at-zero'  => ws_ls_option_to_bool( 'ws-ls-axes-start-at-zero', 'n' )
	] );

	$chart_config[ 'id' ]               = ws_ls_component_id();
	$chart_config[ 'font-config' ]      = [
											'fontColor'  => get_option( 'ws-ls-text-colour', '#AEAEAE' ),
											'fontFamily' => get_option( 'ws-ls-font-family', '' )
	];
	$chart_config[ 'meta-fields' ]      =  WS_LS_IS_PRO ? ws_ls_meta_fields_plottable() : false;
	$chart_config[ 'show-meta-fields' ] = ( true === ws_ls_to_bool( $chart_config[ 'show-meta-fields' ] ) &&
	                                        false === empty( $chart_config[ 'meta-fields' ] ) );
	$chart_config[ 'y-axis-unit' ]      = ( true === ws_ls_get_config( 'WE_LS_IMPERIAL_WEIGHTS' ) ) ? __( 'lbs', WE_LS_SLUG ) : __( 'kg', WE_LS_SLUG );
	$chart_config[ 'points-enabled' ]   = ws_ls_option_to_bool( 'ws-ls-allow-points', 'yes', true );
	$chart_config[ 'point-size' ]       = ws_ls_option_to_int( 'ws-ls-point-size', 3, true );
	$chart_config[ 'line-thickness' ]   = 2;
	$chart_config[ 'target-weight' ]    = false;
	$chart_config[ 'show-target' ]      = ( WE_LS_ALLOW_TARGET_WEIGHTS && true === ws_ls_to_bool( $chart_config[ 'show-target' ] ) );

	// Line graphs only for non-pro
	if ( false === WS_LS_IS_PRO ) {
		$chart_config['type'] = 'line';
	}

	// ----------------------------------------------------------------------
	// Weight
	// ----------------------------------------------------------------------

	$graph_data['labels']                  = [];    // "labels" are the labels along the x axis (dates)
	$graph_data['datasets'][ DATA_WEIGHT ] = [
												'fill'        => false,
												'label'       => __( 'Weight', WE_LS_SLUG ),
												'borderColor' => $chart_config[ 'weight-line-color' ],
												'data'        => [],
												'yAxisID'     => AXIS_WEIGHT_AND_TARGET
	];

	// Determine fill based on chart type
	if ( 'line' == $chart_config['type'] ) {

		// Default to no fill
		$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'lineTension' ] = $chart_config[ 'bezier' ] ? 0.4 : 0;
		$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'pointRadius' ] = $chart_config[ 'point-size' ];
		$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'borderWidth' ] = $chart_config[ 'line-thickness' ];

		// Add a fill colour under weight line?
		if ( true === ws_ls_option_to_bool( 'ws-ls-fill-under-weight-line', 'no', true ) ) {

			$line_colour  = ws_ls_option( 'ws-ls-fill-under-weight-line-colour', '#aeaeae', true );
			$line_opacity = ws_ls_option( 'ws-ls-fill-under-weight-line-opacity', '0.5', true );

			$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'fill' ]            = true;
			$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'backgroundColor' ] = ws_ls_convert_hex_to_rgb( $line_colour, $line_opacity );
		}

	} else {

		$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'fill' ]            = false;
		$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'backgroundColor' ] = ws_ls_convert_hex_to_rgb( $chart_config[ 'bar-weight-fill-color' ], 0.5 );
		$graph_data[ 'datasets' ][ DATA_WEIGHT ][ 'borderWidth' ]     = 2;
	}

	// ----------------------------------------------------------------------
	// Target Weight
	// ----------------------------------------------------------------------

	if ( true === $chart_config[ 'show-target' ] ) {

		$chart_config[ 'target-weight' ] = ws_ls_target_get( $chart_config[ 'user-id' ] );

		// If target weights are enabled, then include into javascript data object
		if ( false === empty( $chart_config[ 'target-weight' ] ) ) {

			$graph_data['datasets'][ DATA_TARGET ] = [
														'label'              => __( 'Target', WE_LS_SLUG ),
														'borderColor'       => $chart_config[ 'target-fill-color' ],
														'borderWidth'       => $chart_config[ 'line-thickness' ],
														'pointRadius'       => 0,
														'borderDash'        => [ 5, 5 ],
														'fill'              => false,
														'type'              => 'line',
														'data'              => [],
														'backgroundColor'   => ws_ls_convert_hex_to_rgb( $chart_config[ 'target-fill-color' ], 0.7 ),
														'yAxisID'           => AXIS_WEIGHT_AND_TARGET
			];

			$chart_config[ 'target-weight' ] = $chart_config[ 'target-weight' ][ 'graph-value' ];
		} else {
			$chart_config[ 'show-target' ] = false;
		}

	}

	$chart_config[ 'min-datasets' ] = ( true === $chart_config[ 'show-target' ] ) ? 2 : 1;

	// ----------------------------------------------------------------------------
	// Custom Fields - setup lines for each
	// ----------------------------------------------------------------------------

	if ( true === $chart_config[ 'show-meta-fields' ] ) {

		$meta_dataset_index = $chart_config[ 'min-datasets' ]; // Determine data set on whether or not a target weight has been displayed

		for( $i = 0; $i < count( $chart_config[ 'meta-fields' ] ); $i++ ) {

			$chart_config[ 'meta-fields' ][ $i ][ 'index' ] = $meta_dataset_index;

			$field = $chart_config[ 'meta-fields' ][ $i ];

			$graph_data['datasets'][ $meta_dataset_index ] = [
																'label'             => $field[ 'field_name' ],
																'borderColor'       => $field[ 'plot_colour' ],
																'borderWidth'       => $chart_config[ 'line-thickness' ],
																'pointRadius'       => $chart_config[ 'point-size' ],
																'fill'              => false,
																'type'              => 'line',
																'data'              => [],
																'spanGaps'          => true,
																'backgroundColor'   => ws_ls_convert_hex_to_rgb( $field[ 'plot_colour' ], 0.7 ),
																'yAxisID'           => AXIS_META_FIELDS
			];

			$meta_dataset_index++;
		}
	}

	if ( false === empty( $weight_data ) ) {

		foreach ( $weight_data as $weight ) {

			$graph_data['labels'][]                          = $weight['chart-date'];
			$graph_data['datasets'][ DATA_WEIGHT ]['data'][] = $weight['graph-value'];

			// Add target weight too
			if ( false !== $chart_config[ 'show-target' ] ) {
				$graph_data['datasets'][ DATA_TARGET ]['data'][] = $chart_config['target-weight'];
			}

			// Custom fields
			if ( true === $chart_config['show-meta-fields'] ) {

				$meta_data = ws_ls_meta( $weight[ 'id' ] );

				$meta_data = wp_list_pluck( $meta_data, 'value', 'meta_field_id' );

				foreach ( $chart_config['meta-fields'] as $field ) {

					$value = ( false === empty( $meta_data[ (int) $field['id'] ] ) ) ? $meta_data[ $field['id'] ] : null;

					$graph_data['datasets'][ $field['index'] ]['data'][] = $value;
				}
			}
		}
	}

	// Remove all data sets that have no data from the graph
	$graph_data['datasets'] = array_filter( $graph_data['datasets'], function ( $dataset ) {

		// Remove empty data entry points
		$dataset[ 'data' ] = array_filter( $dataset[ 'data' ] );

		// Do we have any data for this data set?
		return ! empty( $dataset['data'] );
	} );

	ws_ls_charting_enqueue_scripts();

	// Embed JavaScript data object for this graph into page
	wp_localize_script( 'jquery-chart-ws-ls', $chart_config['id'] . '_data', $graph_data );

	// Set initial y axis for weight
	$graph_options = [
		'scales' => [
			'yAxes' => [
				[
					'scaleLabel' => [
						'display'     => true,
						'labelString' => sprintf( '%s (%s)', __( 'Weight', WE_LS_SLUG ), $chart_config[ 'y-axis-unit' ] ),
						'fontColor'   => $chart_config[ 'font-config' ][ 'fontColor' ],
						'fontFamily'  => $chart_config[ 'font-config' ][ 'fontFamily' ]
					],
					'type'       => 'linear',
					'ticks'      => [ 'beginAtZero' => $chart_config[ 'begin-y-axis-at-zero' ] ],
					'display'    => 'true',
					'position'   => 'left',
					'id'         => AXIS_WEIGHT_AND_TARGET,
					'gridLines'  => [ 'display' => $chart_config[ 'show-gridlines' ] ]
				]
			]
		],
		'maintainAspectRatio' => false
	];

	// Custom fields?
	if ( true === $chart_config[ 'show-meta-fields' ] && count( $graph_data['datasets'] ) > $chart_config[ 'min-datasets' ] ) {

		$graph_options['scales']['yAxes'][] =
			[
				'scaleLabel' => [
					'display'     => true,
					'labelString' => __( 'Additional Fields', WE_LS_SLUG ),
					'fontColor'   => $chart_config['font-config']['fontColor'],
					'fontFamily'  => $chart_config['font-config']['fontFamily']
				],
				'type'       => 'linear',
				'ticks'      => [ 'beginAtZero' => $chart_config[ 'begin-y-axis-at-zero' ] ],
				'display'    => 'true',
				'position'   => 'right',
				'id'         => AXIS_META_FIELDS,
				'gridLines'  => [ 'display' => $chart_config['show-gridlines'] ]
			];
	}

	// Hide Gridlines?
	if ( false === $chart_config[ 'show-gridlines' ] ) {
		$graph_options[ 'scales' ][ 'xAxes' ] = [ [ 'gridLines' => [ 'display' => false ] ] ];
	}

	// Legend
	$graph_options['legend'] = [
		'position' => 'bottom',
		'labels'   => [
			'position'   => 'bottom',
			'boxWidth'   => 10,
			'fontSize'   => 10,
			'fontColor'  => $chart_config[ 'font-config' ][ 'fontColor' ],
			'fontFamily' => $chart_config[ 'font-config' ][ 'fontFamily' ]
		]
	];

	wp_localize_script( 'jquery-chart-ws-ls', $chart_config['id'] . '_options', $graph_options );

	return sprintf( '<div class="ws-ls-chart-container" %4$s>
								<canvas id="%1$s" class="ws-ls-chart" %2$s data-chart-type="%3$s" />
							</div>',
		$chart_config['id'],
		( false === empty( $chart_config['height'] ) ) ? sprintf( 'height="%d"', (int) $chart_config['height'] ) : '',
		esc_attr( $chart_config['type'] ),
		( false === empty( $chart_config['height'] ) ) ? sprintf( ' style="height: 250px"', (int) $chart_config['height'] ) : ''
	);
}

/**
 * Enqueue the relevant scripts for Chart.js
 */
function ws_ls_charting_enqueue_scripts() {

	$minified = ws_ls_use_minified();

	wp_enqueue_script( 'ws-ls-chart-js', WE_LS_CDN_CHART_JS, [ 'jquery' ], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'jquery-chart-ws-ls', plugins_url( '../assets/js/ws-ls-chart' . $minified . '.js', __FILE__ ), [ 'ws-ls-chart-js' ], WE_LS_CURRENT_VERSION, true );
}
