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
			<h2 class="hndle"><span><?php echo __('Quick Stats', WE_LS_SLUG); ?></span></h2>
			<div class="inside">

				<?php $stats = ws_ls_get_entry_counts($user_id); ?>
				<h4><?php echo __('No. of entries', WE_LS_SLUG); ?></h4>
				<p><?php echo $stats['number-of-entries']; ?></p>

				<h4><?php echo __('Start weight', WE_LS_SLUG); ?></h4>
				<p><?php echo ws_ls_weight_start($user_id); ?></p>

				<h4><?php echo __('Latest weight', WE_LS_SLUG); ?></h4>
				<p><?php echo ws_ls_weight_recent($user_id); ?></p>

				<h4><?php echo __('Difference from start weight', WE_LS_SLUG); ?></h4>
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
