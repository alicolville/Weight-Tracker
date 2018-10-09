<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_awards_add_update_page() {

    $id =  ws_ls_querystring_value('id', true);
    $validation_fail = false;

    // Data Posted? If so, replace the above from $_POST object
//    if ( false === empty( $_POST ) && true === ws_ls_meta_fields_is_enabled() ) {
//
//        $award = ws_ls_get_values_from_post( [ 'id', 'field_name', 'abv', 'field_type', 'suffix', 'mandatory', 'enabled', 'suffix', 'sort', 'hide_from_shortcodes' ] );
//
//        // Ensure all mandatory fields have been completed!
//        foreach ( [ 'field_name', 'abv' ] as $key ) {
//            if ( true === empty( $award[ $key ] ) ) {
//                $validation_fail = true;
//            }
//        }
//
//        // If the user has selected a Photo Field, but isn't pro plus, then redirect!
//        if ( false === WS_LS_IS_PRO && 3 === (int) $award['field_type'] ) {
//            $validation_fail = true;
//        }
//
//        if ( false === $validation_fail ) {
//
//            // Add / Update
//            $result = ( true === empty( $award['id'] ) ) ? ws_ls_meta_fields_add( $award ) : ws_ls_meta_fields_update( $award );
//
//            ws_ls_meta_fields_list_page();
//
//            return;
//
//        }
//
//        $id = ( false === empty( $award['id'] ) ) ? $award['id'] : 0 ;
//
//        // Load existing!
//    } elseif ( false === empty( $id ) && $award = ws_ls_meta_fields_get_by_id( $id ) ){
//        $id = $award['id'];
//    }
//
//    $id = intval( $id );

    $award = NULL;

    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-3">
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">
                        <?php
                        if ( false === ws_ls_awards_is_enabled() ) {
                            ws_ls_display_pro_upgrade_notice();
                        }

                        ?>
                        <div class="postbox">
                            <h3 class="hndle"><span><?php echo __('Add / Edit an Award', WE_LS_SLUG); ?> </span></h3>
                            <div style="padding: 0px 15px 0px 15px">
                                <form action="<?php echo esc_url( admin_url('admin.php?page=ws-ls-awards&mode=add-edit' ) ); ?>" method="post" id="ws-ls-awards-form" class="ws-ls-meta-fields-form">
                                    <?php if ( $validation_fail ): ?>
                                        <p class="ws-ls-validation-error">&middot; <?php echo __('Please complete all mandatory fields.', WE_LS_SLUG); ?></p>
                                    <?php endif; ?>
                                    <?php if ( false === empty( $id ) ) : ?>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                    <?php endif; ?>
                                    <div class="ws-ls-table">
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="field_type"><?php echo __('Award Type', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                TODO: Disable changing when editing
                                                <?php
                                                $checked = ( false === empty( $award['category'] ) ) ? intval( $award['category'] ) : 'weight';
                                                ?>
                                                <select name="category" id="category">
                                                    <?php
                                                        foreach ( ws_ls_awards_categories() as $key => $label ) {
                                                                printf( '<option value="%s" %s>%s</option>', $key, selected( $checked, $key ), $label );
                                                        }
                                                    ?>
                                                </select>
                                             </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell">
                                                <label for="gain_loss"><?php echo __('Gain or Loss', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['gain_loss'] ) && 'gain' === $award['gain_loss'] ) ? 'gain' : 'loss'; ?>
                                            <div class="ws-ls-cell">
                                                <select name="gain_loss" id="gain_loss">
                                                    <option value="gain" <?php selected( $checked, 'gain' ); ?>><?php echo __('Increase in value', WE_LS_SLUG); ?></option>
                                                    <option value="loss" <?php selected( $checked, 'loss' ); ?>><?php echo __('Decrease in value', WE_LS_SLUG); ?></option>
                                                </select>
                                                <p class="ws-ls-info"><?php echo __('For example, if you wish to award someone for losing 10Kg, you would select "Decrease in value".', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row ws-ls-hide" id="ws-ls-awards-additional-weight">
                                            <div class="ws-ls-cell">
                                                <label for="weight_percentage"><?php echo __('Weight difference', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <?php
                                                    $weight = [ 'stones' => '', 'pounds' => '' ];
                                                $award = ['category' => 'weight', 'value' => 23]; //todo

                                                    if( ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS') ) {

                                                       if ( ws_ls_get_config('WE_LS_DATA_UNITS') == 'stones_pounds') {

                                                           //$weight['only_pounds'] = ws_ls_to_lb($weight['kg']);
                                                           if ( false === empty( $award['value'] ) ) {

                                                               $conversion = ws_ls_to_stone_pounds( $award['value'] );
                                                               $weight['stones'] = $conversion['stones'];
                                                               $weight['pounds'] = $conversion['pounds'];
                                                           }

                                                           printf( '<input  type="number" step="any" min="0" name="we-ls-weight-stones" id="we-ls-weight-stones" value="%s" placeholder="%s" size="11" >', $weight['stones'], __('Stones', WE_LS_SLUG) );
                                                           printf( '<input  type="number" step="any" min="0" max="13.99" name="we-ls-weight-pounds" id="we-ls-weight-pounds" value="%s" placeholder="%s" size="11"  >',  $weight['pounds'], __('Pounds', WE_LS_SLUG) );
                                                       }
                                                       else {

                                                           if ( false === empty( $award['value'] ) ) {
                                                               $weight['pounds'] = ws_ls_to_lb( $award['value'] );
                                                           }

                                                           printf( '<input  type="number" step="any" min="1" name="we-ls-weight-pounds" id="we-ls-weight-pounds" value="%s" placeholder="%s" size="11"  >', $weight['pounds'], __('Pounds', WE_LS_SLUG) );
                                                       }

                                                    } else {
                                                       printf( '<input  type="number" step="any" min="1" name="we-ls-weight-kg" id="we-ls-weight-kg" value="%d" placeholder="%s" size="11" > %s', intval( $award['value'] ), __('Weight', WE_LS_SLUG) . ' (' . __('kg', WE_LS_SLUG) . ')', __('kg', WE_LS_SLUG)  );
                                                    }
                                                ?>
                                                <p class="ws-ls-info"><?php echo __('The difference in weight from the starting weight.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row ws-ls-hide" id="ws-ls-awards-additional-weight-percentage">
                                            <div class="ws-ls-cell">
                                                <label for="weight_percentage"><?php echo __('% from starting weight', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <input type="number" min="0" max="1000" id="weight_percentage" name="weight_percentage" value="<?php intval( $award['value'] ) ?>" />
                                                <p class="ws-ls-info"><?php echo __('Specify the percentage difference from the starting weight.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell">
                                                <label for="field_name"><?php echo __('Field / Question', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <input type="text" name="field_name" id="field_name" class="<?php if ( true === $validation_fail && true === empty( $award['field_name'] ) ) { echo 'ws-ls-mandatory-field'; } ?>"  size="40" maxlength="40" value="<?php echo ( false === empty( $award['field_name'] ) ) ? esc_attr( $award['field_name'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell">
                                                <label for="abv"><?php echo __('Abbreviation', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <input type="text" name="abv" id="abv" class="<?php if ( true === $validation_fail && true === empty( $award['abv'] ) ) { echo 'ws-ls-mandatory-field'; } ?>" size="40" maxlength="5" value="<?php echo ( false === empty( $award['abv'] ) ) ? esc_attr( $award['abv'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                <p class="ws-ls-info"><?php echo __('Used when displaying the field data in smaller spaces e.g. table headers, charts, etc', WE_LS_SLUG ); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell">
                                                <label for="suffix"><?php echo __('Suffix', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <input type="text" name="suffix" id="suffix" size="40" maxlength="5" value="<?php echo ( false === empty( $award['suffix'] ) ) ? esc_attr( $award['suffix'] ) : ''; ?>"/>
                                                <p class="ws-ls-info"><?php echo __('Text display at to end of the entered value when displaying it to the user. e.g. CM would display in the following manner: 120 CM', WE_LS_SLUG ); ?></p>
                                            </div>
                                        </div>

                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="enabled"><?php echo __('Enabled', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['enabled'] ) && 2 === intval( $award['enabled'] ) ) ? 2 : 1; ?>
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