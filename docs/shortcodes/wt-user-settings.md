## [wt-user-settings]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render a user settings form which allows the user to change their Weight Tracker preferences.

[![]({{ site.baseurl }}/assets/images/settings.png)]({{ site.baseurl }}/assets/images/settings.png)

> Please ensure you have the Weight Tracker setting "User Experience" > "Allow user settings" set to "Yes".


Allow user settings

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
****|hide-activity-level|Hide Activity Level field|true or false (default)|[wt-user-settings hide-activity-level=true]
|hide-dob|Hide Date of Birth field|true or false (default)|[wt-user-settings hide-dob=true]
|hide-extras|Hide additional fields|true or false (default)|[wt-user-settings hide-extras=true]
|hide-height|Hide height field|true or false (default)|[wt-user-settings hide-height=true]
|hide-gender|Hide gender field|true or false (default)|[wt-user-settings hide-gender=true]
|hide-preferences|Hide preference fields|true or false (default)|[wt-user-settings hide-preferences=true]
|hide-titles|Hide the section titles|True or false (default)|[wt-user-settings hide-titles=true]
|redirect-url	|If specified, a URL that the user should be redirected to after completing the form.	|A URL|	[wt-user-settings redirect-url="https://www.somewhere.com/settings-saved"]
|show-delete-data|Show or hide the delete data section|True (default) or false|[wt-user-settings show-delete-data=false]
|show-user-preferences|Show or hide the user preferences section|True (default) or false|[wt-user-settings show-user-preferences=false]