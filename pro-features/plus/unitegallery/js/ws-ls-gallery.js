jQuery( document ).ready(function ($) {
	$(document).ready(function(){

		$(".ws-ls-photos-default, .ws-ls-photos-compact").unitegallery({
			slider_enable_zoom_panel:false,
			gallery_autoplay:false,
			slider_scale_mode: 'fit'
		});

		$(".ws-ls-photos-carousel").unitegallery({
			gallery_width:"100%",
			tile_enable_textpanel:true,
			tile_textpanel_title_text_align: "center",
			tile_textpanel_always_on:true,
			carousel_autoplay: true,
			theme_navigation_margin: 2,
			theme_space_between_arrows: 1
		});

		$("").unitegallery({
			slider_enable_zoom_panel:false,
			gallery_autoplay:false,
			slider_scale_mode: 'fit'
		});
	});
});