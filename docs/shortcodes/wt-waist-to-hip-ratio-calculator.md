## [wt-waist-to-hip-ratio-calculator] 

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/features.html) version of the plugin.
 
**Overview** 

As per the screenshots below, the Waist-to-Hip ratio calculator allows your users (logged in or not) to enter their hip and waist measurements and get an instant calculation of their Waist-to-Hip ratio.   

Note: By default, the user can switch between imperial and metric fields.

***
*Imperial*

***

[![Imperial]({{ site.baseurl }}/assets/images/wt-waist-to-hip-ratio-calculator-imperial.png)]({{ site.baseurl }}/assets/images/wt-waist-to-hip-ratio-calculator-imperial.png)

***
*Metric*

***

[![Metric]({{ site.baseurl }}/assets/images/wt-waist-to-hip-ratio-calculator-metric.png)]({{ site.baseurl }}/assets/images/wt-waist-to-hip-ratio-calculator-metric.png)
    
## Calculations

The Waist-to-Hip ratios are calculated based on the thresholds defined at [Healthline](https://www.healthline.com/health/waist-to-hip-ratio#calculate):    
    
Health risk|Women|Men
low|0.80 or lower|0.95 or lower
moderate|0.81-0.85|0.96-1.0
high|0.86 or higher|1.0 or higher    
    
## Arguments    
The shortcode supports the following arguments:    
    
| Argument | Description | Options | Example |    
|--|--|--|--|    
|default-tab|Specify which tab to display by default. Either Imperial or Metric.|"imperial" or "metric" (default)|[wt-waist-to-hip-ratio-calculator default-tab="imperial"]
|disable-main-font|If set to true will disable the main font used in the shortcode|True or False (default)|[wt-waist-to-hip-ratio-calculator disable-main-font=true]
|disable-theme-css|If set to true will disable the additional CSS used in the shortcode|True or False (default)|[wt-waist-to-hip-ratio-calculator disable-theme-css=true]			