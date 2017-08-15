<?php

defined('ABSPATH') or die('Naw ya dinnie!');

// ------------------------------------------------------------------------------
// User search Search box
// ------------------------------------------------------------------------------

function ws_ls_box_user_search_form() {

	?>	<p><?php echo __('Enter a user\'s email address, display name or username and click Search.', WE_LS_SLUG); ?></p>
		<form method="get" action="<?php echo ws_ls_get_link_to_user_data(); ?>">
			<input type="text" name="search" placeholder=""  />
            <input type="hidden" name="page" value="ws-ls-wlt-data-home"  />
            <input type="hidden" name="mode" value="search-results"  />
			<input type="submit" value="Search" />
		</form>
	<?php
}

// ------------------------------------------------------------------------------
// User Side Bar
// ------------------------------------------------------------------------------

function ws_ls_user_side_bar($user_id) {

	if(true === empty($user_id) )  {
		return;
	}

	?>
		<div class="postbox">
			<h2 class="hndle"><?php echo __('User Search', WE_LS_SLUG); ?></h2>
			<div class="inside">
				<?php ws_ls_box_user_search_form(); ?>
			</div>
		</div>
		<div class="postbox">
			<h2 class="hndle"><span><?php echo __('User Information', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
    			<table class="ws-ls-sidebar-stats">

                    <?php $stats = ws_ls_get_entry_counts($user_id); ?>
                    <tr>
                        <th><?php echo __('No. of Entries', WE_LS_SLUG); ?></th>
                        <td><?php echo $stats['number-of-entries']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Start Weight', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_weight_start($user_id); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Latest Weight', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_weight_recent($user_id); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Diff. from Start', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_weight_difference($user_id); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Target Weight', WE_LS_SLUG); ?></th>
                        <td><?php

                            $target = ws_ls_weight_target_weight($user_id, true);
                            echo (true === empty($target)) ? __('No target set', WE_LS_SLUG) : $target;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('Diff. from Target', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_weight_difference_target($user_id); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('Current BMI', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_get_user_bmi(['user-id' => $user_id, 'display' => 'both', 'no-height-text' => __('No height specified', WE_LS_SLUG)]); ?></td>
                    </tr>
					<tr>
                        <th><?php echo __('Height', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_display_user_setting($user_id, 'height'); ?></td>
                    </tr>
					<tr>
                        <th><?php echo __('Gender', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_display_user_setting($user_id, 'gender'); ?></td>
                    </tr>
					<tr>
                        <th><?php echo __('Activity Level', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_display_user_setting($user_id, 'activity_level', false, true); ?></td>
                    </tr>
					<tr>
                        <th><?php echo __('Date of Birth', WE_LS_SLUG); ?></th>
                        <td><?php echo ws_ls_get_dob_for_display($user_id, false); ?> (<?php echo ws_ls_get_age_from_dob($user_id); ?>)</td>
                    </tr>
					<tr class="last">
                        <th><?php echo __('BMR', WE_LS_SLUG); ?></th>
                        <td>
							<?php
									$bmr = ws_ls_calculate_bmr($user_id, false);
									echo (false === empty($bmr)) ? esc_html($bmr) : __('Missing data', WE_LS_SLUG);
							?>
						</td>
                    </tr>
                </table>
			</div>
		</div>
		<div class="postbox">
			<h2 class="hndle"><span><?php echo __('Add Entry', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-primary" href="<?php echo ws_ls_get_link_to_edit_entry($user_id); ?>">
					<?php echo __('Add a new entry', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>
		<div class="postbox">
			<h2 class="hndle"><span><?php echo __('Export Data', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-secondary" href="<?php echo ws_ls_get_link_to_export('csv', $user_id); ?>">
					<?php echo __('To CSV', WE_LS_SLUG); ?>
				</a>
				<a class="button-secondary" href="<?php echo ws_ls_get_link_to_export('json', $user_id); ?>">
					<?php echo __('To JSON', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>
		<div class="postbox">
			<h2 class="hndle"><span><?php echo __('Settings', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-secondary" href="<?php echo ws_ls_get_link_to_user_settings($user_id); ?>">
					<?php echo __('Edit this user\'s settings', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>
		<div class="postbox">
			<h2 class="hndle"><span><?php echo __('Delete Data', WE_LS_SLUG); ?></span></h2>
			<div class="inside">
				<a class="button-secondary delete-confirm" href="<?php echo esc_url(admin_url( 'admin.php?page=ws-ls-wlt-data-home&mode=user&removedata=y&user-id=' . $user_id )); ?>">
					<?php echo __('Delete ALL data for this user', WE_LS_SLUG); ?>
				</a>
			</div>
		</div>

	<?php

}

// ------------------------------------------------------------------------------
// Helper functions
// ------------------------------------------------------------------------------

/**
	Fetch the user's ID from the querystring key user-id
**/
function ws_get_user_id_from_qs(){

	$user_id = ws_ls_querystring_value('user-id', true);

	return (false === empty($user_id) ) ? $user_id : wp_die(__('Error: The User\'s ID was missing...', WE_LS_SLUG)) ;
}
