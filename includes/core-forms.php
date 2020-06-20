<?php

defined('ABSPATH') or die('Jog on!');

/*
	Displays either a target or weight form
*/
function ws_ls_form_weight( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [   'css-class-form'        => '',
	                                            'entry-id'              => NULL,
	                                            'entry'                 => NULL,
	                                            'hide-button-cancel'    => true,
	                                            'hide-fields-meta'      => false,
	                                            'hide-fields-photos'    => false,
	                                            'hide-login-message'    => false,
	                                            'hide-titles'           => false,
	                                            'html'                  => '',
	                                            'option-force-today'    => false,
	                                            'is-target-form'        => false,
	                                            'redirect-url'          => '',
	                                            'user-id'               => get_current_user_id() ] );

	// Is the user logged in?
	if ( false === is_user_logged_in() ) {
		// Suppress login prompt?
		return ( false === $arguments[ 'hide-login-message' ] ) ? ws_ls_blockquote_login_prompt() : '';
	}

	// If a ID has been specified, let's try and fetch the entry in question
	if ( false === empty( $arguments[ 'entry-id'] ) &&
	        false === $arguments[ 'is-target-form' ] ) {

		$arguments[ 'entry' ] = ws_ls_entry_get( [ 'user-id' => $arguments[ 'user-id' ], 'id' => $arguments[ 'entry-id'] ] );

		// Did we manage to fetch an entry?
		if ( true === empty( $arguments[ 'entry' ] ) ) {
			return ws_ls_blockquote_error( __( 'There was an issue loading the data for this weight entry.', WE_LS_SLUG ) );
		}
	} else {
		$arguments[ 'entry-id'] = 0;    // If a target form, then blank ID
	}

	// Enqueue relevant JS / CSS
	ws_ls_enqueue_files();

	$arguments  = ws_ls_form_init( $arguments );
	$html       = $arguments[ 'html' ];

	$html .= sprintf( '<form action="%1$s" method="post" class="we-ls-weight-form we-ls-weight-form-validate ws_ls_display_form %2$s"
									id="%3$s" data-is-target-form="%4$s" data-metric-unit="%5$s" %9$s>
									<input type="hidden" name="target-form" value="%4$s" />
									<input type="hidden" name="user-id" value="%6$d" />
									<input type="hidden" name="security" value="%7$s" />
									<input type="hidden" name="entry-id" value="%8$d" />
									<input type="hidden" name="redirect-url" value="%10$s" />
									<input type="hidden" name="form-number" value="%11$d" />',
									esc_url_raw( $arguments[ 'post-url' ] ),
									esc_attr( $arguments[ 'css-class-form' ] ),
									$arguments[ 'form-id' ],
									( true === $arguments[ 'is-target-form' ] ) ? 'true' : 'false',
									esc_attr( $arguments[ 'data-unit' ] ),
									esc_attr( $arguments[ 'user-id' ] ),
									esc_attr( wp_hash( $arguments[ 'user-id' ] ) ),
									$arguments[ 'entry-id' ],
									( true === $arguments[ 'photos-enabled' ]  ) ? 'enctype="multipart/form-data"' : '',
									esc_url_raw( $arguments[ 'redirect-url' ] ),
									$arguments[ 'form-number' ]
	);

	$html .= '<div class="ws-ls-inner-form">';

	// Weight form? Display date field?
	if ( false === $arguments[ 'is-target-form' ] ) {

		$arguments['todays-date'] = ws_ls_date_todays_date( $arguments['user-id'] );

		// Don't bother with date picker and instead force to today's date?
		if ( true === ws_ls_to_bool( $arguments['option-force-today'] ) ) {
			$html .= sprintf( '<input type="hidden" name="we-ls-date" value="%s" />', esc_attr( $arguments['todays-date'] ) );
		} else {

			// Display a date picker field
			$date = ( false === empty( $arguments['entry']['display-date'] ) ) ?
				$arguments['entry']['display-date'] : $arguments['todays-date'];

			$html .= ws_ls_form_field_date( [ 'name'        => 'we-ls-date',
			                                  'value'       => $date,
			                                  'placeholder' => $date,
			                                  'title'       => __( 'Date', WE_LS_SLUG )
			] );
		}

		// Target form?
	} else {

		$target_weight = ws_ls_target_get( $arguments[ 'user-id' ] );

		if ( false === empty( $target_weight[ 'display' ] ) ) {

			$html .= sprintf( '<p class="ws-ls-target">%1$s <strong>%2$s</strong>.</p>',
					( false === is_admin() ) ? __( 'Your target weight is', WE_LS_SLUG ) : __( 'The user\'s target weight is currently', WE_LS_SLUG ),
					esc_html( $target_weight[ 'display' ] )
			);

		}
	}

	// Stones field?
	if ( 'stones_pounds' ===  $arguments[ 'data-unit' ] ) {
		$html .= ws_ls_form_field_number( [     'name'          => 'ws-ls-weight-stones',
		                                        'placeholder'   => __( 'Stones', WE_LS_SLUG ),
												'value'         => ( false === empty( $arguments[ 'entry' ][ 'stones' ] ) ) ? $arguments[ 'entry' ][ 'stones' ] : '' ] );
	}

	// Pounds?
	if ( true === in_array( $arguments[ 'data-unit' ], [ 'stones_pounds', 'pounds_only' ] ) ) {
		$html .= ws_ls_form_field_number( [    'name'          => 'ws-ls-weight-pounds',
		                                       'placeholder'   => __( 'Pounds', WE_LS_SLUG ),
		                                       'max'           => ( 'stones_pounds' ===  $arguments[ 'data-unit' ] ) ? '13.99' : '5000',
		                                       'value' => ( false === empty( $arguments[ 'entry' ][ 'pounds' ] ) ) ? $arguments[ 'entry' ][ 'pounds' ] : '' ] );
	}

	// Kg?
	if ( 'kg' ===  $arguments[ 'data-unit' ] ) {
		$html .= ws_ls_form_field_number( [     'name'          => 'ws-ls-weight-kg',
		                                        'placeholder'   => __( 'Kg', WE_LS_SLUG ),
		                                        'value'         => ( false === empty( $arguments[ 'entry' ][ 'kg' ] ) ) ? $arguments[ 'entry' ][ 'kg' ] : '' ] );
	}

	if ( false === $arguments[ 'is-target-form' ] &&
	        'yes' === get_option( 'ws-ls-allow-user-notes', 'yes' ) ) {

		$html .= ws_ls_form_field_textarea( [   'name'          => 'we-ls-notes',
		                                        'placeholder'   => __( 'Notes', WE_LS_SLUG ),
		                                        'value'         => ( false === empty( $arguments[ 'entry' ][ 'notes' ] ) ) ? $arguments[ 'entry' ][ 'notes' ] : '' ] );
	}

	// Render Meta Fields
	if ( false === $arguments[ 'is-target-form' ] && true === $arguments[ 'meta-enabled' ] ) {
		$html .= ws_ls_meta_fields_form( $arguments[ 'entry' ] );
	}

	$html .= sprintf( '<div class="ws-ls-form-buttons">
						<div>
						    <div class="ws-ls-error-summary ws-ls-hide-if-admin">
						        <p>%1$s</p>
                                <ul></ul>
                            </div>
                            <div class="ws-ls-form-processing-throbber ws-ls-loading ws-ls-hide"></div>
							<input name="submit_button" type="submit" id="we-ls-submit"  tabindex="%2$d" value="%3$s" class="button ws-ls-remove-on-submit" />',
							__( 'Please correct the following:', WE_LS_SLUG ),
							ws_ls_form_tab_index_next(),
							( true === $arguments[ 'is-target-form' ] ) ?  __( 'Set Target', WE_LS_SLUG ) :  __( 'Save Entry', WE_LS_SLUG )
	);

	if ( false === $arguments[ 'is-target-form' ] &&
			false === $arguments[ 'hide-button-cancel' ] &&
	            false === empty( $arguments[ 'redirect-url' ] ) ) {

		$html .= sprintf('&nbsp;<button id="ws-ls-cancel" type="button" tabindex="%1$d" class="ws-ls-cancel-form button ws-ls-remove-on-submit" data-form-id="%2$s">%3$s</button>',
			ws_ls_form_tab_index_next(),
			$arguments[ 'form-id '],
			__( 'Cancel', WE_LS_SLUG )
		);
	}


	// If a target form, display "Clear Target" button
	if ( true  === $arguments[ 'is-target-form' ] &&
			false === is_admin() ){
		$html .= sprintf('&nbsp;<button name="ws-ls-clear-target" id="ws-ls-clear-target" type="button" tabindex="%1$d" class="ws-ls-clear-target button ws-ls-remove-on-submit" >%2$s</button>',
			ws_ls_form_tab_index_next(),
			__( 'Clear Target', WE_LS_SLUG )
		);
	}

	$html .= '			</div>
					</div>
				</div>
			</form>';

	return $html;
}


