<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_add_update_page() {

        $edit = false;
        $meta_field =  ws_ls_querystring_value('id', true);
        $word = __('Add', WE_LS_SLUG);

        if ( false === empty( $meta_field ) && $meta_field = ws_ls_meta_fields_get_by_id( $meta_field ) ){
            $edit = true;
            $word = __('Edit', WE_LS_SLUG);
        }
var_dump($meta_field);
        ?>
        <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-3">
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">

                            <div class="postbox">
                                <h3 class="hndle"><span><?php echo $word . ' ' . __('a field', WE_LS_SLUG); ?> </span></h3>
                                <div style="padding: 0px 15px 0px 15px">
                                    <form method="post" class="ws-ls-meta-fields-form">
                                        <?php if ( $edit ) : ?>
                                            <input type="hidden" name="ws-ls-update" value="<?php echo ( false === empty( $meta_field['id'] ) ) ? esc_attr( $meta_field['id'] ) : ''; ?>"/>
                                        <?php endif; ?>
                                        <div class="ws-ls-table">
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="field-type"><?php echo __('Field Type', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <select>
                                                        <option><?php echo __('Number', WE_LS_SLUG); ?></option>
                                                        <option><?php echo __('Text', WE_LS_SLUG); ?></option>
                                                        <option><?php echo __('Yes', WE_LS_SLUG); ?> / <?php echo __('No', WE_LS_SLUG); ?></option>
                                                    </select>
                                                    <?php if ( $edit ) : ?>
                                                        <p class="ws-ls-note"><?php echo __('Note: Changing the field type will cause existing user data to be lost.', WE_LS_SLUG); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="ws-ls-name"><?php echo __('Name', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="ws-ls-name" id="ws-ls-name" size="40" maxlength="40" value="<?php echo ( false === empty( $meta_field['field_name'] ) ) ? esc_attr( $meta_field['field_name'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="ws-ls-abv"><?php echo __('Abbreviation', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="ws-ls-abv" id="ws-ls-abv" size="40" maxlength="4" value="<?php echo ( false === empty( $meta_field['abv'] ) ) ? esc_attr( $meta_field['abv'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                    <p class="ws-ls-info"><?php echo __('Used when displaying the field data in smaller spaces e.g. table headers, charts, etc', WE_LS_SLUG ); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="ws-ls-suffix"><?php echo __('Suffix', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="ws-ls-suffix" id="ws-ls-suffix" size="40" maxlength="4" value="<?php echo ( false === empty( $meta_field['suffix'] ) ) ? esc_attr( $meta_field['suffix'] ) : ''; ?>"/>
                                                    <p class="ws-ls-info"><?php echo __('Text display at to end of the entered value when displaying it to the user. e.g. CM would display in the following manner: 120 CM', WE_LS_SLUG ); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="ws-ls-mandatory"><?php echo __('Mandatory', WE_LS_SLUG); ?></label>
                                                </div>
                                                <?php $checked = ( false === empty( $meta_field['mandatory'] ) && 1 === intval( $meta_field['mandatory'] ) ) ? 1 : 0; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="ws-ls-mandatory" id="ws-ls-mandatory">
                                                        <option value="0" <?php selected( $checked, 0 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="ws-ls-enabled"><?php echo __('Enabled', WE_LS_SLUG); ?></label>
                                                </div>
                                                <?php $checked = ( false === empty( $meta_field['enabled'] ) && 1 === intval( $meta_field['enabled'] ) ) ? 1 : 0; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="ws-ls-enabled" id="ws-ls-enabled">
                                                        <option value="0" <?php selected( $checked, 0 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <input name="submit_button" type="submit" id="we-ls-submit" tabindex="5" value="Save Field" class="comment-submit button">
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