## [wt-notes]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

[wt-notes] shall display all the notes for the current logged in user. Notes can be added against a user via their profile page in the admin interfaces.

>Note: Only notes marked as "Visible to user" are displayed via this shortcode. 

 **Shortcode Arguments**
 
 The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|message-no-data|When rendering the shortcode, if there are no notes for the user, the following error message is displayed.|Text| [wt-notes message-no-data="There are currently no notes"]
|notes-per-page|Number of notes per page|Number (default 10)|[wt-notes notes-per-page=5]
|paging|Enable paging controls|True (default) or false|[wt-notes paging=false]
|user-id|By default, the shortcode will display a calorie figure for the current user. You can display the value for another user by setting this argument to the relevant user ID.|Numeric| [wt-notes user-id="1"]
