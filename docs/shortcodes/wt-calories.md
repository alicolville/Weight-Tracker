## [wt-calories]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

[wt-calories] displays a specific calorie figure from a user's calorie intake figures. For more information on calorie calculations please visit our [calculations guide]({{ site.baseurl }}/calculations.html).
 
The best way to explain how this shortcode works is to look at the output of [wt-calorie-table]. This table displays the calorie intake calculations for a user and how it is split over type (meal type / total) and progress (whether to maintain / lose weight):
 
[![Calories Table]({{ site.baseurl }}/assets/images/calories-table-small.png)]({{ site.baseurl }}/assets/images/calories-table.png)
 
 Taking the table above, you may only be interested in displaying the recommended calories to consume at dinner if wanting to lose weight. This is where the [wt-calories] comes into play. Given that example, you could use the shortcode in the following ways:
 
     [wt-calories progress="gain" type="total"]
     [wt-calories progress="gain" type="breakfast"]
     [wt-calories progress="gain" type="lunch"]
     [wt-calories progress="gain" type="dinner"]
     [wt-calories progress="gain" type="snacks"]
     [wt-calories progress="maintain" type="total"]
     [wt-calories progress="maintain" type="breakfast"]
     [wt-calories progress="maintain" type="lunch"]
     [wt-calories progress="maintain" type="dinner"]
     [wt-calories progress="maintain" type="snacks"]
     [wt-calories progress="lose" type="total"]
     [wt-calories progress="lose" type="breakfast"]
     [wt-calories progress="lose" type="lunch"]
     [wt-calories progress="lose" type="dinner"]
     [wt-calories progress="lose" type="snacks"]

 **Shortcode Arguments**
 
 The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|error-message|When rendering the shortcode, if an error occurs due to missing criteria (for example Date of Birth) a generic message is displayed. You can specify your own message by setting this argument.|Text| [wt-calories error-message="Please complete all the fields on the preference page"]
|percentage|Specify the percentage of the figure to display. For example, yo may only want to display 50% of the calculated value|100(%) - display default value|[wt-calories percentage=60]
|progress|Specifies whether to display the calories for "maintain", "gain" "lose" weight. If 'auto' is specified, it will display the relevant type based on the user's selected aim. For example, if they have an aim of losing weight, it will display the value for 'lose'.|'maintain' (default), 'lose', 'gain' or 'auto'|[wt-calories progress="maintain" type="total"]
|type|Specifies which type of meal or total to display the calorie figures for.|'breakfast', 'lunch', 'dinner', 'snack' or 'total' (default).|[wt-calories progress="maintain" type="total"]
|user-id|By default, the shortcode will display a calorie figure for the current user. You can display the value for another user by setting this argument to the relevant user ID|Numeric| [wt-calories user-id="1"]
