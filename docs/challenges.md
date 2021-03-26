# Challenges

> The following feature is only available in the [Pro Plus](/upgrade.html)  version of the plugin.

Challenges provide the ability, for a given time period, to create league tables to show user progress in relation to others. Users can be ranked on Total Weight Lost, BMI Change, %Body Weight, Weight Tracker Streaks and Meal Tracker streaks.


[![List of challenges](/assets/images/challenges-small.png)](/assets/images/challenges.png)

Once created, league tables are updated on an hourly basis, processing any new user entries within that time period.

### All Challenges

The initial screen lists all of the Challenges that have been setup. They can exist in two states,  **opened**  or  **closed**. If a Challenge is open, then user’s weight entries that fall within the date range criteria will cause an update to Challenge’s league table. However, if closed, the league table will no longer be updated – which is handy if you wish to show historic league tables.

[![List of all challenges](/assets/images/challenges-all-small.png)](/assets/images/challenges-all.png)

A list of all challenges

### Viewing challenge data

Within the  **admin area**, clicking into a challenge will display all of the relevant user data and their data for weight loss, entry streaks, etc – with the ability to sort by each. At the top of the table is a collection of filters that allows you to filter the table by gender, age range, group, opted in status and number of meal entries.

[![List of all challenges within Admin](/assets/images/challenges-admin-small.png)](/assets/images/challenges-admin.png)

Viewing challenge data

To view data on a  **public page or post**, use the shortcode  [[wt-challenges]](https://weight.yeken.uk/shortcodes/?section=wt-challenges)  e.g.

    [wt-challenges id="2" show-filters="false" sums-and-averages="true"]

This will render a similar table seen in the admin and allow your users to interact in the same manner. A shortcode snippet can be found under each table in the admin area.

[![List of all challenges - frontend view](/assets/images/challenges-frontend-small.png)](/assets/images/challenges-frontend.png)

A league table as seen on a public post.

## Additional Information

#### User Opt-in

By default, all of your users are opted out of challenges. This saves their name and data being displayed in public challenge tables. The user will need to opt-in to participate. To do this, they can either update their preferences (a new option has been added) or you can place this shortcode [wt-challenges-optin] to provide simple links allowing them to opt in, or out.

#### Performance

Performance: Please be aware, that every time a user updates their profile by adding or editing a weight, their statistics are recalculated for every challenge that isn’t closed. As the number of challenges grow and remain open, the greater the work load on your web server. Please ensure you close (or delete) every challenge when expired.