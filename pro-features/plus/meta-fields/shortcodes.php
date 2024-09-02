<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Render meta field accumulator
 * @param $user_defined_arguments
 *
 * @return string|void
 */
function ws_ls_meta_fields_shortcode_accumulator( $user_defined_arguments ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$shortcode_arguments = shortcode_atts( [    'button-classes'    => 'button btn-primary',
												'button-text'       => '{increment}',
												'slug'              => '',
												'title'             => '',
												'title-level'       => 'h3',
												'hide-title'        => false,
												'hide-value'        => false,
												'hide-login-prompt' => false,
												'increment-values'  => '-1,-5,-10,1,5,10',         // A string of comma delimited integers of allowed increments
												'value-text'        => sprintf( '%s <strong>{value}</strong>.', esc_html__( 'So far you have recorded:', WE_LS_SLUG ) ),
												'value-level'       => 'p',
												'saved-text'        => esc_html__( 'Your entry has been saved!', WE_LS_SLUG )

	], $user_defined_arguments );

	// Display error if user not logged in
	if ( false === is_user_logged_in() ) {
		return ( true !== ws_ls_to_bool( $shortcode_arguments[ 'hide-login-prompt' ] ) ) ?
							'' :
								ws_ls_display_blockquote( esc_html__( 'You need to be logged in to record your weight.', WE_LS_SLUG ) , '', false, true );
	}

	if ( true === empty( $shortcode_arguments[ 'slug' ] ) ) {
		return esc_html__( 'Please specify a custom field slug e.g. [wt-custom-fields-accumulator slug="cups-of-water-drank-today"].', WE_LS_SLUG );
	}

	$meta_field = ws_ls_meta_fields_get( [ $shortcode_arguments[ 'slug' ] ] );

	if ( 2 !== (int) $meta_field[ 'enabled'] ) {
		return esc_html__( 'The custom field needs to be enabled.', WE_LS_SLUG );
	}

	if ( true === empty( $meta_field ) ) {
		return esc_html__( 'The custom field could not be found for the given slug.', WE_LS_SLUG );
	}

	if ( 0 !== (int) $meta_field[ 'field_type' ] ) {
		return esc_html__( 'This shortcode will only work for numeric custom fields.', WE_LS_SLUG );
	}

	ws_ls_meta_fields_shortcode_accumulator_enqueue_scripts();

	// Do we have an entry for today's date?
	$entry_id = ws_ls_db_entry_for_date( get_current_user_id(), date('Y-m-d' ) );

	$value      = (int) ws_ls_meta_fields_get_value_for_entry( $entry_id, $meta_field[ 'id' ] );
	$main_id    = ws_ls_component_id();

	$html = sprintf(    '<div id="%1$s" class="ws-ls-custom-fields-accumulator" data-value="%2$d">' . PHP_EOL,
						$main_id,
						$value
	);

	if ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-title' ] ) ) {

		$title = ( false === empty( $shortcode_arguments[ 'title' ] ) ) ?
						esc_html( $shortcode_arguments[ 'title' ] ) :
							esc_html( $meta_field[ 'field_name' ] );


		$html .= sprintf( '<%1$s>%2$s</%1$s>' . PHP_EOL, esc_html( $shortcode_arguments[ 'title-level' ] ), $title );
	}

	/*
	 *  Let's determine the current value of the meta field for today
	 */

	if ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-value' ] ) ) {

		$value = str_replace( '{value}',
							'<span class="ws-ls-acc-value">' . $value . '</span>',
									$shortcode_arguments[ 'value-text' ] );

		$html .= sprintf(   '<%2$s>%1$s</%2$s>' . PHP_EOL,
							wp_kses_post( $value ),
							esc_html( $shortcode_arguments[ 'value-level' ] )
		);
	}

	$increments = explode( ',', $shortcode_arguments[ 'increment-values' ] );

	if ( empty( $shortcode_arguments['increment-values'] ) || empty( $increments ) ) {
		return esc_html__( 'Please ensure you have a valid list of increment values e.g. [wt-custom-fields-accumulator increment-values="1,5,10"]', WE_LS_SLUG );
	}


	$html .= sprintf( '<div class="ws-ls-acc-buttons">
							<div id="%1$s" class="ws-ls-status-message ws-meta-hide" data-text-success="%2$s"><p></p></div>', ws_ls_component_id(), wp_kses_post( $shortcode_arguments[ 'saved-text' ] ) );

	foreach ( $increments as $increment ) {

		$button_text    = str_replace( '{increment}', abs( $increment ), $shortcode_arguments[ 'button-text' ] );
		$increment      = (int) $increment;

		$html .= sprintf(   '	<button id="%4$s" type="button" class="%2$s " data-increment="%1$d" data-meta-field-id="%5$d" data-parent-id="%6$s" data-original-text="%3$s" data-width-set="false" data-icon="fa-%7$s">
									<i class="fa fa-%7$s"></i>
									%3$s
								</button>&nbsp;' . PHP_EOL,
							$increment,
							esc_attr( $shortcode_arguments[ 'button-classes' ] ),
							wp_kses_post( $button_text ),
							ws_ls_component_id(),
							$meta_field[ 'id' ],
							$main_id,
							( $increment < 0 ) ? 'minus' : 'plus'
		);
	}

		$html .= '</div>';

	$html .= '</div>';

	return $html;

}
add_shortcode( 'wt-custom-fields-accumulator', 'ws_ls_meta_fields_shortcode_accumulator' );

