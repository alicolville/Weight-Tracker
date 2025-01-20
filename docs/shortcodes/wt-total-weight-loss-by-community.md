
## [wt-total-weight-loss-by-community]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render the weight lost by the entire community. If you wish to display the total weight loss for the current user then you should use: [[wt-difference-since-start]]({{ site.baseurl }}/shortcodes-text.html).

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|display	|By default the shortcode displays the words "Gained" and "Lost" in front of the weight value. The default also strips out the negative symbol when required. If you prefer to remove the text, but keep the negative symbol and only have the weight itself, set the display argument to "number" e.g. display="number"|	The value "number" or leave blank.|	[wt-total-lost display='number']
|force-to-kg	|If set to true, override global and user settings and display in Kg.	|True or false (default)	|[wt-total-lost force-to-kg='true']
|invert	|By default the shortcode displays total community weight lost in negative numbers. For example, say the community lost 140Kg between them, the shortcode would actually show -140Kg. If the community has gained 140Kg between them then it will display 140Kg. Some people prefer to inverse this logic - if you wish to, set the argument "invert" to true e.g. invert="true"	|True or false (default).|	[wt-total-lost invert='true']
