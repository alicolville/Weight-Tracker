## [wt-form-target]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode renders a target form.

**Example Target form:**

[![Target]({{ site.baseurl }}/assets/images/target-small.png)]({{ site.baseurl }}/assets/images/target.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|class|Allows you to specify an additional CSS class on the element.|Text|[wt-target class='additional-css-class']
|hide-titles|If set to true, the title (e.g. Target Weight) above the form will be hidden.|True or false (default)|[wt-target hide-titles='true']
|redirect-url|If specified, a URL that the user should be redirected to after completing the form.|A URL within the current domain.|[wt-target redirect-url='https://yoursite.com/thank-you-page']
|user-id|By default, the shortcode will save data against the current user. You can specify this argument to save the form data against another user ID.|Numeric| [wt-target user-id="1"]