<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_uikit_accordian_open( $args = [] ) {

	$args   = wp_parse_args( $args, [ 'multiple'  => false ] );
	$params = '';

	if ( true === ws_ls_to_bool( $args[ 'multiple' ] ) ) {
		$params .= ' multiple: true;';
	}

	return sprintf( '<ul ykuk-accordion%s>',
		( false === empty( $params ) ) ? '="' . $params . '"' : ''
	);
}

add_filter( 'body_class', function( $classes ) {
	$classes[]  = 'uk-scope';	
	return $classes;
 });

function ws_ls_shortcode_beta( $user_defined_arguments ) {

	$shortcode_arguments = shortcode_atts( [
			'accordian-multiple-open'   => true,                    // NEW: Allow more than one accordian tab to be open
		'min-chart-points' 			=> 2,	                        // Minimum number of data entries before chart is shown
//		'hide-first-target-form' 	=> false,					    // Hide first Target form
//		'hide-second-target-form' 	=> false,					    // Hide second Target form
		'custom-field-groups'       => '',                          // If specified, only show custom fields that are within these groups
		'custom-field-slugs'        => '',                          // If specified, only show the custom fields that are specified
//		'bmi-format'                => 'label',                     // Format for display BMI
		'show-add-button' 			=> false,					    // Display a "Add weight" button above the chart.
//		'show-chart-history' 		=> false,					    // Display a chart on the History tab.
//		'allow-delete-data' 		=> true,                	    // Show "Delete your data" section
		'hide-notes' 				=> ws_ls_setting_hide_notes(),  // Hide notes field
		'hide-photos' 				=> false,                       // Hide photos part of form
		'hide-chart-overview' 		=> false,               	    // Hide chart on the overview tab
//		'hide-tab-photos' 			=> false,                 	    // Hide Photos tab
//		'hide-tab-advanced' 		=> false,               	    // Hide Advanced tab (macroN, calories, etc)
//		'hide-tab-descriptions' 	=> ws_ls_option_to_bool( 'ws-ls-tab-hide-descriptions', 'yes' ), // Hide tab descriptions
//		'hide-advanced-narrative' 	=> false,         			    // Hide text describing BMR, MarcoN, etc
//		'disable-advanced-tables' 	=> false,         			    // Disable advanced data tables.
//		'disable-tabs' 				=> false,                       // Disable using tabs.
//		'disable-second-check' 		=> false,					    // Disable check to see if [wlt] placed more than once
		'enable-week-ranges'        => false,                       // Enable Week Ranges?
		'user-id'					=> get_current_user_id(),
		'weight-mandatory'			=> true,						// Is weight mandatory?
	], $user_defined_arguments );


	ws_ls_enqueue_uikit();

	$user_id 	                                = (int) $shortcode_arguments[ 'user-id' ];
	//$use_tabs 	                                = ( false === ws_ls_to_bool( $shortcode_arguments[ 'disable-tabs' ] ) );
	//$show_advanced_tab                          = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-advanced' ] ) && true === WS_LS_IS_PRO_PLUS );
	//$show_photos_tab                            = ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-tab-photos' ] ) && true === ws_ls_meta_fields_photo_any_enabled( true ) );
	$week_ranges_enabled                        = ws_ls_to_bool( $shortcode_arguments[ 'enable-week-ranges' ] );
	$shortcode_arguments[ 'min-chart-points' ]  = (int) $shortcode_arguments[ 'min-chart-points' ];
	
	//$html                                       = '<div class="ws-ls-tracker uk-scope">';
	$html                                       = '<div class="ws-ls-tracker">';

	$selected_week_number   = ( true === $week_ranges_enabled ) ? ws_ls_post_value_numeric( 'week-number' ) : NULL;
	$weight_data            = ws_ls_entries_get( [  'week'      => $selected_week_number,
	                                                'prep'      => true,
	                                                'week'      => $selected_week_number,
	                                                'reverse'   => true,
	                                                'sort'      => 'desc' ] );

 


$html .= '<ul ykuk-tab class="ykuk-flex-right" ykuk-switcher>
			<li class="ykuk-active"><a href="#"><span ykuk-icon="icon: home"></span></a></li>
			<li><a href="#"><span ykuk-icon="icon: plus"></span></a></li>
			<li><a href="#"><span ykuk-icon="icon: history"></span></a></li>
			<li><a href="#"><span ykuk-icon="icon: heart"></span></a></li>
			<li>
				<a href="#"><span ykuk-icon="icon: settings"></span> <span ykuk-icon="icon: triangle-down"></span></a>
				<div ykuk-dropdown="mode: click">
					<ul class="ykuk-nav ykuk-dropdown-nav">
						<li class="ykuk-active"><a href="#">Active</a></li>
						<li><a href="#">Item</a></li>
						<li class="ykuk-nav-header">Header</li>
						<li><a href="#">Item</a></li>
						<li><a href="#">Item</a></li>
						<li class="ykuk-nav-divider"></li>
						<li><a href="#">Item</a></li>ß
					</ul>
				</div>
			</li>
		</ul>';

$html .= '<ul class="ykuk-switcher switcher-container ykuk-margin">
    		<li>' . ws_ls_uikit_summary();

			// Display chart?
	if ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-chart-overview' ] ) ) {
		$shortcode_arguments[ 'hide-title' ] = true;			// TODO
		$shortcode_arguments[ 'legend-position' ] = 'bottom';

		$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header' 		=> __( 'Chart', WE_LS_SLUG ), 
																'body' 			=> ws_ls_shortcode_embed_chart( $weight_data, $shortcode_arguments ), 
																'footer-link'	=> '#',
																'footer-text' 	=> __( 'View in tabular format', WE_LS_SLUG ) 
															] );

	}


	// $html .= ws_ls_ui_kit_info_box_with_header_footer( [ 		'header' 		=> __( 'Add a new entry', WE_LS_SLUG ), 
	// 															'body' 			=> ws_ls_uikit_sample_form()
	// 														] );


	$entry_id = ws_ls_querystring_value( 'ws-edit-entry', true );

	// $form = '';

	// // Are we in front end and editing enabled, and of course we want to edit, then do so!
	// if( false === empty( $entry_id ) ) {

	// 	//If we have a Redirect URL, base decode.
	// 	$redirect_url = ws_ls_querystring_value( 'redirect' );

	// 	if ( false === empty( $redirect_url ) ) {
	// 		$redirect_url = base64_decode( $redirect_url );
	// 	}

	// 	$form .= ws_ls_form_weight( [    	'css-class-form'        => 'ws-ls-main-weight-form',
	// 											'user-id'               => $user_id,
	// 											'entry-id'              => $entry_id,
	// 											'hide-fields-photos'    => ws_ls_to_bool( $shortcode_arguments[ 'hide-photos' ] ),
	// 											'redirect-url'          => $redirect_url,
	// 											'hide-notes'            => ws_ls_to_bool( $shortcode_arguments[ 'hide-notes' ] ),
	// 											'hide-confirmation'     => true,
	// 											'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
	// 											'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
	// 											'weight-mandatory'		=> $shortcode_arguments[ 'weight-mandatory' ]
	// 	] );

	// } else {

	// 	$form .= ws_ls_form_weight( [    	'css-class-form'        => 'ws-ls-main-weight-form',
	// 											'user-id'               => $user_id,
	// 											'hide-fields-photos'    => ws_ls_to_bool( $shortcode_arguments[ 'hide-photos' ] ),
	// 											'hide-notes'            => ws_ls_to_bool( $shortcode_arguments[ 'hide-notes' ] ),
	// 											'hide-confirmation'     => true,
	// 											'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
	// 											'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
	// 											'weight-mandatory'		=> $shortcode_arguments[ 'weight-mandatory' ]
	// 	] );
	// }

	// $html .= ws_ls_ui_kit_info_box_with_header_footer( [ 		'header' 		=> __( 'Add a new entry', WE_LS_SLUG ), 
	// 															'body' 			=> $form ] );

