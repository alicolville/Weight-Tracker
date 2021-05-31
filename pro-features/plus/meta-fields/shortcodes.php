<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_meta_fields_shortcode_accumulator( $user_defined_arguments ) {

	$shortcode_arguments = shortcode_atts( [    'button-classes'    => 'button btn-primary',
												'button-text'       => '{increment}',
												'slug'              => '',
												'title'             => '',
												'title-level'       => 'h3',
												'hide-title'        => false,
												'hide-value'        => false,
												'increment-values'  => '1,5,10',         // A string of comma delimited integers of allowed increments
												'value-text'        => sprintf( '%s <strong>{value}</strong>.', __( 'So far you have recorded:', WE_LS_SLUG ) ),
												'value-level'       => 'p',
												'saved-text'        => __( 'Your entry has been saved!', WE_LS_SLUG )

	], $user_defined_arguments );

	if ( true === empty( $shortcode_arguments[ 'slug' ] ) ) {
		return __( 'Please specify a custom field slug e.g. [wt-custom-fields-accumulator slug="cups-of-water-drank-today"].', WE_LS_SLUG );
	}

	$meta_field = ws_ls_meta_fields_get( [ $shortcode_arguments[ 'slug' ] ] );

	if ( true === empty( $meta_field ) ) {
		return __( 'The custom field could not be found for the given slug or it needs to be enabled.', WE_LS_SLUG );
	}

	if ( 0 !== (int) $meta_field[ 'field_type' ] ) {
		return __( 'This shortcode will only work for numeric custom fields.', WE_LS_SLUG );
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

	if ( true == empty( $shortcode_arguments[ 'increment-values' ] ) ||
	            true === empty( $increments ) ) {
		return __( 'Please ensure you have a valid list of increment values e.g. [wt-custom-fields-accumulator increment-values="1,5,10"]', WE_LS_SLUG );
	}


	$html .= sprintf( '<div class="ws-ls-acc-buttons">
							<div id="%1$s" class="ws-ls-status-message ws-meta-hide" data-text-success="%2$s"><p></p></div>', ws_ls_component_id(), wp_kses_post( $shortcode_arguments[ 'saved-text' ] ) );

	foreach ( $increments as $increment ) {

		$button_text = str_replace( '{increment}', $increment, $shortcode_arguments[ 'button-text' ] );

		$html .= sprintf(   '	<button id="%4$s" type="button" class="%2$s " data-increment="%1$d" data-meta-field-id="%5$d" data-parent-id="%6$s" data-original-text="%3$s" data-width-set="false">
									<i class="fa fa-plus"></i>
									%3$s
								</button>&nbsp;' . PHP_EOL,
							(int) $increment,
							esc_attr( $shortcode_arguments[ 'button-classes' ] ),
							wp_kses_post( $button_text ),
							ws_ls_component_id(),
							$meta_field[ 'id' ],
							$main_id
		);
	}

		$html .= '</div>';

	$html .= '</div>';


//var_dump($todays_entry);
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
				'text-failure'          => __( 'There was an issue saving your entry. Please try again.', WE_LS_SLUG ),
	];
}


