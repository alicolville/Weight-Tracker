# Custom fields

### What are custom fields?

Custom fields allow a site administrator to ask custom questions of their users when they complete a weight entry. For example, you may want to ask the user “How many cups of water have you drank today?” or “Did you stick to your diet today?”. From the WP Dashboard, under “Weight Tracker” there is a menu item labelled “Custom Fields”. From here, you can see a list of all Custom Fields that have been added and have the ability to add, edit and delete them.

[![Custom fields overview](/assets/images/custom-fields-overview.png)](/assets/images/custom-fields-overview.png)

### Field types

There are four field types currently supported:

-   **Yes or No**  – displays a drop down box allowing the user to select an option of either Yes or No.
-   **Number Field** – allows the user to enter a number.
-   **Text Field**  – allows the user to enter text.
-   **Photo Field ([Pro Plus only](/upgrade.html))**  – allows the user to upload a photo

[![Custom fields in frontend](/assets/images/custom-fields-form-frontend-small.png)](/assets/images/custom-fields-form-frontend.png)

### Managing custom fields

[![Custom fields form](/assets/images/custom-fields-form-small.png)](/assets/images/custom-fields-form.png)

From here, you can delete, add and edit Custom fields.

### Groups

Custom fields can be added into groups. This allows the following shortcodes to be filtered to only display custom fields from one or more groups: [weight-tracker], [wt-form], [wt-chart] and [wt-table]. https://weight.yeken.uk/shortcodes



### Viewing the data

Custom fields are visible within data tables and charts (if a numeric custom field).

*Chart*

[![Custom fields in chart](/assets/images/custom-fields-display-small.png)](/assets/images/custom-fields-display.png)

*Tabular*

[![Custom fields in table](/assets/images/custom-fields-display-admin-small.png)](/assets/images/custom-fields-admin-display.png)
### Shortcodes

Currently there are only shortcodes to render Photo Custom Fields. To render photos the following shortcodes can be used:

 - [[wt-photo-count]](https://weight.yeken.uk/shortcodes/?section=wlt-photo-count)
 - [[wt-photo-oldest]](https://weight.yeken.uk/shortcodes/?section=wlt-photo-oldest)
 - [[wt-photo-recent]](https://weight.yeken.uk/shortcodes/?section=wlt-photo-recent)
 - [[wt-gallery]](https://weight.yeken.uk/shortcodes/?section=wlt-gallery)