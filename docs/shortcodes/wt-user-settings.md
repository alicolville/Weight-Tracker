## [wt-user-settings]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render a user settings form which allows the user to change their Weight Tracker preferences.

[![Table]({{ site.baseurl }}/assets/images/settings.png)]({{ site.baseurl }}/assets/images/settings.png)


**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|allow-delete-data|	If set to false (default is true), the section allowing users to delete their own data is hidden. 5.1.1 onwards.	|True (default) or false|	[wt-user-settings allow-delete-data="false"]
|redirect-url	|If specified, a URL that the user should be redirected to after completing the form.	|A URL|	[wt-user-settings redirect-url="https://www.somewhere.com/settings-saved"]