$html .='	</li>
	<li>';
	
	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 		'header' 		=> __( 'Add a new entry', WE_LS_SLUG ), 
																'body' 			=> ws_ls_uikit_sample_form()
															] );

	
	$html .= '</li>
    <li>Hello again!</li>
	<li>' . ws_ls_uikit_advanced() .'</li>
    <li>Bazinga!</li></ul>';



	return $html;


}
add_shortcode( 'wt-beta', 'ws_ls_shortcode_beta' );

function ws_ls_ui_kit_info_box( $args = [] ) {

	$args = wp_parse_args( $args, [ 'percentage_diff' => 0, 'title' => 'Title', 'value' => '999Kg'] );

	$html = sprintf( ' <div class="ykuk-card ykuk-card-default ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
			                <span class="statistics-text"><a href="#" ykuk-switcher-item="next">%1$s</a></span><br />
			                <span class="statistics-number">
			                    %2$s',
					esc_html( $args[ 'title' ] ),
					esc_html( $args[ 'value' ] )
	);

	if ( false === empty( $args[ 'percentage_diff' ] ) ) {

		$args[ 'percentage_diff' ] = (float) $args[ 'percentage_diff' ];

		$span_class     = 'up';
		$label_class    = 'warning';
		$sign           = '+';

		if ( $args[ 'percentage_diff' ] < 0 ) {
			$span_class     = 'down';
			$label_class    = 'success';
			$sign           = '';
		}

		$html .= sprintf( ' 	<span class="ykuk-label ykuk-label-%3$s">
	            				%4$s%2$s%%
                                    </span>',
							$span_class,
							$args[ 'percentage_diff' ],
							$label_class,
							$sign
		);

	}

	$html .= '     			</span>
                       </div>';

	return $html;
}


