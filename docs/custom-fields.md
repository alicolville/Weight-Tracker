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

[![Custom fields form](/assets/images/custom-fields-form-small.png)](/assets/images/custom-fields-form.png)

### Managing custom fields

[![](https://886487.smushcdn.com/2036968/wp-content/uploads/sites/3/2020/10/new-custom-field-e1603395779649-300x249.png?lossy=1&strip=1&webp=1)](https://weight.yeken.uk/custom-fields/new-custom-field/)Custom Fields can be managed from the WP Dashboard by navigating to Weight Tracker > Custom Fields. The initial screen lists all custom fields and contains summary information for each e.g. their slugs, whether they are enabled, etc.

From here, you can delete, add and edit Custom fields.

### Viewing the data

[![](https://886487.smushcdn.com/2036968/wp-content/uploads/sites/3/2018/11/data-customfields-300x300.png?lossy=1&strip=1&webp=1)](https://weight.yeken.uk/custom-fields/data-customfields/)Custom Field data is visible to viewable in the front end to your users and with the admin screens. The data entered for each custom field can be viewed against the related weight entry.

**Shortcodes**

Currently there are only shortcodes to render Photo Custom Fields. To render photos the following shortcodes can be used:

 - [[wt-photo-count]](https://weight.yeken.uk/shortcodes/?section=wlt-photo-count)
 - [[wt-photo-oldest]](https://weight.yeken.uk/shortcodes/?section=wlt-photo-oldest)
 - [[wt-photo-recent]](https://weight.yeken.uk/shortcodes/?section=wlt-photo-recent)
 - [[wt-gallery]](https://weight.yeken.uk/shortcodes/?section=wlt-gallery)