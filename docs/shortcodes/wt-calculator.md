## [wt-calculator]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode displays a form that allows your users to calculate their BMI, BMR, suggested calorie intake and Macronurtrient suggestions. The calculated figures are derived from a combination of the WT settings and the data entered by the user on the rendered form.

This allows your users, whether logged in or not, to vary the inputs to your formulas and see the relevant figures in tabular format. It is often used on sites as a quick tool, allowing the user to engage without setting up a full profile. 

The following are an example of how the form and results shall look:

**Input form**

[![Form]({{ site.baseurl }}/assets/images/wt-calculator-form.png)]({{ site.baseurl }}/assets/images/wt-calculator-form.png)

**Results**

[![Results]({{ site.baseurl }}/assets/images/wt-calculator-results.png)]({{ site.baseurl }}/assets/images/wt-calculator-results.png)

 **Shortcode Arguments**
 
The following arguments are the most popular for this shortcode:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|bmi-display|Specifies what should be displayed for BMI|"index", "label" or "both" (default)| [wt-calculator bmi-display="label"]
|load|If set to true (default) and the user is logged in, then populate the form with their user preferences|true (default) or false|[wt-calculator load=false]
|responsive-tables|If set to true, responsive HTML tables will be used. If false, plain HTML tables.|true (default) or false|[wt-calculator responsive-tables=false]
|results-show-bmi|If set to true (default), BMI will be included in the results page|true (default) or false|[wt-calculator results-show-bmi=false]
|results-show-calories|If set to true (default), suggested calories will be included in the results page|true (default) or false|[wt-calculator results-show-calories=false]
|results-show-macros|If set to true (default), Macronutrients will be included in the results page|true (default) or false|[wt-calculator results-show-macros=false]
|results-show-form|If set to true (default), the input form shall be displayed on results page|true (default) or false|[wt-calculator results-show-form=false]
|text-bmi|Defines the text displayed in front of the BMI|Text. Default is "Your BMI is:"|[wt-calculator text-bmi="BMI is"]
|text-bmr|Defines the text displayed in front of the BMR|Text. Default is "Your BMR is:"|[wt-calculator text-bmr="BMR is"]
|text-calories|Defines the text displayed above the suggested calories table|Text. Default is "The following table illustrates your recommended calorie intake:"|[wt-calculator text-calories="Suggested Calorie intake"]
|text-macros|Defines the text displayed above the Macronutrients table|Text. Default is "The following table illustrates your recommended macronutrient intake:"|[wt-calculator text-macros="MacroN intake"]