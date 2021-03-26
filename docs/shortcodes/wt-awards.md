## [wt-awards]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

Renders [awards]({{ site.baseurl }}/awards.html) and their associated badges (if applicable) that the user has achieved. Read more about [awards]({{ site.baseurl }}/awards.html).

[![Awards]({{ site.baseurl }}/assets/images/awards-front-end.png)]({{ site.baseurl }}/assets/images/awards-front-end.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|css-class	|Specify an additional CSS class for the gallery frame.	|String (empty by default)	|[wt-awards css-class="a-css-class"]
|height	|Allows you to specify the maximum height of the gallery.|	Number (default: 800).|	[wt-awards height="400"]
|limit|	The maximum number of awards to display.	|Number. Defaults to 20.|	[wt-awards limit="40"]
|user-id|	Specify the user ID to display a gallery for. By default, it will show the gallery for the current user.	|Number. Defaults to user ID of the logged in user.	|[wt-awards user-id=32]
|width	|Allows you to specify the maximum width of the gallery.|	Number (default: 800) or percentage (e.g. 90%)|	[wt-awards width="400"]|