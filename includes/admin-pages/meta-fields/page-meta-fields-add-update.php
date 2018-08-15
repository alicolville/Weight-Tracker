<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_add_update_page() {

        $id =  ws_ls_querystring_value('id', true);
        $validation_fail = false;

        // Data Posted? If so, replace the above from $_POST object
        if ( false === empty( $_POST ) && true === ws_ls_meta_fields_is_enabled() ) {

            $meta_field = ws_ls_get_values_from_post( [ 'id', 'field_name', 'abv', 'field_type', 'suffix', 'mandatory', 'enabled', 'suffix', 'sort', 'hide_from_shortcodes' ] );

            // Ensure all mandatory fields have been completed!
            foreach ( [ 'field_name', 'abv' ] as $key ) {
                if ( true === empty( $meta_field[ $key ] ) ) {
                    $validation_fail = true;
                }
            }

            if ( false === $validation_fail ) {

                // Add / Update
                $result = ( true === empty( $meta_field['id'] ) ) ? ws_ls_meta_fields_add( $meta_field ) : ws_ls_meta_fields_update( $meta_field );

                ws_ls_meta_fields_list_page();

                return;

            }

            $id = ( false === empty( $meta_field['id'] ) ) ? $meta_field['id'] : 0 ;

            // Load existing!
        } elseif ( false === empty( $id ) && $meta_field = ws_ls_meta_fields_get_by_id( $id ) ){
            $id = $meta_field['id'];
        }

        $id = intval( $id );

        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-3">
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">
                            <?php
                            if ( false === ws_ls_meta_fields_is_enabled() ) {
                                ws_ls_display_pro_upgrade_notice();
                            }
                            ?>
                            <div class="postbox">
                                <h3 class="hndle"><span><?php echo __('Add / Edit a Custom Field', WE_LS_SLUG); ?> </span></h3>
                                <div style="padding: 0px 15px 0px 15px">
                                    <form action="<?php echo esc_url( admin_url('admin.php?page=ws-ls-meta-fields&mode=add-edit' ) ); ?>" method="post" class="ws-ls-meta-fields-form">
                                        <?php if ( $validation_fail ): ?>
                                            <p class="ws-ls-validation-error">&middot; <?php echo __('Please complete all mandatory fields.', WE_LS_SLUG); ?></p>
                                        <?php endif; ?>
                                        <?php if ( false === empty( $id ) ) : ?>
                                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                        <?php endif; ?>
                                        <div class="ws-ls-table">
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="field_type"><?php echo __('Field Type', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <?php
                                                        $checked = ( false === empty( $meta_field['field_type'] ) ) ? intval( $meta_field['field_type'] ) : 0;
                                                    ?>
                                                    <select name="field_type" id="field_type">
                                                        <option value="0" <?php selected( $checked, 0 ); ?>><?php echo __('Number', WE_LS_SLUG); ?></option>
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('Text', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?> / <?php echo __('No', WE_LS_SLUG); ?></option>
                                                        <option value="3" <?php selected( $checked, 3 ); ?>><?php echo __('Photo', WE_LS_SLUG); ?></option>
                                                    </select>
                                                    <?php if ( false === empty( $id ) ) : ?>
                                                        <p class="ws-ls-note"><?php echo __('Note: Changing the field type will cause existing user data to be lost.', WE_LS_SLUG); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row ws-ls-hide" id="ws-ls-meta-fields-additional-3">
                                                <div class="ws-ls-cell">
                                                    <label for="hide_from_shortcodes"><?php echo __('Hide from shortcodes', WE_LS_SLUG); ?></label>
                                                </div>
	                                            <?php $checked = ( false === empty( $meta_field['hide_from_shortcodes'] ) && 2 === intval( $meta_field['hide_from_shortcodes'] ) ) ? 2 : 0; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="hide_from_shortcodes" id="hide_from_shortcodes">
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="field_name"><?php echo __('Name', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="field_name" id="field_name" class="<?php if ( true === $validation_fail && true === empty( $meta_field['field_name'] ) ) { echo 'ws-ls-mandatory-field'; } ?>"  size="40" maxlength="40" value="<?php echo ( false === empty( $meta_field['field_name'] ) ) ? esc_attr( $meta_field['field_name'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="abv"><?php echo __('Abbreviation', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="abv" id="abv" class="<?php if ( true === $validation_fail && true === empty( $meta_field['abv'] ) ) { echo 'ws-ls-mandatory-field'; } ?>" size="40" maxlength="5" value="<?php echo ( false === empty( $meta_field['abv'] ) ) ? esc_attr( $meta_field['abv'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                    <p class="ws-ls-info"><?php echo __('Used when displaying the field data in smaller spaces e.g. table headers, charts, etc', WE_LS_SLUG ); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="suffix"><?php echo __('Suffix', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="suffix" id="suffix" size="40" maxlength="5" value="<?php echo ( false === empty( $meta_field['suffix'] ) ) ? esc_attr( $meta_field['suffix'] ) : ''; ?>"/>
                                                    <p class="ws-ls-info"><?php echo __('Text display at to end of the entered value when displaying it to the user. e.g. CM would display in the following manner: 120 CM', WE_LS_SLUG ); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="sort"><?php echo __('Display Order', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <?php
                                                    $checked = ( false === empty( $meta_field['sort'] ) ) ? intval( $meta_field['sort'] ) : 100;
                                                    ?>
                                                    <select name="sort" id="sort">
                                                        <?php for ( $i = 0; $i <= 200; $i = $i + 10 ): ?>
                                                            <option value="<?php echo $i; ?>" <?php selected( $checked, $i ); ?>><?php echo $i; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <p class="ws-ls-info"><?php echo __('Used to specify the order of custom fields. Lower numbers are displayed higher up the form.', WE_LS_SLUG); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="mandatory"><?php echo __('Mandatory', WE_LS_SLUG); ?></label>
                                                </div>
                                                <?php $checked = ( false === empty( $meta_field['mandatory'] ) && 2 === intval( $meta_field['mandatory'] ) ) ? 2 : 0; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="mandatory" id="mandatory">
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="enabled"><?php echo __('Enabled', WE_LS_SLUG); ?></label>
                                                </div>
                                                <?php $checked = ( false === empty( $meta_field['enabled'] ) && 2 === intval( $meta_field['enabled'] ) ) ? 2 : 1; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="enabled" id="enabled">
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell"></div>
                                                <div class="ws-ls-cell">
                                                    <a class="comment-submit button" href="<?php echo ws_ls_meta_fields_base_url(); ?>"><?php echo __('Cancel', WE_LS_SLUG); ?></a>

                                                    <?php if ( true === ws_ls_meta_fields_is_enabled() ): ?>
                                                        <input name="submit_button" type="submit" value="<?php echo __('Save', WE_LS_SLUG); ?>" class="comment-submit button button-primary">
                                                    <?php else: ?>
                                                        <a class="comment-submit button button-primary" href="<?php echo esc_url( admin_url('admin.php?page=ws-ls-license') ); ?>"><?php echo __('Save', WE_LS_SLUG); ?></a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                 <br class="clear">
            </div>
        </div>
        <?php
    }