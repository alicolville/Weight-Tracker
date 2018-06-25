<?php

    defined('ABSPATH') or die("Jog on!");

    function ws_ls_meta_fields_page() {
        ?>
        <div class="wrap">

            <div id="icon-options-general" class="icon32"></div>

            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-3">

                    <!-- main content -->
                    <div id="post-body-content">

                        <div class="meta-box-sortables ui-sortable">

                            <div class="postbox">
                                <h3 class="hndle"><span><?php echo __('TODO', WE_LS_SLUG); ?> </span></h3>
                                <div style="padding: 0px 15px 0px 15px">

                                    <?php
                                        $t = ws_ls_meta_fields();
//var_dump( $t);
                                       // var_dump($t);

                                  // 'abv', 'chartable', 'field_key', 'field_name'

//$r =  ws_ls_meta_unit_update(['id' => 16, 'field_key' => 'cdm', 'field_name' => 'Centremetres', 'abv' => 'cm!', 'chartable' => 1 ]);
//$r =  ws_ls_meta_unit_add(['field_key' => 'abcdefghijklmnopqrstuvwxyz', 'field_name' => 'Centremetres', 'abv' => 'cm!', 'chartable' => 1 ]);
// v
            //$r = ws_ls_meta_unit_delete(3);

                         //           $r = ws_ls_meta_unit_key_exist( 'cm1' );

           // var_dump($r);

                                    // 'abv', 'chartable', 'display_on_chart', 'field_key', 'field_name', 'unit_id', 'system'
                                    ws_ls_meta_fields_delete(5);
 $r = ws_ls_meta_fields_update(['id' => 1, 'field_key' => 'test-should', 'field_name' => 'Shers', 'abv' => 'SH', 'display_on_chart' => 1, 'unit_id' => 99, 'system' => 1 ]);
                                    var_dump($r);
                                    ?>

                                </div>
                            </div>

                        </div>
                        <!-- .meta-box-sortables .ui-sortable -->

                    </div>
                    <!-- post-body-content -->

                </div>
                <!-- #post-body .metabox-holder .columns-2 -->

                <br class="clear">
            </div>
            <!-- #poststuff -->

        </div> <!-- .wrap -->
        <?php
    }