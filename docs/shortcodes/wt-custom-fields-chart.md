## [wt-custom-fields-chart]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode renders a chart of the user’s [custom field]({{ site.baseurl }}/custom-fields.html) entries.

The following is an example of how the rendered chart will look:

[![Chart]({{ site.baseurl }}/assets/images/custom-fields-chart.png)]({{ site.baseurl }}/assets/images/custom-fields-chart.png)

> This shortcode is a helper that re-uses [[wt-chart]]({{ site.baseurl }}/shortcodes/wt-chart.html) functionality. The aim of this shortcode is to configure [wt-chart] to render [custom fields]({{ site.baseurl }}/custom-fields.html) only on a chart.
 
 **Shortcode Arguments**
 
The following arguments are the most popular for this shortcode:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|custom-field-groups|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-custom-fields-chart custom-field-groups='measurements'] [wt-custom-fields-chart custom-field-groups='measurements,fitness-test']
|custom-field-restrict-rows|By default, entries will be shown that have values set against one or more of the specified [custom fields]({{ site.baseurl }}/custom-fields.html).|"any" (default) - Display entries that have at least one custom field with data. "all" - Display entries that have all custom fields populated. Leave blank to show all entries regardless of whether custom fields have values against them.|[wt-custom-fields-chart custom-field-restrict-rows='all']
|custom-field-slugs|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) slugs. Specifying slugs will ensure only the fields specified are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-custom-fields-chart custom-field-slugs='waist'] [wt-custom-fields-chart custom-field-slugs='waist,bicep,distance-run']

*For more options,* please refer to the arguments outlined in [[wt-chart]]({{ site.baseurl }}/shortcodes/wt-chart.html). By using this shortcode weight (show-weight) and target entries (show-target) are disabled by default.