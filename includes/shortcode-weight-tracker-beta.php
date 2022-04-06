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
		'bmi-format'                => 'label',                     // Format for display BMI
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

 


$html .= '<ul ykuk-tab class="ykuk-flex-center ykuk-flex-right@s" ykuk-switcher>
			<li class="ykuk-active ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: home"></span></a></li>
			<li class="ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: plus"></span></a></li>
			<li class="ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: history"></span></a></li>
			<li class="ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: heart"></span></a></li>
			<li class="ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: image"></span></a></li>
			<li class="ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: mail"></span></a></li>
			<li class="ykuk-padding-remove-left"><a href="#"><span ykuk-icon="icon: settings"></span></a></li>
		</ul>';

$html .= '<ul class="ykuk-switcher switcher-container ykuk-margin">
    		<li>' . ws_ls_uikit_summary();

			// Display chart?
	if ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-chart-overview' ] ) ) {
		$shortcode_arguments[ 'hide-title' ] = true;			// TODO
		$shortcode_arguments[ 'legend-position' ] = 'bottom';

		$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header' 		=> __( 'Weight entries', WE_LS_SLUG ), 
																'body' 			=> ws_ls_shortcode_embed_chart( $weight_data, $shortcode_arguments ), 
																'footer-link'	=> '#',
																'footer-text' 	=> __( 'View in tabular format', WE_LS_SLUG ) 
															] );

		$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 	'header' 		=> __( 'Today\'s calorie intake', WE_LS_SLUG ), 
			'body' 			=> ws_ls_uikit_mealtracker_summary( [] )
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

	
	$html .= '</li> <li>';
	
	$html .= ws_ls_uikit_data_summary();

	$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Your entries', WE_LS_SLUG ), 
	'body-class'	=> 'ykuk-text-small',		
	'body' 			=> ws_ls_shortcode_table( [ 'user-id' => $user_id, 'enable-add-edit' => true, 'enable-meta-fields' => true,
						'week' => $selected_week_number, 'bmi-format' => $shortcode_arguments[ 'bmi-format' ],
							'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
							'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ] ] )

	]);

	$html .= '</li>
	<li>' . ws_ls_uikit_advanced( $shortcode_arguments ) .'</li>
	<li>' . ws_ls_uikit_gallery() . '</li>
	<li>';

    $html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Messages', WE_LS_SLUG ), 
														'body-class'	=> 'ykuk-text-small',		
														'body' 			=> ws_ls_uikit_messages() ]);

	$html .= '</li><li>Settings</li></ul>';


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

	$args = wp_parse_args( $args, [ 'header' => '', 'body' => '', 'body-class' => '', 'footer' => '', 'footer-link' => '', 'footer-text' => '' ] );

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
		
	$html .= sprintf( '<div class="ykuk-card-body%s">%s</div>', ( false === empty( $args[ 'body-class' ] ) ) ? ' ' . esc_attr( $args[ 'body-class' ] ) : '', $args[ 'body' ] );

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
							<span class="ykuk-info-box-header">Latest vs Target</span><br />
							<span class="ykuk-text-bold">
								+1st 12lbs <span class="ykuk-label">+12%</span>
							</span>
					</div>
				</div>
				<div>
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header" ykuk-tooltip="The weight that you wish to achieve.">Target Weight</span><br />
							<span class="ykuk-text-bold">11st 12lb</span><br />
							<span class="ykuk-info-box-meta"><a href="#">Adjust</a></span>
					</div>
				</div>
			</div>';
}

function ws_ls_uikit_data_summary() {
	return '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-child-width-1-4@m ykuk-grid-match ykuk-text-small" ykuk-grid>
				<div>
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header">No. of entries</span><br />
							<span class="ykuk-text-bold">
								123 
							</span>
					</div>
				</div>
				<div>
					<div class="ykuk-card ykuk-card-body ykuk-box-shadow-small ykuk-card-small">
							<span class="ykuk-info-box-header">Tracking for</span><br />
							<span class="ykuk-text-bold">1 year, 6 months</span><br />
					</div>
				</div>
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
					<div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
							<span class="ykuk-info-box-header">Start Weight</span><br />
							<span class="ykuk-text-bold">15st 12lb</span><br />
							<span class="ykuk-info-box-meta"><a href="#" ykuk-switcher-item="next">01/01/2001</a></span>
					</div>
				</div>
			</div>';
}


