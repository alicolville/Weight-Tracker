<?php

defined('ABSPATH') or die('Jog on!');

$ws_ls_form_position = 0;

/*
	Displays either a target or weight form
*/
function ws_ls_form_weight( $arguments = [] ) {

	global $ws_ls_form_position; $ws_ls_form_position++;

	$arguments = wp_parse_args( $arguments, [   'css-class-form'        => '',
												'custom-field-groups'   => '',      // If specified, only show custom fields that are within these groups
												'custom-field-slugs'    => '',      // If specified, only show the custom fields that are specified
	                                            'entry-id'              => ws_ls_querystring_value( 'load-entry', NULL ),
	                                            'entry'                 => NULL,
	                                            'hide-button-cancel'    => true,
	                                            'hide-fields-meta'      => false,
	                                            'hide-fields-photos'    => false,
	                                            'hide-login-message'    => false,
	                                            'hide-confirmation'     => false,
	                                            'hide-notes'            => ws_ls_setting_hide_notes(),
	                                            'hide-title'            => false,           // Hide main form title
	                                            'hide-titles'           => false,
	                                            'html'                  => '',
	                                            'load-placeholders'     => ws_ls_setting_populate_placeholders_with_previous_values(), // Should we set previous values as form placeholders?	                                            'option-force-today'    => false,
	                                            'kiosk-mode'            => false,
	                                            'option-force-today'    => false,
	                                            'option-tiny-mce-notes' => is_admin(),
	                                            'title'                 => '',
	                                            'type'                  => 'weight',
	                                            'weight-mandatory'		=> true,
	                                            'redirect-url'          => '',
	                                            'user-id'               => get_current_user_id(),
												'uikit'                 => false,
	] );

	// Is the user logged in?
	if ( false === is_user_logged_in() ) {
		// Suppress login prompt?
		return ( false === $arguments[ 'hide-login-message' ] ) ? ws_ls_blockquote_login_prompt() : '';
	}

	// If a ID has been specified, let's try and fetch the entry in question
	if ( false === empty( $arguments[ 'entry-id'] ) &&
	        'target' !== $arguments[ 'type' ] ) {

		$arguments[ 'entry' ] = ws_ls_entry_get( [ 'user-id' => $arguments[ 'user-id' ], 'id' => $arguments[ 'entry-id'], 'meta' => ( false === $arguments[ 'hide-fields-meta' ] ) ] );

		// Did we manage to fetch an entry?
		if ( true === empty( $arguments[ 'entry' ] ) ) {
			return ws_ls_blockquote_error( __( 'The selected entry no longer exists.', WE_LS_SLUG ) );
		}

		$arguments[ 'hide-button-cancel' ] = false;
	} else {
		$arguments[ 'entry-id'] = 0;    // If a target form, then blank ID
	}

	// Enqueue relevant JS / CSS
	ws_ls_enqueue_files();

	$arguments[ 'form-key' ] 	= sprintf( 'ws-ls-form-%d', $ws_ls_form_position );	// We need to generate a semi consistent form key that will hopefully remain the same between page loads. This is for the JS focus
	$arguments  				= ws_ls_form_init( $arguments );
	$html      					= $arguments[ 'html' ];

	$html .= sprintf( '		<form action="%1$s" method="post" class="%12$s%14$s %2$s" id="%3$s" data-form-type="%4$s" data-metric-unit="%5$s" %9$s data-form-key="%13$s" ">
									<input type="hidden" name="type" value="%4$s" />
									<input type="hidden" name="user-id" value="%6$d" />
									<input type="hidden" name="security" value="%7$s" />
									<input type="hidden" name="entry-id" value="%8$d" />
									<input type="hidden" name="redirect-url" id="redirect-url" value="%10$s" />
									<input type="hidden" name="form-number" value="%11$d" />',
									esc_url_raw( $arguments[ 'post-url' ] ),
									esc_attr( $arguments[ 'css-class-form' ] ),
									$arguments[ 'form-id' ],
									esc_attr( $arguments[ 'type' ] ),
									esc_attr( $arguments[ 'data-unit' ] ),
									esc_attr( $arguments[ 'user-id' ] ),
									esc_attr( wp_hash( $arguments[ 'user-id' ] ) ),
									( NULL === ws_ls_querystring_value( 'load-entry', NULL ) ) ? $arguments[ 'entry-id' ] : '',
									( true === $arguments[ 'photos-enabled' ]  ) ? 'enctype="multipart/form-data"' : '',
									esc_url_raw( $arguments[ 'redirect-url' ] ),
									$arguments[ 'form-number' ],
									( 'weight' === $arguments[ 'type' ] && true === ws_ls_to_bool( $arguments[ 'weight-mandatory' ] ) ) ? 'weight-required ' : '',
									$arguments[ 'form-key' ],
									( false === $arguments[ 'uikit' ] ) ? 'we-ls-weight-form we-ls-weight-form-validate ws_ls_display_form' : ''
	);

	/*
	 *  If the form is just loading for the first time, with no entry or entry id passed, let's assume it's setting it self up for today's date.
	 *  If so, let's check if already an entry for this date and load the data if applicable!
	 */

	if ( true === in_array( $arguments[ 'type' ], [ 'custom-fields', 'weight' ] ) &&
	        ( true === empty( $arguments[ 'entry-id' ] ) && empty( $arguments[ 'entry' ] ) )
				&& WS_LS_IS_PRO && ws_ls_option_to_bool( 'ws-ls-populate-form-with-values-on-date', 'yes' )
	) {

		$date           = ws_ls_convert_date_to_iso( $arguments[ 'todays-date' ]);
		$existing_id    = ws_ls_db_entry_for_date( $arguments[ 'user-id' ], $date );

		if ( false === empty( $existing_id ) ) {

			$url = add_query_arg( 'load-entry', (int) $existing_id, $arguments[ 'post-url' ] );

			$url .= '#' . $arguments[ 'form-key' ];

			$html .= sprintf( '<p class="ws-ls-previous-entry-noticews-ls-previous-entry-notice"><strong>%1$s:</strong> %2$s <a href="%3$s">%4$s</a></p>',
								__( 'Note', WE_LS_SLUG ),
								__( 'Data has previously been entered for this date and will be replaced if this form is submitted.', WE_LS_SLUG ),
								esc_url( $url ),
								__( 'Load the existing data.', WE_LS_SLUG )
			);

		}
	}

	$html .= sprintf( '<div class="ws-ls-inner-form">
							<div class="ws-ls-error-summary">
						        <p>%1$s</p>
                                <ul></ul>
                            </div>', __( 'Please correct the following:', WE_LS_SLUG ) );

	// Weight / Custom fields form? Display date field?
	if ( true === in_array( $arguments[ 'type' ], [ 'custom-fields', 'weight' ] ) ) {

		// Don't bother with date picker and instead force to today's date?
		if ( true === ws_ls_to_bool( $arguments['option-force-today'] ) ) {
			$html .= sprintf( '<input type="hidden" name="we-ls-date" value="%s" />', esc_attr( $arguments['todays-date'] ) );
		} else {

			// Display a date picker field
			$date = ( false === empty( $arguments['entry']['display-date'] ) ) ?
				$arguments['entry']['display-date'] : $arguments['todays-date'];

			$html .= ws_ls_form_field_date( [   'name'          => 'we-ls-date',
			                                    'value'         => $date,
			                                    'placeholder'   => $date,
			                                    'title'         => __( 'Date', WE_LS_SLUG ),
												'form-id'       => $arguments[ 'form-id' ],
												'uikit'         => $arguments[ 'uikit' ],
												'css-class'     => 'we-ls-datepicker' . ( true === $arguments[ 'uikit' ] ? ' ykuk-width-1-1' : '' )
			] );
		}
	}

	// Target form?
	if ( 'target' === $arguments[ 'type' ] ) {

		$target_weight = ws_ls_target_get( $arguments[ 'user-id' ] );

		if ( false === empty( $target_weight[ 'display' ] ) ) {

			$html .= sprintf( '<p class="ws-ls-target">%1$s <strong>%2$s</strong>.</p>',
					( false === is_admin() ) ? __( 'Your target weight is', WE_LS_SLUG ) : __( 'The user\'s target weight is currently', WE_LS_SLUG ),
					esc_html( $target_weight[ 'display' ] )
			);

		}
	}

	$placeholders = [   'stones'    => '',
						'pounds'    => '',
						'kg'        => '',
						'meta'      => []
	];

	if (  'target' !== $arguments[ 'type' ] &&
	        true === $arguments[ 'load-placeholders' ] ) {

		$latest_entry = ( true === empty( $arguments[ 'entry' ] ) ) ?
							ws_ls_entry_get_latest( $arguments ) :
								$arguments[ 'entry' ];

		if ( false === empty( $latest_entry ) ) {
			if ( false === empty( $latest_entry[ 'kg' ] ) ) {
				$placeholders = ws_ls_weight_display( $latest_entry[ 'kg' ] );
			}
			if ( false === empty( $latest_entry[ 'meta' ] ) ) {
				$placeholders[ 'meta' ] = $latest_entry[ 'meta' ];
			}
		}
	}

	if ( true === in_array( $arguments[ 'type' ], [ 'target', 'weight' ] ) ) {

		// Stones field?
		if ( 'stones_pounds' ===  $arguments[ 'data-unit' ] ) {
			$html .= ws_ls_form_field_number( [     'name'          => 'ws-ls-weight-stones',
			                                        'placeholder'   => false === empty( $placeholders[ 'stones' ] ) ? $placeholders[ 'stones' ] . __( 'st', WE_LS_SLUG ) : __( 'st', WE_LS_SLUG ),
			                                        'css-class'     => 'ykuk-input ykuk-width-1-2 ykuk-margin',
			                                        'uikit'         => $arguments[ 'uikit' ],
			                                        'value'         => ( false === empty( $arguments[ 'entry' ][ 'stones' ] ) ) ? $arguments[ 'entry' ][ 'stones' ] : '' ] );
		}

		// Pounds?
		if ( true === in_array( $arguments[ 'data-unit' ], [ 'stones_pounds', 'pounds_only' ] ) ) {
			$html .= ws_ls_form_field_number( [    'name'          => 'ws-ls-weight-pounds',
			                                       'placeholder'   =>( false === empty( $placeholders[ 'pounds' ] ) ) ? $placeholders[ 'pounds' ] . __( 'lb', WE_LS_SLUG ) : __( 'lb', WE_LS_SLUG ),
			                                       'max'           => ( 'stones_pounds' ===  $arguments[ 'data-unit' ] ) ? '13.99' : '5000',
			                                       'css-class'     => 'ykuk-input ykuk-width-1-2 ykuk-margin',
			                                       'uikit'         => $arguments[ 'uikit' ],
			                                       'value' => ( true === isset( $arguments[ 'entry' ][ 'pounds' ] ) ) ? $arguments[ 'entry' ][ 'pounds' ] : '' ] );
		}

		// Kg
		if ( 'kg' ===  $arguments[ 'data-unit' ] ) {
			$html .= ws_ls_form_field_number( [     'name'          => 'ws-ls-weight-kg',
			                                        'placeholder'   => $placeholders[ 'kg' ] . __( 'kg', WE_LS_SLUG ),
			                                        'value'         => ( false === empty( $arguments[ 'entry' ][ 'kg' ] ) ) ? $arguments[ 'entry' ][ 'kg' ] : '' ] );
		}
	}

	if ( 'weight' === $arguments[ 'type' ] &&
	        false === $arguments[ 'hide-notes' ] ) {

		$html .= ws_ls_form_field_textarea( [   'name'          => 'we-ls-notes',
		                                        'placeholder'   => __( 'Notes', WE_LS_SLUG ),
		                                        'value'         => ( false === empty( $arguments[ 'entry' ][ 'notes' ] ) ) ? $arguments[ 'entry' ][ 'notes' ] : '' ] );
	}

	// Render Meta Fields
	if ( true === $arguments[ 'meta-enabled' ] && true !== $arguments[ 'hide-fields-meta' ] ) {
		$html .= ws_ls_meta_fields_form( $arguments, $placeholders );
	}

	$html .= sprintf( '<div class="ws-ls-form-buttons">
						<div>
							<div class="ws-ls-form-processing-throbber ws-ls-loading ws-ls-hide"></div>
							<button name="submit_button" type="submit" tabindex="%1$d" class="button ws-ls-remove-on-submit ykuk-button ykuk-button-default" for="%3$s" >%2$s</button>',
							ws_ls_form_tab_index_next(),
							( 'target' === $arguments[ 'type' ] ) ?  __( 'Set Target', WE_LS_SLUG ) :  __( 'Save Entry', WE_LS_SLUG ),
							$arguments[ 'form-id' ]
	);

	if ( 'target' !== $arguments[ 'type' ] &&
			false === $arguments[ 'hide-button-cancel' ] &&
	            false === empty( $arguments[ 'redirect-url' ] ) ) {

		$html .= sprintf('&nbsp;<button type="button" tabindex="%1$d" class="ws-ls-cancel-form button ws-ls-remove-on-submit ykuk-button ykuk-button-default" data-form-id="%2$s">%3$s</button>',
			ws_ls_form_tab_index_next(),
			$arguments[ 'form-id' ],
			__( 'Cancel', WE_LS_SLUG )
		);
	}


	// If a target form, display "Clear Target" button
	if ( 'target' === $arguments[ 'type' ] &&
			false === is_admin() &&
				false === empty( ws_ls_target_get( $arguments[ 'user-id' ] ) ) ){
		$html .= sprintf('&nbsp;<button type="button" tabindex="%1$d" class="ws-ls-clear-target button ws-ls-remove-on-submit ykuk-button ykuk-button-default" data-user-id="%3$d" >%2$s</button>',
			ws_ls_form_tab_index_next(),
			__( 'Clear Target', WE_LS_SLUG ),
			$arguments[ 'user-id' ]
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
function  ws_ls_form_init( $arguments = [] ) {

	$user_id                    = ( false === $arguments[ 'kiosk-mode' ] ) ? $arguments[ 'user-id' ] : get_current_user_id();
	$arguments[ 'form-id' ]     = ws_ls_component_id();
	$arguments[ 'form-number' ] = ws_ls_form_number_next();
	$arguments[ 'data-unit' ]   = ws_ls_setting( 'weight-unit', $user_id );
	$arguments[ 'todays-date' ] = ws_ls_date_todays_date( $user_id );

	global $save_response;

	$arguments[ 'html' ] .= sprintf( '<a name="%s"></a>', $arguments[ 'form-key' ] );

	// Has this form been previously submitted?
	if ( false === empty( $save_response ) &&
	        false === $arguments[ 'hide-confirmation' ] &&
	     $arguments[ 'form-number'] === $save_response['form_number'] ){
		$arguments[ 'html' ] .=  ( true === $save_response[ 'error' ] ) ? $save_response[ 'message' ] : ws_ls_display_blockquote( __( 'Your entry has been successfully saved.', WE_LS_SLUG ) );
	}

	// Main title for form
	$title = $arguments[ 'title' ];

	if ( true === empty( $title ) ) {
		if ( 'target' === $arguments[ 'type' ] ) {
			$title = __( 'Target weight', WE_LS_SLUG );
		} elseif ( 'weight' === $arguments[ 'type' ] ) {
			if ( false === empty( $arguments[ 'entry' ] ) ) {
				$title = __( 'Edit an existing entry', WE_LS_SLUG );
			} else {
				$title = __( 'Add a new weight entry', WE_LS_SLUG );
			}
		} elseif ( 'custom-fields' === $arguments[ 'type' ] ) {
			$title = __( 'Complete the following form', WE_LS_SLUG );
		}
	}

	if ( false === empty( $title ) && false === $arguments[ 'hide-title' ] ) {
		$arguments[ 'html' ] .= ws_ls_form_title( $title, $arguments[ 'hide-titles' ] );
	}

	// Allow others to determine where form is posted too
	$arguments[ 'post-url' ] = apply_filters( 'wlt_form_url', ws_ls_get_url() );

	$arguments[ 'post-url' ] = remove_query_arg('load-entry', $arguments[ 'post-url' ] );
	$arguments[ 'post-url' ] = remove_query_arg('user-delete-all', $arguments[ 'post-url' ] );

	// Are meta fields enabled for this form?
	$arguments[ 'meta-enabled' ]  = ( true === in_array( $arguments[ 'type' ], [ 'custom-fields', 'weight' ] ) &&
	                                  false === $arguments[ 'hide-fields-meta' ] &&
	                                  true === ws_ls_meta_fields_is_enabled() &&
	                                  ws_ls_meta_fields_number_of_enabled() > 0 );


	// Are photo fields enabled for this form?
	$arguments[ 'photos-enabled' ] = ( false === $arguments[ 'hide-fields-photos' ] &&
	                                   true === $arguments[ 'meta-enabled' ] &&
	                                   true === ws_ls_meta_fields_photo_any_enabled( false ) );

	// Custom field filtering?
	$arguments[ 'custom-field-groups' ] = ws_ls_meta_fields_groups_slugs_to_ids( $arguments[ 'custom-field-groups' ] );
	$arguments[ 'custom-field-slugs' ]  = ws_ls_meta_fields_slugs_to_ids( $arguments[ 'custom-field-slugs' ] );

	return $arguments;
}

/**
 * Display a text field
 * @param array $arguments
 * @return string
 */
function ws_ls_form_field_text( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'text',
												'id'                    => ws_ls_component_id(),
												'name'                  => '',
												'value'                 => NULL,
												'placeholder'           => NULL,
												'show-label'            => false,
												'title'                 => '',
												'css-class'             => '',
												'size'                  => 22,
												'trailing-html'         => '',
												'include-div'           => true,
												'required' 				=> false ]);
	$html = '';

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="w1s-ls-form-row">', $arguments[ 'name' ] );
	}

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s">%2$s</label>', $arguments[ 'id' ], $arguments[ 'title' ]);
	}

	$html .= sprintf( '<input type="text" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" size="%6$d" class="ykui-input %7$s" %8$s />',
		$arguments[ 'name' ],
		esc_attr( $arguments[ 'id' ] ),
		ws_ls_form_tab_index_next(),
		esc_attr( $arguments[ 'value' ] ),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'size' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		( true === $arguments[ 'required' ] ) ? 'required="required"' : ''
	);

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Display a date field
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_form_field_date( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'date',
												'id'                    => ws_ls_component_id(),
												'name'                  => '',
	                                            'form-id'               => NULL,
	                                            'value'                 => NULL,
												'placeholder'           => NULL,
	                                            'show-label'            => false,
	                                            'title'                 => '',
	                                            'css-class'             => 'we-ls-datepicker',
	                                            'css-class-label'       => '',
												'css-class-row'         => '',
	                                            'size'                  => 22,
	                                            'trailing-html'         => '',
												'include-div'           => true,
												'uikit'                 => false,
												'icon'                  => 'calendar'
	]);
	$html = '';

	$display_wrapping_div = ( true === $arguments[ 'include-div' ] || true === $arguments[ 'uikit' ] );

	if ( true === $display_wrapping_div ) {
		$html .= sprintf( '<div id="%1$s-row" class="ws-ls-form-row%2$s">', $arguments[ 'name' ], ( false === empty( $arguments[ 'css-class-row' ] ) ) ? ' ' . esc_attr( $arguments[ 'css-class-row' ] ) : '' );
	}

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="%3$s">%2$s</label>',
							$arguments[ 'id' ],
							esc_html( $arguments[ 'title' ] ),
							esc_attr( $arguments[ 'css-class-label' ] ) );
	}

	$component = sprintf( '<input type="text" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" size="%6$d" class="ykuk-input %7$s" data-form-id="%8$s" />',
							$arguments[ 'name' ],
							esc_attr( $arguments[ 'id' ] ),
							ws_ls_form_tab_index_next(),
							esc_attr( $arguments[ 'value' ] ),
							esc_attr( $arguments[ 'placeholder' ] ),
							$arguments[ 'size' ],
							$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
							esc_attr( $arguments[ 'form-id' ] )
	);

	$html .= ws_ls_form_field_add_icon( $component, $arguments[ 'icon' ] );

	if ( false === empty( $arguments[ 'trailing-html' ] ) ) {
		$html .= $arguments[ 'trailing-html' ];
	}

	if ( true === $display_wrapping_div ) {
		$html .= '</div>';
	}

	return $html;
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
												'css-class-label'       => 'we-ls-textarea',
												'css-class-row'         => 'ws-ls-form-row',
												'trailing-html'         => '',
												'cols'                  => 39,
												'rows'                  => 4,
												'mandatory'				=> false,
												'disabled'				=> false
	]);

	$html = sprintf( '<div id="%1$s-row" class="%2$s">', $arguments[ 'name' ], $arguments[ 'css-class-row' ] );

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="ykuk-form-label %3$s">%2$s</label>', $arguments[ 'name' ], $arguments[ 'title' ], $arguments[ 'css-class-label' ] );
	}

	$html .= sprintf( '<textarea name="%1$s" id="%1$s" tabindex="%2$d" placeholder="%3$s" cols="%4$d" rows="%5$d" class="%6$s" %8$s data-msg="%9$s \'%10$s\'." %11$s>%7$s</textarea>',
		$arguments[ 'name' ],
		ws_ls_form_tab_index_next(),
		esc_attr( $arguments[ 'placeholder' ] ),
		$arguments[ 'cols' ],
		$arguments[ 'rows' ],
		$arguments[ 'name' ] . ' ' . $arguments[ 'css-class' ],
		esc_textarea( $arguments[ 'value' ] ),
		( true === $arguments[ 'mandatory' ] ? 'required' : ''),
		__( 'Please select a value for'),
		esc_attr( $arguments[ 'title' ] ),
		( true === $arguments[ 'disabled' ] ) ? ' disabled="disabled"' : ''
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
												'step'                  => 'any',
												'uikit'                 => false
	]);

	$html = '';

	if ( false === $arguments[ 'uikit' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="ws-ls-form-row">', $arguments[ 'name' ] );
	}

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="ykuk-form-label %3$s">%2$s</label>', $arguments[ 'name' ], $arguments[ 'title' ], $arguments[ 'css-class' ] );
	}

	$html .= sprintf( '<input type="number" name="%1$s" step="%2$s" tabindex="%3$d" value="%4$s" placeholder="%5$s" size="%6$d" class="ykuk-input %7$s" min="%8$s" max="%9$s" />',
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

	if ( false === $arguments[ 'uikit' ] ) {
		$html .= '</div>';
	}
	return $html;
}

