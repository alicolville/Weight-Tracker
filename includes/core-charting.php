<?php

defined( 'ABSPATH' ) or die( 'Jog on!' );


define( 'AXIS_WEIGHT_AND_TARGET', 'y' );
define( 'AXIS_META_FIELDS', 'y1' );

/*
 * Useful example: https://www.chartjs.org/docs/latest/samples/line/multi-axis.html
 */

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
												'custom-field-groups'   => '',      // If specified, only show custom fields that are within these groups
												'custom-field-slugs'    => '',      // If specified, only show the custom fields that are specified
												'height'                => 250,
												'message-no-data'       => __( 'No entries could be found for this user.', WE_LS_SLUG ),
												'show-gridlines'        => ws_ls_option_to_bool( 'ws-ls-grid-lines', 'yes', true ),
												'show-weight'           => true,
												'show-target'           => true,
												'show-meta-fields'      => true,
												'type'                  => get_option( 'ws-ls-chart-type', 'line' ),
												'user-id'               => get_current_user_id(),
												'weight-line-color'     => get_option( 'ws-ls-line-colour', '#aeaeae' ),
												'bar-weight-fill-color' => get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ),
												'target-fill-color'     => get_option( 'ws-ls-target-colour', '#76bada' ),
												'reverse'               => false
	] );

	if ( true === empty( $weight_data ) ) {
		return esc_html( $chart_config[ 'message-no-data' ] );
	}

	$chart_config[ 'id' ]               = ws_ls_component_id();
	$chart_config[ 'font-config' ]      = [
											'fontColor'  => get_option( 'ws-ls-text-colour', '#AEAEAE' ),
											'fontFamily' => get_option( 'ws-ls-font-family', '' )
	];

	// Custom field filtering?
	$chart_config[ 'custom-field-groups' ] = ws_ls_meta_fields_groups_slugs_to_ids( $chart_config[ 'custom-field-groups' ] );
	$chart_config[ 'custom-field-slugs' ]  = ws_ls_meta_fields_slugs_to_ids( $chart_config[ 'custom-field-slugs' ] );

	$chart_config[ 'meta-fields' ]      =  WS_LS_IS_PRO ? ws_ls_meta_fields_plottable( $chart_config ) : false;
	$chart_config[ 'show-meta-fields' ] = ( true === ws_ls_to_bool( $chart_config[ 'show-meta-fields' ] ) &&
	                                        false === empty( $chart_config[ 'meta-fields' ] ) );
	$chart_config[ 'y-axis-unit' ]      = ( 'kg' !== ws_ls_setting( 'weight-unit', $chart_config[ 'user-id' ] ) ) ? __( 'lbs', WE_LS_SLUG ) : __( 'kg', WE_LS_SLUG );
	$chart_config[ 'point-size' ]       = ws_ls_option_to_int( 'ws-ls-point-size', 3, true );
	$chart_config[ 'line-thickness' ]   = 2;
	$chart_config[ 'target-weight' ]    = false;
	$chart_config[ 'show-target' ]      = ( true === ws_ls_targets_enabled() && true === ws_ls_to_bool( $chart_config[ 'show-target' ] ) );

	// Line graphs only for non-pro
	if ( false === WS_LS_IS_PRO ) {
		$chart_config['type'] = 'line';
	}
	$chart_config[ 'show-weight' ] = false;
	$chart_config[ 'show-target' ] = true;
	// ----------------------------------------------------------------------
	// Weight
	// ----------------------------------------------------------------------

	$bezier_line_tension    = $chart_config[ 'bezier' ] ? 0.4 : 0;
	$graph_data['labels']   = [];    // "labels" are the labels along the x axis (dates)
	$dataset_index_count    = 0;
	$index_weight           = 0;
	$index_target           = 0;

	if ( true === $chart_config[ 'show-weight' ] ) {

		$index_weight = $dataset_index_count;

		$graph_data['datasets'][ $index_weight ] = [
			'fill'        => false,
			'label'       => __( 'Weight', WE_LS_SLUG ),
			'borderColor' => $chart_config[ 'weight-line-color' ],
			'data'        => [],
			'yAxisID'     => AXIS_WEIGHT_AND_TARGET,
			'spanGaps'    => true
		];


		// Determine fill based on chart type
		if ( 'line' == $chart_config['type'] ) {

			// Default to no fill
			$graph_data[ 'datasets' ][ $index_weight ][ 'lineTension' ] = $bezier_line_tension;
			$graph_data[ 'datasets' ][ $index_weight ][ 'pointRadius' ] = $chart_config[ 'point-size' ];
			$graph_data[ 'datasets' ][ $index_weight ][ 'borderWidth' ] = $chart_config[ 'line-thickness' ];
	
			// Add a fill colour under weight line?
			if ( true === ws_ls_option_to_bool( 'ws-ls-fill-under-weight-line', 'no', true ) ) {

				$line_colour  = ws_ls_option( 'ws-ls-fill-under-weight-line-colour', '#aeaeae', true );
				$line_opacity = ws_ls_option( 'ws-ls-fill-under-weight-line-opacity', '0.5', true );

				$graph_data[ 'datasets' ][ $index_weight ][ 'fill' ]            = true;
				$graph_data[ 'datasets' ][ $index_weight ][ 'backgroundColor' ] = ws_ls_convert_hex_to_rgb( $line_colour, $line_opacity );
			}

		} else {

			$graph_data[ 'datasets' ][ $index_weight ][ 'fill' ]            = false;
			$graph_data[ 'datasets' ][ $index_weight ][ 'backgroundColor' ] = ws_ls_convert_hex_to_rgb( $chart_config[ 'bar-weight-fill-color' ], 0.5 );
			$graph_data[ 'datasets' ][ $index_weight ][ 'borderWidth' ]     = 2;
		}

		$dataset_index_count++;
	}

	// ----------------------------------------------------------------------
	// Target Weight
	// ----------------------------------------------------------------------

	if ( true === $chart_config[ 'show-target' ] ) {

		$index_target = $dataset_index_count;

		$chart_config[ 'target-weight' ] = ws_ls_target_get( $chart_config[ 'user-id' ] );

		// If target weights are enabled, then include into javascript data object
		if ( false === empty( $chart_config[ 'target-weight' ] ) ) {

			$graph_data['datasets'][ $index_target ] = [  'label'           => __( 'Target', WE_LS_SLUG ),
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

		$dataset_index_count++;
	}

	$chart_config[ 'min-datasets' ] = $dataset_index_count;

	// ----------------------------------------------------------------------------
	// Custom Fields - setup lines for each
	// ----------------------------------------------------------------------------

	$y_axis_label       = '';
	$count_meta_fields  = 0;

	if ( true === $chart_config[ 'show-meta-fields' ] ) {

		$meta_dataset_index = $chart_config['min-datasets']; // Determine data set on whether or not a target weight has been displayed

		$use_abbreviation = ( 'abbv' === get_option( 'ws-ls-abbv-or-question', 'abbv' ) );

		for ( $i = 0; $i < count( $chart_config['meta-fields'] ); $i ++ ) {

			$field = $chart_config['meta-fields'][ $i ];

			$chart_config['meta-fields'][ $i ]['index'] = $meta_dataset_index;

			$graph_data['datasets'][ $meta_dataset_index ] = [
																'label'           => ( $use_abbreviation ) ? $field['abv'] : $field['field_name'],
																'pointRadius'     => $chart_config['point-size'],
																'borderColor'     => $field[ 'plot_colour' ],
																'borderWidth'     => $chart_config[ 'line-thickness' ],
																'fill'            => false,
																'type'            => 'line',
																'data'            => [],
																'spanGaps'        => true,
																'lineTension'     => $bezier_line_tension,
																'backgroundColor' => ws_ls_convert_hex_to_rgb( $field['plot_colour'], 0.7 ),
																'yAxisID'         => AXIS_META_FIELDS
			];

			if ( false === empty( $field[ 'suffix' ] ) ) {
				$y_axis_label = $field[ 'suffix' ];
			}

			$meta_dataset_index++;
			$count_meta_fields++;
		}
	}

	if ( false === empty( $weight_data ) ) {

		if ( true === $chart_config[ 'reverse' ] ) {
			$weight_data = array_reverse( $weight_data );
		}

		foreach ( $weight_data as $weight ) {

			$graph_data['labels'][] = $weight['chart-date'];

			if ( false !== $chart_config[ 'show-weight' ] ) {
				$graph_data['datasets'][ $index_weight ]['data'][] = ( false === empty( $weight[ 'graph-value' ] ) ) ? $weight[ 'graph-value' ] : NULL;
			}

			// Add target weight too
			if ( false !== $chart_config[ 'show-target' ] ) {
				$graph_data['datasets'][ $index_target ]['data'][] = $chart_config['target-weight'];
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

	// If we strip a meta field out due to above, then we may have a missing array index e.g. 0,1,2,3,5, we need this line to
	// reshuffle and allow the chart to render.
	$graph_data['datasets'] = array_values($graph_data['datasets']);

	ws_ls_charting_enqueue_scripts();

	// Embed JavaScript data object for this graph into page
	wp_localize_script( 'jquery-chart-ws-ls', $chart_config['id'] . '_data', $graph_data );

	$show_weight_axis = ( true === $chart_config[ 'show-weight' ] || true === $chart_config[ 'show-target' ] );

	// Set initial y axis for weight
	$graph_options = [
		'scales' => [
						'y' =>
								[
									'display'   => $show_weight_axis,
									'grid'      => [ 'drawOnChartArea' => $chart_config[ 'show-gridlines' ] ],
									'position'  => 'left',
									'title'     => [	'display'   => true,
														'text'      => sprintf( '%s (%s)', __( 'Weight', WE_LS_SLUG ), $chart_config[ 'y-axis-unit' ] ),
														'color'     => $chart_config['font-config']['fontColor'],
														'font'      => [ 'family' => $chart_config[ 'font-config' ][ 'fontFamily' ] ]
									],
									'type' => 'linear'
								]
		],
		'maintainAspectRatio' => false
	];

	// If we only have one custom field, then use that suffix for the y axis label.
	if( $count_meta_fields > 1 ||
	    true === empty( $y_axis_label ) ) {
		$y_axis_label = __( 'Additional Fields', WE_LS_SLUG );
	}

	// Custom fields?
	if ( true === $chart_config[ 'show-meta-fields' ] && count( $graph_data['datasets'] ) > $chart_config[ 'min-datasets' ] ) {

		$graph_options['scales']['y1'] =	[
												'display'       => true,
												'grid'          => [ 'drawOnChartArea' => $chart_config[ 'show-gridlines' ] ],
												'position'      => ( true === $show_weight_axis ) ? 'right' : 'left',
												'title' => [
													'display'   => true,
													'text'      => $y_axis_label,
													'color'     => $chart_config['font-config']['fontColor'],
													'font'      => [ 'family' => $chart_config[ 'font-config' ][ 'fontFamily' ] ],
												],
												'type'  => 'linear',
											];
	}

	// Hide Gridlines?
	if ( false === $chart_config[ 'show-gridlines' ] ) {
		$graph_options[ 'scales' ][ 'x' ][ 'grid' ]  = [ 'drawOnChartArea' => $chart_config[ 'show-gridlines' ] ];
	}

	// Graph legend
	$graph_options[ 'plugins' ][ 'legend' ][ 'labels' ] = [ 'color'     => $chart_config[ 'font-config' ][ 'fontColor' ],
															'display'   => true,
				                                            'font'      => [ 'family' => $chart_config[ 'font-config' ][ 'fontFamily' ] ],
				                                            'position'  => 'bottom'
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

	wp_enqueue_script( 'ws-ls-chart-js-polyfill', 'https://polyfill.io/v3/polyfill.min.js?features=ResizeObserver', [ 'jquery' ], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'ws-ls-chart-js', WE_LS_CDN_CHART_JS, [ 'jquery', 'ws-ls-chart-js-polyfill' ], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'jquery-chart-ws-ls', plugins_url( '../assets/js/ws-ls-chart' . $minified . '.js', __FILE__ ), [ 'ws-ls-chart-js' ], WE_LS_CURRENT_VERSION, true );
}
