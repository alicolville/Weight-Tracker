## [wt-macronutrients-table]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

[wt-macronutrients-table] displays recommendations on the best way to split your calorie intake (for maintaining and losing weight) into macronutrients i.e. Fats, Carbohydrates and Proteins. For more information on macronutrient calculations please read our [calculations guide]({{ site.baseurl }}/calculations.html).

The following is an example of how the rendered table looks:

[![Macronutrient table]({{ site.baseurl }}/assets/images/macronutrients-small.png)]({{ site.baseurl }}/assets/images/macronutrients.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|css-class|Adds an additional CSS class to the HTML <table> tag|Text|[wt-macronutrients-table css-class="my-table-format-css"]
|disable-jquery|If set to true, the JavaScript that converts the simple HTML table into a responsive one shall be disabled.|True or False (default)	|[wt-macronutrients-table disable-jquery=true]
|error-message|When rendering the table, if an error occurs due to missing criteria (for example Date of Birth) a generic message is displayed. This can be replaced by specifying this argument.|Text|[wt-macronutrients-table error-message="Please complete all the fields on the preference page"]
|user-id|By default, the shortcode will display the macronutrients table for the current user. You can display a macronutrients table for another user by setting this argument to the relevant user ID.|Numeric|[wt-macronutrients-table user-id="1"]
