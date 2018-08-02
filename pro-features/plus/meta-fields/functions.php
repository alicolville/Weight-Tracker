<?php


    /**
     * Returns true if Meta Fields fully enabled (i.e. not trial mode)
     *
     * @return bool
     */
    function ws_ls_meta_fields_is_enabled() {
        return true; //TODO: This needs to be based upn pro license?
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

        return [
            0 => __('Number', WE_LS_SLUG),
            1 => __('Text', WE_LS_SLUG),
            2 => __('Yes', WE_LS_SLUG) . ' / ' . __('No', WE_LS_SLUG)
        ];

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

        return ( 1 == $value ) ? __('Yes', WE_LS_SLUG) : __('No', WE_LS_SLUG);
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
     * Render Meta Fields form
     *
     * @param null $entry_id
     * @return string
     */
    function ws_ls_meta_fields_form( $entry_id = NULL ) {

        $html = '';

        foreach ( ws_ls_meta_fields_enabled() as $field ) {

            $value = ws_ls_meta_fields_get_value_for_entry( $entry_id, $field[ 'id' ] );

            switch ( intval( $field[ 'field_type' ] ) ) {

                case 1:
                    $html .= ws_ls_meta_fields_form_field_text( $field, $value );
                    break;
            }

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

        return sprintf('<label for="%1$s">%2$s:</label>
                        <input type="text" id="%1$s" name="%1$s" %3$s tabindex="%4$s" maxlength="200" value="%5$s" class="ws-ls-meta-field" />',
            ws_ls_meta_fields_form_field_generate_id( $field['id'] ),
            esc_attr($field['field_name']),
            1 === intval($field['mandatory']) ? ' required' : '',
            ws_ls_get_next_tab_index(),
            ( false === empty( $value ) ) ? esc_attr( $value ) : ''
        );

    }