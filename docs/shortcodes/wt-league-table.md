## [wt-league-table]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

A shortcode that renders a league table of Weight Tracker users and their respective weight differences.

[![League Table]({{ site.baseurl }}/assets/images/league-table-small.png)]({{ site.baseurl }}/assets/images/league-table.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|display|By default the shortcode displays the words "Gained" and "Lost" in front of the weight value. The default also strips out the negative symbol when required. If you prefer to remove the text, but keep the negative symbol and only have the weight itself, set the display argument to "number" e.g. display="number". By default it will be set to "number".|The value "number" or leave blank.|[wt-league-table display='number']
|force-to-kg|If set to true, override global and user settings and display in Kg.|True or false (default)|[wt-league-table force-to-kg='true']
|ignore_cache|By default the shortcode only looks in the database every 15 mins for the latest stats (reduces load on your database). If you wish to have instant stats, then set this argument to "true" (not recommended).|True or false (default)|[wt-league-table ignore_cache=true]
|invert|By default the shortcode displays total community weight lost in negative numbers. For example, say the community lost 140Kg between them, the shortcode would actually show -140Kg. If the community has gained 140Kg between them then it will display 140Kg. Some people prefer to inverse this logic - if you wish to, set the argument "invert" to true e.g. invert="true"	|True or false (default)|[wt-league-table invert='true']
|losers_only|If set to "true" only users that have lost weight will be in the table.|True or false (default).|[wt-league-table losers_only=true]
|number_to_show|Maximum number of entries to show in table (default is 10).|Number|	[wlt-league-table number_to_show=20]
|show_percentage|If set to "true" then the % lost / gained column shall be included in the table.|True (default) or false|[wt-league-table show_percentage=false]
|order|Sort the table by weight lost / gained. "asc" = most weight lost to least. "desc" = least weight lost to most.|"asc" (default) or "desc"|[wt-league-table order='desc']			