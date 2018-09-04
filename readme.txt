=== Weight Tracker ===
Contributors: aliakro
Tags: weight, loss, lose, helper, bmi, body, mass, index, graph, track, stones, kg, table, data, plot, target, history, pounds, responsive, chart, measurements, cm, centimeters, inches, hip, waist, bicep, thigh
Requires at least: 4.4.0
Tested up to: 4.9.8
Stable tag: 6.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate Link: https://www.paypal.me/yeken

Allow registered users of your website to track their weight, measurements, custom fields and much more! History can be displayed in both tables & charts. Support for BMI, BMR, Calorie Intake, Macronutrients and much more!

== Description ==

= Demo =

Use our free site for tracking your weight which shows off some of the plugin's feature: [TrackYourWeight.co.uk - Demo Site](https://www.trackyourweight.co.uk "TrackYourWeight.co.uk")

= Documentation =

[Weight Tracker Website](https://weight.yeken.uk/ "Weight Tracker Website")

= Core Features =

An easy to use plugin that allows your users to track their weight, body measurements and any custom fields you define. Their entries can be seen in various ways, charts, tables, shortcodes and widgets. The user is able to set targets and modify their entries.

The admin area features a rich user interface to allow site owners and personal trainers interact with their user base and help them achieve their goals.

Support for US/UK date formats as well as Imperial and Metric measurements.

For further information read our documentation:

[Weight Tracker Website - Shortcode guide](https://weight.yeken.uk/shortcodes "Weight Tracker Website - Shortcode guide")

= Pro Plus Version =

* All of the features that come with a standard Pro license.
* **Photo uploads**. Your users can now upload photos of their progress. Photos can be viewed, updated and removed by the end user and administrators. Handy shortcodes are provided for displaying galleries, most recent and oldest photo.
* **Basal Metabolic Rate (BMR) calculations per user**. Shortcodes and extended admin screens to display a user's BMR. For further information on BMR and how it is calculated visit our calculations page.
* **Harris Benedict formula**. Shortcodes and extended admin screens to a view a person's calorie intake required to maintain and lose weight. For further information on Harris Benedict Formula and how it is calculated visit our calculations page.
* **Recommended calorie intake per meal time**. Shortcodes and extended admin screens to recommend how a person should split their daily calorie intake across meals. For further information on how this is calculated please visit our calculations page.
* **Macronutrients Calculator**. Shortcodes and extended admin screens to recommend how their calorie consumption should be split into fats, carbohydrates and proteins. For further information on the Macronutrients Calculator and how these calculations are performed please visit our calculations page.
* **Additional user preference fields**. Additional user preference fields and shortcodes to display them: Activity Level, Date of Birth and Gender.

= Pro Version =

* **Support for Gravity Forms.** Scan Gravity Form submissions for relevant Weight / Measurement fields and create a weight entry automatically. [Read more](https://weight.yeken.uk/gravity-forms/ "Read more")
* **Admin can view, edit and delete user data.** Various tools for viewing user's graphs, tables of entries, BMI, targets, weight lost / gained stats and much more.
* **CSV and JSON exports** for all data or a particular user.
* **Measurements.** Support for recording measurements like Hip, Waist, Leg, Bicep, etc. Displayed on charts and tables.
* **Custom Fields.** Define and ask your user's custom questions on weight entry forms.
* **BMI.** Allows a user to specify their height. Once specified, their BMI is displayed next to each weight entry. There is also a shortcode to render the latest BMI.
* **Email notifications.** Receive email notifications when a person updates their target or adds / edits a weight.
* **Overall user stats.** Shortcodes that allow you to display the total lost / gained for the community and another to display a league table.
* **Widgets.** Widgets that allow you to display the graph and quick weight entry form within any widget area.
* **Chart and form Shortcodes.** That allow you to display the graph and quick weight entry form by placing a shortcode on any post or page.
* **Progress Bar shortcode.** A shortcode that visually displays the logged in user\'s progress towards their target
* **Reminder shortcode.** A shortcode that can be used to remind the user to enter their target or weight for today.
* **Message shortcode** A shortcode that allows you to congratulate a user when they lose weight x number of times. It also provides the opposite allowing you to provide encouragement when someone gains weight.
* **Text Shortcodes.** Additional shortcodes for earliest and most recent dates entered.
* **Progress Bar shortcode / widget.** Display a user\'s progress towards their weight target.
* **Reminder shortcode.** Display a reminder to enter their weight for the given day or enter a target.
* **User preferences**. If enabled, the user will be able to select which unit they wish to store their weight in Metric or Imperial. They will also be able to specify date format and clear all their weight data.
* **Bar Charts**. Fancy something different to a line chart? The plugin will also support Bar Charts.
* **Delete existing entry**. A logged in user will be able to delete or edit an existing weight entry.
* **Better Tables.**. Data tables in front end and admin will support paging and sorting.
* **Admin: Extra Settings**. Extra settings to customise the plugin will be added e.g. number of plot points on graph, rows per page, etc.', WE_LS_SLUG)

= Languages support =

The plugin is written in English (UK) but has support for other languages such as French, Spanish, Dutch, Italian, Norwegian, Portuguese-Brazil, etc. For a list of supported languages please visit:

[Weight Tracker Website - Supported Languages](https://weight.yeken.uk/weight-units-date-formats-languages/ "Weight Tracker Website - Supported Languages")

Need a translation? Please read the following guide: [Translating the Weight Tracker plugin](https://weight.yeken.uk/translating-weight-loss-tracker-plugin/ "Translating the Weight Tracker plugin")

= Documentation =

Need further help? Please visit the dedicated site:

[Weight Tracker Website](https://weight.yeken.uk "Weight Tracker Website")

= Donate =

Paypal Donate: [www.paypal.me/yeken](https://www.paypal.me/yeken "www.paypal.me/yeken")

== Installation ==

1. Install "Weight Tracker" via the "Plugins" page in WordPress Admin (or download zip and upload).
2. Setup the plugin in WordPress Admin panel by goto to Settings > Weight Tracker
3. Create a page that users will visit. Within the page content add the shortcode [wlt].
4. Voila

== Frequently Asked Questions ==

= Do you have any guides / documentation? =

Yes! Please visit our dedicated site [Weight Tracker Website](https://weight.yeken.uk "Weight Tracker Website")

= Does it create any custom mySQL tables =

Yes it creates six:

- WP_WS_LS_DATA_TARGETS - Stores user target data.
- WP_WS_LS_DATA - Store weight and measurement information per user.
- WS_LS_DATA_USER_PREFERENCES - Stores user preferences.
- WS_LS_DATA_USER_STATS - Stores user statistics data.
- WS_LS_META_FIELDS - Defines the Custom Fields that can be asked on weight entry forms.
- WS_LS_META_ENTRY - Stores the answers for Custom Fields.

= What date formats doe it support? =

Currently it supports both UK (dd/mm/yyy) and US (mm/dd/yyyy) date formats.

= What measurement formats doe it support? =

Currently it supports both Centimetres (CM) and Inches.

= Can I change measurement units while the site is live? =

Yes. Only recommended if you first installed the plugin at version 1.6 or greater. Newer versions stores measurements in Kg and Pounds. Versions prior 1.6 didn't so you may find data isn't present for previous date entries.

== Screenshots ==

1. All three tabs of the main [wlt] shortcode
2. Tab one of [wlt]: Chart, Target and Weight form.
3. Tab two of [wlt]: Target Weight and Weight History
4. Tab three of [wlt]: User preferences page
5. Examples of random placements of [wlt-chart] and [wlt-form]
6. Examples of the Chart and Form widgets
7. Settings page: General
8. Settings page: User Experience
9. Settings page: Chart
10. Settings page: Measurements
11. Admin - User data summary
12. Admin - Displaying a user's data card
13. Admin - User search results
14. Admin - Edit a user's data entry
15. Admin - User's data card displayed on a tablet
16. Admin - Displaying a another view of the user's data card
17. Admin - Displaying all Custom Fields
18. Admin - Add / Edit custom field

== Upgrade Notice ==

6.0 - New Custom Fields! Add your own questions to weight entry forms!

== Changelog ==

= 6.1 =


// review all caching and is_admin() checks in meta fields db.php
//todo: Update readme and doc site to state that photos is now Pro - not Pro Plus
//test with minmified scrtips
//todo: photos enabled check isn't working when creating a enw meta field. It should be set to true if photos were enabled at time of migratng.
//todo: test in all license modes
// Check logic to ensure photos are PRO plus only!
//todo: test emails on wegiht.yeken

* New Feature: Added a new Meta Field type of "Photos" (Pro Plus only). This will allow site administrators to add one or more photo field per entry form.
* New Feature: Build in tool to migrate photos from old system to new.
* Improvement: Removed old photo upload and migrated to to new meta fields.
* Improvement: Added custom field data to email notifications.
* Improvement: Added additional logic to remove photos from media library when no longer used by Weight Tracker (e.g. user has deleted them)

= 6.0 =

* New Feature: Added Custom Fields. Allow admin specified questions to be asked on weight entry forms.
* New Feature: Added Russian translations.
* Improvement: Updated Finnish translations.
* Improvement: Added error logging functionality.
* Improvement: Refactored Pro / Pro Plus features file include logic.
* Improvement: Upgraded Chart.js to 2.7.2
* Bug Fix: Fixed PHP errors causing the log entry "PHP Warning:  count(): Parameter must be an array or an object that implements Countable"

= 5.4.5 =

* Bug Fix: Measurements are now exported (via CSV / JSON) as inches where applicable.
* Bug fix: Fixed text alignment in table headers for RTL languages.

= 5.4.4 =

* Improvement: Added Arabic for Saudi Arabia (front end only). Thanks @Saeed

= 5.4.3 =

* Improvement: Added new option "auto" for "progress" argument [wlt-calories progress="auto"] shortcode. It will display the calories for the aim specified by the user e.g. if they select Lose Weight it will show the calories required to lose.
* Improvement: Added new option "auto" for "progress" argument wlt-macronutrients progress="auto"] shortcode. It will display the relevant macroN for the aim specified by the user e.g. if they select Lose Weight it will show the calories required to lose.

= 5.4.2 =

* Improvement: Added filter to allow Macro N shortcode and allowed progress options to be modified.

= 5.4.1 =

Bug fix: Issue with is_Array() function killing certain shortcodes.

= 5.4 =

* Improvement: Added support for Gravity Forms - read more: https://weight.yeken.uk/gravity-forms/
* Improvement: Added filter to modify allowed wlt-if fields (wlt-filter-if-allowed-fields)
* Improvement: Added filter to allow new wlt-if conditions to be added (wlt-filter-if-condition-[field name])
* Bug fix: Ensured object is countable before trying to count() it.
* Security fix: Added rel="noopener noreferrer" to all links that open in a new window ( i.e. target="_blank" )

= 5.3.1 =

* Improvement: Added filter to modify allowed wlt-if fields (wlt-filter-if-allowed-fields)
* Improvement: Added filter to allow new wlt-if conditions to be added (wlt-filter-if-condition-[field name])

= 5.3 =

* Improvement: Added filter to allow Activity Levels to be overridden (wlt-filter-activity-levels)
* Improvement: Added filter to allow Aims to be overridden (wlt-filter-aims)
* Improvement: Added filter to allow BMR calculation to be overridden (wlt-filter-bmr-calculation)
* Improvement: Added filter to allow Macro calculation per meal to be overridden (wlt-filter-macros-[key])
* Improvement: Added filter to allow Macro calculations per total to be overridden (wlt-filter-macros-[key]-total)
* Improvement: Added filter to allow all Macro calculations to be overridden (wlt-filter-macros)
* Improvement: Added filter to display an additional field below gender on user preferences form (wlt-filter-user-settings-below-gender)
* Improvement: Added filter to change a user's setting ('wlt-filter-user-setting-[key])
* Improvement: Added filter to allow calories to be overridden (wlt-filter-calories-lose)
* Improvement: Added filter to allow calories to maintain weight to be overridden (wlt-filter-calories-maintain)
* Improvement: Added filter to allow calories to lose weight to be overridden (wlt-filter-calories-lose)
* Improvement: Added filter to allow table rows to be filtered in Harris Benedict tables (wlt-filter-harris-benedict-rows)
* Improvement: Added filter to allow default macros to be specified and override WLT plugin. (wlt-filter-macros-custom)
* Improvement: Added filter to allow specify what types of calories should be converted into macros. wlt-filter-macros-calculate)
* Improvement: Added filter to allow specify what types of macros should be displayed. wlt-filter-macros-display)

= 5.2.26 =

* Improvement: Added Slovakian translations (thanks @Richard)
* Improvement: Added a new filter "wlt-filter-form-saved-message" to allow the save confirmation on form submission to be changed.

= 5.2.25 =

* Bug fix: Don't display notification from YeKen if it's blank!

= 5.2.24 =

* Added additional German translations (thanks @Benjamin)

= 5.2.23 =

* SVN Repo fix.

= 5.2.22 =

* Bug fix: CSV export. If the column is missing in underlying data then add a dummy field.
* Bug fix: Only display users on the stats table if they still exist in WP Users table.
* Bug fix: Correct display Pro Plus license price from Yeken Data feed.
* Bug fix: Only tell YeKen of license deactivation when one actually occurs.
* Bug fix: Fixed wpdb->prepare() errors thrown on user search.
* Reviewed data santisation and where required called relevant WordPress functions to sanitise user data.
* Removed redundant files.
* Removed commented lines of code.

= 5.2.21 =

* Added caching for user lookup.

= 5.2.20 =

* Added tweaks to Admin search for to support future AJAX lookups.
* Added some core functions to to lookup a user's previous weight and difference between that and latest.
* Added some core functions for fetching a user and limiting search results.

= 5.2.19 =

* Bug fix: If measurements enabled, ensure one or measurement fields have been enabled before rendering form fields.
* Added a hidden tool to fix the accuracy of Stones and Pounds to Kg.

= 5.2.18 =

* Improvement: Added options to allow admins to specify a fill colour and opacity under the weight line on charts.
* Improvement: Added new options to allow admins to specify font family and colour for charts.
* Updated Chart.js to 2.7.1

= 5.2.17 =

* Improvement: Added Arabic translations (thanks Firas).
* Bug fix: Record license expired properly.
* Bug fix: Clear WLT cache when plugin has been upgraded.
* Bug fix: Fixed rounding issues in data tables causing differences to look wrong.

= 5.2.16 =

* Improvement: Added additional argument for [wlt] shortcode that disables the check to see if [wlt] has already been placed on the page. Some users (and clashing plugins) were causing it to fail.
* Bug fix: Allow decimal entries for pounds (when in stones and pounds).
* Bug fix: When display comparison values, a rounding to one decimal place was causing the difference values to be slightly out. This has been changed to two decimals.

= 5.2.15 =

* Bug fix: When "Server Default" was selected for Photo uploads it would not allow any photo uploads to happen. This has been fixed.
* Added helper function ws_ls_if() for simplified IF checks.

= 5.2.14 =

* Updated Dutch translations (Thanks Robin!)

= 5.2.13 =

* Bug fix: Sorted issue with user preferences not being saved correctly

= 5.2.12 =

* Improvement: Refactored User preferences code so it can be extended.
* Improvement: Added new filters "wlt-filter-admin-user-sidebar-top", "wlt-filter-admin-user-sidebar-middle" and "wlt-filter-admin-user-sidebar-bottom" to allow developers to add HTML to user sidebar in "Manage Data".
* Improvement: Added new filter "wlt-filter-js-ws-ls-config" to allow developers to filter JS config.
* Improvement: Added new filter "wlt-filter-user-settings-below-aim" to allow developers to add to the User settings page.
* Improvement: Added the filters 'wlt-filter-user-settings-db-formats' and 'wlt-filter-user-settings-save-fields' to allow a developer to save other user preference fields.
* Bug fix: Stopped [wlt-calories] and [wlt-macronutrients] throwing an error when the user was logged out. Thanks @MARKONEX
* Bug fix: Fixed a bug where "Your modifications have been saved" message was always being shown on [wlt-table] shortcode.
* Database schema changes for future releases.

= 5.2.11 =

* Fixed a PHP check on a constant that in some cases threw an exception.

= 5.2.10 =

* Added some additional CSS to help those who have a theme that hides the [wlt] shortcode.

= 5.2.9 =

* Removed email from being sent when License expired. Appears it is sending in some cases when the license has not expired!

= 5.2.8 =

* Improvement: Added a new "About You" field called "Aim". This allows the user (or Admin) to specify their aim e.g. maintain, gain, or lose weight.
* Improvement: Expanded [wlt-if] to include a new field of "aim". Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Calorie caps can now be disabled by setting them to 0.
* Improvement: jQuery validation added to prompt the user to upload a smaller image if above file size limit.
* Improvement: New setting to limit the file size of images being uploaded.
* Improvement: Added check to ensure [wlt] shortcode is only placed once on a page or post.
* Improvement: An email is set to the Admin email address when the license expires.
* Improvement: License expire notifications are now sent to YeKen.
* Improvement: New hook "wlt-hook-license-expired" is fired when a license expires.
* Improvement: Removed setting "Advanced data tables?". This has been moved onto the shortcode themselves. See [wlt] argument "disable-advanced-tables". Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Removed setting "Display in tabs?". This has been moved onto the shortcode themselves. See [wlt] argument "disable-tabs". Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Merged and tidied up "User Experience" and "General" setting tabs.
* Improvement: Updated Chart.js to 2.7.0. Read more: https://github.com/chartjs/Chart.js/releases/tag/v2.7.0
* Bug fix: Ensured the string "photo" can be translated on [wlt] shortcode.
* Bug fix: Fixed issue where the width of chart lines was being effected by the chart "width" attribute.
* Removed "width" argument from [wlt-chart]. The attribute wasn't used.
* Notifications from YeKen are on by default and disabled when
* Updated Languages.

= 5.2.7 =

* Improvement: Added 7 day trial button
* Bug fix: Ensured the string "This field is required." can be translated.
* Bug fix: Fixed issue with empty "dob" field when using [wlt-if] shortcode.

= 5.2.6 =

* Updated language files again :(

= 5.2.5 =

* Updated language files

= 5.2.4 =

* Improvement: Added an Advanced tab to [wlt] shortcode to display a BMI, BMR, Calories and Macronutrients. This tab can be hidden with the attribute "hide-tab-advanced" - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Added a Photos tab to [wlt] shortcode to display a gallery. This tab can be hidden with the attribute "hide-tab-photos" - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Added footable.js to Macronutrient and Calorie tables in Admin (tables looks a lot better in responsive mode).
* Improvement: Added "alternate" class for Macronutrient table to make alternative rows stand out.
* Bug fix: Ensured "No data found" can be translated on data tables.
* Bug fix: Show hamburger menu icon on tabs.

= 5.2.3 =

Removing changes that weren't supposed to go out in 5.2.2 as they weren't completed! Apologies!

= 5.2.2 =

* Improvement: Added footable.js to Macronutrient and Calorie tables (tables looks a lot better in responsive mode).
* Bug fix: Fixed "Can't use return function in write context" appearing in older versions of PHP.
* Bug fix: Ensured "Measurements are in" can be translated.
* Bug fix: Ensured "Search" text on new data tables can be translated.

= 5.2.1 =

* Bug fix: Fixed issue with target weight failing to save if photos enabled.

= 5.2 =

* Photo Uploads!
	* New field on attachments "Don't show to public" (set to true by default) to stop user photo's being rendered on standard attachment pages.
	* Users can now upload / replace / remove a photo alongside their weight / measurement entries.
	* Admin can view all photos uploaded by a user.
	* Admin can upload / replace / remove a user's photo.
	* New shortcode [wlt-photo-count] to display the number of photos uploaded by the user - Read more: https://weight.yeken.uk/shortcodes/
 	* New shortcode [wlt-photo-oldest] to display the user's oldest photo - Read more: https://weight.yeken.uk/shortcodes/
 	* New shortcode [wlt-photo-recent] to display the user's most recent photo - Read more: https://weight.yeken.uk/shortcodes/
 	* New shortcode [wlt-gallery] to display the user's most recent photo - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Added "css-class" argument for [wlt-macronutrients-table] shortcode - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Added "css-class" argument for [wlt-calories-table] shortcode - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Standard Pro Users can now upgrade to Pro Plus for 50% cheaper!
* Improvement: [wlt] shortcode has a new argument "hide-photos". If set to true, the photo section of the form will be hidden. Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Added field "photo" to [wlt-if] shortcode. Allows you to check if the user has uploaded a photo.
* Improvement: New hook "wlt-hook-data-entry-deleted" created. Fired when an entry is deleted.
* Improvement: Removed DataTables.js and replaced with Footable.js for advanced data tables. This keeps the front end consistent with admin area.
* Improvement: [wlt-table] has a new argument to disable data being edited / deleted "enable-add-edit". Read more: https://weight.yeken.uk/shortcodes/
* Improvement: [wlt-table] user's now have the option to edit their data with this shortcode (not just delete).
* Improvement: Tweaked form headers to say "Edit" intead of "Add" when editing an entry.
* Improvement: Editing an entry is done using PHP and HTML. jQuery / Ajax has been removed so more reliable.
* Improvement: Re-factored all <blockquotes> to use one function to keep things consistent.
* Improvement: Added French-speaking Canada (Québec), French-speaking Belgium and French-speaking Switzerland- thanks Pierre
* Improvement: Updated French language - thanks Pierre
* Bug fix: When display a user's weight entries on Search results, if there are no entries, display nothing instead of 0[weight unit]
* Bug fix: Don't display "Difference form target" if there is no recent or target weights to calculate from.
* Bug fix: Fixed locale issues in data tables.
* Removed "(needed for BMR)" from setting fields.

= 5.1.7 =

* Improvement: Updated French translations - thanks @Pierre

= 5.1.6 =

    * Improvement: Added "strip-p-br" argument to [wlt-if] shortcode. - Read more: https://weight.yeken.uk/shortcodes/
    * Improvement: Added .pot file
    * Updated language files
    * Bug fix: Search results now shows user's that have no weight entries.

= 5.1.5 =

	* Improvement: [wlt-if] can now be nested. You can nest [wlt-if] statements upto three levels deep. - Read more: https://weight.yeken.uk/shortcodes/
    * Improvement: [wlt-if] field now supports one or more fields (creating an AND statement). Fields can be specified in a comma delimited list. - Read more: https://weight.yeken.uk/shortcodes/
    * Improvement: Prompt to login text when [wlt-form] is in target mode has been modified to remove reference to weight entry.
    * Improvement: Added additional text to display whether the measurements are optional or mandatory.
    * Improvement: Added a license check to be performed on software update.
    * Improvement: Added [wlt-bmi] - a shorter name for [wlt-recent-bmi].- Read more: https://weight.yeken.uk/shortcodes/
    * Bug fix: Fixed a typo in a Progress Bar error message.
	* Bug fix: Removed "Delete user data" command from redirect querystring if previously specified.

= 5.1.4 =

    * Improvement: Added new field "logged-in" to [wlt-if]. Allows you to check whether user is logged in. - Read more: https://weight.yeken.uk/shortcodes/
	* Improvement: Added "redirect-url" argument to [wlt-user-settings] shortcode - Read more: https://weight.yeken.uk/shortcodes/
	* Improvement: Added some translations for Spanish / Spanish Latin America (es_MX). Thanks Gerardopianist!
	* Bug fix: Fixed issue where Stones / Pounds was displaying 14 pounds instead of incrementing stones when displaying comparison weights.

= 5.1.3 =

	* New Feature (Pro): Shortcode to add some simple conditional logic around WLT fields [wlt-if] - Read more: https://weight.yeken.uk/shortcodes/
    * Bug fix: Fixed an issue with is_admin flag logic causing "Can't use function return value in write context" on some installs.
 	* Bug fix: If a user had no height, the "Add Entry" form would fail to save the user's weight / measurement entry.
    * Bug fix: If the user has no weight entries, then don't attempt to calculate BMI!

= 5.1.2 =

    * Bug fix: Removed default for MySQL date fields that was causing some MySQL tables not to be created properly.

= 5.1.1 =

	* New feature: Added new setting "'About You' fields mandatory?". When set to Yes, user's will be forced to select a value for all About You fields.
	* Improvement: Added 'allow-delete-data' attribute to shortcodes [wlt] and [wlt-user-settings]. If set to false (default is true), the section allowing users to delete their own data is hidden. - Read more: https://weight.yeken.uk/shortcodes/
	* Bug fix: Date issues when saving DOB in front end if saving in US format.
	* Bug fix: Hide [wlt-user-settings] form if the user is not logged in.
	* Bug fix: The height field can be viewed and save regardless of BMI being enabled.
	* Bug fix: If "Allow user settings" is set to "No" in settings then disable [wlt-user-settings].
	* Bug fix: When entering weights in Stones and Pounds, a user can no longer enter 14lbs. Instead, they need to up the stone measurement.
	* Bug fix: When displaying a Stone / Pounds weight, if the pounds figure is 14lb, set it to 0 and increment Stones by one.
	* Bug fix: When viewing a user's data card, the "Height" link now clicks through to their preferences page.
	* Updated core language PO and MO files (to reflect 5.1+ changes)

= 5.1 =

	* New feature: New Pro Plus license with more extended features.
	* New Feature (Pro Plus): Shortcode to display a user's BMR [wlt-bmr] - Read more: https://weight.yeken.uk/shortcodes/
	* New Feature (Pro Plus): Shortcode to display a user's recommended calorie intake in tabular form [wlt-calories-table] - Read more: https://weight.yeken.uk/shortcodes/
	* New Feature (Pro Plus): Shortcode to display a specific calorie intake figure for a user [wlt-calories] - Read more: https://weight.yeken.uk/shortcodes/
	* New Feature (Pro Plus): Shortcode to display a user's recommended macronutrient intake in tabular form [wlt-macronutrients-table] - Read more: https://weight.yeken.uk/shortcodes/
	* New Feature (Pro Plus): Shortcode to display a specific macronutrient intake figure for a user [wlt-macronutrients] - Read more: https://weight.yeken.uk/shortcodes/
    * New feature: New licensing core to support new yearly subscriptions and communicate stats to Yeken.uk
	* New feature: Shortcode to display the user's height [wlt-height] - Read more: https://weight.yeken.uk/shortcodes/
	* New feature: Shortcode to display the user's gender [wlt-gender] - Read more: https://weight.yeken.uk/shortcodes/
	* New feature: Shortcode to display the user's Date of Birth [wlt-dob] - Read more: https://weight.yeken.uk/shortcodes/
	* New feature: Shortcode to display the user's activity level [wlt-activity-level] - Read more: https://weight.yeken.uk/shortcodes/
	* New feature: Shortcode to display the number of newly registered users in last x days [wlt-new-users] - Read more: https://weight.yeken.uk/shortcodes/
	* Various tweaks to the underlying code to support future versions.
	* Improvement: Back button added when viewing user records.
	* Improvement: Icons added to buttons when managing user data.
	* Improvement: Show user side bar when managing user data in more relevant places.
	* Bug Fix: Edit Settings was loading DoB for the wrong user.

= 5.0.5 =

* Improvement: Standardised unit display e.g. St is now consistently lowercase.
* Bug fix: Fixed issue where Stones / Pounds was displaying 14 pounds instead of incrementing stones.

= 5.0.4 =

* Bug fix: Fixed missing charts doe to clashes with other plugins. JS enqueue issue where other plugins were using Chart.js (myCred in this example) with the same enqueue slug.

= 5.0.3 =

* Bug fix: Fixed JS issue where charts were only being rendered for Pro users!
* Bug fix: Missing semi colon in JS file.
* Improvement: Updated all .PO language files.

= 5.0.2 =

* Bug fix: Fixed an issue where JS enqueue order was causing some charts not to appear in IE.

= 5.0.1 =

* Bug fix: Re-instated JS required (on all admin screens) to dismiss WLT admin notifications.

= 5.0 =

* New Feature: New and improved admin interface for interacting with your user's data!
	* View all user entries in tabular and chart format.
	* Add, edit and delete user entries.
	* Sortable and responsive tables.
	* View league tables of most lost / gained.
	* View user stats, weight lost, recent weight, start weight, BMI, etc.
	* Edit a user's preferences / settings
	* Export all or a particular user's data in CSV / JSON.
	* Administrators can specify the minimum user role allowed to access / edit user data.
* New Feature: New settings fields for user Date of Birth, Gender and Activity Level
* Improvement: Tweaked jQuery datepicker to display a year range of -100 to now.
* Improvement: Added "hide-measurements" argument to [wlt-form] shortcode e.g. [wlt-form hide-measurements='true']
* Bug fix: Fixed Edit and delete links when using is navigating their data.

= 4.2.8 =

* Added localisation for missing strings.


= 4.2.7 =

* Improvement: New shortcode for user settings [wlt-user-settings] - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: New option added to [wlt-chart] "ignore-login-status". If set to true, will display the chart's data in the event the user is not logged in (should be used alongside user-id attribute) - Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Added "Redirect URL" to Form Widget.
* Improvement: Optimised speed of user preference lookup (if non sepcified). Previously if the user hadn't specified any user preferences the DB would have be queried for every key lookup. This was due to an empty array not being cached. Rules have changed to store an empty array to save querying DB for each user preference.
* Improvement: Standardised Hook / Filter names (possible breaking change if you use these). Reade more: https://weight.yeken.uk/hooks-and-filters/
* Improvement: Added "Neck" measurements.
* Bug fix: Fixed issue with cache keys for pounds and Kgs conflicting and replacing each other when querying extremes.
* Bug fix: Ensure both cron jobs are removed on plugin deactivation.

= 4.2.6 =

* Bug fix: Fixed an issue with cache not being cleared properly upon target being set.
* Bug fix: Fixed issue with cache keys for pounds and Kgs conflicting and replacing each other.

= 4.2.5 =

* Decimal places are now a core feature and no longer just be a perk of Pro.

= 4.2.4 =

* Added simpler shortcodes (don't worry the old ones still work):
	* [wlt] for [weight-loss-tracker].
	* [wlt-weight-diff] for [weightloss_weight_difference]
	* [wlt-weight-start] for [weightloss_weight_start]
	* [wlt-weight-most-recent] for [weightloss_weight_most_recent]
	* [wlt-weight-diff-from-target] for [weightloss_weight_difference_from_target]
	* [wlt-target] for [weightloss_target_weight]
	* [wlt-chart] for [weight-loss-tracker-chart]
	* [wlt-form] for [weight-loss-tracker-form]
	* [wlt-table] for [weight-loss-tracker-table]
	* [wlt-recent-bmi] for [weight-loss-tracker-most-recent-bmi]
	* [wlt-total-lost] for [weight-loss-tracker-total-lost]
	* [wlt-league-table] for [weight-loss-tracker-league-table]
	* [wlt-reminder] for [weight-loss-tracker-reminder]
	* [wlt-progress-bar] for [weight-loss-tracker-progress-bar]
	* [wlt-message] for [weight-loss-tracker-message]
* Updated Dutch translations (thanks @Robin).
* Added a new setting to disable admin notifications from YeKen.
* Slight tweak to rounding on pound values. Now to one decimal place instead of two.
* Bug fix: Fixed JS error being thrown in admin.js - wouldn't have caused any issues to users.
* Bug fix: Removed edit icon from Advanced Table when placed as a shortcode. Edit form only exists in main shortcode.
* Bug fix: Date column not sorting correctly once clicked in advanced data tables.
* Bug fix: Fixed issue with JS progress bar displaying "undefined"

= 4.2.3 =

* Bug fix: Rewritten jQuery function. IE 11 having issues with ES6 function declartion (used in progressbar.js).
* Bug fix: Re-wrote array declartions to use array() instead of [] due to strange errors.
* Improvement: Minified JS and CSS.

= 4.2.2 =

Bug fix: Fixed super large / delete icons in advanced table (probably due to cached CSS more than anything).
Bug fix: Use defined() instead of empty() to check for a empty constant.

= 4.2.1 =

* Bug fix: Pre PHP 5.5 issue within globals.php has been fixed. Please upgrade folks.

= 4.2 =

* New feature: Message shortcode to motivate people when they gain weight x number of times or congratulate if they lose weight x number of times. - Read more: https://weight.yeken.uk/shortcodes/
* New feature: Reminder shortcode to display reminder messages for users to add a target weight or weight / measurement entry for the day. - Read more: https://weight.yeken.uk/shortcodes/
* New feature: Progress bar towards target [weight-loss-tracker-progress-bar] - Read more: https://weight.yeken.uk/shortcodes/
* New Feature: Added widget for progress bar shortcode above. - Read more: https://weight.yeken.uk/widgets/
* New feature: Email notifications for update of user's target or adding / editing a weight entry: https://weight.yeken.uk/email-notifications/
* New feature: New attribute "redirect-url" added for the shortcode [ws_ls_shortcode_form]. If specified and once the data has been saved, the user will be redirected to the given URL. Please note, the URL has to be one for the current site, otherwise the redirect will not happen (URL is passed through wp_safe_redirect()). Read more https://weight.yeken.uk/shortcodes/
* New feature / improvements: Additional hooks and filters added. Read more:
* Improvement: Added the following to [weight-loss-tracker] shortcode: 'hide-first-target-form', 'hide-second-target-form' and 'show-add-button'. Read more: https://weight.yeken.uk/shortcodes/
* Improvement: Every license activation is instantly sent to YeKen.
* Improvement: Stats now sent weekly (instead of monthly) using a WP cron job. If "Send usage data to YeKen" is enabled, usage stats will be sent on weekly basis to YeKen by a scheduled WP cron job (instead of relying on an expired cached value to trigger the send).
* Improvement: Upgraded Chart.js to 2.5.0.
* Improvement: Changed "Upgrade to Pro" and documentation links to point to new website https://weight.yeken.uk
* Improvement: Changed comms and license activations to be sent to https://weight.yeken.uk (instead of yeken.uk)
* Improvement: Slight tweaks to advanced data table layouts.
* Bug fix: Setting "min-chart-points" attribute to 0 will now display the chart if no weight data has been entered.
* Bug fix: Issue generating stats for a user when a target weight is entered but no user weights exist (division by zero).
* Bug fix: Stats are instantly tidied up / re-generated if all user data is delete or an individual user deletes their data.
* Bug fix: If no target weight has been set, the shortcode will now inform the user when the shortcode [ws_ls_weight_difference_target] is rendered.
* Bug fix: Weight loss comparison figures shown properly when in stone / pounds.
* Bug fix: When using [weight-loss-tracker-total-lost], Kg values are now rounded to two decimal places.

= 4.1.10 =

* Updated Portuguese translations. (thanks @Thomas)

= 4.1.9 =

* Updated Dutch translations (thanks @Robin)
* Updated Italian translations (thanks @Salvo)
* Bug fix: Deal with negative numbers in ws_ls_pounds_to_stone_pounds() and ws_ls_to_stone_pounds() - Thanks (@GatorDev)

= 4.1.8 =

* Updated German translations (thanks @Christian)

= 4.1.7 =

* Bug fix: Corrected nonce name. Issue on some installs to do with verfiying nonces used to secure AJAX calls for loading user data in Admin.

= 4.1.6 =

* Added fixes to sort issues with Ultimate Tables plugin when both including the Datatables JS library.

= 4.1.5 =

* Updated Danish translations (thanks @spaniole)

= 4.1.4 =

* Added % lost / gained to stats table.
* Changed graph text when no weight data has been entered.
* Added some additional Danish translations for date picker.

= 4.1.3 =

* Added some Norwegian (Norsk bokmål) translations (Thanks Rodrigo).
* Date picker now translated into relevant locale - the majority driven from the WP Locale object. The words "Today" and "Done" can further be translated if provided.
* Bug fix: Removed rogue double quote in Chart HTML (Thanks Rodrigo).
* Bug fix: Only include ws_ls_config_advanced_datatables[] JS object when actually on the admin User Data page (Thanks Rodrigo).
* Bug fix: Only include wp_config[] JS object once. If multiple widgets / shortcodes were placed it would embed the object more than once.

= 4.1.2 =

* Bug fix: When saving weight data / targets it was trying to generate stats when the user didn't have a Pro license. This caused 500 errors!
* Bug fix: Stats settings should only be editable in Pro mode.
* Updated "Go Pro" page

Thanks @Terrence and @Steevie for your help!

= 4.1.1 =

Bug fix: Certain array declarations causing issues on non PHP7 sites.

= 4.1 =

* New shortcode: [weight-loss-tracker-total-lost] - Total lost / gained by the entire community.
* New shortcode: [weight-loss-tracker-league-table] - Show a league table of weight loss users.
* New cron job that runs every hour to refresh old user stats.
* Upgraded DataTables.js library to 1.10.13.
* Upgraded Chart.js library to 2.4.0.
* Bug fix: If weight data was empty the Chart widget would throw a PHP exception. Now fixed.
* Bug fix: Forcing a SQL query to uppercase was causing issues with case sensitive MySQL table names. Thanks Rodrigo!

= 4.0.3 =

* Added Hungarian (Magyar) translations - about 80% translations supplied. Thanks Noam.

= 4.0.2 =

* Added some more Dutch translations (thanks Yvon and Bart).
* Bug fix: Chart yAxes can now be translated
* Bug fix: Measurement labels can now be translated on form.
* Bug fix: Measurement columns can now be translated in data table.
* Bug fix: Measurement columns can now be translated in admin when viewing user data.
* Bug fix: Header and footer columns on user data page (admin) can now be translated

= 4.0.1 =

* Added "Shoulders" to measurement fields.
* Changed order of measurement fields to be more inline with body order.

= 4.0 =

**Pro Changes:**

* Support for measurements added - can now add and edit measurements for each entry!
* Measurements for "Bicep - Left", "Bicep - Right", "Forearm - Left", "Forearm - Right", "Calf - Left", "Bust / Chest", "Buttocks", "Hips", "Navel", "Thigh - Left", "Thigh - Right" and "Waist".
* Height can also be specified on the user preferences page.
* If height is specified the BMI value for the given height / weight will be displayed.
* New Shortcodes:
  [weight-loss-tracker-most-recent-bmi] - Displays the user's BMI for most recent weight given. The argument "display" determines what is rendered: 'index' - Actual BMI value. 'label' - BMI label for given value. 'both' - Label and BMI value in brackets

**Other changes:**

* Upgraded jQuery Datatables library to 1.10.12
* Upgraded Chart.js to version 2! (1.0.2 to 2.3.0)
* Target Weight line is now dashed to make it stand out more.
* New chart option added to start y Axes at 0 (instead of auto calculated).
* Use of tabs defaults to "Yes" on settings page.

**Bug fixes:**

* User preferences are now cached correctly.
* Fixed nounce check when fetching all user data.
* Various minor bug fixes.

= 3.6.1 =

* Fudge fix to remove .git folder from plugin directory!

= 3.6 =

 * Added the shortcode [weightloss_target_weight] to display the user's target weight (assuming they have specified one).

= 3.5.1 =

* BUG FIX: Additional logic around array_reverse when rendering a chart to ensure the object is an array and not empty.

= 3.5 =

* Added locale support for advanced data tables (Pro feature)
* Updated Dutch translations for advanced data tables and others

= 3.4.1 =

* CSS is included by default as some installations had issues. JavaScript includes have been moved to footer.

= 3.4 =

* Modified the including of JavaScript / CSS dependencies. To reduce conflicts with other plugins, JavaScript and CSS files are only included when widgets and shortcodes are placed.

= 3.3 =

* Added some additional Romanian translations (although not complete for plugin).
* BUG FIX: Finnish translation files were given the wrong code so not included as expected.

= 3.2 =

* Updated Finnish translations.

= 3.1.1 =

* Modified the main charts ([weight-loss-tracker-chart] and [weight-loss-tracker]) so they display the most recent date entries as opposed to the earliest.
* BUG FIX: Fixed difference in date ordering of widget and main charts. Previously the Widget displayed dates in desc as opposed to asc.

= 3.1 =

**Pro Changes:**

* Added new Shortcode for displaying a data table: [weight-loss-tracker-table] - By default it will display data for the current user logged in. Specifying the attribute "user-id" will allow you to display a table for the given user e.g. [weight-loss-tracker-table user-id="5"]
* Admin can now specify a message to be displayed in the place of widgets when the user is not logged in.
* BUG FIX:  Charts and Form shortcodes now display a login message when the user isn't logged. Previously they would see the data weight data for admin account.

**Core changes:**

 * Can now specify the minimum number of weight entries needed before chart is displayed e.g. [weight-loss-tracker min-chart-points=5]
 * Improved jQuery loading of tabs. Tabs are hidden until jQuery has rendered them correctly (this will stop the unstyled tabs appearing).
 * "Clear Target" button added to target forms.

= 3.0.1 =

- Extended MySQL table checker to ensure the data table for user preferences has been created.
- Removed CSS import statements for Open Sans and Lato.

= 3.0 =

Wey hey! Our first Pro version, if you upgrade you get the following additional features:

- Widgets. Widgets that allow you to display the graph and quick weight entry form within any widget area.
- Chart and form Shortcodes. That allow you to display the graph and quick weight entry form by placing a shortcode on any post or page.
- Text Shortcodes. Additional shortcodes for earliest and most recent dates entered.
- Admin: View / Delete user data. Admin will be able to view and delete existing user data.
- User preferences. If enabled, the user will be able to select which unit they wish to store their weight in Metric or Imperial. They will also be able to specify date format and clear all their weight data.
- Bar Charts. Fancy something different to a line chart? The plugin will also support Bar Charts.
- Decimals. Decimals will be allowed weight in Pounds only or Kg modes.
- Delete existing entry. A logged in user will be able to delete or edit an existing weight entry.
- Better Tables.. Data tables in front end and admin will support paging and sorting.
- Admin: Extra Settings. Extra settings to customise the plugin will be added e.g. number of plot points on graph, rows per page, etc.

Core changes:

- Removed support for Avada theme
- Changed jQuery to document ready instead of window ready (jQuery kicks in quicker)

= 2.0.1 =

BUG FIX: Target weight was appearing twice on non-tabbed mode.

- Removed some reundant jQuery regarding old tab library.
- Added an additional CSS rule to override some theme CSS causing issues.

= 2.0 =

This is a major release of the plugin and a complete rewrite of its core. As the plugin has grown unexpectedly the code base has became unmaintainable and each upgrade was a pain in the a$$. So, this version fixes that and provides a solid base for the future Pro version. Changes in this release:

New features:

* Better out the box styling and look - looks sharper and more presentable with no need for CSS knowledge.
* Supports UK and US date formats
* jQuery UI tabs replaced with cleaner, animated and responsive tabs.
* Shortcode name added [weight-loss-tracker] (works in exactly the same way as [weightlosstracker]).
* Admin menu has been expanded (ready for Pro version).
* Improved form validation and added more visual cues to prompt the user to correct the form.

Bug fixes:

* Other shortcodes (like weightloss_weight_difference) return the weight unit but no value when not logged in. Fixed to return nothing at all.
* Tab index order fixed on forms.

Underlying changes:

* Added caching to database queries - all queries are cached for upto 15 minutes. This will reduce the number of queries against the MySQL database and reduce overall load. It is estimated that DB queries will reduce by two thirds.
* All JavaScript (e.g. Chart) has been removed from inline statements to external files.
* All JavaScript and CSS files have been minified.
* Redundant / development copies of JavaScript, PHP and CSS files have been removed. Folder structures have been simplified and tidied.
* Increased chart height. Will be configurable in future releases.
* Major refactoring of ALL code.
* Various PHP performance tweaks to increase load time (e.g. replacing all double quotes for strings and replacing with single).
* Added additional CSS class names to HTML elements allowing developers to target their CSS better.
* Support added for multiple charts to be placed (the base to build shortcodes / widgets in the Pro version).

= 1.20.1 =

BUG FIX: Previous release broke the "Delete all data" button on admin page. Fixed.

= 1.20 =

* Changed input fields for weight to: type="number".
* Settings page: Added logic to check the required MySQL tables are present. If not, an option to rebuild them is available.
* Removed redundant upgrade script to add column to main data table.
* BUG FIX: If DateInterval class is not present (i.e. older version of PHP) disable date range selection on weight history table
* BUG FIX: Stopped non numeric values being entered for target weights.
* BUG FIX: When in Stones & Pounds mode, maximum pounds that can be entered is limited to 14.

= 1.19.2 =

* BUG FIX: Rookie mistake! Left debug data in!

= 1.19.1 =

* BUG FIX: Pound Only value not being saved correctly.

= 1.19 =

* Ability to specify the line and fill colours used on the graph. (thanks for the suggestion @Hardware Guru)
* Added Spanish translations - THANKS @idelfonsog2
* BUG FIX: In some cases the MySQL Target table was not being created correctly. The activate logic has been modified to correct this.

= 1.18 =

* BUG FIX: Gave the language function a unique name as clashing with some other plugins.
* IMPROVEMENT: Removed -50px off width of graph.

= 1.17 =

* Updated Romanian translations for admin page - THANKS @alexiarna
* Removed some redundant PHP files.
* BUG FIX: All text translated on admin page. One piece of text wasn't being translated.

= 1.16 =

* Added Danish translations - THANKS @kfasterholdt
* Updated Romanian translations (front end) - THANKS @alexiarna

= 1.15 =

- Removed upgrade check. This was trying to recreate the MySQL table for target data and throwing errors on some installs.
- Added versions numbers when enqueuing Js and CSS scripts.
- Tidied up includes statements.

= 1.14 =

- Chart.new.js (library used for charts) has been reverted back to Chart.js. Unfortunately this means labels are lost on graphs. However, Chart.js is still maintained by the developers.
- New option to display plot points on graph. If enabled, target and recorded weight will be displayed on hover.
- Chart JavaScript tidied up.
- BUG FIX: Removed references to jQuery Tabs when Tabs disabled.

= 1.13 =

- BUG FIX: Some third party plugins / installs insert additional input fields within <form>. Added additional code to remove any non expected ones.
- BUG FIX: Target form appears when tabs enbaled - regardless of whether or not Targets are enabled.

= 1.12 =

- Added a button on admin page to delete all existing user data.

= 1.11 =

- Added an admin page to manage settings for plugin

= 1.10 =

- Weight History data presented on seperate tab

= 1.9 =

- Users can now specify their target weight
- New short code [weightloss_weight_difference_from_target] - end weight of the logged in member to display the difference between target and latest weight
- New filters to filter data by week

= 1.8 =

* Upgraded Datepicker to use jquery (as opposed to HTML5 control) for better browser support

= 1.7 =

* Added Dutch translations

= 1.6 =

* Now supports pounds only. As opposed to just Stones / Pounds
* Translations added for:
  - Romanian
  - French
* Minor tweaks to conversions between stones / pounds
* Corrected ws_ls_to_stone_pounds to calculate pounds correctly
* [weightloss_weight_difference] corrected to display pounds
* Small bug fixes

= 1.5.1 =

Minor bug fix for new tags. Writing out values in the wrong place.

= 1.5 =

* Replaced chart.js with chartNew.js (https://github.com/FVANCOP/ChartNew.js/) to allow graph axis labels
* Axis labels added to graph
* Bug fix. Imperial measurements are now displayed on the graph in pounds (as opposed to stones / lbs) due to this bug:

	The problem is plotting imperial values on a graph. Say you have the following imperial data (stones / pounds) data:

	'15 7','15 3','15 0','14 12','14 10','14 7'

	I originally added a decimal place to stones / ozs so I can graph it:

	'15.7','15.3','15.0','14.12','14.10','14.7'

	At a glance it looks like this should work.

	However, the 14.7 is treated as 14.70 and therefore comes higher than 14.10.

  I couldn't see a quick way to correct this. So to be safe (and save me time), I've changed it to display in pounds only

= 1.4 =

* Added the following tags:

	[weightloss_weight_difference] - total weight lost by the logged in member
	[weightloss_weight_start] - start weight of the logged in member
	[weightloss_weight_most_recent] - end weight of the logged in member

= 1.2 / 1.3 =
* Various changes made upon feedback from WordPress submission

= 1.1 =
* Support imperial as well as metric weights

= 1.0 =
* Initial Build
