## [wt-bmi]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode can be used to display the user's latest BMI and has the following arguments:

| Argument | Description | Options | Example |
|--|--|--|--|
| display | Specifies the format of the shortcode output. | The "display" argument has the following options: "index" (default), renders the numerical value of the person's BMI. "label", renders the readable BMI label e.g. Healthy, Overweight, etc. "both", renders both the label and numerical value. | [wt-bmi display="both"] |
| no-height-text | To calculate a user's height, we need their BMI. This argument allows you to override the default message displayed to the user when their height is missing. | Text | [wt-bmi no-height-text="To view your BMI, please enter your height."] |