/**
 * Render a check box field
 * @param array $arguments
 * @return string
 */
function ws_ls_form_field_checkbox( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [	'type'                  => 'text',
												'id'                    => ws_ls_component_id(),
												'name'                  => '',
												'value'                 => NULL,
												'checked'               => false,
												'show-label'            => false,
												'css-class'             => '',
												'css-class-row'         => 'ws-ls-form-row',
												'include-div'           => true,
												'required' 				=> false ]);
	$html = '';

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= sprintf( '<div id="%1$s-row" class="%2$s">', esc_attr( $arguments[ 'name' ] ), esc_attr( $arguments[ 'css-class-row' ] ) );
	}

	$html .= sprintf( '<input type="checkbox" name="%1$s" id="%2$s" tabindex="%3$d" value="%4$s" class="%5$s" %6$s />',
		$arguments[ 'name' ],
		esc_attr( $arguments[ 'id' ] ),
		ws_ls_form_tab_index_next(),
		esc_attr( $arguments[ 'value' ] ),
		$arguments[ 'css-class' ],
		true === $arguments[ 'checked' ] ? ' checked="checked" ' : ''
	);

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf( '<label for="%1$s" class="">%2$s</label>', $arguments[ 'id' ], $arguments[ 'title' ]);
	}

	if ( true === $arguments[ 'include-div' ] ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Render a <select> for the given key / value array
 *
 * @param $arguments
 *
 * @return string
 */
function ws_ls_form_field_select( $arguments ) {

	$arguments = wp_parse_args( $arguments, [	'key'                           => '',
												'label'                         => '',
												'values'                        => [],
												'empty-option'                  => false,
												'selected'                      => NULL,
												'show-label'                    => true,
												'css-class'                     => '',
												'css-class-row'                 => 'ws-ls-form-row',
												'css-class-title'               => '',
												'required'                      => false,
												'js-on-change'                  => '',
												'reload-page-on-select'         => false,
												'reload-page-on-select-qs-key'  => '',
												'include-div'                   => false,
												'uikit'                         => false
	]);

	$html           = '';
	$label_id       = ws_ls_component_id();
	$include_row    = ( true === $arguments[ 'include-div' ] || true === $arguments[ 'uikit' ] ) ;

	if ( $include_row ) {
		$html .= sprintf( '<div id="%1$s-row" class="%2$s ykuk-width-1-1">', $arguments[ 'key' ], esc_attr( $arguments[ 'css-class-row' ] ) );
	}

	if ( true === $arguments[ 'show-label' ] ) {
		$html .= sprintf(   '<label for="%1$s" class="ykuk-form-label %3$s">%2$s</label>',
							esc_attr( $arguments[ 'key' ] ),
							esc_attr( $arguments[ 'label' ] ),
							esc_attr( $arguments[ 'css-class-title' ] )
		);
	}

	$html .= sprintf( '<select id="%1$s" name="%1$s" tabindex="%2$d" class="%3$s ykuk-select ykuk-padding-remove-right" %4$s %5$s data-msg="%6$s \'%7$s\'.">',
		esc_attr( $arguments[ 'key' ] ),
		ws_ls_form_tab_index_next(),
		esc_attr( $arguments[ 'css-class' ] ),
		( true === $arguments[ 'required' ] ) ? ' required="required" ' : '',
		( false === empty( $arguments[ 'js-on-change' ] ) ) ? sprintf( ' onchange="%s"', $arguments[ 'js-on-change' ] ) : '',
		__( 'Please select a value for'),
		esc_attr( $arguments[ 'label' ] ),
		$label_id
	);

	if ( true === $arguments[ 'reload-page-on-select' ] ) {

		$url = remove_query_arg( [ $arguments[ 'reload-page-on-select-qs-key' ], 'ws-ls-i' ], ws_ls_get_url() );
		$url = add_query_arg( 'ws-ls-i', 'y', ws_ls_get_url() );

		$js = sprintf( '( function( $ ) { $("#%1$s").change(function(){ window.location.replace( "%2$s&%3$s=" + $(this).val() ) }) } )( jQuery );',
						esc_attr( $arguments[ 'key' ] ),
						$url,
						$arguments[ 'reload-page-on-select-qs-key' ]
		);

		wp_add_inline_script(	'yk-uikit', $js);
	}

	if ( true === $arguments[ 'empty-option' ] ) {
		$html .= '<option value=""></option>';
	}

	foreach ( $arguments[ 'values' ] as $id => $value ) {
		$html .= sprintf('<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $id ),
			selected( $arguments[ 'selected' ], $id, false ),
			esc_html( $value )
		);
	}

	$html .= '</select>';

	if ( $include_row ) {
		$html .= '</div>';
	}

	return $html;
}

/**
 * Add icon to field
 *
 * @param $html
 * @param string $icon
 * @param string $css_class
 *
 * @return string
 */
function ws_ls_form_field_add_icon( $html, $icon = '', $css_class = 'ykuk-width-1-1' ) {

	if ( true === empty( $icon ) ) {
		return $html;
	}

	return sprintf( '<div class="ykuk-inline %s">
			            <span class="ykuk-form-icon" ykuk-icon="icon: %s"></span>
			            %s
			        </div>',
		$css_class,
		$icon,
		$html
	);
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

	return $ws_ls_tab_index++;
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
