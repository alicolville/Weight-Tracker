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
	$html                                       = '<div class="ws-ls-tracker uk-scope">';

	$selected_week_number   = ( true === $week_ranges_enabled ) ? ws_ls_post_value_numeric( 'week-number' ) : NULL;
	$weight_data            = ws_ls_entries_get( [  'week'      => $selected_week_number,
	                                                'prep'      => true,
	                                                'week'      => $selected_week_number,
	                                                'reverse'   => true,
	                                                'sort'      => 'desc' ] );

	$html .= '<div class="ykuk-card ykuk-card-small ykuk-card-default ykuk-width-1-2@m">
    <div class="ykuk-card-header">
        <div class="ykuk-grid-small ykuk-flex-middle" ykuk-grid>

            <div class="ykuk-width-expand">
                <h3 class="ykuk-card-title uk-margin-remove-bottom">Target</h3>
                <p class="ykuk-text-meta ykuk-margin-remove-top"><time datetime="2016-04-01T19:00">April 01, 2016</time></p>
            </div>
        </div>
    </div>
    <div class="ykuk-card-body">
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
    </div>
    <div class="ykuk-card-footer">
        <a href="#" class="ykuk-button ykuk-button-text">Read more</a>
    </div>
</div>';

$html .= '<ul ykuk-tab class="ykuk-flex-right">
    <li class="ykuk-active"><a href="#"><span ykuk-icon="icon: home"></span></a></li>
    <li><a href="#"><span ykuk-icon="icon: history"></span></a></li>
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
</ul>
';




	$html .= '


<ul class="ykuk-switcher switcher-container ykuk-margin">
    <li>
		<h4>Summary</h4>
		<span class="ykuk-text-small"><span data-ykuk-icon="icon:users" class="ykuk-margin-small-right ykuk-text-primary ykuk-icon"></span>Current weight</span>
		<h1 class="ykuk-heading-primary ykuk-margin-remove  ykuk-text-primary">24Kg</h1>
		<div class="ykuk-text-small">
			<span class="ykuk-text-success uk-icon" data-ykuk-icon="icon: triangle-up">15%</span> more than last week.
		</div>

	</li>
    <li>Hello again!</li>

    <li>Bazinga!</li></ul>';



	// Display chart?
	if ( false === ws_ls_to_bool( $shortcode_arguments[ 'hide-chart-overview' ] ) ) {
		$shortcode_arguments[ 'hide-title' ] = true;
		$html .= ws_ls_shortcode_embed_chart( $weight_data, $shortcode_arguments );
	}

	$html .= '<span class="ykuk-label">Start: 12st 10lb</span>&nbsp;';

	$html .= '<span class="ykuk-label ykuk-label-success">Current: 12st 10lb</span>';

	$html .= ws_ls_uikit_accordian_open( [ 'multiple' => $shortcode_arguments[ 'accordian-multiple-open' ] ] );

	// Display "Add Weight" anchor?
	if(true == $shortcode_arguments['show-add-button']) {
		$html .= '<a name="add-weight"></a>';
	}

	$entry_id = ws_ls_querystring_value('ws-edit-entry', true);

	// Are we in front end and editing enabled, and of course we want to edit, then do so!
	if( false === empty( $entry_id ) ) {

		//If we have a Redirect URL, base decode.
		$redirect_url = ws_ls_querystring_value( 'redirect' );

		if ( false === empty( $redirect_url ) ) {
			$redirect_url = base64_decode( $redirect_url );
		}

//		$html .= ws_ls_form_weight( [    'css-class-form'        => 'ws-ls-main-weight-form',
//		                                        'user-id'               => $user_id,
//		                                        'entry-id'              => $entry_id,
//		                                        'hide-fields-photos'    => ws_ls_to_bool( $shortcode_arguments[ 'hide-photos' ] ),
//		                                        'redirect-url'          => $redirect_url,
//		                                        'hide-notes'            => ws_ls_to_bool( $shortcode_arguments[ 'hide-notes' ] ),
//		                                        'hide-confirmation'     => true,
//		                                        'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
//		                                        'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
//		                                        'weight-mandatory'		=> $shortcode_arguments[ 'weight-mandatory' ]
//		] );

	} else {

//		$html .= ws_ls_form_weight( [    'css-class-form'        => 'ws-ls-main-weight-form',
//		                                        'user-id'               => $user_id,
//		                                        'hide-fields-photos'    => ws_ls_to_bool( $shortcode_arguments[ 'hide-photos' ] ),
//		                                        'hide-notes'            => ws_ls_to_bool( $shortcode_arguments[ 'hide-notes' ] ),
//		                                        'hide-confirmation'     => true,
//		                                        'custom-field-groups'   => $shortcode_arguments[ 'custom-field-groups' ],
//		                                        'custom-field-slugs'    => $shortcode_arguments[ 'custom-field-slugs' ],
//		                                        'weight-mandatory'		=> $shortcode_arguments[ 'weight-mandatory' ]
//		] );
	}


	$html .= '
			    <li class="ykuk-open">
			        <a class="ykuk-accordion-title" href="#">Quick Entry 123</a>
			        <div class="ykuk-accordion-content">Some content here</div>
			    </li>
			     <li>
			        <a class="ykuk-accordion-title" href="#">Header 2</a>
			        <div class="ykuk-accordion-content">2 Some content here</div>
			    </li>
			</ul>


		</div>';

	return $html;

//	return '
//<div class="uk-scope">
//
//
//<button class="ykuk-button ykuk-button-default ykuk-margin-small-right" type="button" uk-toggle="target: #offcanvas-usage">Open</button>
//
//<a href="#offcanvas-usage" ykuk-toggle>Open</a>
//
//<div id="offcanvas-usage" ykuk-offcanvas>
//    <div class="ykuk-offcanvas-bar">
//
//        <button class="ykuk-offcanvas-close" type="button" ykuk-close></button>
//
//        <h3>Title</h3>
//
//        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
//
//    </div>
//</div>
//
//
//<div class="ykuk-flex ykuk-flex-center">
//    <div>1</div>
//    <div>2</div>
//    <div>4</div>
//</div>
//
//				<ul ykuk-tab >
//			    <li class="ykuk-active"><a href="">Quick Entry</a></li>
//			    <li><a href="">All Entries</a></li>
//			    <li class="ykuk-disabled"><a>Hello</a></li>
//			</ul>
//			<ul class="ykuk-switcher ykuk-margin">
//    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
//    <li>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</li>
//    <li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur, sed do eiusmod.</li>
//</ul>
//<span class="ykuk-badge">1</span>
//<ul ykuk-accordion>
//    <li class="ykuk-open">
//        <a class="ykuk-accordion-title" href="#">Quick Entry</a>
//        <div class="ykuk-accordion-content">Some content here</div>
//    </li>
//     <li>
//        <a class="ykuk-accordion-title" href="#">Header 2</a>
//        <div class="ykuk-accordion-content">2 Some content here</div>
//    </li>
//</ul>
//
//			<script>
//			jQuery( document ).ready(function ($) {
//			ykukUIkit.notification(\'My message\');
//			});
//</script>
//<div class="ykuk-inline">
//    <button class="ykuk-button ykuk-button-default" type="button">Hover</button>
//    <div ykuk-dropdown>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
//</div>
//
//<div class="ykuk-inline">
//    <button class="ykuk-button ykuk-button-default" type="button">Click</button>
//    <div ykuk-dropdown="mode: click">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
//</div>
//
//	</div>
//			';


}
add_shortcode( 'wt-beta', 'ws_ls_shortcode_beta' );
