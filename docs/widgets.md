# Widgets

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

Widgets allow you to embed Weight Tracker functionality into your widget areas. These can be added to your widget areas / side bars via the WP dashboard under Appearance > Widgets.

## Chart

The Chart widget renders a chart within the desired widget area. It has the following options:

[![Widget Settings]({{ site.baseurl }}/assets/images/widget-chart.png)]({{ site.baseurl }}/assets/images/widget-chart.png)

Each option is self explanatory, the colours and settings for the chart are derived from the main admin page unless you have specified it at the widget level. One useful widget setting is “User ID”; this setting allows you to specify which user data should be displayed for. The above settings render the following chart:

[![Widget Example]({{ site.baseurl }}/assets/images/widget-chart-example.png)]({{ site.baseurl }}/assets/images/widget-chart-example.png)

*The chart shall not be rendered if no weight entries was found.*

## Form

The form widget renders a quick entry weight within widget areas. It has the following settings:

[![Widget Form]({{ site.baseurl }}/assets/images/widget-form.png)]({{ site.baseurl }}/assets/images/widget-form.png)

The settings are fairly self explanatory, three to bear in mind are “Allow user to specify date”, “Hide Measurements” and “Hide Photos”. The first, allows you to show or hide a date picker. If hidden, the date for the weight entry will be set to the current date. The latter options will allow you to hide Measurement and Photo fields respectively.

Below are examples with the picker enabled and disabled:

[![Widget Form Example]({{ site.baseurl }}/assets/images/widget-form-example.png)]({{ site.baseurl }}/assets/images/widget-form-example.png)

An example form with measurements enabled:

[![Widget Form Example]({{ site.baseurl }}/assets/images/widget-form-meta.png)]({{ site.baseurl }}/assets/images/widget-form-meta.png)

## Progress Bar

The Progress Bar widget allows you to add a progress bar to display the user’s progress towards their target. The settings below are self explanatory and explained in better detail on the [[wt-progress-bar] page]({{ site.baseurl }}/shortcodes/wt-progress-bar.html).

[![Widget Progress Form]({{ site.baseurl }}/assets/images/widget-progress-bar.png)]({{ site.baseurl }}/assets/images/widget-progress-bar.png)

The above placement gives the following output:

[![Widget Progress Example]({{ site.baseurl }}/assets/images/widget-progress-output.png)]({{ site.baseurl }}/assets/images/widget-progress-output.png)