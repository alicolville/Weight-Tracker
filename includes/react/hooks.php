<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Weight Tracker shortcode powered by Reacts
 *
 * @return string
 * @throws Exception
 */
function ws_ls_react_shortcode() {

	ws_ls_react_enqueue();

	return '<div id="yk-wt-react"></div>';
}

add_shortcode('weight-tracker', 'ws_ls_react_shortcode');
