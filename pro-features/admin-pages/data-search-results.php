<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_search_results() {

    ?>
    <div class="wrap">
    <h1><?php echo __('Search Results', WE_LS_SLUG); ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <h2 class="hndle"><span><?php echo __('Search Results', WE_LS_SLUG); ?></span></h2>

                        <div class="inside">
                            <?php

                                $search_term = (false === empty($_GET['search'])) ? $_GET['search'] : false ;

                                if($search_term) {

                                    $results = ws_ls_user_search($search_term);

                                    var_dump($search_term,$results);

                                } else {
                                    echo __('No search terms were specified', WE_LS_SLUG);
                                }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}
