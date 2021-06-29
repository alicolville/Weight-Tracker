## [wt-photo-oldest]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render the the oldest photo ([a custom field]({{ site.baseurl }}/custom-fields.html)) uploaded by the user.

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|css-class|	Specify an additional CSS class for the photo frame.|	String (empty by default)|	[wt-photo-oldest css-class="a-css-class"]
|custom-fields-to-use|	Slugs of one or more photo ([custom field]({{ site.baseurl }}/custom-fields.html)) to display within the shortcode.	|All enabled photo fields that aren't hidden from shortcodes.	|[wt-photo-recent custom-fields-to-use="front,back" ]
|error-message|	Message to display if a relevant photo could not be found.	|String. Defaults to an in-build message.|	[wt-photo-oldest error-message="No photo!!"]
|height|	Allows you to specify the maximum height for the photo. It is best to specify the width argument as well.|	Number (default: 200).|	[wt-photo-oldest height="400" width="400"]
|hide-date|	If set to true, hide the date that is displayed.|	True or false (default)	|[wt-photo-oldest hide-date=true]|
|maximum|	Maximum number of photos to render.	|Numeric (defaults to 1)	|[wt-photo-recent maximum=3]
|width|	Allows you to specify the maximum width for the photo. It is best to specify the height argument as well.|	Number (default: 200).	|[wt-photo-oldest height="400" width="400"]
|user-id|By default, the shortcode will display the oldest photo for the current user. You can display the oldest photo for another user by setting this argument to the relevant user ID.|Numeric| [wt-photo-oldest user-id="1"]
