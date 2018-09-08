<?php

    defined('ABSPATH') or die('Jog on!');

    /**
     * Returns true if Meta Fields fully enabled (i.e. not trial mode)
     *
     * @return bool
     */
    function ws_ls_meta_fields_is_enabled() {
        return WS_LS_IS_PRO;
    }

    /**
     * Return base URL for meta fields
     * @return string
     */
    function ws_ls_meta_fields_base_url() {
        return admin_url( 'admin.php?page=ws-ls-meta-fields');
    }

    /**
     * Return an array of field types
     *
     * @return array
     */
    function ws_ls_meta_fields_types() {

        $fields = [
            0 => __('Number', WE_LS_SLUG),
            1 => __('Text', WE_LS_SLUG),
            2 => __('Yes', WE_LS_SLUG) . ' / ' . __('No', WE_LS_SLUG)
        ];

	    if ( true === WS_LS_IS_PRO_PLUS ) {
		    $fields[ 3 ] = __('Photo', WE_LS_SLUG);
	    } else {
            $fields[ 3 ] = __('Photo (Pro Plus Only)', WE_LS_SLUG);
        }

	    return $fields;
    }

    /**
     * Return the text value of a field type ID
     *
     * @param $id
     * @return mixed|string
     */
    function ws_ls_meta_fields_types_get_string( $id ) {

        $types = ws_ls_meta_fields_types();

        return ( false === empty( $types[ $id ] ) ) ? $types[ $id ] : '';
    }

    /**
     * Return the text value of enabled value
     *
     * @param $value
     * @return mixed|string
     */
    function ws_ls_meta_fields_enabled_get_string( $value ) {

        return ( 2 == $value ) ? __('Yes', WE_LS_SLUG) : __('No', WE_LS_SLUG);
    }

    /**
     * Return a count of enabled meta fields
     *
     * @return int
     */
    function ws_ls_meta_fields_number_of_enabled() {

        return count( ws_ls_meta_fields_enabled() );
    }

    /**
     * Return the value for a given entry / meta field
     *
     * @param $entry_id
     * @param $meta_field_id
     * @return null
     */
    function ws_ls_meta_fields_get_value_for_entry( $entry_id, $meta_field_id ) {

        if ( false === empty( $entry_id ) ) {

            $data_for_entry = ws_ls_meta( $entry_id );

            foreach ( $data_for_entry as $entry ) {

                if ( intval( $meta_field_id ) === intval( $entry[ 'meta_field_id' ] ) ) {
                    return $entry[ 'value' ];
                }

            }

        }

        return NULL;
    }


    /**
     * Fetch all HTML keys for enabled meta fields
     *
     * @return array
     */
    function ws_ls_meta_fields_form_field_ids() {

        $ids = [];

        foreach ( ws_ls_meta_fields_enabled() as $field ) {
            $ids[] = ws_ls_meta_fields_form_field_generate_id( $field['id'] );
        }

        return $ids;
    }

    /**
     *Generate field key
     *
     * @param $id
     * @return string
     */
    function ws_ls_meta_fields_form_field_generate_id( $id ) {
        return ( false === empty( $id ) ) ? 'ws-ls-meta-field-' . intval( $id ) : '';
    }

    /**
     *
     * Get Meta Fields for entry (used in table display)
     *
     * @param $entry_id
     * @return array
     */
    function ws_ls_meta_fields_for_entry_display( $entry_id ) {

        $return = [];

        $data = ws_ls_meta( $entry_id );

        foreach ( $data as $field ) {
            $return[ $field['meta_field_id'] ] = $field;
        }

        return $return;

    }

    /**
     * Format meta data for display
     *
     * @param $value
     * @param int $type
     * @return int
     */
    function ws_ls_fields_display_field_value( $value, $meta_field_id ) {

        $meta_field = ws_ls_meta_fields_get_by_id( $meta_field_id );

        if ( false === empty( $meta_field['field_type'] ) ) {

            $meta_field['field_type'] = intval( $meta_field['field_type'] );

            // Yes / No
            if ( 2 === $meta_field['field_type'] ) {
                return ws_ls_fields_display_field_value_yes_no( $value);
            } else if ( 3 === $meta_field['field_type'] ) {
		        return ws_ls_fields_display_field_value_photo( $value);
	        }

        }

        return $value;

    }

	/**
	 * Render Photo Field
	 *
	 * @param $value
	 * @return string
	 *
	 */
	function ws_ls_fields_display_field_value_photo( $value ) {

		if ( false === empty( $value ) ) {

			$photo = ws_ls_photo_get( $value , 120, 120);

			return sprintf('<a href="%1$s" rel="noopener noreferrer" target="_blank">%2$s</a>',
				esc_url($photo['full']),
				$photo['thumb']
			);

		}

		return '';
	}

    /**
     * Render Yes / No field
     *
     * @param $value
     * @return string
     *
     */
    function ws_ls_fields_display_field_value_yes_no( $value ) {

        switch ( intval( $value ) ) {
            case 1:
                return __('No', WE_LS_SLUG);
                break;
            case 2:
                return __('Yes', WE_LS_SLUG);
                break;
            default:
                return '';

        }
    }

    /**
     * Render Meta Fields form
     *
     * @param null $entry_id
     * @return string
     */
    function ws_ls_meta_fields_form( $entry_id = NULL ) {

        $html = '';

        $photo_fields_rendered = 0;

        foreach ( ws_ls_meta_fields_enabled() as $field ) {

            $value = ws_ls_meta_fields_get_value_for_entry( $entry_id, $field[ 'id' ] );

            switch ( intval( $field[ 'field_type' ] ) ) {

                case 1:
                    $html .= ws_ls_meta_fields_form_field_text( $field, $value );
                    break;
                case 2:
                    $html .= ws_ls_meta_fields_form_field_yes_no( $field, $value );
                    break;
	            case 3:
		            $html .= ws_ls_meta_fields_form_field_photo( $field, $value );

		            $photo_fields_rendered++;

		            break;
                default: // 0
                    $html .= ws_ls_meta_fields_form_field_number( $field, $value );
            }

        }

        // Any photos displayed? Add a wee notice about file sizes etc
        if ( $photo_fields_rendered > 0 ) {
            $html .= ws_ls_meta_fields_photos_form_display_info();
        }

        return $html;
    }

    /**
     * Generate the HTML for a meta field text field
     *
     * @param $field
     * @param $value
     * @return string
     */
    function ws_ls_meta_fields_form_field_text( $field, $value ) {

        return sprintf('<div class="ws-ls-meta-field">
                            <label for="%1$s" class="ws-ls-meta-field-title" >%2$s:</label>
                            <input type="text" id="%1$s" name="%1$s" %3$s tabindex="%4$s" maxlength="200" value="%5$s" class="ws-ls-meta-field" />
                        </div>',
            ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
            esc_attr($field['field_name']),
            2 === intval($field['mandatory']) ? ' required' : '',
            ws_ls_get_next_tab_index(),
            ( false === empty( $value ) ) ? esc_attr( $value ) : ''
        );

    }

    /**
     * Generate the HTML for a meta field number field
     *
     * @param $field
     * @param $value
     * @return string
     */
    function ws_ls_meta_fields_form_field_number( $field, $value ) {

        return sprintf('<div class="ws-ls-meta-field">
                            <label for="%1$s" class="ws-ls-meta-field-title">%2$s:</label>
                            <input type="number" id="%1$s" name="%1$s" %3$s step="any" tabindex="%4$s" maxlength="200" value="%5$s" class="ws-ls-meta-field" />
                        </div>',
            ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
            esc_attr($field['field_name']),
            2 === intval($field['mandatory']) ? ' required' : '',
            ws_ls_get_next_tab_index(),
            ( false === empty( $value ) ) ? esc_attr( $value ) : ''
        );

    }

    /**
     * Generate the HTML for a meta field yes / no field
     *
     * @param $field
     * @param $value
     * @return string
     */
    function ws_ls_meta_fields_form_field_yes_no( $field, $value ) {

        $html = sprintf( '<div class="ws-ls-meta-field">  
                            <label for="%1$s" class="ws-ls-meta-field-title">%2$s:</label>
                            <select name="%1$s" id="%1$s" tabindex="%3$s">
                            ',
                            ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
                            esc_attr( $field['field_name'] ),
                            ws_ls_get_next_tab_index()
        );

        $value = intval( $value );

        if ( 2 !== intval($field['mandatory']) ) {
            $html .= sprintf( '<option value="0" %1$s ></option>', selected( $value, 0, false ) );
        }

        $html .= sprintf( '<option value="1" %1$s>%2$s</option>', selected( $value, 1, false ), __('No', WE_LS_SLUG) );
        $html .= sprintf( '<option value="2" %1$s>%2$s</option>', selected( $value, 2, false ), __('Yes', WE_LS_SLUG) );

        $html .= '</select></div>';

        return $html;

    }

	/**
	 * Generate the HTML for a meta field photo
	 *
	 * @param $field
	 * @param $value
	 * @return string
	 */
	function ws_ls_meta_fields_form_field_photo( $field, $value ) {

		if ( false === WS_LS_IS_PRO ) {
			return '';
		}

		$html = sprintf('<div class="ws-ls-meta-field ws-ls-meta-field-photo">
                            <label for="%1$s" class="ws-ls-meta-field-title">%2$s</label>',
                            ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
                            esc_attr( $field['field_name'] )
                        );

        $attachment_id = NULL;

        $html .= '<div class="ws-ls-table ws-ls-photo-current">
                       <div class="ws-ls-row">';

        // Show Add button
        $html .= sprintf('<div class="ws-ls-cell ws-ls-photo-select">
                            <input type="file" name="%1$s" id="%1$s" tabindex="%2$s" class="ws-ls-hide ws-ls-input-file ws-ls-meta-fields-photo"  %5$s data-required="%4$s" />
                            <label for="%1$s" class="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17"><path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"/></svg> 
                                <span>%3$s</span>
                            </label>
                        </div>',
            ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
            ws_ls_get_next_tab_index(),
            __('Select a photo', WE_LS_SLUG),
            2 === intval($field['mandatory']) ? 'y' : 'n',
            true === empty( $attachment_id ) && 2 === intval($field['mandatory']) ? 'required' : ''
        );

		// Do we have an existing photo?
		if ( false === empty( $value ) ) {

			$attachment_id = intval( $value );

			$thumbnail = wp_get_attachment_image_src( $attachment_id );
			$full_url = wp_get_attachment_url( $attachment_id );

			if ( false === empty($thumbnail) ) {
            $html .= sprintf('      
                                            <div class="ws-ls-cell ws-ls-photo-current">
                                                <p>%8$s:</p>
                                                <a href="%1$s" target="_blank" rel="noopener noreferrer">
                                                    <img src="%2$s" alt="%3$s" width="%5$s" height="%6$s" />
                                                </a> 
                                                <div class="ws-ls-photo-delete-existing">
                                                    <input type="checkbox" name="%9$s-delete" id="%9$s-delete" data-required="%10$s" data-field-id="%9$s" class="ws-ls-photo-field-delete" value="y" /> 
                                                    <label for="%9$s-delete">%7$s</label>
                                                </div>
                                            </div>        
                                               
                                     
                                     <input type="hidden" name="%9$s-previous" value="%4$s" />
									 ',
					esc_url( $full_url ),
					esc_url( $thumbnail[0] ),
					__('Existing photo for this date', WE_LS_SLUG),
					intval( $attachment_id),
					intval( $thumbnail[1] ),
					intval( $thumbnail[2] ),
					__( 'Delete existing photo', WE_LS_SLUG ),
					__( 'Existing photo', WE_LS_SLUG ),
                    ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
                    2 === intval( $field['mandatory'] ) ? 'y' : 'n'
				);
			}
		}

        $html .= '</div></div></div>';

		return $html;

}