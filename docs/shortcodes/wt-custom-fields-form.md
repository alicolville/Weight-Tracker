## [wt-custom-fields-form]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode is an extension of [[wt-form]]({{ site.baseurl }}/shortcodes/wt-form.html) and displays a form for custom [custom fields]({{ site.baseurl }}/custom-fields.html) only.

> By default, all custom fields shall be displayed by this shortcode. To restrict which custom fields are shown, use the arguments "custom-field-groups" or "custom-field-slugs".

Below are example custom field forms:

### Custom fields for a given group

[![]({{ site.baseurl }}/assets/images/wt-custom-fields-form-1.png)]({{ site.baseurl }}/assets/images/wt-custom-fields-form-1.png)

### All enabled custom fields

[![]({{ site.baseurl }}/assets/images/wt-custom-fields-form-2.png)]({{ site.baseurl }}/assets/images/wt-custom-fields-form-2.png)


 **Shortcode Arguments**
 
The following arguments are the most popular for this shortcode:
 
| Argument | Description | Options | Example |
|class|Allows you to specify an additional CSS class on the element.|Text|[wt-form class='additional-css-class']
|custom-field-groups|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-form custom-field-groups='measurements'] [wt-form custom-field-groups='measurements,fitness-test']
|custom-field-slugs|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) slugs. Specifying slugs will ensure only the fields specified are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-form custom-field-slugs='waist'] [wt-form custom-field-slugs='waist,bicep,distance-run']
|force-todays-date|If set to true, date picker will be hidden. Instead, entries will be added for today's date.|True or false (default)|[wt-form force-todays-date='true']
|hide-titles|If set to true, the title (e.g. Target Weight) above the form will be hidden.|True or false (default)|[wt-form hide-titles='true']
|load-placeholders|If set to true, the weight and [custom field]({{ site.baseurl }}/custom-fields.html) values will be added as placeholders form form fields.|True (default) or false|[wt-form load-placeholders='true']
|redirect-url|If specified, a URL that the user should be redirected to after completing the form.|A URL within the current domain.|[wt-form redirect-url='https://yoursite.com/thank-you-page']
|title|The title to be displayed at the top of the form|Text. By default, it will try to be automatically determined based on the type of form|[wt-form title='Enter your cups of water for today']
|user-id|By default, the shortcode will save data against the current user. You can specify this argument to save the form data against another user ID.|Numeric| [wt-form user-id="1"]