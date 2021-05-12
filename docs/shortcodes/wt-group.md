## [wt-group]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render the user's current [group]({{ site.baseurl }}/groups.html).

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|no-group-text|	Message to display if the user isn't in a [group]({{ site.baseurl }}/groups.html).|	String. Defaults to "No Group"	|[wt-group no-group-text="You are not in a group"]
|user-id	|Specify the user ID to display the [group]({{ site.baseurl }}/groups.html) for. By default, it will show the group for the current user	|Number. Defaults to user ID of the logged in user.|	[wt-group user-id=3]