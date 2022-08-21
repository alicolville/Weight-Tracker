<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Return an array of Pro Plus features.
 * @return array
 */
function ws_ls_feature_list_pro_plus() {
	return [
		__(' <strong>All of the features that come with a standard <a href="' . WE_LS_LICENSE_TYPES_URL . '" target="_blank">Pro license</a>.</strong>', WE_LS_SLUG ),
		__( '<strong>[wt-kiosk]</strong> - allowing your administrators and staff to search and edit a user\'s record on the front end of your website.' , WS_LS_SLUG ),
		__(' <strong>Basal Metabolic Rate (BMR) calculations per user</strong>. Shortcodes and extended admin screens to display a user\'s BMR. For further information on BMR and how it is calculated visit <a href="' . WE_LS_CALCULATIONS_URL . '" rel="noopener noreferrer" target="_blank">our calculations page</a>.</strong>', WE_LS_SLUG ),
		__(' <strong>Harris Benedict formula</strong>. Shortcodes and extended admin screens to a view a person\'s calorie intake required to maintain and lose weight. For further information on Harris Benedict Formula and how it is calculated visit <a href="' . WE_LS_CALCULATIONS_URL . '" rel="noopener noreferrer" target="_blank">our calculations page</a>.</strong>', WE_LS_SLUG ),
		__(' <strong>Recommended calorie intake per meal time</strong>. Shortcodes and extended admin screens to recommend how a person should split their daily calorie intake across meals. For further information on how this is calculated please visit <a href="' . WE_LS_CALCULATIONS_URL . '" rel="noopener noreferrer" target="_blank">our calculations page</a>.</strong>', WE_LS_SLUG ),
		__(' <strong>Macronutrients Calculator</strong>. Shortcodes and extended admin screens to recommend how their calorie consumption should be split into fats, carbohydrates and proteins. For further information on the Macronutrients Calculator and how these calculations are performed please visit <a href="' . WE_LS_CALCULATIONS_URL . '" rel="noopener noreferrer" target="_blank">our calculations page</a>.</strong>', WE_LS_SLUG ),
		__(' <strong>Additional user preference fields</strong>. Additional user preference fields and shortcodes to display them: Activity Level, Date of Birth and Gender.', WE_LS_SLUG ),
		__(' <strong>Awards</strong>. Awards and Badges! Set awards for: BMI Change, BMI Equals, Weight Gain / Loss from start and Percentage of weight lost from start.', WE_LS_SLUG ),
		__(' <strong>Challenges</strong>. Set challenges for your user\'s within a given time period? Display Total Weight Lost, BMI Change, %Body Weight, Weight Tracker Streaks and Meal Tracker streaks achieved by each user in a league table. Besides viewing all your challenges and their data, the shortcode will allow you to display the league table in the public facing website.', WE_LS_SLUG )
	];
}

/**
 * Return an array of Pro features.
 * @return array
 */
function ws_ls_feature_list_pro() {
	return [
		__(' <strong>Access your user\'s data.</strong> Admin can view, edit and delete user data. Various tools for viewing user\'s graphs, tables of entries, BMI, targets, weight lost / gained stats and much more.', WE_LS_SLUG ),
		__(' <strong>Challenges.</strong> Create and display challenges for users over different time periods.', WE_LS_SLUG ),
		__(' <strong>Custom Fields.</strong> Create and add your own questions to weight entry forms to gather additional information.', WE_LS_SLUG ),
		__(' <strong>Photo Custom Fields</strong>. Add one or more photo fields to your weight entry forms and allow your users to upload photos of their progress. Photos can be viewed, updated and removed by the end user and administrators. Handy shortcodes are provided for displaying galleries, most recent and oldest photo.', WE_LS_SLUG ),
		__(' <strong>Export all data or a particular user.</strong> Export in JSON or CSV format.', WE_LS_SLUG ),
		__(' <strong>Webhooks, Zapier & Slack</strong>. Push weight entry data and targets to Slack channels, Zapier or your own custom Webhooks!', WE_LS_SLUG ),
		__(' <strong>Groups</strong>. Define user groups and assign your user\'s to them. View Weight Difference statistics for the group as a whole.', WE_LS_SLUG ),
		__(' <strong>Admin notes</strong>. Administrators have the ability to store notes against their users. If set to visible, the user can view these via [wt-notes] or receive emails with their content.', WE_LS_SLUG ),
		__(' <strong>Gamification</strong>. Support for myCred, a popular gamification plugin. Reward your users for weight entries and setting their targets.', WE_LS_SLUG ),
		__(' <strong>BMI.</strong> Allows a user to specify their height. Once specified, their BMI is displayed next to each weight entry. There is also a shortcode to render the latest BMI.', WE_LS_SLUG ),
		__(' <strong>Email notifications.</strong> Receive email notifications when a person updates their target or adds / edits a weight.', WE_LS_SLUG ),
		__(' <strong>Birthday Emails.</strong> Automatically send your user\'s a birthday email (when they have entered a date of birth)', WE_LS_SLUG ),
		__(' <strong>Overall user stats.</strong> Shortcodes that allow you to display the total lost / gained for the community and another to display a league table.', WE_LS_SLUG ),
		__(' <strong>Widgets.</strong> Widgets that allow you to display the graph and quick weight entry form within any widget area.', WE_LS_SLUG ),
		__(' <strong>Chart and form Shortcodes.</strong> That allow you to display the graph and quick weight entry form by placing a shortcode on any post or page.', WE_LS_SLUG ),
		__(' <strong>Progress Bar shortcode.</strong> A shortcode that visually displays the logged in user\'s progress towards their target', WE_LS_SLUG ),
		__(' <strong>Reminder shortcode.</strong> A shortcode that can be used to remind the user to enter their target or weight for today.', WE_LS_SLUG ),
		__(' <strong>Message shortcode</strong> A shortcode that allows you to congratulate a user when they lose weight x number of times. It also provides the opposite allowing you to provide encouragement when someone gains weight.', WE_LS_SLUG ),
		__(' <strong>Text Shortcodes.</strong> Additional shortcodes for earliest and most recent dates entered.', WE_LS_SLUG ),
		__(' <strong>Progress Bar shortcode / widget.</strong> Display a user\'s progress towards their weight target.', WE_LS_SLUG ),
		__(' <strong>Reminder shortcode.</strong> Display a reminder to enter their weight for the given day or enter a target.', WE_LS_SLUG ),
		__(' <strong>Admin: View / Delete user data</strong>. Admin will be able to view and delete existing user data.', WE_LS_SLUG ),
		__(' <strong>User preferences</strong>. If enabled, the user will be able to select which unit they wish to store their weight in Metric or Imperial. They will also be able to specify date format and clear all their weight data.', WE_LS_SLUG ),
		__(' <strong>Bar Charts</strong>. Fancy something different to a line chart? The plugin will also support Bar Charts.', WE_LS_SLUG ),
		__(' <strong>Decimals</strong>. Decimals will be allowed weight in Pounds only or Kg modes.', WE_LS_SLUG ),
		__(' <strong>Delete existing entry</strong>. A logged in user will be able to delete or edit an existing weight entry.', WE_LS_SLUG ),
		__(' <strong>Better Tables.</strong>. Data tables in front end and admin will support paging and sorting.', WE_LS_SLUG ),
		__(' <strong>Admin: Extra Settings</strong>. Extra settings to customise the plugin will be added e.g. number of plot points on graph, rows per page, etc.', WE_LS_SLUG )
	];
}
