## [wt-group-view-as-table]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

A shortcode that renders a table of summary information for users within a given group.

[![Table]({{ site.baseurl }}/assets/images/wt-group-view-as-table.png)]({{ site.baseurl }}/assets/images/wt-group-view-as-table.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|disable-main-font|If set to true will disable the main font used in the shortcode|True or False (default)|[wt-group-view-as-table disable-main-font=true]
|disable-theme-css|If set to true will disable the additional CSS used in the shortcode|True or False (default)|[wt-group-view-as-table disable-theme-css=true]
|group-id	|Specifies the group ID to show user information for.	|Number. Defaults to 0|	[wt-group-view-as-table group-id=123]
|enable-group-select	|If enabled, display a dropdown list of all user groups. Selecting an option shall show data for that given group.|"true" (default) or "false"|	[wt-group-view-as-table enable-group-select="false"]
|todays-entries-only|If enabled, users will only be shown for the given group if they have added an entry for today|"true" or "false" (default)|[wt-group-view-as-table todays-entries-only="true"]