<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_search_results() {

    ws_ls_user_data_permission_check();

    ?>
    <div class="wrap ws-ls-user-data">
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

                                    if(false === empty($results)) {
                                        echo sprintf('<p>%s %s: <em>"%s"</em></p>',
                                            count($results),
                                            __('results were found for:', WE_LS_SLUG),
                                            esc_html($search_term)
                                        );

                                        ?>

                                        <table class="widefat">
                                            <tr>
                                                <th class="row-title"><?php echo __('Username', WE_LS_SLUG) ?></th>
                                                <th><?php echo __('Email', WE_LS_SLUG) ?></th>
                                                <th><?php echo __('No. of Entries', WE_LS_SLUG) ?></th>
                                                <th><?php echo __('Start Weight', WE_LS_SLUG) ?></th>
                                                <th><?php echo __('Recent Weight', WE_LS_SLUG) ?></th>
                                                <th><?php echo __('Target Weight', WE_LS_SLUG) ?></th>
                                            </tr>
                                            <?php

                                                foreach ($results as $user) {
                                                    ws_ls_search_row($user);
                                                }
                                            ?>
                                            </table>
                                            <?php

                                    } else {
                                        echo sprintf('<p>%s: <em>"%s"</em></p>',
                                            __('No users were found for the given search criteria:', WE_LS_SLUG),
                                            esc_html($search_term)
                                        );
                                    }
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

function ws_ls_search_row($user, $class = '') {

    if(false === empty($user)) { ?>

        <tr valign="top" class="<?php echo esc_attr($class); ?>">
            <td scope="row"><a href="<?php echo ws_ls_get_link_to_user_profile($user['ID']); ?>"><?php echo $user['user_nicename']; ?></a></td>
            <td><?php echo sprintf('<a href="mailto:%s">%s</a>', esc_attr($user['user_email']), esc_html($user['user_email'])); ?></td>
            <td><?php echo $user['no_entries']; ?></td>
            <td><?php echo ws_ls_weight_start($user['ID']); ?></td>
            <td><?php echo ws_ls_weight_recent($user['ID']); ?></td>
            <td><?php echo ws_ls_weight_target_weight($user['ID']); ?></td>
        </tr>

    <?php

    }
}