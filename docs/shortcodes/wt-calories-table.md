## [wt-calories-table]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

[wt-calories-table] displays recommendations on the best way to split your calorie intake (for maintaining, gaining or losing weight) over the day. For more information on calorie calculations please visit our [calculations guide]({{ site.baseurl }}/calculations.html).
 
The following is an example of how the rendered table looks:

[![Calories Table]({{ site.baseurl }}/assets/images/calories-table-small.png)]({{ site.baseurl }}/assets/images/calories-table.png)
 
 **Shortcode Arguments**
 
 The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|css-class|Set this argument if you wish to add your own CSS class to the <table> tag.|Text|[wt-calories-table css-class="my-table-format-css"]
|disable-jquery|If set to true, the JavaScript that converts the simple HTML table into a responsive one shall be disabled.|True or False (default)	|[wt-calories-table disable-jquery=true]
|error-message|When rendering the table, if an error occurs due to missing criteria (for example Date of Birth) a generic message is displayed. You can specify your own message by setting this argument.|Text|[wt-calories-table error-message="Please complete all the fields on the preference page"]
|user-id|By default, the shortcode will display the table for the current user. You can display the table for another user by setting this argument to the relevant user ID|Numeric| [wt-calories-table user-id="1"]
