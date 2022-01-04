<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_shortcode_beta( $user_defined_arguments ) {

	ws_ls_enqueue_uikit();

	return '
<div class="uk-scope">


<button class="ykuk-button ykuk-button-default ykuk-margin-small-right" type="button" uk-toggle="target: #offcanvas-usage">Open</button>

<a href="#offcanvas-usage" ykuk-toggle>Open</a>

<div id="offcanvas-usage" ykuk-offcanvas>
    <div class="ykuk-offcanvas-bar">

        <button class="ykuk-offcanvas-close" type="button" ykuk-close></button>

        <h3>Title</h3>

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

    </div>
</div>


<div class="ykuk-flex ykuk-flex-center">
    <div>1</div>
    <div>2</div>
    <div>4</div>
</div>

				<ul ykuk-tab >
			    <li class="ykuk-active"><a href="">Hello</a></li>
			    <li><a href="">Hello</a></li>
			    <li class="ykuk-disabled"><a>Hello</a></li>
			</ul>
			<ul class="ykuk-switcher ykuk-margin">
    <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
    <li>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</li>
    <li>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur, sed do eiusmod.</li>
</ul>
<span class="ykuk-badge">1</span>
<ul ykuk-accordion>
    <li class="ykuk-open">
        <a class="ykuk-accordion-title" href="#">Header 1</a>
        <div class="ykuk-accordion-content">Some content here</div>
    </li>
     <li>
        <a class="ykuk-accordion-title" href="#">Header 2</a>
        <div class="ykuk-accordion-content">2 Some content here</div>
    </li>
</ul>

			<script>
			jQuery( document ).ready(function ($) {
			ykukUIkit.notification(\'My message\');
			});
</script>
<div class="ykuk-inline">
    <button class="ykuk-button ykuk-button-default" type="button">Hover</button>
    <div ykuk-dropdown>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
</div>

<div class="ykuk-inline">
    <button class="ykuk-button ykuk-button-default" type="button">Click</button>
    <div ykuk-dropdown="mode: click">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</div>
</div>

	</div>
			';


}
add_shortcode( 'wt-beta', 'ws_ls_shortcode_beta' );
