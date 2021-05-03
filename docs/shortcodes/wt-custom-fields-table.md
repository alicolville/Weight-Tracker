## [wt-custom-fields-table]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode displays the [custom field]({{ site.baseurl }}/custom-fields.html) entries for the given user in a tabular format. Below is an example of how the rendered shortcode looks.

The following is an example of how the rendered table will look:

[![Chart]({{ site.baseurl }}/assets/images/custom-fields-table.png)]({{ site.baseurl }}/assets/images/custom-fields-table.png)

> This shortcode is a helper that re-uses [[wt-table]]({{ site.baseurl }}/shortcodes/wt-table.html) functionality. The aim of this shortcode is to configure [wt-table] to render [custom fields]({{ site.baseurl }}/custom-fields.html) only on a table.
 
 **Shortcode Arguments**
 
The following arguments are the most popular for this shortcode:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|custom-field-groups|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-custom-fields-table custom-field-groups='measurements'] [wt-custom-fields-table custom-field-groups='measurements,fitness-test']
|custom-field-slugs|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) slugs. Specifying slugs will ensure only the fields specified are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-custom-fields-table custom-field-slugs='waist'] [wt-custom-fields-table custom-field-slugs='waist,bicep,distance-run']

*For more options,* please refer to the arguments outlined in [[wt-table]]({{ site.baseurl }}/shortcodes/wt-table.html). By using this shortcode BMI (enable-bmi), weight (enable-weight) and notes (enable-notes) are disabled by default.