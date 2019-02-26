<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_awards_add_update_page() {

    $id =  ws_ls_querystring_value('id', true);
    $validation_fail = false;

    // Data Posted? If so, replace the above from $_POST object
    if ( false === empty( $_POST ) && true === ws_ls_awards_is_enabled() ) {

        $award = ws_ls_get_values_from_post( [ 'id', 'title', 'category', 'gain_loss', 'stones', 'apply_to_update', 'apply_to_add', 'bmi_equals',
                                                 'pounds', 'value', 'weight_percentage', 'custom_message', 'max_awards', 'send_email', 'enabled', 'url' ] );

        $mandatory_fields = [ 'title' ];

        //------------------------------------------------------------------------------
        // Add mandatory units to list for validation
        //------------------------------------------------------------------------------
        if ( 'weight-percentage' === $award['category'] ) {
            $mandatory_fields = array_merge( $mandatory_fields, [ 'weight_percentage' ] );
        } else if ( 'weight' === $award['category'] ) {
            if( ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS') ) {
                if ( 'stones_pounds' === ws_ls_get_config('WE_LS_DATA_UNITS') ) {
                    $mandatory_fields = array_merge( $mandatory_fields, [ 'stones', 'pounds' ] );
                    $award['value'] = ws_ls_to_kg( $award['stones'], $award['pounds'] );
                } else {
                    $mandatory_fields = array_merge( $mandatory_fields, [ 'pounds' ] );
                    $award['value'] = ws_ls_pounds_to_kg( $award['pounds'] );
                }
            } else {
                $mandatory_fields = array_merge( $mandatory_fields, [ 'value' ] );
            }
        }

        $failed_validation = [];

        // Ensure all mandatory fields have been completed!
        foreach ( $mandatory_fields as $key ) {
            if ( true === empty( $award[ $key ] ) && '0' !== $award[ $key ] ) {
                $validation_fail = true;
                $failed_validation[] = $key;
            }
        }

        // Handle photo upload
        $award['badge'] = ws_ls_meta_fields_photos_process_upload( 'award-badge-yeken', NULL, NULL, NULL, NULL, 'award-upload' );

        if ( false === $validation_fail ) {

	        // If weight percentage, switch the values.
	        if ( 'weight-percentage' === $award['category'] ) {
		        $award['value'] = $award['weight_percentage'] ;
	        }

	        // If weight percentage, switch the values.
	        if ( 'bmi-equals' === $award['category'] ) {
		        unset( $award['gain_loss'] );
	        }

            unset( $award['stones'], $award['pounds'], $award['weight_percentage'] );

            $award['max_awards'] = 1;

            // Add / Update
            $result = ( true === empty( $award['id'] ) ) ? ws_ls_awards_add( $award ) : ws_ls_awards_update( $award );

            ws_ls_awards_list_page();

            return;

        }

        $id = ( false === empty( $award['id'] ) ) ? $award['id'] : 0 ;

        // Load existing!
    } elseif ( false === empty( $id ) && $award = ws_ls_award_get( $id ) ){
       $id = $award['id'];
    }

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
                                <form action="<?php echo esc_url( admin_url('admin.php?page=ws-ls-awards&mode=add-edit' ) ); ?>" novalidate enctype="multipart/form-data" method="post" id="ws-ls-awards-form" class="ws-ls-meta-fields-form">
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
                                                <input type="text" name="title" id="title"  size="40" maxlength="200" class="<?php if ( true === $validation_fail && true === empty( $award['title'] ) ) { echo 'ws-ls-mandatory-field'; } ?>" value="<?php echo ( false === empty( $award['title'] ) ) ? esc_attr( $award['title'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>

                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="category"><?php echo __('Award Type', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                    <?php
                                                        $checked = ( false === empty( $award['category'] ) ) ? $award['category'] : 'weight';
                                                    ?>
                                                    <select name="category" id="category" <?php echo ( false === empty( $award['id'] ) ) ? 'disabled' : ''; ?>>
                                                        <?php
                                                            foreach ( ws_ls_awards_categories() as $key => $label ) {
                                                                    printf( '<option value="%s" %s>%s</option>', $key, selected( $checked, $key, false ), $label );
                                                            }
                                                        ?>
                                                    </select>
                                                    <p class="ws-ls-info"><?php echo __('Awards are decided by comparing the difference between the user\'s latest weight and their starting weight.', WE_LS_SLUG); ?></p>
                                                    <?php if ( false === empty( $award['id'] ) ) : ?>
                                                        <input type="hidden" name="category" value="<?php echo $checked; ?>" />
                                                    <?php endif; ?>
                                             </div>
                                        </div>
                                        <div class="ws-ls-row hide-bmi-equals">
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
                                        <div class="ws-ls-row" id="ws-ls-awards-additional-weight">
                                            <div class="ws-ls-cell">
                                                <label><?php echo __('Weight difference', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <?php

                                                    $weight = [ 'stones' => '', 'pounds' => '' ];

                                                    if( ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS') ) {

                                                       if ( ws_ls_get_config('WE_LS_DATA_UNITS') == 'stones_pounds') {

                                                           if ( true === $validation_fail ) {
                                                               $weight['stones'] = $award['stones'];
                                                               $weight['pounds'] = $award['pounds'];
                                                           } elseif ( false === empty( $award['value'] ) ) {

                                                               $conversion = ws_ls_to_stone_pounds( $award['value'] );
                                                               $weight['stones'] = $conversion['stones'];
                                                               $weight['pounds'] = $conversion['pounds'];
                                                           }

                                                           printf( '<input novalidate type="number" step="any" min="0" name="stones" id="stones" value="%s" placeholder="%s" size="11" class="%s">', esc_attr( $weight['stones'] ), __('Stones', WE_LS_SLUG),
                                                               ( true === $validation_fail && in_array( 'stones', $failed_validation ) ) ? 'ws-ls-mandatory-field' : '');
                                                           printf( '<input novalidate type="number" step="any" min="0" max="13.99" name="pounds" id="pounds" value="%s" placeholder="%s" size="11"  class="%s">',  esc_attr( $weight['pounds'] ), __('Pounds', WE_LS_SLUG),
                                                               ( true === $validation_fail && in_array( 'pounds', $failed_validation ) ) ? 'ws-ls-mandatory-field' : '' );
                                                       }
                                                       else {

                                                           if ( true === $validation_fail ) {
                                                               $weight['pounds'] = $award['pounds'];
                                                           } else if ( false === empty( $award['value'] ) ) {
                                                               $weight['pounds'] = ws_ls_to_lb( $award['value'] );
                                                           }

                                                           printf( '<input novalidate type="number" step="any" min="1" name="pounds" id="pounds" value="%s" placeholder="%s" size="11" class="%s" >', esc_attr( $weight['pounds'] ), __('Pounds', WE_LS_SLUG),
                                                               ( true === $validation_fail && in_array( 'pounds', $failed_validation ) ) ? 'ws-ls-mandatory-field' : '');
                                                       }

                                                    } else {
                                                        printf( '<input novalidate type="number" step="any" min="1" name="value" id="value" value="%s" placeholder="%s" size="11" class="%s"> %s', esc_attr( $award['value'] ), __('Weight', WE_LS_SLUG) . ' (' . __('kg', WE_LS_SLUG) . ')',
                                                           ( true === $validation_fail && in_array( 'value', $failed_validation ) ) ? 'ws-ls-mandatory-field' : '' , __('kg', WE_LS_SLUG)  );
                                                    }
                                                ?>
                                                <p class="ws-ls-info"><?php echo __('The difference in weight from the starting weight.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row" id="ws-ls-awards-additional-bmi-equals">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="bmi_equals"><?php echo __('BMI Equals', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
			                                    <?php
			                                    $checked = ( false === empty( $award['bmi_equals'] ) ) ? $award['bmi_equals'] : '0';
			                                    ?>
                                                <select name="bmi_equals" id="bmi_equals">
				                                    <?php
				                                    foreach ( ws_ls_bmi_all_labels() as $key => $label ) {
					                                    printf( '<option value="%s" %s>%s</option>', $key, selected( $checked, $key, false ), $label );
				                                    }
				                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="ws-ls-row" id="ws-ls-awards-additional-weight-percentage">
                                            <div class="ws-ls-cell">
                                                <label for="weight_percentage"><?php echo __('Percentage from starting weight', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php
                                                $value = ( true === $validation_fail && false === empty( $award['weight_percentage'] ) ) ? $award['weight_percentage'] : $award['value'];
                                            ?>
                                            <div class="ws-ls-cell">
                                                <input type="number" min="0" novalidate max="1000" id="weight_percentage" name="weight_percentage" class="<?php if ( true === $validation_fail && in_array( 'weight_percentage', $failed_validation ) ) { echo 'ws-ls-mandatory-field'; } ?>" value="<?php echo esc_attr( $value ) ?>" />
                                                <p class="ws-ls-info"><?php echo __('Specify the percentage difference from the starting weight.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-top">
                                                <label for="award-badge-yeken"><?php echo __('Award Badge', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <?php
                                                    $value = ( false === empty( $award['badge'] ) ) ? $award['badge'] : NULL;

                                                    echo ws_ls_meta_fields_form_field_photo([ 'field_name' => '', 'mandatory' => 1], $value, 'award-badge-yeken' );

                                                ?>
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
                                            <div class="ws-ls-cell ws-ls-label-top">
                                                <label for="max_awards"><?php echo __('URL', WE_LS_SLUG); ?></label>
                                            </div>
                                            <div class="ws-ls-cell">
                                                <input type="text" name="url" id="url"  size="70" maxlength="200" value="<?php echo ( false === empty( $award['url'] ) ) ? esc_attr( $award['url'] ) : ''; ?>"/>
                                                <p class="ws-ls-info"><?php echo __('If specified, badges and award title will click through to the given URL.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="apply_to_add"><?php echo __('Apply to new entries?', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['apply_to_add'] ) && 1 === (int) $award['apply_to_add'] ) ? 1 : 0; ?>
                                            <div class="ws-ls-cell">
                                                <select name="apply_to_add" id="apply_to_add">
                                                    <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    <option value="0" <?php selected( $checked, 0 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                </select>
                                                <p class="ws-ls-info"><?php echo __('Can the award be given when a user adds a new weight entry?.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="apply_to_update"><?php echo __('Apply to updated entries?', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['apply_to_update'] ) && 1 === (int) $award['apply_to_update'] ) ? 1 : 0; ?>
                                            <div class="ws-ls-cell">
                                                <select name="apply_to_update" id="apply_to_update">
                                                    <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    <option value="0" <?php selected( $checked, 0 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                </select>
                                                <p class="ws-ls-info"><?php echo __('Can the award be given when a user updates an existing weight entry?.', WE_LS_SLUG); ?></p>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="enabled"><?php echo __('Send Email', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['send_email'] ) && 2 === (int) $award['send_email'] ) ? 2 : 0; ?>
                                            <div class="ws-ls-cell">
                                                <select name="send_email" id="send_email">
                                                    <option value="2" <?php selected( $checked, 2 ); ?>><?php echo __('Yes', WE_LS_SLUG); ?></option>
                                                    <option value="1" <?php selected( $checked, 1 ); ?>><?php echo __('No', WE_LS_SLUG); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="ws-ls-row">
                                            <div class="ws-ls-cell ws-ls-label-col">
                                                <label for="enabled"><?php echo __('Enabled', WE_LS_SLUG); ?></label>
                                            </div>
                                            <?php $checked = ( false === empty( $award['enabled'] ) && 2 === (int) $award['enabled'] ) ? 2 : 0; ?>
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