/**
 * Add a info box with header and footer
 *
 * @param $args
 *
 * @return string
 */
function ws_ls_ui_kit_info_box_with_header_footer( $args = [] ) {

	$args = wp_parse_args( $args, [ 'header' => '', 'body' => '', 'footer' => '', 'footer-link' => '', 'footer-text' => '' ] );

	$html = '<div class="ykuk-card ykuk-card-small ykuk-card-default ykuk-margin-top">';

	if ( false === empty( $args[ 'header' ] ) ) {
		$html .= sprintf( ' <div class="ykuk-card-header">
								<div class="ykuk-grid-small ykuk-flex-middle" ykuk-grid>
									<div class="ykuk-width-expand">
										<h5 class="ykuk-margin-remove-bottom ykuk-margin-remove-top">%s</h5>
									</div>
								</div>
							</div>',
							esc_html( $args[ 'header' ] )
		);
	}
		
	$html .= sprintf( '<div class="ykuk-card-body">%s</div>', $args[ 'body' ] );

	if ( false === empty( $args[ 'footer-link' ] ) 
		&& false === empty( $args[ 'footer-text' ] ) ) {

		$args[ 'footer' ] = sprintf( '<a href="%s" class="ykuk-button ykuk-button-text">%s</a>',
								esc_url( $args[ 'footer-link' ] ),
								esc_html( $args[ 'footer-text' ] )
		);
	}

	if ( false === empty( $args[ 'footer' ] ) ) {
		$html .= sprintf( '<div class="ykuk-card-footer">%s</div>', wp_kses_post( $args[ 'footer' ] ) );
	}		

	$html .= '</div>';

	return $html;

}

