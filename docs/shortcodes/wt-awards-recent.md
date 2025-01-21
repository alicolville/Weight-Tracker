## [wt-awards-recent]

> The following shortcode is only available in the [Premium]({{ site.baseurl }}/upgrade.html) version of the plugin.

Render the most recent [award]({{ site.baseurl }}/awards.html) for the given user. Read more about [awards]({{ site.baseurl }}/awards.html). Awards are sorted by category and then value. 

**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|error-message|	Message to display if a relevant photo could not be found.	|String. Defaults to an in-build message.	|[wt-awards-recent error-message="No photo!!"]
|height|	Allows you to specify the maximum height for the photo. It is best to specify the width argument as well.|	Number (default: 200).	|[wt-awards-recent height="400" width="400"]
|width|	Allows you to specify the maximum width for the photo. It is best to specify the height argument as well.|	Number (default: 200).|	[wt-awards-recent height="400" width="400"]
|user-id|	Specify the user ID to display an awardfor. By default, it will show the gallery for the current user|	Number. Defaults to user ID of the logged in user.	|[wt-awards-recent user-id=3]