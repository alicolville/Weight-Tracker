## [wt-gallery]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render a gallery of the user’s uploaded photos ([custom fields]({{ site.baseurl }}/custom-fields.html)). The shortcode supports two different types of gallery:

**Default**

[![Default]({{ site.baseurl }}/assets/images/photo-gallery-default.png)]({{ site.baseurl }}/assets/images/photo-gallery-default.png)

**Carousel**

[![Default]({{ site.baseurl }}/assets/images/photo-gallery-carousel.png)]({{ site.baseurl }}/assets/images/photo-gallery-carousel.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|css-class	|Specify an additional CSS class for the gallery frame.|	String (empty by default)|	[wt-gallery css-class="a-css-class"]
|direction	|Order to show photos in.	|'desc' (default) to show newest first or 'asc' for oldest first.|	[wt-gallery direction="desc"]
|custom-fields-hide-from-shortcodes	|Determines whether or not to exclude all photo fields marked as "Hide from shortcodes"|	True (default) or False|	[wt-gallery custom-fields-hide-from-shortcodes="false"]
|custom-fields-to-use	|Keys of one or more Custom Field Photo keys to display within the gallery.	|Defaults to all enabled Custom Photo Fields if not specified	|[wt-gallery custom-fields-to-use="24"] [wt-gallery custom-fields-to-use="34,53"]
|error-message|	Message to display if a no photos could not be found.	|String. Defaults to an in-build message.	|[wt-gallery error-message="No photos"]
|height|	Allows you to specify the maximum height of the gallery.	|Number (default: 800).	|[wt-gallery height="400"]
|limit	|Maximum number of photos to show.	|Number (default: 20).	|[wt-gallery limit="40"]
|mode	|Specify the type of gallery to display. (note: Not applicable when using [[wt-awards]]({{ site.baseurl }}/shortcodes/wt-awards.html)	|'default' (default), 'compact', 'carousel' or 'tilesgrid'.	|[wt-gallery mode="carousel"]
|user-id|	Specify the user ID to display a gallery for. By default, it will show the gallery for the current user	|Number. Defaults to user ID of the logged in user.	|[wt-gallery user-id=32]
|width|	Allows you to specify the maximum width of the gallery.|	Number (default: 800) or percentage (e.g. 90%)|	[wt-gallery width="400"]