/**
 * Initialise the form config
 * @param array $arguments
 *
 * @return array
 */
function ws_ls_form_init( $arguments = [] ) {

	$arguments[ 'form-id' ]     = ws_ls_component_id();
	$arguments[ 'form-number' ] = ws_ls_form_number_next();
	$arguments[ 'data-unit' ]   = ws_ls_get_config('WE_LS_DATA_UNITS', $arguments[ 'user-id' ] );

	global $save_response;

	// Has this form been previously submitted?
	if ( false === empty( $save_response ) &&
	     $arguments[ 'form-number'] === $save_response['form_number'] ){
		$arguments[ 'html' ] .=  ( true === $save_response[ 'error' ] ) ? $save_response[ 'message' ] : ws_ls_display_blockquote( __( 'Your entry has been successfully saved.', WE_LS_SLUG ) );
	}

	// Main title for form
	if ( true === $arguments[ 'is-target-form' ] ) {
		$title = __( 'Target weight', WE_LS_SLUG );
	} else if ( false === empty( $arguments[ 'entry' ] ) ) {
		$title = __( 'Edit an existing entry', WE_LS_SLUG );
	} else {
		$title = __( 'Add a new weight entry', WE_LS_SLUG );
	}

	$arguments[ 'html' ] .= ws_ls_form_title( $title, $arguments[ 'hide-titles' ] );

	// Allow others to determine where form is posted too
	$arguments[ 'post-url' ] = apply_filters( 'wlt_form_url', get_permalink() );

	// Are meta fields enabled for this form?
	$arguments[ 'meta-enabled' ]  = ( false === $arguments[ 'is-target-form' ] &&
	                                  false === $arguments[ 'hide-fields-meta' ] &&
	                                  true === ws_ls_meta_fields_is_enabled() &&
	                                  ws_ls_meta_fields_number_of_enabled() > 0 );


	// Are photo fields enabled for this form?
	$arguments[ 'photos-enabled' ] = ( false === $arguments[ 'hide-fields-photos' ] &&
	                                   true === $arguments[ 'meta-enabled' ] &&
	                                   true === ws_ls_meta_fields_photo_any_enabled( true ) );

	return $arguments;
}

