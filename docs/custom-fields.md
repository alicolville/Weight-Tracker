# Custom fields

> The following feature is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

### What are custom fields?

Custom fields allow a site administrator to ask custom questions of their users when they complete a weight entry. For example, you may want to ask the user “How many cups of water have you drank today?” or “Did you stick to your diet today?”. From the WP Dashboard, under “Weight Tracker” there is a menu item labelled “Custom Fields”. From here, you can see a list of all Custom Fields that have been added and have the ability to add, edit and delete them.

[![Custom fields overview](/assets/images/custom-fields-overview.png)](/assets/images/custom-fields-overview.png)

### Field types

There are four field types currently supported:

-   **Yes or No**  – displays a drop down box allowing the user to select an option of either Yes or No.
-   **Number Field** – allows the user to enter a number.
-   **Text Field**  – allows the user to enter text.
-   **Photo Field** ([Pro Plus only](/upgrade.html))  – allows the user to upload a photo

[![Custom fields in frontend](/assets/images/custom-fields-form-frontend-small.png)](/assets/images/custom-fields-form-frontend.png)

### Managing custom fields

[![Custom fields form](/assets/images/custom-fields-form-small.png)](/assets/images/custom-fields-form.png)

From here, you can delete, add and edit Custom fields.

### Groups

Custom fields can be added into groups. This allows the following shortcodes to be filtered to only display custom fields from one or more groups: [[wt]]({{ site.baseurl }}/shortcodes/wt.html), [[wt-form]]({{ site.baseurl }}/shortcodes/wt-form.html), [[wt-chart]]({{ site.baseurl }}/shortcodes/wt-chart.html) and [[wt-table]]({{ site.baseurl }}/shortcodes/wt-table.html).



### Viewing the data

Custom fields are visible within data tables and charts (if a numeric custom field).

*Chart*

[![Custom fields in chart](/assets/images/custom-fields-display-small.png)](/assets/images/custom-fields-display.png)

*Tabular*

[![Custom fields in table](/assets/images/custom-fields-display-admin-small.png)](/assets/images/custom-fields-admin-display.png)
### Shortcodes

The following shortcodes are available for custom fields:

 - [[wt-custom-fields-chart]]({{ site.baseurl }}/shortcodes/wt-custom-fields-chart.html)
 - [[wt-custom-fields-form]]({{ site.baseurl }}/shortcodes/wt-custom-fields-form.html)
 - [[wt-custom-fields-table]]({{ site.baseurl }}/shortcodes/wt-custom-fields-table.html)
 - [[wt-custom-fields-accumulator]]({{ site.baseurl }}/shortcodes/wt-custom-fields-accumulator.html)
 - [[wt-photo-count]]({{ site.baseurl }}/shortcodes/wt-photo-count.html)
 - [[wt-photo-oldest]]({{ site.baseurl }}/shortcodes/wt-photo-oldest.html)
 - [[wt-photo-recent]]({{ site.baseurl }}/shortcodes/wt-photo-recent.html)
 - [[wt-gallery]]({{ site.baseurl }}/shortcodes/wt-photo-gallery.html)