## [wt-challenges]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode will render a [Challenge]({{ site.baseurl }}/challenges.html) league table.

[![Challenges]({{ site.baseurl }}/assets/images/challenges-frontend-small.png)]({{ site.baseurl }}/assets/images/challenges-frontend.png)
 
**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|id|ID of the challenge league table to display|Numeric|[wt-challenges id="1"]
|show-filters|If true, displays a set of filters that allows the user to filter the table by gender, age range, group, opted in status and number of meal entries.|True or False (default)|[wt-challenges id="1" show-filters="false"]
|sums-and-averages|If true, display the additional summary table containing the sums and averages of the league table.|Boolean (default true)|[wt-challenges id="1" sums-and-averages="false"]