/**
 * Enqueue relevant JS
 */
function ws_ls_meta_fields_shortcode_accumulator_enqueue_scripts() {

	$minified = ws_ls_use_minified();

	wp_enqueue_script( 'ws-ls-meta-fields-js', plugins_url( '/meta-fields/assets/meta-fields' . $minified . '.js', __DIR__ ), [], WE_LS_CURRENT_VERSION, true );

	// Add localization data for JS
	wp_localize_script('ws-ls-meta-fields-js', 'ws_ls_meta_fields_config', ws_ls_meta_fields_shortcode_js() );

	wp_enqueue_style( 'ws-ls-meta-fields-css', plugins_url( '/meta-fields/assets/meta-fields.css', __DIR__ ), [], WE_LS_CURRENT_VERSION );

	wp_enqueue_style( 'wlt-font-awesome', WE_LS_CDN_FONT_AWESOME_CSS, [], WE_LS_CURRENT_VERSION );
}

/**
 * JS config used for localise script
 * @return mixed|void
 */
function ws_ls_meta_fields_shortcode_js() {

	return [    'ajax-url'              => admin_url( 'admin-ajax.php' ),
				'ajax-security-nonce'   => wp_create_nonce( 'ws-ls-nonce' ),
				'text-saving'           => '<i class="fa fa-circle-o-notch fa-spin"></i>',
				'text-failure'          => esc_html__( 'There was an issue saving your entry. Please try again.', WE_LS_SLUG ),
	];
}
/**
 * Render [wt-form] form
 * @param $user_defined_arguments
 *
 * @return bool|mixed|string
 */
function ws_ls_meta_fields_shortcode_form( $user_defined_arguments ) {

	if( false === WS_LS_IS_PRO ) {
		return false;
	}

	$arguments = shortcode_atts( [      'user-id'                => get_current_user_id(),
	                                    'class'                  => false,
	                                    'force-todays-date'      => false,
	                                    'hide-titles'            => false,
	                                    'redirect-url'           => false,
	                                    'load-placeholders'      => true,
	                                    'title'                  => '',
	                                    'custom-field-groups'    => '',      // If specified, only show custom fields that are within these groups
	                                    'custom-field-slugs'     => '',      // If specified, only show the custom fields that are specified
	], $user_defined_arguments );

	// Port shortcode arguments to core function
	$arguments[ 'css-class-form' ]      = $arguments[ 'class' ];
	$arguments[ 'hide-titles' ]         = ws_ls_to_bool( $arguments[ 'hide-titles' ] );
	$arguments[ 'option-force-today' ]  = ws_ls_to_bool( $arguments[ 'force-todays-date' ] );
	$arguments[ 'hide-fields-meta' ]    = false;
	$arguments[ 'type' ]                = 'custom-fields';

	return ws_ls_form_weight( $arguments );

}
add_shortcode( 'wt-custom-fields-form', 'ws_ls_meta_fields_shortcode_form' );

/**
 * Shortcode for [wt-custom-fields-chart]
 * @param $user_defined_arguments
 *
 * @return bool|string
 */
function ws_ls_meta_fields_shortcode_chart( $user_defined_arguments ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$user_defined_arguments = shortcode_atts( [ 'bezier'              	        => ws_ls_option_to_bool( 'ws-ls-bezier-curve' ),
												'height'              	        => 250,
												'ignore-login-status' 	        => false,
												'message-no-data'               => esc_html__( 'Currently there is no data to display on the chart.', WE_LS_SLUG ),
												'max-data-points'     	        => ws_ls_option( 'ws-ls-max-points', '25', true ),
												'show-gridlines'      	        => ws_ls_option_to_bool( 'ws-ls-grid-lines' ),
												'type'                	        => get_option( 'ws-ls-chart-type', 'line' ),
												'user-id'            	        => get_current_user_id(),
												'weight-fill-color'   	        => get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ),
												'weight-line-color'   	        => get_option( 'ws-ls-line-colour', '#aeaeae' ),
												'weight-target-color' 	        => get_option( 'ws-ls-target-colour', '#76bada' ),
												'reverse'				        => true,
												'custom-field-restrict-rows'    => 'any',   // Only fetch entries that have either all custom fields completed (all), one or more (any) or leave blank if not concerned.
												'custom-field-groups'           => '',      // If specified, only show custom fields that are within these groups
												'custom-field-slugs'            => '',      // If specified, only show the custom fields that are specified
	], $user_defined_arguments );

	$user_defined_arguments[ 'show-weight' ]        = false;
	$user_defined_arguments[ 'show-target' ]        = false;
	$user_defined_arguments[ 'show-custom-fields' ] = true;

	return ws_ls_shortcode_chart( $user_defined_arguments );
}
add_shortcode( 'wt-custom-fields-chart', 'ws_ls_meta_fields_shortcode_chart' );

