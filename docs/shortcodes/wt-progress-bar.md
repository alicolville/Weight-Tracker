# [wt-progress-bar]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

This shortcode can be used to display a progress bar indicating the current user's progress towards their target weight. Below is an example of the rendered shortcode:

[![Progress Bar]({{ site.baseurl }}/assets/images/progress-bar-small.png)]({{ site.baseurl }}/assets/images/progress-bar.png)

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
animation-duration|How many milliseconds it takes to animate the progress bar.|Numeric value.|Default is 1400.|[wt-progress-bar animation-duration=100]
|display-errors|By default, useful error messages are displayed to the user if the progress bar is not displayed. For example, the user may be prompted to enter a target weight or add a weight entry.|True (default) or false|[wt-progress-bar display-errors=false]
height|The height of the progress bar.|% or px. Default "100%".|[wt-progress-bar height='50px']
percentage-text|The text displayed underneath the progress bar. {t} Can be used to display target weight.|String. Default "towards your target of {t}."|[wt-progress-bar percentage-text='near your target weight of {t}']
stroke-colour|Colour of the progress line.|Hex value. Default is #FFEA82.|[wt-progress-bar stroke-colour='#000000']
stroke-width|The thickness of the progress line.|Numeric value. Default is 3.|[wt-progress-bar stroke-width=10]
text-colour|	Colour of the text displayed under the progress bar.|	Hex value. Default is #000.|	[wt-progress-bar text-colour='#CCCCCC']
trail-colour|	Colour of the progress trail line.	Hex value. Default is #eee.|	[wt-progress-bar trail-colour='#000000']
trail-width|	The thickness of the progress line trail	|Numeric value. Default is 1.|	[wt-progress-bar trail-width=1]
type|	The type of chart. Currently there is only one properly supported "line".	|"line" (default) and "circle" (beta - not supported)|	[wt-progress-bar type='line']
width	|The width of the progress bar.	|% or px. Default "100%".|	[wt-progress-bar width='50%']