// function ws_ls_uikit_mealtracker_summary() {

// 	return '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
// 				<div>
// 					' .  yk_mt_shortcode_chart( [] ) . '
// 				</div>
// 				<div>
					
// 				</div>
			
// 			</div>';
// }

function ws_ls_uikit_gallery( $arguments = [] ) {
	$html = '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
				<div>
					' . ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Latest Photo', WE_LS_SLUG ), 
																	'body-class'	=> 'ykuk-text-small ykuk-text-center',		
																	'body' 			=> ws_ls_photos_shortcode_recent( [] )
					] ) . '
				</div>
				<div>
				' . ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Oldest Photo', WE_LS_SLUG ), 
																'body-class'	=> 'ykuk-text-small ykuk-text-center',		
																'body' 			=> ws_ls_photos_shortcode_oldest( [] )
				] ) . '
			</div>
			</div>
			
			';
				
			$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'All of your photos', WE_LS_SLUG ), 
			'body-class'	=> 'ykuk-text-small ykuk-text-right',		
			'body' 			=> ws_ls_photos_shortcode_gallery( [] )
			] );
				
	
	return $html;
}

function ws_ls_uikit_advanced( $arguments = [] ) {
	$html = '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
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
			</div>';
				
				$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Suggested Calorie Intake', WE_LS_SLUG ), 
				'body-class'	=> 'ykuk-text-small ykuk-text-right',		
				'body' 			=> '
					<p><a ykuk-toggle="cls: ykuk-hidden; target: #calorie-intake-info; animation: ykuk-animation-slide-bottom" class="ykuk-text-right ykuk-icon-link" ykuk-icon="triangle-down">Lean more about suggested calorie intakes</a></p>
					<p id="calorie-intake-info" class="ykuk-hidden ykuk-text-left">Once we know your BMR (the number of calories to keep you functioning at rest), we can go on to give you suggestions on how to spread your calorie intake across the day. Firstly we split the figures into daily calorie intake to maintain weight and daily calorie intake to lose weight. Daily calorie intake to lose weight is calculated based on NHS advice – they suggest to lose 1 – 2lbs a week you should subtract 600 calories from your BMR. The two daily figures can be further broken down by recommending how to split calorie intake across the day i.e. breakfast, lunch, dinner and snacks.</p>	
					
						<table class="ws-ls-footable ykuk-table ykuk-table-striped ykuk-table-small"  >
				<tr>
					<th class="ws-ls-empty-cell row-title"></th>
					<th>Total</th>
					<th data-breakpoints="xs sm">Breakfast (20%)</th>
					<th data-breakpoints="xs sm">Lunch (30%)</th>
					<th data-breakpoints="xs sm">Dinner (30%)</th>
					<th data-breakpoints="xs sm">Snacks (20%)</th>
				</tr><tr valign="top" class="alternate">
						<td class="ws-ls-col-header">Maintain</td>
						<td>2,588</td>
						<td>518</td>
						<td>776</td>
						<td>776</td>
						<td>518</td>
					</tr><tr valign="top" class="">
						<td class="ws-ls-col-header">Lose</td>
						<td>2,188</td>
						<td>438</td>
						<td>656</td>
						<td>656</td>
						<td>438</td>
					</tr><tr valign="top" class="alternate">
						<td class="ws-ls-col-header">Gain</td>
						<td>3,188</td>
						<td>638</td>
						<td>956</td>
						<td>956</td>
						<td>638</td>
					</tr></table>
					'
				] );
				
				$html .= ws_ls_ui_kit_info_box_with_header_footer( [ 'header' 		=> __( 'Macronutrients', WE_LS_SLUG ), 
						'body-class'	=> 'ykuk-text-small',
						'body' 			=> '<p>With calories calculated, the we can recommend how those calories should be split into Fats, Carbohydrates and Proteins.</p>	<div class="ws-ls-tab-advanced-data">
						<table class="ws-ls-footable ykuk-table ykuk-table-striped ykuk-table-small"  >
				<tr>
					<th class="row-title">Maintain (2,588kcal)</th>
					<th>Total</th>
					<th data-breakpoints="xs sm">Breakfast</th>
					<th data-breakpoints="xs sm">Lunch</th>
					<th data-breakpoints="xs sm">Dinner</th>
					<th data-breakpoints="xs sm">Snacks</th>
				</tr>
			  <tr valign="top" class="alternate">
					<td class="ws-ls-col-header">Proteins (10%)</td>
					<td>64.70</td>
					<td>12.94</td>
					<td>19.41</td>
					<td>19.41</td>
					<td>12.94</td>
				</tr>  <tr valign="top" >
					<td class="ws-ls-col-header">Carbs (20%)</td>
					<td>129.40</td>
					<td>25.88</td>
					<td>38.82</td>
					<td>38.82</td>
					<td>25.88</td>
				</tr>  <tr valign="top" class="alternate">
					<td class="ws-ls-col-header">Fats (70%)</td>
					<td>201.29</td>
					<td>40.26</td>
					<td>60.39</td>
					<td>60.39</td>
					<td>40.26</td>
				</tr>
				<tr>
					<th class="row-title">Lose (2,188kcal)</th>
					<th>Total</th>
					<th data-breakpoints="xs sm">Breakfast</th>
					<th data-breakpoints="xs sm">Lunch</th>
					<th data-breakpoints="xs sm">Dinner</th>
					<th data-breakpoints="xs sm">Snacks</th>
				</tr>
			  <tr valign="top" class="alternate">
					<td class="ws-ls-col-header">Proteins (40%)</td>
					<td>218.80</td>
					<td>43.76</td>
					<td>65.64</td>
					<td>65.64</td>
					<td>43.76</td>
				</tr>  <tr valign="top" >
					<td class="ws-ls-col-header">Carbs (20%)</td>
					<td>109.40</td>
					<td>21.88</td>
					<td>32.82</td>
					<td>32.82</td>
					<td>21.88</td>
				</tr>  <tr valign="top" class="alternate">
					<td class="ws-ls-col-header">Fats (40%)</td>
					<td>97.24</td>
					<td>19.45</td>
					<td>29.17</td>
					<td>29.17</td>
					<td>19.45</td>
				</tr>
				<tr>
					<th class="row-title">Gain (3,188kcal)</th>
					<th>Total</th>
					<th data-breakpoints="xs sm">Breakfast</th>
					<th data-breakpoints="xs sm">Lunch</th>
					<th data-breakpoints="xs sm">Dinner</th>
					<th data-breakpoints="xs sm">Snacks</th>
				</tr>
			  <tr valign="top" class="alternate">
					<td class="ws-ls-col-header">Proteins (10%)</td>
					<td>79.70</td>
					<td>15.94</td>
					<td>23.91</td>
					<td>23.91</td>
					<td>15.94</td>
				</tr>  <tr valign="top" >
					<td class="ws-ls-col-header">Carbs (20%)</td>
					<td>159.40</td>
					<td>31.88</td>
					<td>47.82</td>
					<td>47.82</td>
					<td>31.88</td>
				</tr>  <tr valign="top" class="alternate">
					<td class="ws-ls-col-header">Fats (70%)</td>
					<td>247.96</td>
					<td>49.59</td>
					<td>74.39</td>
					<td>74.39</td>
					<td>49.59</td>
				</tr></table>'
				] );
			
			
	$html .='<div id="modal-bmi" ykuk-modal>
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

	return $html;
}