/**
 * Render data table [wt-custom-fields-table]
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_meta_fields_shortcode_table( $user_defined_arguments ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'enable-add-edit'               => false,
									'enable-bmi'                    => false,
	                                'enable-notes'                  => false,
	                                'enable-weight'                 => false,
	                                'enable-meta-fields'            => true,
	                                'custom-field-restrict-rows'    => 'any',   // Only fetch entries that have either all custom fields completed (all), one or more (any) or leave blank if not concerned.
	                                'custom-field-groups'           => '',      // If specified, only show custom fields that are within these groups
	                                'custom-field-slugs'            => '',      // If specified, only show the custom fields that are specified
	                                'custom-field-col-size'         => 'sm' ] , $user_defined_arguments );

	$arguments[ 'weight-mandatory' ] = false;

	return ws_ls_shortcode_table( $arguments );
}
add_shortcode( 'wt-custom-fields-table', 'ws_ls_meta_fields_shortcode_table' );

/**
 * Display the latest value for the given custom field
 * @param $user_defined_arguments
 *
 * @return mixed
 */
function ws_ls_meta_fields_shortcode_value_latest( $user_defined_arguments ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'slug' => '', 'user-id' => get_current_user_id(), 'which' => 'latest', 'return-as-array' => false ] , $user_defined_arguments );

	if ( true === empty( $arguments[ 'slug' ] ) ) {
		return esc_html__( 'You must specify a slug.', WE_LS_SLUG );
	}

    $meta_field = [ 'id' => ws_ls_meta_fields_slug_to_id( $arguments[ 'slug' ] ), 'slug' => $arguments[ 'slug' ] ];

    $meta_field[ 'id' ] = ws_ls_meta_fields_slug_to_id( $arguments[ 'slug' ] );

	if ( true === empty( $meta_field[ 'id' ] ) ) {
		return esc_html__( 'The slug you specified does not exist.', WE_LS_SLUG );
	}

	$arguments[ 'key' ] = $meta_field[ 'id' ];
	$value              = ws_meta_fields_value_get( $arguments );

	if ( false === empty( $value[ 'error' ] ) ) {
		return $value[ 'error' ];
	}

    $meta_field[ 'value' ]      = $value;
    $meta_field[ 'display' ]    = ws_ls_fields_display_field_value( $value[ 'value' ], $arguments[ 'key' ] );

	return ( false === $arguments[ 'return-as-array' ] ) ? $meta_field[ 'display' ] : $meta_field;
}
add_shortcode( 'wt-custom-fields-latest', 'ws_ls_meta_fields_shortcode_value_latest' );

/**
 * Display the oldest value for the given custom field
 * @param $user_defined_arguments
 *
 * @return mixed
 */
function ws_ls_meta_fields_shortcode_value_oldest( $user_defined_arguments ) {

	$arguments = shortcode_atts( [  'slug' => '', 'user-id' => get_current_user_id(), 'which' => 'oldest' ] , $user_defined_arguments );

	return ws_ls_meta_fields_shortcode_value_latest( $arguments );
}
add_shortcode( 'wt-custom-fields-oldest', 'ws_ls_meta_fields_shortcode_value_oldest' );

/**
 * Display the oldest value for the given custom field
 * @param $user_defined_arguments
 *
 * @return mixed
 */
function ws_ls_meta_fields_shortcode_value_previous( $user_defined_arguments ) {

	$arguments = shortcode_atts( [  'slug' => '', 'user-id' => get_current_user_id(), 'which' => 'previous' ] , $user_defined_arguments );

	return ws_ls_meta_fields_shortcode_value_latest( $arguments );
}
add_shortcode( 'wt-custom-fields-previous', 'ws_ls_meta_fields_shortcode_value_previous' );

/**
 * Display a count of how many times the custom field has been entered [wt-custom-fields-count]
 * @param $user_defined_arguments
 *
 * @return int|mixed
 */
function ws_ls_meta_fields_shortcode_value_count( $user_defined_arguments ) {

	if ( false === ws_ls_meta_fields_is_enabled() ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'slug' => '', 'user-id' => get_current_user_id() ] , $user_defined_arguments );

	if ( true === empty( $arguments[ 'slug' ] ) ) {
		return esc_html__( 'You must specify a slug.', WE_LS_SLUG );
	}

	$meta_field_id = ws_ls_meta_fields_slug_to_id( $arguments[ 'slug' ] );

	if ( true === empty( $meta_field_id ) ) {
		return esc_html__( 'The slug you specified does not exist.', WE_LS_SLUG );
	}

	$arguments[ 'key' ] = $meta_field_id;
	$value              = ws_meta_fields_value_count( $arguments );

	if ( false === empty( $value[ 'error' ] ) ) {
		return $value[ 'error' ];
	}

	return (int) $value[ 'value' ];
}
add_shortcode( 'wt-custom-fields-count', 'ws_ls_meta_fields_shortcode_value_count' );
