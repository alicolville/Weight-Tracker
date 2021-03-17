## [wt] 
This is the most popular shortcode for the Weight Loss Tracker plugin and displays three sections / tabs when the user is logged into WordPress.    
    
> The following shortcode is available in both the free and [Pro/Pro Plus]https://alicolville.github.io/Weight-Tracker/features.html) version of the plugin. The Pro > version has the following additional features: > > -   Support for measurements and  [custom fields](https://weight.yeken.uk/custom-fields/). > -   Advanced data table for viewing weight entries. Supporting sorting, paging, editing and deleting. > -   Support for BMI values. > -   A settings page to allow the user to tailor the plugin to their needs. > -   User Settings    
 *Note: Please only place this shortcode once per page. If placed more than once you may experience unstable results.*    
 **Overview**    
 ![enter image description here](http://yeken.uk/wp-content/uploads/2021/03/wt-e1615930590182.png)    
    
Displays a chart of the user’s weight and custom field ([Pro feature](https://weight.yeken.uk/pro/)) entries in either imperial or metric. The appearance of chart can be customised within the admin settings to change its appearance, type, maximum number of points, etc.    
    
Below the chart is a target form and weight / custom field entry form. If enabled, the target form allows the user to enter their desired target weight. This target is then displayed on the chart and used by other shortcodes / widgets.    
    
The main feature of the Overview screen is the ability to enter a weight for the given day or overwrite a previous entry. These entries are then stored against the user’s record in chronological order and displayed on the chart and “In Detail” tab.    
    
[Pro](https://weight.yeken.uk/pro/) users that have enabled measurements will be presented with additional fields that allow your user’s to enter measurements for various parts of their body. Measurement entries are displayed alongside the weight entries    
    
**In detail**    
 “The in Detail” tab has another target form for ease of use but its primary function is to display all of the user’s weight and custom field entries in tabular format. The  [Pro](https://weight.yeken.uk/pro/) version has an improved data table with features such as sortable, expandable and contains links to edit / delete each weight entry. The weight entries can also be filtered by the drop down to show all entries or select them by a week by week basis. This filtering is reflected in the table and the chart.    

**Advanced**

![Advanced tab](https://yeken.uk/wp-content/uploads/2021/03/advanced-tab-e1616003559458.png)
 
With a [Pro Plus](https://shop.yeken.uk/product/weight-tracker-pro-plus/) license an additional tab is present with "Advanced" data. This tab contains the following information;
 
- BMI (Body Mass Index)
- BMR (Basal Metabolic Rate)
- Suggested Calorie Intake
- Macronutrients

For more information on these values, please read our guide on [Weight Tracker calculations](/calculations.html).
   
**Settings**    
 If enabled and you have [Pro](https://weight.yeken.uk/pro/), the last tab presents the logged in user with a settings page. This allows users to specify their height (if BMI enabled), select their preferred measurement units and date format.    
    
They also have the option to delete their existing data.    
    
**Shortcode arguments**    
 The shortcode supports the following arguments:    
    
| Argument | Description | Options | Example |    
|--|--|--|--|   
|  allow-delete-data  | If set to false (default is true), the section allowing users to delete their own data is hidden.   | True (default) or false   | [wt allow-delete-data="false"] |  
| bmi-format | Specify the format that BMI should be displayed in.  |'label' (default), 'both' or 'index'    | [wt bmi-format='both'] |   
| custom-field-groups (8.4+)   | Specify one or more custom field group slugs. Specifying groups will ensure that only custom fields within those groups are displayed on the control.   | An individual slug or multiple slugs comma delimited.   | [wt custom-field-groups='measurements'] [wt custom-field-groups='measurements,fitness-test'] |   
  | custom-field-slugs (8.4+)   | Specify one or more custom field slugs. Specifying slugs will ensure only the fields specified are displayed on the control.   | An individual slug or multiple slugs comma delimited.   | [wt custom-field-slugs='waist'] [wt custom-field-slugs='waist,bicep,distance-run'] |    
|disable-advanced-tables    |If set to true, disable advanced data tables (responsive with delete and add / edit options).    | True or false (default).   |  [wt disable-advanced-tables=true] | disable-second-check   | Disables the check to see if the [wt] shortcode has already been placed on the page. Some themes and plugins may throw an error when this check is enabled.   | True or false (default).   | [wt disable-second-check=true] |   
|disable-tabs    | If set to true, disable tabs and display all content on one "page"  | True or false (default).  |  [wt disable-tabs=true] | |   
| enable-week-ranges   | If enabled, a drop down of weeks shall be displayed above the "Weight History" table. When a week is selected, the table will be filtered to only show entries within that week. | True or false (default).   | [wt enable-week-ranges=true] |   
| hide-advanced-narrative | If set to true (default is false) hide the text on the Advanced tab which explains each section.   | True or false (default).   | [wt hide-advanced-narrative=true] |    
| hide-chart-overview (8.4+)   |  Hide the chart from the "Overview" tab.   | True or false (default).  | [wt hide-chart-overview=true] |     
| hide-first-target-form | Hide the target form from the Overview tab. | True or false (default).  | [wt hide-first-target-form=true] |  
| hide-notes | If set to true (default is false) hide the "notes" section of the form.   | True or false (default).   | [wt hide-notes=true] |   
 | hide-photos   | If set to true (default is false) hide the photo upload section of the form.   | True or false (default).   |  [wt hide-photos=true] |   
 | hide-tab-advanced   | If set to true (default is false) hide the Advanced tab.   | True or false (default).   | [wt hide-tab-advanced=true] |     
 |hide-tab-descriptions    | If set to true (default is false) hide the descriptions under the tab title.  | True or false (default).   |  [wt hide-tab-descriptions=true] |   
 | hide-tab-photos   | If set to true (default is false) hide the Photos tab.   |  True or false (default).   | [wt hide-tab-photos=true] |    
| hide-second-target-form | Hide the target form from the "In Detail" tab.  | True or false (default).   |[wt hide-second-target-form=true] |    
|min-chart-points|Specifies how many weight entries must be present before the chart is displayed. By default, it is set to 2.|A numeric value. Default 2.|[wt min-chart-points=0]|    
| show-add-button  | If true, an "Add Entry" button is displayed above the chart. Clicking this button jumps the user to the weight entry form.   | True or false (default).   | [wl show-add-button=true] |    
| show-chart-history (8.4+)   | If true, display a chart on the "History" tab. | True or false (default).   | [wt show-chart-history=true] |    
|  |  |  |  |