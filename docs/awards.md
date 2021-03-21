# Awards
> The following feature is only available in the [Pro Plus]({{ site.baseurl }}/upgrade.html)  version of the plugin.

[Pro Plus]({{ site.baseurl }}/upgrade.html)  sites have the ability to give users awards for reaching defined goals. Currently you can set awards for:

-   **Weight**. Define goals in terms of weight lost or gained. For example, set goals of 1Kg Lost, 2Kg Lost, etc
-   **Weight %**. Define goals in terms of percentage of weight lost or gained. For example, set goals of 5% Weight Lost, 10% Weight Lost, etc
-   **BMI: Change.** Define goals in terms of BMI changing. For example, issue an award for someone’s BMI decreasing in terms of classification
-   **BMI Equals**. Define goals in terms of BMI equalling a given BMI classification. For example, their BMI has now changed and equals “Healthy”

### Defining Awards

[![Advanced tab](/assets/images/awards-list-small.png)](/assets/images/awards-list.png)

From this screen it is possible to delete and edit existing awards as well as define new ones.

[![Awards Add / Edit](/assets/images/awards-add-edit-small.png)](/assets/images/awards-add-edit.png)

When defining an award, you have several options. As the image on the illustrates, you have the ability to specify the type of award, whether it is rewarded for a gain or loss in weight, a badge (image), a custom message (appears in emails), weight entry types that should be considered, whether an email should be sent and finally whether it is enabled.

### How are awards issued?

Whether an award should be issued or not is determined when a user adds or edits a weight entry. The system starts by fetching all possible awards and filtering them down. Each award is then considered in the following manner. If each condition is met, then the award is issued:

1.  The user hasn’t been issued this award before
2.  Can the award be issued for this type of change in weight (e.g. if the award is only for a gain then ensure the user has triggered a gain in weight)?
3.  Can this award be issued for this type of entry (e.g. if the user is entering a new weight, then only consider awards that are allowed for new weight entries)?
4.  Finally, consider the user’s start weight in relation to weight entered. Does the difference create the right conditions to meet award criteria?

### Emails

If enabled at the award level and for all awards, an email will be sent to the user for each award issued.

### Viewing Awards

Awards that have been issued can either be displayed via shortcodes (see the following selection) or by viewing the user’s record in the WP Dashboard.

[![](/assets/images/awards-example-small.png)](/assets/images/awards-example.png)

### Shortcodes

The following shortcodes can be used to render a user’s awards:

-   [[wt-awards]](https://weight.yeken.uk/shortcodes/?section=wlt-awards)
-   [[wt-awards-grid]](https://weight.yeken.uk/section/wlt-awards-grid/)
-   [[wt-award-latest]](https://weight.yeken.uk/shortcodes/?section=wlt-awards-latest)

### Clearing Awards

It is possible to clear all awards assigned to all users. This will allow the award to be re-issued to the user on future weight entries. To clear previously assigned awards, navigate to “Weight Tracker” > “Help and Log” and click the button labelled “Delete all issued awards”.