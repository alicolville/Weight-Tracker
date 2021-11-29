## [wt-chart]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode renders a chart of the user's weight and [custom field]({{ site.baseurl }}/custom-fields.html) entries.

The following is an example of how the rendered chart will look:

[![Chart]({{ site.baseurl }}/assets/images/chart-small.png)]({{ site.baseurl }}/assets/images/chart.png)
 
 **Shortcode Arguments**
 
 The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|bezier|If set to false, the bezier curve is disabled. Default is specified within admin settings. Only applies to a line chart.|True or false|[wt-chart bezier=false]
|custom-field-groups|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-chart custom-field-groups='measurements'] [wt-chart custom-field-groups='measurements,fitness-test']
|custom-field-restrict-rows|By default, all entries will be even if none of the [custom fields]({{ site.baseurl }}/custom-fields.html) have been completed. This option allows entries only to be shown if one or more fields have been completed (any) or if all of the fields have been completed (all)|"any" - Display entries that have at least one custom field with data. "all" - Display entries that have all custom fields populated. Leave blank (default) to show all entries regardless of whether custom fields have values against them.|[wt-chart custom-field-restrict-row='all']
|custom-field-slugs|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) slugs. Specifying slugs will ensure only the fields specified are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-chart custom-field-slugs='waist'] [wt-chart custom-field-slugs='waist,bicep,distance-run']
|ignore-login-status|If set to true, the chart will be show even if the user is not logged in (should be used alongside user-id attribute).|True or false (default)|[wt-chart user-id=21 ignore-login-status=true]
|message-no-data|Specify the message to display if there is no data to display on the chart.|Text|[wt-chart message-no-data="No data could be found"]|
|max-data-points|The maximum number of data points to plot on the chart. Default is the specified in admin settings.|Numeric|[wt-chart max-data-points=5]
|show-gridlines|If set to false, gridlines are removed from the chart. Default is specified within admin settings.|True or false|[wt-chart show-gridlines=false]
|show-custom-fields|If set to false, meta fields shall be hidden from the chart. Default is specified within admin settings.|True or false.|[wt-chart show-custom-fields=false]
|show-target|If set to false, the target weight shall be hidden from the chart. If setting this value to false, you may consider using [[wt-custom-fields-chart]]({{ site.baseurl }}/shortcodes/wt-custom-fields-chart.html) instead.|True or false.|[wt-chart show-target=false]
|show-weight|If set to false, the weight entries shall be hidden from the chart. If setting this value to false, you may consider using [[wt-custom-fields-chart]]({{ site.baseurl }}/shortcodes/wt-custom-fields-chart.html) instead.|True or false.|[wt-chart show-weight=false]
|type|Type of chart to display.|'line' (default) or 'bar'|[wt-chart type='bar']
|user-id|By default, the shortcode will display the chart for the current user. You can display the chart for another user by setting this argument to the relevant user ID. You may wish to set the argument "ignore-login-status" to true.|Numeric| [wt-chart user-id="1"]
|weight-fill-color|The fill colour for weight entries (only when type is set to "bar"). Default is the specified in admin settings.|Hex value|[wt-chart weight-fill-color='#000']
|weight-line-color|The line colour for weight entries. Default is the specified within admin settings.|Hex value|[wt-chart weight-line-color='#000']
|weight-target-color|The line colour for target. Default is the specified within admin settings.|Hex value|[wt-chart target-line-color='#000']		