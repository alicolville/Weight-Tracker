## [wt-macronutrients]

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html) version of the plugin.

[wt-macronutrients] displays a specific macronutrient figure from the macronutrient intake calculations. For more information on macronutrient calculations please visit our [calculations guide]({{ site.baseurl }}/calculations.html).

The best way to explain how this shortcode works is to look at the output of [[wt-macronutrients-table]]({{ site.baseurl }}/shotcodes/wt-macronutrients-table.html)). This table displays the macronutrient intake calculations for a user and how it is split over  **type**  (meal type / total), **progress**  (whether to maintain / lose weight) and  **nutrient**  (fats, carbs or proteins):

[![Macronutrient table]({{ site.baseurl }}/assets/images/macronutrients-small.png)]({{ site.baseurl }}/assets/images/macronutrients.png)

Taking the table above, you may only be interested in displaying the recommended proteins to consume at lunch if wanting to lose weight. This is where the [wt-macronutrients] comes into play. Given that example, you would use this shortcode:

    [wt-macronutrients type=”lose” type=”lunch” nutrient=”protein”]

For further examples, please look at  [this Gist](https://gist.github.com/alicolville/be4ab064dbd4d0a723ecd75649831e45).

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|error-message|When rendering the shortcode, if an error occurs due to missing criteria (for example Date of Birth) a generic message is displayed. This can be replaced by specifying this argument.|Text|[wt-macronutrients error-message="Please complete all the fields on the preference page"]
|nutrient|Specifies which type of nutrient should be displayed i.e. fats, proteins or carbohydrates.|'fats' (default), 'protein' or 'carbs'|[wt-macronutrients progress="maintain" type="total" nutrient="fats"]
|progress|Specifies whether to display the calories for "maintain", "lose" or "gain" weight. If 'auto' is specified, it will display the relevant type based on the user's selected aim. For example, if they have an aim of losing weight, it will display the value for 'lose'.|'maintain' (default), 'lose', 'gain' or 'auto'|[wt-macronutrients progress="maintain" type="total"]
|type|Specifies which type of meal or total to display the calorie figures for.|'breakfast', 'lunch', 'dinner', 'snack' or 'total' (default).|[wt-macronutrients progress="maintain" type="total"]
|user-id|By default, the shortcode will display the calorie figure for the current user. You can display figures for another user by setting this argument to the relevant user ID.|Numeric|[wt-macronutrients user-id="1"]


		
			
