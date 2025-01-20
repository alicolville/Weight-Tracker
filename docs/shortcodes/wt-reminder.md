## [wt-reminder]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render reminders to logged in users asking them to add a target weight or weight / [custom fields]({{ site.baseurl }}/custom-fields.html) entry for today.

The following examples are the default look of the shortcode:

    [wt-reminder]

[![Reminder]({{ site.baseurl }}/assets/images/reminder-entry.png)]({{ site.baseurl }}/assets/images/reminder-entry.png)

    [wt-reminder type=”target”]

[![Reminder Target]({{ site.baseurl }}/assets/images/reminder-target.png)]({{ site.baseurl }}/assets/images/reminder-target.png)

If you wish to have complete control over message displayed, you may override the above arguments. The following example shows you how to display your own message box / HTML.

    [wt-reminder]
        Enter some custom message here  
    [/wt-reminder]

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|additional_css|	Add an additional CSS class for the reminder box.|	String|	[wt-reminder additiona_css='wlt-css-class']
|link	|Wraps the notification in a link so you can direct the user to data entry page.	|Link	|[wt-reminder link='https://domainname.com/weight-entry']
|message	|The message to be displayed if the check is true. This will override the default message.	|String|	[wt-reminder message='Get a target weight entered!' type='target']
|number-of-days	|If considering weight entries, how many days back should we look for an entry? e.g. if set to 7, display a message if they haven't added a weight in the last 7 days.	|Number|	[wt-reminder number-of-days='7' type='weight']
|type|	The type of value to check for. Check that either weight entry has been added for today or a target. If set to "both", will display the message if both target and today's weight missing.|	'weight' (default), 'target' or 'both'	|[wt-reminder type='target']