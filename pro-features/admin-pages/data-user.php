<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_user() {

	$user_id = ws_ls_querystring_value('user-id', true);

	if(true === empty($user_id) )  {
		return;
	}

	$user_data = get_userdata( $user_id );

?>
<div class="wrap">
	<h1><?php echo $user_data->user_nicename; ?>
			<?php echo ws_ls_get_email_link($user_id, true); ?></h1>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Chart', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php

								// Fetch last 25 weight entries
								$weight_data = ws_ls_get_weights($user_id, 25, -1, 'desc');

								// Reverse array so in cron order
								$weight_data = array_reverse($weight_data);

								echo ws_ls_display_chart($weight_data, ['type' => 'line', 'max-points' => 25, 'user-id' => $user_id]);

							?>
						</div>
					</div>
					<div class="postbox">
						<h2 class="hndle"><span><?php echo __('Entries', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
							<?php ws_ls_data_table_placeholder($user_id); ?>
						</div>
					</div>
				</div>
			</div>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h2><span><?php echo __('Quick Stats', WE_LS_SLUG); ?></span></h2>
						<div class="inside">
                           	<h4><?php echo __('Start weight', WE_LS_SLUG); ?></h4>
							<p><?php echo ws_ls_weight_start($user_id); ?></p>

							<h4><?php echo __('Latest weight', WE_LS_SLUG); ?></h4>
							<p><?php echo ws_ls_weight_recent($user_id); ?></p>

							<h5><?php echo __('Difference from start weight', WE_LS_SLUG); ?></h5>
							<p><?php echo ws_ls_weight_difference($user_id); ?></p>

							<h4><?php echo __('Target weight', WE_LS_SLUG); ?></h4>
							<p><?php

                                    $target = ws_ls_weight_target_weight($user_id, true);
                                    echo (true === empty($target)) ? __('No target weight has been set', WE_LS_SLUG) : $target;
                                ?>
                            </p>

							<h4><?php echo __('Difference from target', WE_LS_SLUG); ?></h4>
							<p><?php echo ws_ls_weight_difference_target($user_id); ?></p>

                            <?php
                                $height = ws_ls_get_user_height($user_id);
                                $heights = (false === $height) ? false : ws_ls_heights();
                            ?>
							<h4><?php echo __('Current Height', WE_LS_SLUG); ?></h4>
							<p>
                                <?php
                                    echo (false === empty($heights[$height])) ? $heights[$height] : __('No height specified', WE_LS_SLUG) ;
                                ?>
                            </p>

							<h4><?php echo __('Current BMI', WE_LS_SLUG); ?></h4>
							<p><?php echo ws_ls_get_user_bmi(['user-id' => $user_id, 'display' => 'both', 'no-height-text' => __('No height specified', WE_LS_SLUG)]); ?></p>

						</div>
					</div>
                    <div class="postbox">
                        <h2><span><?php echo __('Add Entry', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <a class="button-primary" href="<?php echo ws_ls_get_link_to_edit_entry($user_id); ?>">
                                <?php echo __('Add a new entry', WE_LS_SLUG); ?>
                            </a>
                        </div>
                    </div>
                    <div class="postbox">
                        <h2><span><?php echo __('Export Data', WE_LS_SLUG); ?></span></h2>
                        <div class="inside">
                            <a class="button-secondary" href="#">
                                <?php echo __('To CSV', WE_LS_SLUG); ?>
                            </a>
                            <a class="button-secondary" href="#">
                                <?php echo __('To JSON', WE_LS_SLUG); ?>
                            </a>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
<?php
}