function ws_ls_uikit_mealtracker_summary() {

	return '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-2@s ykuk-grid-match ykuk-text-small" ykuk-grid>
				<div>
					' .  yk_mt_shortcode_chart( [] ) . '
				</div>
				<div>
					
				</div>
			
			</div>';
}

//' . yk_mt_shortcode_table_entries( [] ) . '


function ws_ls_uikit_messages() {
	return '<ul class="ykuk-comment-list">
    <li>
        <article class="ykuk-comment ykuk-visible-toggle ykuk-comment-primary  ykuk-text-small" tabindex="-1">
            <header class="ykuk-comment-header ykuk-position-relative">
                <div class="ykuk-grid-medium ykuk-flex-middle" ykuk-grid>
                    <div class="ykuk-width-auto">
                        <img class="ykuk-comment-avatar" src="http://one.wordpress.test/wp-content/plugins/Weight-Tracker/assets/uikit/avatar.jpg" width="80" height="80" alt="">
                    </div>
                    <div class="ykuk-width-expand">
                        <h4 class="ykuk-comment-title ykuk-margin-remove"><a class="ykuk-link-reset" href="#">Author</a></h4>
                        <p class="ykuk-comment-meta ykuk-margin-remove-top"><a class="ykuk-link-reset" href="#">12 days ago</a></p>
                    </div>
                </div>
                <div class="ykuk-position-top-right ykuk-position-small ykuk-hidden-hover"><a class="ykuk-link-muted" href="#">Reply</a></div>
            </header>
            <div class="ykuk-comment-body">
                <p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
            </div>
        </article>
    </li>
	<li>
		<article class="ykuk-comment ykuk-visible-toggle  ykuk-text-small" tabindex="-1">
			<header class="ykuk-comment-header ykuk-position-relative">
				<div class="ykuk-grid-medium ykuk-flex-middle" ykuk-grid>
					<div class="ykuk-width-auto">
						<img class="ykuk-comment-avatar" src="http://one.wordpress.test/wp-content/plugins/weight-tracker/assets/uikit/avatar.jpg" width="80" height="80" alt="">
					</div>
					<div class="ykuk-width-expand">
						<h4 class="ykuk-comment-title ykuk-margin-remove"><a class="ykuk-link-reset" href="#">Author</a></h4>
						<p class="ykuk-comment-meta ykuk-margin-remove-top"><a class="ykuk-link-reset" href="#">12 days ago</a></p>
					</div>
				</div>
				<div class="ykuk-position-top-right ykuk-position-small ykuk-hidden-hover"><a class="ykuk-link-muted" href="#">Reply</a></div>
			</header>
			<div class="ykuk-comment-body">
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
			</div>
		</article>
	</li>
	<li>
		<article class="ykuk-comment ykuk-visible-toggle ykuk-comment-primary  ykuk-text-small" tabindex="-1">
			<header class="ykuk-comment-header ykuk-position-relative">
				<div class="ykuk-grid-medium ykuk-flex-middle" ykuk-grid>
					<div class="ykuk-width-auto">
						<img class="ykuk-comment-avatar" src="http://one.wordpress.test/wp-content/plugins/weight-tracker/assets/uikit/avatar.jpg" width="80" height="80" alt="">
					</div>
					<div class="ykuk-width-expand">
						<h4 class="ykuk-comment-title ykuk-margin-remove"><a class="ykuk-link-reset" href="#">Author</a></h4>
						<p class="ykuk-comment-meta ykuk-margin-remove-top"><a class="ykuk-link-reset" href="#">12 days ago</a></p>
					</div>
				</div>
				<div class="ykuk-position-top-right ykuk-position-small ykuk-hidden-hover"><a class="ykuk-link-muted" href="#">Reply</a></div>
			</header>
			<div class="ykuk-comment-body">
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
			</div>
		</article>
	</li>
</ul>
<ul class="ykuk-pagination ykuk-flex-center" ykuk-margin>
    <li><a href="#"><span ykuk-pagination-previous></span></a></li>
    <li><a href="#">1</a></li>
    <li class="ykuk-disabled"><span>...</span></li>
    <li><a href="#">4</a></li>
    <li><a href="#">5</a></li>
    <li><a href="#">6</a></li>
    <li class="uk-active"><span>7</span></li>
    <li><a href="#">8</a></li>
    <li><a href="#">9</a></li>
    <li><a href="#">10</a></li>
    <li class="ykuk-disabled"><span>...</span></li>
    <li><a href="#">20</a></li>
    <li><a href="#"><span ykuk-pagination-next></span></a></li>
</ul>
	';
}