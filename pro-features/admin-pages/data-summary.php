<?php

defined('ABSPATH') or die('Naw ya dinnie!');

function ws_ls_admin_page_data_summary() {

?>
<div class="wrap">

			<h1>WordPress Admin Style</h1>

					<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<div class="postbox">
							<h2><span><?php echo __('All entries', WE_LS_SLUG); ?></span></h2>
							<div class="inside">
								<?php ws_ls_data_table_placeholder(false, false); ?>
							</div>
						</div>
					</div>
				</div>

				<!-- sidebar -->
				<div id="postbox-container-1" class="postbox-container">
					<div class="meta-box-sortables">
						<div class="postbox">
							<h2><span><?php echo __('User Search', WE_LS_SLUG); ?></span></h2>
							<div class="inside">
								<?php ws_ls_box_user_search_form(); ?>
							</div>
						</div>
						<div class="postbox">

							<h2><span><?php echo __('Quick Stats', WE_LS_SLUG); ?></span></h2>

							<div class="inside">
								<ul>
									<li>
										<a href="http://dotorgstyleguide.wordpress.com/">WordPress.org UI Style Guide</a>
									</li>
									<li>
										<a href="http://make.wordpress.org/core/handbook/coding-standards/html/">HTML Coding Standards</a>
									</li>
									<li>
										<a href="http://make.wordpress.org/core/handbook/coding-standards/css/">CSS Coding Standards</a>
									</li>
									<li>
										<a href="http://make.wordpress.org/core/handbook/coding-standards/php/">PHP Coding Standards</a>
									</li>
									<li>
										<a href="http://make.wordpress.org/core/handbook/coding-standards/javascript/">JavaScript Coding Standards</a>
									</li>
									<li><a href="http://make.wordpress.org/ui/">WordPress UI Group</a></li>
								</ul>
							</div>

						</div>
						<!-- .postbox -->

					</div>
					<!-- .meta-box-sortables -->

				</div>
				<!-- #postbox-container-1 .postbox-container -->

			</div>
			<br class="clear">
		</div>

<?php

}
