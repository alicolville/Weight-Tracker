<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_awards_add_update_page() {

    $id =  ws_ls_querystring_value('id', true);
    $validation_fail = false;

    // Data Posted? If so, replace the above from $_POST object
    if ( false === empty( $_POST ) && true === ws_ls_awards_is_enabled() ) {

        $t = ws_ls_meta_fields_photos_process_upload( 'award-badge-yeken', NULL, NULL, NULL, NULL, 'award-upload' );

            var_dump($t);


            $award = ws_ls_get_values_from_post( [ 'id', 'title', 'category', 'gain_loss', 'stones',
                                                     'pounds', 'kg', 'weight_percentage', 'custom_message', 'max_awards', 'send_email', 'enabled' ] );

            var_dump($award);

            $mandatory_fields = [ 'title', 'max_awards' ];

            //TODO: depending on category expand mandatory list

            // Ensure all mandatory fields have been completed!
            foreach ( $mandatory_fields as $key ) {
                if ( true === empty( $award[ $key ] ) ) {
                    $validation_fail = true;
                }
            }
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

        // Load existing!
    } elseif ( false === empty( $id ) && $award = ws_ls_meta_fields_get_by_id( $id ) ){
       // $id = $award['id'];
    }

    //$id = intval( $id );

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
                                <form action="<?php echo esc_url( admin_url('admin.php?page=ws-ls-awards&mode=add-edit' ) ); ?>" enctype="multipart/form-data" method="post" id="ws-ls-awards-form" class="ws-ls-meta-fields-form">
                                    <?php if ( $validation_fail ): ?>
                                        <p class="ws-ls-validation-error">&middot; <?php echo __('Please complete all mandatory fields.', WE_LS_SLUG); ?></p>
                                    <?php endif; ?>
                                    <?php if ( false === empty( $id ) ) : ?>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                    <?php endif; ?>
                                    <div class="ws-ls-table">
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell">
                                                <label for="title"><?php echo __('Title', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <input type="text" name="title" id="title"  size="40" maxlength="40" class="<?php if ( true === $validation_fail && true === empty( $award['title'] ) ) { echo 'ws-ls-mandatory-field'; } ?>" value="<?php echo ( false === empty( $award['title'] ) ) ? esc_attr( $award['title'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                <p class="ws-ls-info"><?php echo __('Add a custom message to be inserted into the email. Replaces the {custom_message} within the email template.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="category"><?php echo __('Award Type', WE_LS_SLUG); ?></label>
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

                                                    if( ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS') ) {

                                                       if ( ws_ls_get_config('WE_LS_DATA_UNITS') == 'stones_pounds') {

                                                           //$weight['only_pounds'] = ws_ls_to_lb($weight['kg']);
                                                           if ( false === empty( $award['value'] ) ) {

                                                               $conversion = ws_ls_to_stone_pounds( $award['value'] );
                                                               $weight['stones'] = $conversion['stones'];
                                                               $weight['pounds'] = $conversion['pounds'];
                                                           }

                                                           printf( '<input  type="number" step="any" min="0" name="stones" id="stones" value="%s" placeholder="%s" size="11" >', $weight['stones'], __('Stones', WE_LS_SLUG) );
                                                           printf( '<input  type="number" step="any" min="0" max="13.99" name="pounds" id="pounds" value="%s" placeholder="%s" size="11"  >',  $weight['pounds'], __('Pounds', WE_LS_SLUG) );
                                                       }
                                                       else {

                                                           if ( false === empty( $award['value'] ) ) {
                                                               $weight['pounds'] = ws_ls_to_lb( $award['value'] );
                                                           }

                                                           printf( '<input  type="number" step="any" min="1" name="pounds" id="pounds" value="%s" placeholder="%s" size="11"  >', $weight['pounds'], __('Pounds', WE_LS_SLUG) );
                                                       }

                                                    } else {
                                                       printf( '<input  type="number" step="any" min="1" name="kg" id="kg" value="%d" placeholder="%s" size="11" > %s', intval( $award['value'] ), __('Weight', WE_LS_SLUG) . ' (' . __('kg', WE_LS_SLUG) . ')', __('kg', WE_LS_SLUG)  );
                                                    }
                                                ?>
                                                <p class="ws-ls-info"><?php echo __('The difference in weight from the starting weight.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
<!--                                        <div class="ws-ls-row ws-ls-hide" id="ws-ls-awards-additional-weight-percentage">-->
<!--                                            <div class="ws-ls-cell">-->
<!--                                                <label for="weight_percentage">--><?php //echo __('% from starting weight', WE_LS_SLUG); ?><!--</label>-->
<!--                                            </div>-->
<!--                                            <div class="ws-ls-cell">-->
<!--                                                <input type="number" min="0" max="1000" id="weight_percentage" name="weight_percentage" value="--><?php //intval( $award['value'] ) ?><!--" />-->
<!--                                                <p class="ws-ls-info">--><?php //echo __('Specify the percentage difference from the starting weight.', WE_LS_SLUG); ?><!--</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
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
                                            <div class="ws-ls-cell ws-ls-label-top">
                                                <label for="award-badge-yeken"><?php echo __('Award Badge', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <?php echo ws_ls_meta_fields_form_field_photo([ 'field_name' => '', 'mandatory' => 1], 170, 'award-badge-yeken' ); ?>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-top">
                                                <label for="max_awards"><?php echo __('Custom Message', WE_LS_SLUG); ?></label>
                                            </div>
                                          <div class="ws-ls-cell">
                                                <input type="text" name="custom_message" id="custom_message"  size="70" maxlength="190" value="<?php echo ( false === empty( $award['custom_message'] ) ) ? esc_attr( $award['custom_message'] ) : ''; ?>"/>
                                                <p class="ws-ls-info"><?php echo __('Add a custom message to be inserted into the email. Replaces the {custom_message} within the email template.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell">
                                                <label for="max_awards"><?php echo __('Max. times to award', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php

                                                $max_awards = ( false === empty( $award['max_awards'] ) ) ? intval( $award['max_awards'] ) : 1;

                                            ?>
                                            <div class="ws-ls-cell">
                                                <input type="number" min="0" max="1000" id="max_awards" class="<?php if ( true === $validation_fail && true === empty( $award['max_awards'] ) ) { echo 'ws-ls-mandatory-field'; } ?>" name="max_awards" value="<?php echo $max_awards; ?>" />
                                                <p class="ws-ls-info"><?php echo __('Specify the maximum number of times this award can be given to a user.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="enabled"><?php echo __('Send Email', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['send_email'] ) && 1 === intval( $award['send_email'] ) ) ? 1 : 0; ?>
                                            <div class="ws-ls-cell">
                                                <select name="send_email" id="send_email">
                                                    <option value="0" <?php selected( $checked, 1 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                    <option value="1" <?php selected( $checked, 2 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="enabled"><?php echo __('Enabled', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['enabled'] ) && 2 === intval( $award['enabled'] ) ) ? 1 : 0; ?>
                                            <div class="ws-ls-cell">
                                                <select name="enabled" id="enabled">
                                                    <option value="0" <?php selected( $checked, 0 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                    <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell"></div>
                                            <div class="ws-ls-cell">
                                                <a class="comment-submit button" href="<?php echo ws_ls_awards_base_url(); ?>"><?php echo __('Cancel', WE_LS_SLUG); ?></a>

                                                <?php if ( true === ws_ls_awards_is_enabled() ): ?>
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