// Delete this
function ws_ls_uikit_sample_form() {
	return '<form>
		<fieldset class="ykuk-fieldset">

		
			<div class="ykuk-margin">
				<input class="ykuk-input" type="text" placeholder="Input">
			</div>

			<div class="ykuk-margin">
				<select class="ykuk-select">
					<option>Option 01</option>
					<option>Option 02</option>
				</select>
			</div>

			<div class="ykuk-inline">
				<span class="ykuk-form-iconr" ykuk-icon="icon: calendar" data-ykuk-datepicker="{}"></span>
				<input class="ykuk-input">
			</div>

			<div class="ykuk-margin">
				<textarea class="ykuk-textarea" rows="5" placeholder="Textarea"></textarea>
			</div>

			<div class="ykuk-margin ykuk-grid-small ykuk-child-width-auto ykuk-grid">
				<label><input class="ykuk-radio" type="radio" name="radio2" checked> A</label>
				<label><input class="ykuk-radio" type="radio" name="radio2"> B</label>
			</div>

			<div class="ykuk-margin ykuk-grid-small ykuk-child-width-auto ykuk-grid">
				<label><input class="ykuk-checkbox" type="checkbox" checked> A</label>
				<label><input class="ykuk-checkbox" type="checkbox"> B</label>
			</div>

			<div class="ykuk-margin">
				<button class="ykuk-button ykuk-button-default">Save</button>
			</div>

		</fieldset>
	</form>';
}

// ykuk-child-width-expand@s

function ws_ls_uikit_summary() {
	return '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-child-width-1-4@m ykuk-grid-match ykuk-text-small" ykuk-grid>
				<div>
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header" ykuk-tooltip="The weight you have entered most recently.">Latest Weight</span><br />
							<span class="ykuk-text-bold">
								12st 12lbs <span class="ykuk-label ykuk-label-warning" ykuk-tooltip="The difference between your latest weight and previous.">+999%</span>
							</span>
							<span class="ykuk-info-box-meta"><a href="#" ykuk-switcher-item="next">01/01/2001</a></span>
					</div>
				</div>
				<div>
					<div class="ykuk-card ykuk-card-body ykuk-box-shadow-small ykuk-card-small" ykuk-tooltip="The difference between your latest weight and previous.">
							<span class="ykuk-info-box-header">Previous Weight</span><br />
							<span class="ykuk-text-bold">13st 1lb</span><br />
							<span class="ykuk-info-box-meta"><a href="#" ykuk-switcher-item="next">01/01/2001</a></span>
					</div>
				</div>
				<div>
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header" ykuk-tooltip="The weight that you wish to achieve.">Target Weight</span><br />
							<span class="ykuk-text-bold">11st 12lb</span><br />
							<span class="ykuk-info-box-meta"><a href="#">Adjust</a></span>
					</div>
				</div>
				<div>
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header">Start Weight</span><br />
							<span class="ykuk-text-bold">15st 12lb</span><br />
							<span class="ykuk-info-box-meta"><a href="#" ykuk-switcher-item="next">01/01/2001</a></span>
					</div>
				</div>
			</div>';
}

function ws_ls_uikit_advanced() {
	return '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
				<div>
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header" ykuk-toggle="target: #modal-bmi" >BMI</span><br />
							<span class="ykuk-text-bold">
								<span class="ykuk-label ykuk-label-warning">21 - Overweight</span>
							</span><br />
							<span class="ykuk-info-box-meta"><a href="#" ykuk-toggle="target: #modal-bmi">What is BMI?</a></span>
					</div>
				</div>

				<div>
					<div class="ykuk-card ykuk-card-body ykuk-box-shadow-small ykuk-card-small">
							<span>BMR</span><br />
							<span class="ykuk-text-bold">1882</span><br />
							<span class="ykuk-info-box-meta"><a href="#" ykuk-toggle="target: #modal-bmr">What is BMR?</a></span>
					</div>
				</div>
				
				</div>
			
			
			<div id="modal-bmi" ykuk-modal>
				<div class="ykuk-modal-dialog ykuk-modal-body">
					<h2 class="ykuk-modal-title">Body Mass Index (BMI)</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					<p class="ykuk-text-right">
						<button class="ykuk-button ykuk-button-default ykuk-modal-close" type="button">Close</button>
					</p>
				</div>
			</div>

			<div id="modal-bmr" ykuk-modal>
				<div class="ykuk-modal-dialog ykuk-modal-body">
					<h2 class="ykuk-modal-title">Basal Metabolic Rate (BMR)</h2>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
					<p class="ykuk-text-right">
						<button class="ykuk-button ykuk-button-default ykuk-modal-close" type="button">Close</button>
					</p>
				</div>
			</div>

			';
}