/**
 * Display a date field
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_form_field_date( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'date',
												'name'                  => '',
	                                            'value'                 => NULL,
												'placeholder'           => NULL,
	                                            'show-label'            => false,
	                                            'title'                 => '',
	                                            'css-class'             => 'we-ls-datepicker',
	                                            'size'                  => 22,
	                                            'trailing-html'         => '' ]);

	$html = sprintf( '<div id="%1$s-row" class="ws-ls-form-row">', $arguments[ 'name' ] );

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="yk-mt__label %3$s">%2$s</label>', $arguments[ 'name' ], $arguments[ 'title' ], $arguments[ 'css-class' ] );
	}


	$html .= sprintf( '<input type="text" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" size="%6$d" class="%7$s" />',
			$arguments[ 'name' ],
			ws_ls_component_id(),
			ws_ls_form_tab_index_next(),
			esc_attr( $arguments[ 'value' ] ),
			esc_attr( $arguments[ 'placeholder' ] ),
			$arguments[ 'size' ],
			$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ]
	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	return $html . '</div>';
}

/**
 * HTML field for textarea
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_form_field_textarea( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'date',
												'name'                  => '',
												'value'                 => NULL,
												'placeholder'           => __( 'Notes', WE_LS_SLUG ),
												'show-label'            => false,
												'title'                 => '',
												'css-class'             => 'we-ls-textarea',
												'trailing-html'         => '',
												'cols'                  => 39,
												'rows'                  => 4
	]);

	$html = sprintf( '<div id="%1$s-row" class="ws-ls-form-row">', $arguments[ 'name' ] );

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="yk-mt__label %3$s">%2$s</label>', $arguments[ 'name' ], $arguments[ 'title' ], $arguments[ 'css-class' ] );
	}

	$html .= sprintf( '<textarea name="%1$s" id="%1$s" tabindex="%2$d" placeholder="%3$s" cols="%4$d" rows="%5$d" class="%6$s" >%7$s</textarea>',
		$arguments[ 'name' ],
		ws_ls_form_tab_index_next(),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'cols' ],
		$arguments[ 'rows' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		esc_textarea( $arguments[ 'value' ] )

	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	return $html . '</div>';
}

/**
 * Display a number field
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_form_field_number( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [
												'type'                  => 'date',
												'name'                  => '',
												'value'                 => NULL,
												'placeholder'           => NULL,
												'show-label'            => false,
												'title'                 => '',
												'css-class'             => 'we-ls-number',
												'size'                  => 11,
												'trailing-html'         => '',
												'min'                   => 0,
												'max'                   => 9999,
												'step'                  => 'any'
		]);

	$html = sprintf( '<div id="%1$s-row" class="ws-ls-form-row">', $arguments[ 'name' ] );

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="yk-mt__label %3$s">%2$s</label>', $arguments[ 'name' ], $arguments[ 'title' ], $arguments[ 'css-class' ] );
	}


	$html .= sprintf( '<input type="number" name="%1$s" step="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" size="%6$d" class="%7$s" min="%8$s" max="%9$s" />',
		$arguments[ 'name' ],
		$arguments[ 'step' ],
		ws_ls_form_tab_index_next(),
		esc_attr( $arguments[ 'value' ] ),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'size' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		$arguments[ 'min' ],
		$arguments[ 'max' ]
	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	return $html . '</div>';
}
/**
 * Format form title
 * @param $title
 * @param $hide
 *
 * @return string
 */
function ws_ls_form_title( $title, $hide ) {
	return ( false === $hide ) ?
		sprintf( '<h3 class="ws_ls_title">%s</h3>', esc_html( $title ) ) :
			'';
}
/**
 * Keep track of the current tab index and increment
 * @return int
 */
function ws_ls_form_tab_index_next() {

	global $ws_ls_tab_index;

	$current_index = $ws_ls_tab_index;
	$ws_ls_tab_index++;

	return $current_index;
}

/**
 * Keep track of the current form number and increment
 * @return mixed
 */
function ws_ls_form_number_next() {

	global $form_number;

	if ( true === empty( $form_number ) ) {
		$form_number = 1;
	}

	$current_index = $form_number;
	$form_number++;

	return $current_index;
}
