## [wt-table]

> The following shortcode is available in both the free and [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin. However, the non-pro version is a simple HTML table with limited functionality.

This shortcode displays the weight and [custom fields]({{ site.baseurl }}/custom-fields.html) for the given user in a tabular format. Below is an example of how the rendered shortcode looks ([Pro]({{ site.baseurl }}/upgrade.html) version).

[![Table]({{ site.baseurl }}/assets/images/table.png)]({{ site.baseurl }}/assets/images/table.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|bmi-format|	Specify the format that BMI should be displayed in (within data tables)	|'label' (default), 'both' or 'index'	|[wt-table bmi-format='both']
|custom-field-groups (8.4+)|	Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.	|An individual slug or multiple slugs comma delimited.	|[wt-table custom-field-groups='measurements'] [wt-table custom-field-groups='measurements,fitness-test']
|custom-field-restrict-rows|By default, all entries will shown even if none of the [custom fields]({{ site.baseurl }}/custom-fields.html) have been completed. This option allows entries only to be shown if one or more custom fields have been completed (any) or only if all of the fields have been completed (all)|"any" - Display entries that have at least one custom field with data. "all" - Display entries that have all custom fields populated. Leave blank (default) to show all entries regardless of whether custom fields have values against them.|[wt-table custom-field-restrict-rows='all']
|custom-field-slugs (8.4+)|	Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) slugs. Specifying slugs will ensure only the fields specified are displayed on the control.|	An individual slug or multiple slugs comma delimited.|	[wt-table custom-field-slugs='waist'] [wt-table custom-field-slugs='waist,bicep,distance-run']
|enable-add-edit	|If set to true (default is false) show the delete/edit icons to allow a users to edit their data.	|true or false (default)|	[wt-table enable-add-edit=true]
|enable-bmi|	If set to false (default is true), BMI will be hidden from each row.|	true (default) or false	|[wt-table enable-bmi=true]
|enable-notes|	If set to false (default is true), notes will be hidden from each row.|	true (default) or false	|[wt-table enable-notes=true]
|enable-weight|	If set to false (default is true), Weight and Weight difference will be hidden from each row.|	true (default) or false	|[wt-table enable-weight=true]
|enable-custom-fields|	If set to true (default is false), meta fields shall be included in each row.|	true or false (default)	|[wt-table enable-custom-fields=true]
|user-id|By default, the shortcode will display the table for the current user. You can display the table for another user by setting this argument to the relevant user ID.|Numeric| [wt-table user-id="1"]
