<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Return an array of Pro Plus features.
 * @return array
 */
function ws_ls_feature_list_pro_plus() {
	return [
				[ 	
					'title'			=> esc_html__( 'All of the features that come with a standard Pro License', WE_LS_SLUG ), 
					'description'	=> esc_html__( 'and the following additional features:', WE_LS_SLUG )
				],
				[ 	
					'title'			=> '[wt-kiosk]', 
					'description'	=> esc_html__( 'allowing your administrators and staff to search and edit a user\'s record on the front end of your website.' , WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Barcode scanner', WE_LS_SLUG ), 
					'description'	=> esc_html__( 'An integrated Barcode scanner for scanning user IDs when using [wt-kiosk]', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Basal Metabolic Rate (BMR) calculations per user', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and extended admin screens to display a user\'s BMR', WE_LS_SLUG ),
				],
				[ 	
					'title'			=> esc_html__( 'Harris Benedict formula', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and extended admin screens to a view a person\'s calorie intake required to maintain and lose weight.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Recommended calorie intake per meal time', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and extended admin screens to recommend how a person should split their daily calorie intake across meals. ', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Macronutrients Calculator', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and extended admin screens to recommend how their calorie consumption should be split into fats, carbohydrates and proteins.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Additional user preference fields', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Additional user preference fields and shortcodes to display them: Activity Level, Date of Birth and Gender', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Awards', WE_LS_SLUG ),
					'description'	=> esc_html__( ' Awards and Badges! Set awards for: BMI Change, BMI Equals, Weight Gain / Loss from start and Percentage of weight lost from start.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Challenges', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Set challenges for your user\'s within a given time period? Display Total Weight Lost, BMI Change, %Body Weight, Weight Tracker Streaks and Meal Tracker streaks achieved by each user in a league table. Besides viewing all your challenges and their data, the shortcode will allow you to display the league table in the public facing website.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'BMI Calculator', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A quick tool to allow your users to enter their measurements/weight to calculate their BMI.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Waist-to-Hip ratio Calculator', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A quick tool to allow your users to enter their measurements to calculate their Waist-to-Hip ratio.', WE_LS_SLUG )
				]
	];	
}

/**
 * Return an array of Pro features.
 * @return array
 */
function ws_ls_feature_list_pro() {
	return [
				[ 	
					'title'			=> esc_html__( 'Access your user\'s data', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Admin can view, edit and delete user data. Various tools for viewing user\'s graphs, tables of entries, BMI, targets, weight lost / gained stats and much more.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Challenges', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Create and display challenges for users over different time periods.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Custom Fields', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Create and add your own questions to weight entry forms to gather additional information.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Photo Custom Fields', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Add one or more photo fields to your weight entry forms and allow your users to upload photos of their progress. Photos can be viewed, updated and removed by the end user and administrators. Handy shortcodes are provided for displaying galleries, most recent and oldest photo.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Data Export.', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Export data for one more users in either JSON or CSV format.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Webhooks, Zapier & Slack', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Push weight entry data and targets to Slack channels, Zapier or your own custom Webhooks!', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Groups', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Define user groups and assign your user\'s to them. View Weight Difference statistics for the group as a whole.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Admin notes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Administrators have the ability to store notes against their users. If set to visible, the user can view these via [wt-notes] or receive emails with their content.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Gamification', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Support for myCred, a popular gamification plugin. Reward your users for weight entries and setting their targets.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'BMI', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Allows a user to specify their height. Once specified, their BMI is displayed next to each weight entry. There is also a shortcode to render the latest BMI.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Email notifications', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Receive email notifications when a person updates their target or adds / edits a weight.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Birthday Emails', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Automatically send your user\'s a birthday email (when they have entered a date of birth)', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Overall user stats', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes that allow you to display the total lost / gained for the community and another to display a league table.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Widgets', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Widgets that allow you to display the graph and quick weight entry form within any widget area.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Chart and form shortcodes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'That allow you to display the graph and quick weight entry form by placing a shortcode on any post or page.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Chart and form shortcodes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'That allow you to display the graph and quick weight entry form by placing a shortcode on any post or page', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Progress Bar shortcode / widget', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A shortcode that visually displays the logged in user\'s progress towards their target', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Reminder shortcode', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A shortcode that can be used to remind the user to enter their target or weight for today.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Message shortcode', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A shortcode that allows you to congratulate a user when they lose weight x number of times. It also provides the opposite allowing you to provide encouragement when someone gains weight.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Text Shortcodes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Additional shortcodes for earliest and most recent dates entered.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Admin: View / Delete user data', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Admin will be able to view and delete existing user data.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'User preferences', WE_LS_SLUG ),
					'description'	=> esc_html__( 'If enabled, the user will be able to select which unit they wish to store their weight in Metric or Imperial. They will also be able to specify date format and clear all their weight data', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Bar Charts', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Fancy something different to a line chart? The plugin will also support Bar Charts.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Decimals', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Decimals will be allowed weight in Pounds only or Kg modes.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Delete existing entry', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A logged in user will be able to delete or edit an existing weight entry.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Better Tables', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A logged in user will be able to delete or edit an existing weight entry.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Admin: Extra Settings', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Extra settings to customise the plugin will be added e.g. number of plot points on graph, rows per page, etc.', WE_LS_SLUG )
				],
	];
}
