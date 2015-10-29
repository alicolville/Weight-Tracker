=== Plugin Name ===
Contributors: aliakro
Tags: weight, graph, track, stones, kg, table, data, plot, targetm history
Requires at least: 4.0.0
Tested up to: 4.3.1
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An easy to use plugin that allows a user to keep track of their weight history in both tabular and chart format.

== Description ==

= Pro Version =

We are currently developing a Pro version with many additional features! Read more and register your interest here: https://www.yeken.uk/show-interest-in-weight-loss-tracker-pro/

= Features =

An easy to use plugin that allows a user to keep track of their weight history in both tabular and chart format (Chart.js). Simply place the tag [weight-loss-tracker] on a given page and the user is presented with a tabbed form to enter a date (UK or US), weight and notes for that entry. When the person saves their entry the data table and graph are refreshed. The plugin also allows users to specify their target weight which is drawn on the chart as a comparison. If data is entered for an existing date, then the previous entry is simply updated. The graph is shown when there are two or more entries.

The following weight formats are supported:

- Metric (Kg)
- Imperial - Stones & Pounds
- Imperial - Pounds only

Also supports the following tags:

	[weightloss_weight_difference] - total weight lost by the logged in member
	[weightloss_weight_start] - start weight of the logged in member
	[weightloss_weight_most_recent] - end weight of the logged in member
	[weightloss_weight_difference_from_target] - difference from target

= Languages support =

Fully supported (have all translations):

- English (UK)

Languages partially supported (have some of the translations):

- French (thanks @alexiarna)
- Spanish (thanks @idelfonsog2)
- Romanian (thanks @alexiarna)
- Danish (thanks @kfasterholdt)
- Dutch

Need a translation? Email us: email@YeKen.uk and get the Pro version for free

* Developed by YeKen.uk *

Paypal Donate: email@YeKen.uk

== Installation ==

1. Install "Weight Loss Tracker" via the "Plugins" page in WordPress Admin (or download zip and upload).
2. Setup the plugin in WordPress Admin panel by goto to Settings > Weight Loss Tracker
3. Create a page that users will visit. Within the page content add the shortcode [weightlosstracker].
4. Voila

== Frequently Asked Questions ==

= Does it create any custom mySQL tables =

Yes it creates two. One to store weight information per user and another to store target weights:

- WP_WS_LS_DATA_TARGETS - Stores user target data
- WP_WS_LS_DATA - Stores weight history

= What date formats doe it support? =

Currently it supports both UK (dd/mm/yyy) and US (mm/dd/yyyy) date formats.

= Does it support the Avada theme? =

Yes. However, as of version 2.0 it will be deprecated and finally removed. In WordPress Admin goto Settings > Weight Loss Tracker and change the setting "Enable support for Avada theme?" to Yes.

= How do I switch it from Metric (Kg) to Imperial (Stones / Pounds)? =

In WordPress Admin goto Settings > Weight Loss Tracker and change the setting "Weight Units" to the desried type.

= How do I enable tabs? =

Yes. In WordPress Admin goto Settings > Weight Loss Tracker and change the setting "Display data in tabs?" to Yes.

= Can I change measurement units while the site is live? =

It is only recommended if you first installed the plugin at version 1.6+ (as it stores measurements in Kg / Pound). Before this, you may find data isn't present for previous date entries.

= How do I disable "Target weight" =

Yes. In WordPress Admin goto Settings > Weight Loss Tracker and change the setting "Allow targets?" to No.

== Screenshots ==

1. Tab one: Chart with Target and Weight entry forms.
2. Tab two: Target form and Weight history table.
3. Example usage of other shortcodes.
4. Settings page

== Changelog ==

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
