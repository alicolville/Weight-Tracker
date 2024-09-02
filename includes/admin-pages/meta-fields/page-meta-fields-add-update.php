<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_add_update_page() {

        $id =  ws_ls_querystring_value('id', true);
        $validation_fail = false;

        // Data Posted? If so, replace the above from $_POST object
        if ( false === empty( $_POST ) && true === ws_ls_meta_fields_is_enabled() ) {

            $meta_field = ws_ls_get_values_from_post( [ 'id', 'field_name', 'abv', 'field_type', 'suffix', 'mandatory', 'enabled',
															'suffix', 'sort', 'hide_from_shortcodes', 'plot_on_graph', 'plot_colour', 'group_id', 'include_empty',
																'min_value', 'max_value', 'step', 'show_all_labels', 'options-values', 'options-labels' ] );
            // Ensure all mandatory fields have been completed!
            foreach ( [ 'field_name', 'abv' ] as $key ) {
                if ( true === empty( $meta_field[ $key ] ) ) {
                    $validation_fail = true;
                }
            }

            // If the user has selected a Photo Field, but isn't pro plus, then redirect!
            if ( false === WS_LS_IS_PRO && 3 === (int) $meta_field['field_type'] ) {
                $validation_fail = true;
            }

            if ( false === $validation_fail ) {

                // Add / Update
                $result = ( true === empty( $meta_field['id'] ) ) ? ws_ls_meta_fields_add( $meta_field ) : ws_ls_meta_fields_update( $meta_field );

				ws_ls_cache_user_delete( 'custom-fields-groups' );

                ws_ls_meta_fields_list_page();

                return;

            }

            $id = ( false === empty( $meta_field['id'] ) ) ? $meta_field['id'] : 0 ;

            // Load existing!
        } elseif ( false === empty( $id ) && $meta_field = ws_ls_meta_fields_get_by_id( $id ) ){
            $id = $meta_field['id'];
        }

        $id = (int) $id;

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
                                <h3 class="hndle"><span><?php echo esc_html__('Add / Edit a Custom Field', WE_LS_SLUG); ?> </span></h3>
                                <div style="padding: 0px 15px 0px 15px">
                                    <form action="<?php echo esc_url( admin_url('admin.php?page=ws-ls-meta-fields&mode=add-edit' ) ); ?>" method="post" class="ws-ls-meta-fields-form">
                                        <?php if ( $validation_fail ): ?>
                                            <p class="ws-ls-validation-error">&middot; <?php echo esc_html__('Please complete all mandatory fields.', WE_LS_SLUG); ?></p>
                                        <?php endif; ?>
                                        <?php if ( false === empty( $id ) ) : ?>
                                            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                        <?php endif; ?>
                                        <div class="ws-ls-table">
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="field_type"><?php echo esc_html__('Field Type', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <?php
                                                        $checked = ( false === empty( $meta_field['field_type'] ) ) ? (int) $meta_field['field_type'] : ws_ls_querystring_value('field_type', true);
                                                    ?>
                                                    <select name="field_type" id="field_type">
														<?php
															$meta_field_types 	= ws_ls_meta_fields_types();

															foreach ( $meta_field_types as $key => $name ) {
																printf( '<option value="%1$d" %2$s>%3$s</option>',
																				$key,
																				selected( $checked, $key, false ),
																				$name
																);
															}
														?>
													</select>
                                                    <?php if ( false === empty( $id ) ) : ?>
                                                        <p class="ws-ls-note"><?php echo esc_html__('Note: Changing the field type will cause existing user data to be lost.', WE_LS_SLUG); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-3">
                                                <div class="ws-ls-cell">
                                                    <label for="hide_from_shortcodes"><?php echo esc_html__('Hide from shortcodes', WE_LS_SLUG); ?></label>
                                                </div>
	                                            <?php $checked = ( false === empty( $meta_field['hide_from_shortcodes'] ) && 2 === (int) $meta_field['hide_from_shortcodes'] ) ? 2 : 0; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="hide_from_shortcodes" id="hide_from_shortcodes">
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo esc_html__('No', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo esc_html__('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                    <p class="ws-ls-info"><?php echo esc_html__('Note: If set to Yes, photos uploaded into this custom field cannot be used in shortcodes i.e. the photos will only be visible to admin. However, the photo fields can appear in forms.', WE_LS_SLUG); ?></p>
                                                </div>
                                            </div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-0 ws-ls-meta-fields-additional-4 ws-ls-meta-fields-additional-5 ws-ls-meta-fields-additional-7">
												<div class="ws-ls-cell">
													<label for="plot_on_graph"><?php echo esc_html__('Show on charts?', WE_LS_SLUG); ?></label>
												</div>
												<?php $checked = ( false === empty( $meta_field['plot_on_graph'] ) ); ?>
												<div class="ws-ls-cell">
													<select name="plot_on_graph" id="plot_on_graph">
														<option value="0" <?php selected( $checked, 0 ); ?>><?php echo esc_html__('No', WE_LS_SLUG); ?></option>
														<option value="1" <?php selected( $checked, 1 ); ?>><?php echo esc_html__('Yes', WE_LS_SLUG); ?></option>
													</select>
													<p class="ws-ls-info"><?php echo esc_html__('Note: If set to Yes, this custom field will also be plotted on graphs. If the custom field is a radio button, ensure all values are numeric.', WE_LS_SLUG); ?></p>
												</div>
											</div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-0  ws-ls-meta-fields-additional-4 ws-ls-meta-fields-additional-5 ws-ls-meta-fields-additional-7">
												<div class="ws-ls-cell">
													<label for="plot_colour"><?php echo esc_html__('Line colour on graph', WE_LS_SLUG); ?></label>
												</div>
												<div class="ws-ls-cell">
													<?php
														$colour = ( false === empty( $meta_field['plot_colour'] ) ) ? $meta_field['plot_colour'] : '#000000';
													?>
													<input name="plot_colour" id="plot_colour" type="color" value="<?php echo esc_attr( $colour ); ?>">
													<p class="ws-ls-info"><?php echo esc_html__('Note: The HEX colour to use for the line when plotting on a graph.', WE_LS_SLUG); ?></p>
												</div>
											</div>
											<div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="field_name"><?php echo esc_html__('Field / Question', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="field_name" id="field_name" class="<?php if ( true === $validation_fail && true === empty( $meta_field['field_name'] ) ) { echo 'ws-ls-mandatory-field'; } ?>"  size="40" maxlength="200" value="<?php echo ( false === empty( $meta_field['field_name'] ) ) ? esc_attr( $meta_field['field_name'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                </div>
                                            </div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-4">
												<div class="ws-ls-cell">
													<label for="min_value"><?php echo esc_html__('Minimum value', WE_LS_SLUG); ?></label>
												</div>
												<div class="ws-ls-cell">
													<?php
														$min_value = ( false === empty( $meta_field[ 'min_value' ] ) ) ? (int) $meta_field[ 'min_value' ] : 0;
													?>
													<input name="min_value" id="min_value" type="number"  min="-1000000" max="1000000" step="any" value="<?php echo $min_value; ?>">
													<p class="ws-ls-info"><?php echo esc_html__('Specifies the lowest number on the range slider.', WE_LS_SLUG); ?></p>
												</div>
											</div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-4">
												<div class="ws-ls-cell">
													<label for="max_value"><?php echo esc_html__('Maximum value', WE_LS_SLUG); ?></label>
												</div>
												<div class="ws-ls-cell">
													<?php
													$max_value = ( false === empty( $meta_field[ 'max_value' ] ) ) ? (int) $meta_field[ 'max_value' ] : 100;
													?>
													<input name="max_value" id="max_value" type="number" min="-1000000" max="1000000" step="any" value="<?php echo $max_value; ?>">
													<p class="ws-ls-info"><?php echo esc_html__('Specifies the maximum number on the range slider.', WE_LS_SLUG); ?></p>
												</div>
											</div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-4">
												<div class="ws-ls-cell">
													<label for="step"><?php echo esc_html__('Step', WE_LS_SLUG); ?></label>
												</div>
												<div class="ws-ls-cell">
													<?php
														$step = ( false === empty( $meta_field[ 'step' ] ) ) ? (int) $meta_field[ 'step' ] : 10;
													?>
													<input name="step" id="step" type="number" min="-1000000" max="1000000" step="any" value="<?php echo $step; ?>">
													<p class="ws-ls-info"><?php echo esc_html__('Specifies the steps between points on the slider.', WE_LS_SLUG); ?></p>
												</div>
											</div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-4">
												<div class="ws-ls-cell">
													<label for="show_all_labels"><?php echo esc_html__('Show all labels', WE_LS_SLUG); ?></label>
												</div>
												<?php $checked = ( false === empty( $meta_field['show_all_labels'] ) && 2 === (int) $meta_field['show_all_labels'] ) ? 2 : 1; ?>
												<div class="ws-ls-cell">
													<select name="show_all_labels" id="show_all_labels">
														<option value="1" <?php selected( $checked, 1 ); ?>><?php echo esc_html__('No', WE_LS_SLUG); ?></option>
														<option value="2" <?php selected( $checked, 2 ); ?>><?php echo esc_html__('Yes', WE_LS_SLUG); ?></option>
													</select>
													<p class="ws-ls-info"><?php echo esc_html__('Note: If set to Yes, all labels shall be displayed on the slider. If no, only the start and finish.', WE_LS_SLUG); ?></p>
												</div>
											</div>
											<div class="ws-ls-row ws-ls-hide ws-ls-meta-fields-additional-5 ws-ls-meta-fields-additional-7">
												<div class="ws-ls-cell">
													<label for="add_options"><?php echo esc_html__('Add options', WE_LS_SLUG); ?></label>
												</div>
												<div class="ws-ls-cell">
													<table class="widefat ws-ls-radio-button-options-table">
														<thead>
														<tr>
															<th class="row-title" width="50%"><?php echo esc_html__( 'Value (visible to admin/export)' , WE_LS_SLUG); ?></th>
															<th><?php echo esc_html__( 'Label (visible to user)' , WE_LS_SLUG); ?></th>
														</tr>
														</thead>
														<tbody>
														<?php
															for( $i = 0; $i < 30; $i++ ) {

																printf(
																	'<tr class="%4$s">
																				<td>
																					<input type="text" name="options-values[%1$d]" id="options-value-%1$d" value="%2$s" maxlength="50" class="widefat"  />
																				</td>
																				<td>
																					<input type="text" name="options-labels[%1$d]" id="options-label-%1$d" value="%3$s" maxlength="50"  class="widefat" />
																				</td>
																			</tr>',
																			$i,
																			( false === empty( $meta_field[ 'options-values' ][ $i ] ) ) ? stripslashes( $meta_field[ 'options-values' ][ $i ] ) : '',
																			( false === empty( $meta_field[ 'options-labels' ][ $i ] ) ) ? stripslashes( $meta_field[ 'options-labels' ][ $i ] ) : '',
																			( $i >= 5 ) ? 'ws-ls-hide' : ''
																);
															}
														?>
														</tbody>
													</table>
													<p><a class="button ws-ls-radio-button-options-show-more"><?php echo esc_html__( 'Show more rows' , WE_LS_SLUG); ?></a></p>
													<p class="ws-ls-info"><?php echo esc_html__('Please note, editing values to existing fields shall cause the user selection to be lost.', WE_LS_SLUG ); ?></p>
												</div>
											</div>
											<div class="ws-ls-row ws-ls-meta-fields-additional-7">
												<div class="ws-ls-cell ws-ls-label-col">
													<label for="mandatory"><?php echo esc_html__('Include empty option', WE_LS_SLUG); ?></label>
												</div>
												<?php $checked = ( false === empty( $meta_field['include_empty'] ) && 2 === (int) $meta_field['include_empty'] ) ? 2 : 0; ?>
												<div class="ws-ls-cell">
													<select name="include_empty" id="include_empty">
														<option value="1" <?php selected( $checked, 1 ); ?>><?php echo esc_html__('No', WE_LS_SLUG); ?></option>
														<option value="2" <?php selected( $checked, 2 ); ?>><?php echo esc_html__('Yes', WE_LS_SLUG); ?></option>
													</select>
													<p class="ws-ls-info"><?php echo esc_html__('If yes, a blank option shall be added at the top of the drop down options.', WE_LS_SLUG ); ?></p>
												</div>
											</div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="abv"><?php echo esc_html__('Abbreviation', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="abv" id="abv" class="<?php if ( true === $validation_fail && true === empty( $meta_field['abv'] ) ) { echo 'ws-ls-mandatory-field'; } ?>" size="40" maxlength="5" value="<?php echo ( false === empty( $meta_field['abv'] ) ) ? esc_attr( $meta_field['abv'] ) : ''; ?>"/><span class="ws-ls-mandatory">*</span>
                                                    <p class="ws-ls-info"><?php echo esc_html__('Used when displaying the field data in smaller spaces e.g. table headers, charts, etc', WE_LS_SLUG ); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell">
                                                    <label for="suffix"><?php echo esc_html__('Suffix', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <input type="text" name="suffix" id="suffix" size="40" maxlength="10" value="<?php echo ( false === empty( $meta_field['suffix'] ) ) ? esc_attr( $meta_field['suffix'] ) : ''; ?>"/>
                                                    <p class="ws-ls-info"><?php echo esc_html__('Text display at to end of the entered value when displaying it to the user. e.g. CM would display in the following manner: 120 CM', WE_LS_SLUG ); ?></p>
                                                </div>
                                            </div>
											<div class="ws-ls-row">
												<div class="ws-ls-cell ws-ls-label-col">
													<label for="sort"><?php echo esc_html__('Group', WE_LS_SLUG); ?></label>
												</div>
												<div class="ws-ls-cell">
													<?php
														$groups 	= ws_ls_meta_fields_groups();
														$groups 	= wp_list_pluck( $groups, 'name', 'id' );
														$selected 	= ( false === empty( $meta_field[ 'group_id' ] ) ) ? (int) $meta_field[ 'group_id' ] : 0;

														echo ws_ls_form_field_select( [ 'key' => 'group_id', 'show-label' => false, 'values' => $groups, 'selected' => $selected ] );

													 	printf( '&nbsp;<a href="%s">%s</a>', ws_ls_meta_fields_groups_link(), esc_html__('Add / remove Groups', WE_LS_SLUG) );
													?>
													<p class="ws-ls-info"><?php echo esc_html__( 'Used to group custom fields together. This gives the ability to specify which groups of fields should appear on forms and charts.', WE_LS_SLUG); ?></p>
												</div>
											</div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="sort"><?php echo esc_html__('Display Order', WE_LS_SLUG); ?></label>
                                                </div>
                                                <div class="ws-ls-cell">
                                                    <?php
                                                    $checked = ( false === empty( $meta_field['sort'] ) ) ? (int) $meta_field['sort'] : 100;
                                                    ?>
                                                    <select name="sort" id="sort">
                                                        <?php for ( $i = 0; $i <= 200; $i = $i + 10 ): ?>
                                                            <option value="<?php echo $i; ?>" <?php selected( $checked, $i ); ?>><?php echo $i; ?></option>
                                                        <?php endfor; ?>
                                                    </select>
                                                    <p class="ws-ls-info"><?php echo esc_html__('Used to specify the order of custom fields. Lower numbers are displayed higher up the form.', WE_LS_SLUG); ?></p>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="mandatory"><?php echo esc_html__('Mandatory', WE_LS_SLUG); ?></label>
                                                </div>
                                                <?php $checked = ( false === empty( $meta_field['mandatory'] ) && 2 === (int) $meta_field['mandatory'] ) ? 2 : 0; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="mandatory" id="mandatory">
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo esc_html__('No', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo esc_html__('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell ws-ls-label-col">
                                                    <label for="enabled"><?php echo esc_html__('Enabled', WE_LS_SLUG); ?></label>
                                                </div>
                                                <?php $checked = ( false === empty( $meta_field['enabled'] ) && 2 === (int) $meta_field['enabled'] ) ? 2 : 1; ?>
                                                <div class="ws-ls-cell">
                                                    <select name="enabled" id="enabled">
                                                        <option value="1" <?php selected( $checked, 1 ); ?>><?php echo esc_html__('No', WE_LS_SLUG); ?></option>
                                                        <option value="2" <?php selected( $checked, 2 ); ?>><?php echo esc_html__('Yes', WE_LS_SLUG); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="ws-ls-row">
                                                <div class="ws-ls-cell"></div>
                                                <div class="ws-ls-cell">
                                                    <a class="comment-submit button" href="<?php echo ws_ls_meta_fields_base_url(); ?>"><?php echo esc_html__('Cancel', WE_LS_SLUG); ?></a>
													<input name="submit_button" type="submit" value="<?php echo esc_html__('Save', WE_LS_SLUG); ?>" class="comment-submit button button-primary"  <?php if ( false === ws_ls_meta_fields_is_enabled() ) { echo ' disabled'; } ?> >
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
