# Custom sizes

For some photo based shortcodes, you have the ability to pass a "custom-size" argument. In essence, this allows you pass a WordPress image sizes. These can include the standard ones [defined in WordPress core](https://developer.wordpress.org/reference/functions/add_image_size/#reserved-image-size-names)  (e.g. ‘thumb’, ‘thumbnail’, ‘medium’, ‘medium_large’, ‘large’, and ‘post-thumbnail’) or ones you have defined yourself.

### Defining your own images sizes

There are various ways to define your own images sizes, in code, themes, plugins, etc. This guide will briefly cover how to do it in code.

In essence, you need a snippet of code like the one below. This defines a custom image size of "wt-photo", with a with of 400px, a height of 600px and for it to be cropped.

```
function wpdocs_theme_setup() {
    add_image_size( 'wt-photo', 400, 600, true ); // (cropped)
}
add_action( 'after_setup_theme', 'wpdocs_theme_setup' );
```

There are plenty of guides out there, but I've found [this one pretty handy](https://developer.wordpress.org/reference/functions/add_image_size).

> Please note: When creating new custom sizes, you will need to force WordPress to regenerate all thumbnails in the media library. A good plugin for this is [Regenrate Thumbnails](https://wordpress.org/plugins/regenerate-thumbnails/).

### Using your custom size

Once you have a custom size, you can use the size name within supported Weight Tracker shortcodes. Given the example above, you can use the custom size "wt-photo" in the following way:

```
[wt-photo-oldest custom-fields-to-use="photo" custom-size="wt-photo"]
```