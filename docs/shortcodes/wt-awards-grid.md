## [wt-awards-grid]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render all [awards]({{ site.baseurl }}/awards.html) (that have a badge) in a grid format for the given user. Read more about [awards]({{ site.baseurl }}/awards.html).

[![Awards]({{ site.baseurl }}/assets/images/awards-grid.png)]({{ site.baseurl }}/assets/images/awards-grid.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|message	|Message to display if no awards can be found.	|String. Defaults to an in-build message.|	[wt-awards-grid message="No awards found!"]
|thumb-height	|Allows you to specify the maximum height for the badge. It is best to specify the width argument as well.|	Number (default: 200).|	[wt-awards-grid thumb-height="400" thumb-width="400"]
|thumb-width	|Allows you to specify the maximum width for the badge. It is best to specify the height argument as well.|	Number (default: 200).	|[wt-awards-grid thumb-height="400" thumb-width="400"]
|user-id	|Specify the user ID to display awards for. By default, it will show awards for the current user.|	Number. Defaults to user ID of the logged in user.	|[wt-awards-grid user-id=3]