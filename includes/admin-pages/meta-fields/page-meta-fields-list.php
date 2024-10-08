<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_list_page() {

        ws_ls_data_table_enqueue_scripts();

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
                            <h3 class="hndle"><span><?php echo esc_html__('Custom Fields', WE_LS_SLUG); ?></span></h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <p><?php echo esc_html__( 'Custom Fields allows you to ask your user\'s additional questions when adding a weight entry.
                                                        For example, you may wish to ask them how many cups of water they drank today or perhaps how they are feeling.
                                                            You can use the following screen to add as many questions as you wish.', WE_LS_SLUG); ?>
                                                        <a href="https://docs.yeken.uk/custom-fields.html" target="_blank" rel="noopener"><?php echo esc_html__('Read more about Custom Fields', WE_LS_SLUG); ?></a>
                                                        </p>

	                            <?php

									$base_url 			= ws_ls_meta_fields_base_url( [ 'mode' => 'add-edit' ] );
									$meta_field_types 	= ws_ls_meta_fields_types();

									foreach ( $meta_field_types as $key => $name ) {
										printf( '<a href="%1$s&amp;field_type=%2$d" class="button">
															%3$s %4$s %5$s
														</a> ',
														esc_url( $base_url ),
														$key,
														esc_html__( 'Add', WE_LS_SLUG ),
														$name,
														esc_html__( 'field', WE_LS_SLUG )
										);
									}

	                            ?>
								<a href="<?php echo esc_url( ws_ls_meta_fields_groups_link() ); ?>&amp;field_type=2" class="button"><?php echo esc_html__( 'Add / remove groups', WE_LS_SLUG ); ?></a>

                                <table class="ws-ls-meta-fields-list-ajax table ws-ls-loading-table" id="meta-fields-list"
                                       data-paging="true"
                                       data-filtering="true"
                                       data-sorting="true"
                                       data-editing="true"
                                       data-cascade="true"
                                       data-toggle="true"
                                       data-paging-size="40"
                                       data-use-parent-width="true">
                                </table>
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

