## [wt-photo-recent]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render the the recent photo ([a custom field]({{ site.baseurl }}/custom-fields.html)) uploaded by the user.

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|css-class|	Specify an additional CSS class for the photo frame.|	String (empty by default)|	[wt-photo-recent css-class="a-css-class"]
|custom-fields-to-use|	Keys of one or more ([custom field]({{ site.baseurl }}/custom-fields.html)) photo keys to display within the shortcode.	|All enabled photo fields that aren't hidden from shortcodes.	|[wt-photo-recent custom-fields-to-use="3,4" ]
|error-message|	Message to display if a relevant photo could not be found.	|String. Defaults to an in-build message.|	[wt-photo-recent error-message="No photo!!"]
|height|	Allows you to specify the maximum height for the photo. It is best to specify the width argument as well.|	Number (default: 200).|	[wt-photo-recent height="400" width="400"]
|hide-date|	If set to true, hide the date that is displayed.|	True or false (default)	|[wt-photo-recent hide-date=true]|
|maximum|	Maximum number of photos to render.	|Numeric	|[wt-photo-recent maximum=3]
|width|	Allows you to specify the maximum width for the photo. It is best to specify the height argument as well.|	Number (default: 200).	|[wt-photo-recent height="400" width="400"]
|user-id|By default, the shortcode will display the oldest photo for the current user. You can display the oldest photo for another user by setting this argument to the relevant user ID.|Numeric| [wt-photo-recent user-id="1"]