## [wt-kiosk] 

> The following shortcode is only available in the [Pro Plus]({{ site.baseurl }}/features.html) version of the plugin.

[wt-kiosk] is an extension of the popular [wt]({{ site.baseurl }}/shortcodes/wt.html) shortcode. [wt] is aimed at the end user and provides them a tool to view and edit their data. [wt-kiosk] however, is designed for your administrators and staff and extends the [wt] user interface by providing an additional search box allowing you search for your site users. Selecting a user reloads the page and popluates the [wt] tool with the selected user's data. This allows your team to search and update all of their user records from the front end of the website.

An additional summary tab is also added allowing your team to get a quick summary of the user's accounts.
   
**Overview**    

[![Main Image]({{ site.baseurl }}/assets/images/wt-kiosk.png)]({{ site.baseurl }}/assets/images/wt-kiosk.png)
   
**Shortcode arguments**

Below lists kiosk specific arguments, however, please view the documentation for [wt]({{ site.baseurl }}/shortcodes/wt.html) as those arguments are also supported.
    
The shortcode supports the following arguments:    
    
| Argument | Description | Options | Example |    
|--|--|--|--|   
| bmi-format | Specify the format that BMI should be displayed in.  |'label' (default), 'both' or 'index'    | [wt bmi-format='both'] |   
|summary-boxes-kiosk|Specify the [summary boxes]({{ site.baseurl }}/components.html) to display on the summary tab.|Comma delimited list, [read more]({{ site.baseurl }}/components.html) ( default: "weight-difference-since-previous,latest-weight,latest-versus-target,latest-versus-start,latest-award,bmi,calories-lose,calories-maintain,divider,start-weight,aim,target-weight,start-bmi,start-bmr,previous-weight,divider,name-and-email,gender,age-dob,height,activity-level,group")|[wt-kiosk summary-boxes-kiosk="latest-weight,start-weight"]
