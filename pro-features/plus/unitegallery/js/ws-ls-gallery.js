jQuery( document ).ready(function ($) {
	$(document).ready(function(){

		$(".ws-ls-photos-default, .ws-ls-photos-compact").unitegallery({
			gallery_autoplay:false,
			slider_enable_zoom_panel:false,
			slider_scale_mode: 'fit',
            gallery_height: ws_ls_gallery_config['height'],
            gallery_width: ws_ls_gallery_config['width']
       	});

		$(".ws-ls-photos-tilesgrid").unitegallery({
			gallery_theme: "tilesgrid",
			grid_num_rows: 1,
			grid_padding:0,
			grid_space_between_cols:5,
			grid_space_between_rows:5,
			tile_enable_border:false,
			tile_enable_shadow:false,
			tile_enable_textpanel: true,
			tile_height: 140,
			tile_textpanel_always_on: true,
			tile_textpanel_title_text_align: "center",
			tile_width: 140
		});

		$(".ws-ls-photos-carousel").unitegallery({
			tile_enable_textpanel: true,
			tile_height: 140,
			tile_width: 140,
            carousel_autoplay: false,
            carousel_space_between_tiles:5,
            gallery_images_preload_type: 'visible',
            gallery_width: "100%",
            slider_textpanel_desc_font_size: '4px',
            slider_textpanel_title_font_size: '4px',
            theme_navigation_margin: 2,
            theme_space_between_arrows: 1,
            tile_enable_image_effect:false,
            tile_enable_overlay:false,
            tile_textpanel_always_on: true,
            tile_textpanel_title_text_align: "center"
        });
	});
});