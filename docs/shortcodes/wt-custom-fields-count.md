## [wt-custom-fields-count]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode is used to display a count of how many times a custom field has been entered for the given custom field.

    [wt-custom-fields-count slug="cups-of-water-drank-today"]

 **Shortcode Arguments**
 
The following arguments are the most popular for this shortcode:
 
| Argument | Description | Options | Example |
|slug|Specify the slug of the [custom field]({{ site.baseurl }}/custom-fields.html) you wish to display the count for.|Text|[wt-custom-fields-count slug="cups-of-water"]
|user-id|By default, the shortcode will display data for the current user. You can specify this argument to display data for another user ID.|Numeric| [wt-custom-fields-count user-id="1" slug="cups-of-water"]