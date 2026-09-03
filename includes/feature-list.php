<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Return an array of Premium features.
 * @return array
 */
function ws_ls_feature_list_premium() {
	return array_merge( ws_ls_feature_list_pro_plus(), ws_ls_feature_list_pro() );
}

/**
 * Return an array of Premium features. (Legacy license type)
 * @return array
 */
function ws_ls_feature_list_pro_plus() {
	return [
				[ 	
					'title'			=> '[wt-kiosk]', 
					'description'	=> esc_html__( 'A shortcode enabling your administrators and staff to search for and edit user records directly from the front end of your website.' , WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Barcode scanner', WE_LS_SLUG ), 
					'description'	=> esc_html__( 'an integrated barcode scanner for seamless user ID scanning when utilising the [wt-kiosk] feature.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Basal Metabolic Rate (BMR) calculations per user', WE_LS_SLUG ),
					'description'	=> esc_html__( 'with shortcodes and enhanced admin screens to display and manage individual BMR data.', WE_LS_SLUG ),
				],
				[ 	
					'title'			=> esc_html__( 'Harris Benedict formula', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and expanded admin screens to view a person\'s required calorie intake for maintaining or losing weight.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Recommended calorie intake per meal time', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and advanced admin screens to recommend how a person should distribute their daily calorie intake across meals.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Macronutrients Calculator', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes and enhanced admin screens to recommend how to divide calorie consumption among fats, carbohydrates, and proteins.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Additional user preference fields', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Additional user preference fields and shortcodes for displaying Activity Level, Date of Birth, and Gender.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Awards', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Awards and badges for achievements such as BMI Change, BMI Milestones, Weight Gain/Loss from the start, and Percentage of Weight Lost from the start.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Challenges', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Create challenges for your users within a specified time frame, and showcase their achievements such as Total Weight Lost, BMI Change, Percentage of Body Weight Lost, Weight Tracker Streaks, and Meal Tracker Streaks in a league table. The shortcode will enable you to view all challenges and their data, as well as display the league table on your public-facing website.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'BMI Calculator', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A handy tool for users to quickly input their measurements and weight to calculate their BMI.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Waist-to-Hip ratio Calculator', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A convenient tool for users to quickly enter their measurements and calculate their Waist-to-Hip ratio.', WE_LS_SLUG )
				]
	];	
}

/**
 * Return an array of Pro features. (Legacy license type)
 * @return array
 */
function ws_ls_feature_list_pro() {
	return [	[ 	
					'title'			=> esc_html__( 'Custom Fields', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Customise weight entry forms by creating and adding your own questions to collect additional information from users.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Photo Custom Fields', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Enhance your weight entry forms by adding one or more photo fields, enabling users to upload photos showcasing their progress. Both users and administrators can view, update, and delete these photos. Convenient shortcodes are provided to display galleries, as well as the most recent and oldest photos.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Access your user\'s data', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Administrators can view, edit, and delete user data, with tools for analysing user graphs, entry tables, BMI, targets, weight loss/gain statistics, and more.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Challenges', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Develop and showcase challenges for users across different time frames, allowing for flexible goal-setting and progress tracking.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Data Export.', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Export data for individual users in either JSON or CSV format', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Webhooks, Zapier & Slack', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Send weight entry data and targets to Slack channels, Zapier, or your custom webhooks for seamless integration and notifications.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Groups', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Create user groups and assign users to them, then view collective Weight Difference statistics for each group.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Admin notes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Administrators can store notes for users, which can be set to visible so that users can view them through the [wt-notes] shortcode or receive them via email.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Gamification', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Integrate with myCred, a popular gamification plugin, to reward users for logging weight entries and setting targets.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Body Mass Index', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Allows users to enter their height, automatically displaying their BMI alongside each weight entry. Additionally, shortcodes are available to render the most recent and starting BMI.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Email notifications', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Receive email notifications whenever a user updates their target or adds/edits their weight entries.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Birthday Emails', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Automatically send birthday emails to your users on their special day.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Comprehensive user statistics', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes to display the total weight lost or gained by the community, and another to showcase a league table.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Widgets', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Widgets to display a graph and a quick weight entry form in any widget area.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Chart and form shortcodes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Shortcodes that allow you to display the graph and quick weight entry form on any post or page.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Progress Bar shortcode / widget', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A shortcode that visually displays the logged-in user\'s progress towards their target.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Reminder shortcode', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A shortcode that prompts users to enter their target or weight for today.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Message shortcode', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A shortcode that congratulates users when they achieve weight loss milestones and can also offer encouragement when they gain weight.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Text Shortcodes', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Additional shortcodes for displaying the earliest and most recent dates entered.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Admin: View / Delete user data', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Administrators will be able to view and delete existing user data.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'User preferences', WE_LS_SLUG ),
					'description'	=> esc_html__( 'If enabled, users can choose to store their weight in either Metric or Imperial units, specify their preferred date format, and clear all their weight data.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Bar Charts', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Fancy something different to a line chart? The plugin will also support Bar Charts.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Delete existing entry', WE_LS_SLUG ),
					'description'	=> esc_html__( 'A logged-in user can delete or edit their existing weight entries.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Richer data tables', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Data can be visualised and interacted with through enhanced HTML tables.', WE_LS_SLUG )
				],
				[ 	
					'title'			=> esc_html__( 'Admin: Additional settings', WE_LS_SLUG ),
					'description'	=> esc_html__( 'Additional settings will be added for further customisation of the plugin, such as adjusting the number of plot points on graphs, the number of rows per page, and more.', WE_LS_SLUG )
				]
	];
}
