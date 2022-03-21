## [wt-if]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

In some cases, you may only wish to display content if the user has completed certain WT fields. Let's say for example, you have a page where you wish to display a chart and some text introductory text, there is no point displaying the chart if the user has not entered their weight. The shortcode [wt-if] allows you some basic logic to specify “if exists” or “if not exists” type logic. So using the example above, you could have something like:

    [wt-if field="weight" operator="exists]
    	Some introductory text here explaining the chart.
    	[wt-chart]
    [/wt-if]

We can also further expand this with an [else] clause. If the above condition is not met, we can specify what should be display instead e.g.

    [wt-if field="weight" operator="exists]
    	Some introductory text here explaining the chart.
    	[wt-chart]
    [else]
    	Please add a weight entry or two!
    [/wt-if]

**Specifying more that one field**

You may wish to create an AND condition where multiple fields must exist or not exist for the condition to evaluate as true. This can be done by comma separating field names – The following example states the person must have a height and recent weight:

    [wt-if field="weight,height"]
    	Thank you - we can now calculate your BMI!
    [else]
    	Please add a weight entry as well as your height.
    [/wt-if]

At the moment this shortcode is in it's infancy, so  [please get in touch]({{ site.baseurl }}/contact.html)  with any suggestions. Below you can see the fields and operators it supports and further examples of its usage  [can be found on the [wt-if] gist](https://gist.github.com/alicolville/d33fbdabc628c92e4e40b7f08b343fe7).

**Nesting [wt-if] statements**

There maybe instances where you wish to nest IF statements. For example you may want to ensure a person is logged in before checking if their weight and height. To do this, you can use the additional shortcodes to nest [wt-if-1], [wt-if-2] and [wt-if-3]. Each corresponds to the level of nesting. Below is a simple example:

    [wt-if field="is-logged-in"]
    	You are logged in!
    	[wt-if-1 field="weight"]
    		We have your weight!
    		[wt-if-2 field="height"]
    			Great! We have all we need!
    			[wt-bmi]		
    		[else-2]
    			Please add a height!
    		[/wt-if-2]
    	[else-1]
    		Please add a weight!
    	[/wt-if-1]
    [else]
    	Please login first :)
    [/wt-if]

Another example can be found at the  [following Gist](https://gist.github.com/alicolville/5cd83f503ec3e135938a93d62423afc4). It is important to note, if you nest an IF statement within another, you must add the -1, -2, -3 to the shortcode as you go deeper. Failure to do so will lead to unexpected behaviour.


**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|field|Allows you to specify which field should be examined.|weight (default), previous-weight, is-logged-in, challenges-opted-in, target, bmr, height, aim, gender, photo, activity-level or dob.|[wt-if field="dob" operator="not-exists"]Please enter your Date of Birth on the settings page.[/wt-if]
|operator|Allows you to state whether field has been populated (exists) or not (not-exists).|exists (default) or not-exists|[wt-if operator="exists" field="weight"]Thank you for adding a weight entry[else]Please add a weight entry[/wt-if]
|strip-p-br|Specifies whether to remove <p> and <br> tags added by WordPress|true or false (default)|[wt-if strip-p-br="true"]Something[/wt-if]
|user-id|By default, the shortcode will determine the result for the current user. If you wish to determine the result based on another user, use the following argument.|Numeric| [wt-if user-id="1" field="weight"]Something[/wt-if]

			