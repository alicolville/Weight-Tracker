## [wt-custom-fields-accumulator]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

The accumulator shortcode displays a series of buttons that allow the user to increment the value of a numeric [custom field]({{ site.baseurl }}/custom-fields.html) for today's entry. 

The aim is to provide an interactive and simpler way to increment a custom field without having to complete a larger form (that may contain other fields). For example, if you have a [custom field]({{ site.baseurl }}/custom-fields.html) that tracks the total cups of water consumed today then you may not want your user's having to load the previous total, increment the value and then manually save. Instead, this shortcode displays a series of buttons that allow your users to increase their daily total by set increments. Upon each button press, the current total is incremented by the desired amount, saved within the database and displayed back to the user.

[![Chart]({{ site.baseurl }}/assets/images/wt-custom-fields-accumulator-1.png)]({{ site.baseurl }}/assets/images/wt-custom-fields-accumulator-1.png)

| Argument | Description | Options | Example |
|--|--|--|--|
|button-classes|The css class names to add to each button|Text|[wt-custom-fields-accumulator button-classes="button btn-primary"]
|button-text|The text to display on each button. Please note, it must contain {increment}. This placeholder is replaced by the number by which the total is incremented by|Text. Default is "{increment}"|[wt-custom-fields-accumulator button-text="Add {increment}"]
|hide-title|If true, hide the title.|True or false (default).|[wt-custom-fields-accumulator hide-title="true"]
|hide-value|If true, hide the current value.|True or false (default).|[wt-custom-fields-accumulator hide-value="true"]
|hide-login-prompt|If true, hide the login prompt.|True or false (default).|[wt-custom-fields-accumulator hide-login-prompt="true"]
|**increment-values**|A comma delimited list of increment values. Each increment value will be displayed on a button.|Text. Default "-1,-5,-10,1,5,10"|[wt-custom-fields-accumulator increment-values="5,10,20"]
|saved-text|The text displayed when the entry is saved.|Text. Defaults to "Your entry has been saved!".|[wt-custom-fields-accumulator saved-text="Saved."]
|**slug**|Specify the slug of the numeric [custom field]({{ site.baseurl }}/custom-fields.html) to increment.|Text|[wt-custom-fields-accumulator slug='cups-of-water']
|title|Override the default title.|Text. Defaults to the [custom field]({{ site.baseurl }}/custom-fields.html) title.|[wt-custom-fields-accumulator title="Please log your water consumption"]
|title-level|The HTML element to encapsulate the title|Text. Defaults to h3.|[wt-custom-fields-accumulator title-level="h1"]
|value-text|Override the default text that summarises the current value. The placeholder {value} can be used and is replaced by the current value.|Text. Defaults to "So far you have recorded <strong>{value}</strong>".|[wt-custom-fields-accumulator value-text="Current: {value}"]
|value-level|The HTML element to encapsulate the value|Text. Defaults to p.|[wt-custom-fields-accumulator title-value="p"]