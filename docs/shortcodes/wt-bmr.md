## [wt-bmr]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

[wt-bmr] renders the user's Basal Metabolic Rate (BMR). For more information on BMR and how it is calculated please read the [calculations guide]({{ site.baseurl }}/calculations.html).

The shortcode supports the following arguments:

| Argument | Description | Options | Example |
|--|--|--|--|
|user-id|By default, the shortcode will render the BMR for the logged in user. You can display the BMR for another user by setting this argument to the relevant user ID.|Numeric|[wt-bmr user-id="1"]
|suppress-errors|When calculating BMR, errors maybe displayed if certain criteria is missing e.g. Date of Birth, Gender, etc. Setting this argument to true will hide the errors from the user.|True or false (default).|[wt-bmr suppress-errors="true"]