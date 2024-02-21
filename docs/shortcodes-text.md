## Shortcodes

**[wt-difference-since-start]** ( [Pro]({{ site.baseurl }}/upgrade.html) )

The total weight lost by the logged in member.

**[wt-start-weight]**

Display the weight for first weight entry (for the current logged in user).

**[wt-start-date]** (  [Pro]({{ site.baseurl }}/upgrade.html)  )

Display the date of first weight entry (for the current logged in user).

**[wt-previous-weight]** (  [Pro]({{ site.baseurl }}/upgrade.html) )

Display the weight for previous weight entry (for the current logged in user).

**[wt-previous-date]** (  [Pro]({{ site.baseurl }}/upgrade.html)  )

Display the date of previous weight entry (for the current logged in user).

**[wt-difference-from-previous]** (  [Pro]({{ site.baseurl }}/upgrade.html) )

Display the difference between the user's current weight and the previous entry.

**[wt-days-between-start-and-latest]** (  [Pro]({{ site.baseurl }}/upgrade.html)  )

Display the number of days between the user's current weight and the previous entry. The shortcode has two arguments “include-days” (default false) to include the word “days” after the number and “include-brackets” to wrap the data within brackets.

e.g

    [wt-days-between-start-and-latest include-brackets=”true” include-days=”true”]

**[wt-latest-weight]**

Display the latest weight for the logged in user.

**[wt-latest-date]** (  [Pro]({{ site.baseurl }}/upgrade.html)  )

Display the weight for latest weight entry (for the current logged in user).

**[wt-target-weight]**

Display the logged in user's current target weight.

**[wt-difference-from-target]** (  [Pro]({{ site.baseurl }}/upgrade.html) )

Display the difference between the current user's most recent weight entry and their target weight.

    [wt-difference-from-target invert="true" display="percentage"]

Also supports the following arguments:
* "display" - Specifies whether to display a "weight" (default) or "percentage"
* "invert" to invert negative numbers into positive and vice versa e.g.
* "include-percentage-sign" - if displaying a percentage, specifies whether or not to include the percentage sign. Accepts "true" (default) or "false".

**[wt-difference-between-latest-previous]** 


Display the difference between the current user's most recent weight entry and the one they have previously entered.
 
    [wt-difference-between-latest-previous invert="true" display="percentage”] 
      
Also supports the following arguments:
* "display" - Specifies whether to display a "weight" (default), "percentage" or "both".
* "invert" to invert negative numbers into positive and vice versa e.g.
* "include-percentage-sign" - if displaying a percentage, specifies whether or not to include the percentage sign. Accepts "true" (default) or "false".

**[wt-height]**

Displays the user's height. It also supports the argument “not-specified-text” so you can display a message the user hasn't specified their height e.g.

    [wt-height not-specified-text=”No height specified”]

or to blank the default text:

    [wt-height not-specified-text=””]

**[wt-gender]**

Displays the user's gender. It also supports the argument “not-specified-text” so you can display a message the user hasn't specified their gender e.g.

    [wt-gender not-specified-text=”No gender specified”]

or to blank the default text:

    [wt-gender not-specified-text=””]

**[wt-dob]**

Displays the user's Date of Birth. It also supports the argument “not-specified-text” so you can display a message the user hasn't specified their DOB e.g.

    [wt-dob not-specified-text=”No DOB specified”]

or to blank the default text:

    [wt-dob not-specified-text=””]

**[wt-activity-level]**

Displays the user's  [Activity Level]({{ site.baseurl }}/calculations.html). It also supports the arguments “not-specified-text” and “shorten”. “not-specified-text” allows you to display a message the user hasn't specified their Activity Level e.g.

    [wt-activity-level not-specified-text=”No activity level specified”]

or to blank the default text:

    [wt-activity-level not-specified-text=””]

The second argument “shorten” if set to true shortens the string returned for Activity Level – some levels have more information in brackets (for example Moderate exercise (3-5 days/wk)), this is removed if shorten is set to true.

**[wt-new-users]**

Displays the number of  _newly  registered_ users in the last x days. By default, it will set the number of days to 7. It supports the following arguments;

“days” – the number of days to look back. For example, to show the number of registered users in the last year you would use: [wt-new-users days=365]

“count-all-roles” – by default, it will only count new users that have a role of “Subscriber”. If you wish to count all roles (administrators, editors, etc) then set this to “true” e.g. [wt-new-users count-all-roles=true]