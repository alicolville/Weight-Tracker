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

                        <div class="postbox">
                            <h3 class="hndle"><span><?php echo __('Custom Fields', WE_LS_SLUG); ?> </span></h3>
                            <div style="padding: 0px 15px 0px 15px">

                                <p>TODO: ADD TEXT describing custom fields</p>
                                <table class="ws-ls-meta-fields-list-ajax table" id="meta-fields-list"
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

