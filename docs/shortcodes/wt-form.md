## [wt-form]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode renders a target or weight entry / [custom fields]({{ site.baseurl }}/custom-fields.html) form.

**Example Target form:**

[![Target]({{ site.baseurl }}/assets/images/target-small.png)]({{ site.baseurl }}/assets/images/target.png)

**Example weight / custom fields form:**

[![Target]({{ site.baseurl }}/assets/images/form-small.png)]({{ site.baseurl }}/assets/images/form.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|class|Allows you to specify an additional CSS class on the element.|Text|[wt-form class='additional-css-class']
|custom-field-groups|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-form custom-field-groups='measurements'] [wt-form custom-field-groups='measurements,fitness-test']
|custom-field-slugs|Specify one or more [custom field]({{ site.baseurl }}/custom-fields.html) slugs. Specifying slugs will ensure only the fields specified are displayed on the control.|An individual slug or multiple slugs comma delimited.|[wt-form custom-field-slugs='waist'] [wt-form custom-field-slugs='waist,bicep,distance-run']
|force-todays-date|If set to true, date picker will be hidden. Instead, entries will be added for today's date.|True or false (default)|[wt-form force-todays-date='true']
|hide-titles|If set to true, the title (e.g. Target Weight) above the form will be hidden.|True or false (default)|[wt-form hide-titles='true']
|hide-custom-fields|If set to true, the [custom fields]({{ site.baseurl }}/custom-fields.html) part of the form will be hidden.|True or false (default)|[wt-form hide-custom-fields='true']
|load-placeholders|If set to true, the weight and [custom field]({{ site.baseurl }}/custom-fields.html) values will be added as placeholders form form fields.|True (default) or false|[wt-form load-placeholders='true']
|redirect-url|If specified, a URL that the user should be redirected to after completing the form.|A URL within the current domain.|[wt-form redirect-url='https://yoursite.com/thank-you-page']
|title|The title to be displayed at the top of the form|Text. By default, it will try to be automatically determined based on the type of form|[wt-form title='Enter your cups of water for today']
|type|The type of form. Either weight entry (weight), Set target (target) or [custom field]({{ site.baseurl }}/custom-fields.html) entry (custom-field).|"weight" (default), "target" or "custom-field"|[wt-form type='target']
|user-id|By default, the shortcode will save data against the current user. You can specify this argument to save the form data against another user ID.|Numeric| [wt-form user-id="1"]
|weight-mandatory|By default, for a weight form, it is mandatory for a weight to be entered. If setting this argument to false, the form can be submitted with out.|true or false (default)|[wt-form weight-mandatory~~~~='true']