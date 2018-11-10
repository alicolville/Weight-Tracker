<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_awards_list_page() {

        ws_ls_data_table_enqueue_scripts();

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
                            <h3 class="hndle"><span><?php echo __('Awards', WE_LS_SLUG); ?></span></h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <p><?php echo __('TODO', WE_LS_SLUG); ?>
                                                        <a href="https://weight.yeken.uk/awards/" target="_blank" rel="noopener"><?php echo __('Read more about Awards', WE_LS_SLUG); ?></a>
                                                        </p>


                                <table class="table ws-ls-awards-list-ajax" id="awards-list"
                                       data-paging="true"
                                       data-filtering="true"
                                       data-sorting="true"
                                       data-editing="true"
                                       data-cascade="true"
                                       data-toggle="true"
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

