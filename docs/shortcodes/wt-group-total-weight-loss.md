
## [wt-group-total-weight-loss]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render the the weight lost by the entire [group]({{ site.baseurl }}/groups.html).

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|auto-detect|Auto detect the group that the current user belongs to. This will replace the value specified by the "id" argument|true or false (default)|[wt-group-total-weight-loss auto-detect=true]
|id	|Specifies the group ID to show total weight loss for.	|Number. Defaults to 0|	[wt-group-total-weight-loss id=123]
|text-no-difference|Specifies the text to be displayed if the group cannot be found or there is no weight difference|text|[wt-group-total-weight-loss auto-detect=true text-no-difference="No data could be found" ]