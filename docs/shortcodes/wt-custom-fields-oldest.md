## [wt-custom-fields-oldest]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode is used to display the oldest value for the given custom field.

    [wt-custom-fields-oldest slug="cups-of-water-drank-today"]

 **Shortcode Arguments**
 
The following arguments are the most popular for this shortcode:
 
| Argument | Description | Options | Example |
|slug|Specify the slug of the [custom field]({{ site.baseurl }}/custom-fields.html) you wish to display the value for.|Text|[wt-custom-fields-oldest slug="cups-of-water"]
|user-id|By default, the shortcode will display data for the current user. You can specify this argument to display data for another user ID|Numeric| [wt-custom-fields-oldest user-id="1" slug="cups-of-water"]