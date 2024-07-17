## [wt] 

This is the most popular shortcode for the Weight Tracker plugin and displays upto four sections / tabs when a user is logged into WordPress.    
    
> The following shortcode is available in both the free and [Pro/Pro Plus]({{ site.baseurl }}/features.html) version of the plugin. The Pro version has the following additional features: 
> * Support for measurements and  [custom fields]({{ site.baseurl }}/custom-fields.html). 
> * Advanced data table for viewing weight entries. Supporting sorting, paging, editing and deleting. 
> * Support for BMI, BMR, suggested calorie intake and Macronutrients.
> * A settings page to allow the user to tailor the plugin to their needs. 
> * Notes
> * Gallery
> * User Settings    
 
*Note: Please only place this shortcode once per page. If placed more than once you may experience unstable results.*    
 
**Overview**    

[![Main Image]({{ site.baseurl }}/assets/images/wt2.png)]({{ site.baseurl }}/assets/images/wt2.png)
   
The main tab displays the following summary information to user as well as a chart of the user's weight and plottable custom field data ([Pro feature]({{ site.baseurl }}/upgrade.html))

* Latest weight
* Previous weight
* Latest vs Target
* Target Weight

**Add/edit an entry**  

The next tab allows a user to add an entry. Here they can select a date, specify their weight anc complete any additional [custom fields]({{ site.baseurl }}/custom-fields.html) that have been setup.

[![Add]({{ site.baseurl }}/assets/images/wt2-add.png)]({{ site.baseurl }}/assets/images/wt2-add.png)

**History**

The history tab provides the following summary data as well as a data table containing the user's entries:

* Number of weight entries
* Number of days tracking
* Latest weight
* Start weight
 
[![Table]({{ site.baseurl }}/assets/images/wt2-history.png)]({{ site.baseurl }}/assets/images/wt2-history.png)
 
**Awards**
 
 With a [Pro Plus](https://shop.yeken.uk/product/weight-tracker-pro-plus/) license the "Awards" tab is visible. This allows the user to see which awards have been given to them.
 
 [![Advanced]({{ site.baseurl }}/assets/images/wt2-awards.png)]({{ site.baseurl }}/assets/images/wt2-awards.png)
 
**Advanced**

With a [Pro Plus](https://shop.yeken.uk/product/weight-tracker-pro-plus/) license an additional tab is present with "Advanced" data. This tab contains the following information;
 
- BMI (Body Mass Index)
- BMR (Basal Metabolic Rate)
- Suggested Calorie Intake
- Macronutrients

For more information on these values, please read our guide on [Weight Tracker calculations]({{ site.baseurl }}/calculations.html).

[![Advanced]({{ site.baseurl }}/assets/images/wt2-advanced.png)]({{ site.baseurl }}/assets/images/wt2-advanced.png)

**Gallery**

The photos tab will show the user their oldest and latest photo. Below this will be a gallery of all of their photos.

[![Gallery]({{ site.baseurl }}/assets/images/wt2-photos.png)]({{ site.baseurl }}/assets/images/wt2-photos.png)
   
**Messages**  
 
If enabled, this tab shall display all of the messages sent to a user by an administrator.  
   
Note: This is a [Pro]({{ site.baseurl }}/upgrade.html) feature. 

[![Messages]({{ site.baseurl }}/assets/images/wt2-messages.png)]({{ site.baseurl }}/assets/images/wt2-messages.png)
   
**User Preferences**  
  
If enabled, the last tab allows the user to edit their profile (specifying height, gender, etc), preferred data units, and the option to delete all of their data.    
    
Note: This is a [Pro]({{ site.baseurl }}/upgrade.html) feature. 

[![Settings]({{ site.baseurl }}/assets/images/wt2-settings.png)]({{ site.baseurl }}/assets/images/wt2-settings.png)
    
**Shortcode arguments**
    
The shortcode supports the following arguments:    
    
| Argument | Description | Options | Example |    
|--|--|--|--|   
| bmi-format | Specify the format that BMI should be displayed in.  |'label' (default), 'both' or 'index'    | [wt bmi-format='both'] |   
| bmi-alert-if-below | If specified, show an alert if the user's BMI is below this value|false (default) or numeric | [wt bmi-alert-if-below='17'] |
| bmi-alert-if-above | If specified, show an alert if the user's BMI is above this value|false (default) or numeric | [wt bmi-alert-if-above='30'] |
| custom-field-groups   | Specify one or more custom field group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.   | An individual slug or multiple slugs comma delimited.   | [wt custom-field-groups='measurements'] [wt custom-field-groups='measurements,fitness-test'] |   
| custom-field-slugs   | Specify one or more custom field slugs. Specifying slugs will ensure only the fields specified are displayed on the control.   | An individual slug or multiple slugs comma delimited.   | [wt custom-field-slugs='waist'] [wt custom-field-slugs='waist,bicep,distance-run'] |    
|disable-main-font|If set to true will disable the main font used in the shortcode|True or False (default)|[wt disable-main-font=true]
|disable-theme-css|If set to true will disable the additional CSS used in the shortcode|True or False (default)|[wt disable-theme-css=true]
| enable-week-ranges   | If enabled, a drop down of weeks shall be displayed above the "Weight History" table. When a week is selected, the table will be filtered to only show entries within that week. | True or false (default).   | [wt enable-week-ranges=true] |   
| hide-advanced-narrative | If set to true (default is false) hide the text on the Advanced tab which explains each section.   | True or false (default).   | [wt hide-advanced-narrative=true] |    
| hide-chart-overview   |  Hide the chart from the "Overview" tab.   | True or false (default).  | [wt hide-chart-overview=true] |     
| hide-custom-fields-chart   |  Hide custom fields from the chart.   | True or false (default).  | [wt hide-custom-fields-chart=true] |     
| hide-custom-fields-form   |  Hide custom fields from the form.   | True or false (default).  | [wt hide-custom-fields-form=true] |     
| hide-custom-fields-table   |  Hide custom fields from the table.   | True or false (default).  | [wt hide-custom-fields-table=true] |     	
| hide-email-optout | Hide the email opt out form on settings tab. | True or false (default).  | [wt hide-email-optout=true] |  				
| hide-first-target-form | Hide the target form from the Overview tab. | True or false (default).  | [wt hide-first-target-form=true] |  
| hide-notes | If set to true (default is false) hide the "notes" section of the form.   | True or false (default).   | [wt hide-notes=true] |   
| hide-notifications | If set to true (default is false) hide the "notifications" at the top of the shortcode.   | True or false (default).   | [wt hide-notifications=true] |   
| hide-photos   | If set to true (default is false) hide the photo upload section of the form.   | True or false (default).   |  [wt hide-photos=true] |   
| hide-tab-advanced   | If set to true (default is false) hide the Advanced tab.   | True or false (default).   | [wt hide-tab-advanced=true] |     
| hide-tab-awards   | If set to true (default is false) hide the Awards tab.   | True or false (default).   | [wt hide-tab-awards=true] |     
| hide-tab-messages    | If set to true (default is false) hide the messages tab.  | True or false (default).   |  [wt hide-tab-descriptions=true] |   
| hide-tab-descriptions    | If set to true (default is false) hide the descriptions under the tab title.  | True or false (default).   |  [wt hide-tab-descriptions=true] |   
| hide-tab-photos   | If set to true (default is false) hide the Photos tab.   |  True or false (default).   | [wt hide-tab-photos=true] |         
| show-delete-data  | If set to false (default is true), the section allowing users to delete their own data is hidden.   | True (default) or false   | [wt show-delete-data=false] |  
|kiosk-mode|If set to true (default is false) then the tool will be switched to kiosk mode (read more: [wt-kiosk]({{ site.baseurl }}/shortcodes/wt-kiosk.html))|true or false (default)| [wt kiosk-mode=true] |  
|kiosk-barcode-scanner|If set to true, a barcode scanner shall appear at the top when in Kiosk mode. Please note, the barcode/QR code must contain a WordPress user ID e.g. 233. Upon a successful scan, the relevant user record shall be loaded.|True or False (default)|[wt kiosk-mode=true kiosk-barcode-scanner=true]
|summary-boxes-advanced|Specify the [summary boxes]({{ site.baseurl }}/components.html) to display at top of the advanced tab.|Comma delimited list, [read more]({{ site.baseurl }}/components.html)|[wt summary-boxes-advanced="number-of-entries,number-of-days-tracking,latest-weight,start-weight"]
|summary-boxes-data|Specify the [summary boxes]({{ site.baseurl }}/components.html) to display at top of the history tab.|Comma delimited list, [read more]({{ site.baseurl }}/components.html)|[wt summary-boxes-data="bmi,bmr"]
|summary-boxes-home|Specify the [summary boxes]({{ site.baseurl }}/components.html) to display at top of the home tab.|Comma delimited list, [read more]({{ site.baseurl }}/components.html)|[wt summary-boxes-home="latest-weight,start-weight"]
|summary-boxes-kiosk|Specify the [summary boxes]({{ site.baseurl }}/components.html) to display on the summary tab (when in kiosk mode).|Comma delimited list, [read more]({{ site.baseurl }}/components.html) ( default: "weight-difference-since-previous,latest-weight,latest-versus-target,latest-versus-start,latest-award,bmi,calories-lose,calories-maintain,divider,start-weight,aim,target-weight,start-bmi,start-bmr,previous-weight,divider,name-and-email,gender,age-dob,height,activity-level,group")|[wt kiosk-mode=true summary-boxes-kiosk="latest-weight,start-weight"]
|weight-mandatory|By default, for a weight form, it is mandatory for a weight to be entered. If setting this argument to false, the form can be submitted with out.|true or false (default)|[wt weight-mandatory='true']					