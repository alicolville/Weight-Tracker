
# Calculations

Below summarises how certain default values are calculated within the plugin.

## BMR

BMR is short for Basal Metabolic Rate. The Basal Metabolic Rate is the number of calories required to keep the person's body functioning at rest, also known as your metabolism. Weight Loss Tracker calculates BMR using the following formula (taken from [www.diabetes.co.uk](http://www.diabetes.co.uk/bmr-calculator.html)):

-   **BMR for Men** = `66.47 + (13.7 * weight [kg]) + (5 * size [cm]) − (6.8 * age [years])`
-   **BMR for Women** = `655.1 + (9.6 * weight [kg]) + (1.8 * size [cm]) − (4.7 * age [years])`

Sources: [Be Strong](https://mybestrong.com/) and  [Diabetes UK](http://www.diabetes.co.uk/bmr-calculator.html).

## Harris Benedict Formula

Once a user's BMR has been calculated, the plugin can calculate the user's total calorie intake required to maintain their current weight. This is calculated by multiplying your BMR i.e.

    Total Calorie Need to Maintain Weight = BMR x Activity Level

The following are the Activity Levels that can be selected within the plugin and their corresponding multiplying value:

-   **Little/no exercise:** 1.2
-   **Light exercise:** 1.375
-   **Moderate exercise (3-5 days/wk):** 1.55
-   **Very active (6-7 days/wk):** 1.725
-   **Extra active (very active & physical job):** 1.9

Sources [Be Strong](https://mybestrong.com/) and  [Diabetes UK](http://www.diabetes.co.uk/bmr-calculator.html).

##### CALORIE INTAKE TO LOSE WEIGHT

Based on [NICE guidance](http://www.nice.org.uk/guidance/cg189), which states that to lose weight, the average person should reduce their daily calorie intake by 600kcal the plugin can calculate the recommended daily calorie intake to lose weight i.e.

    Daily Calorie Need to Lose weight = Total Calorie Need to Maintain Weight – 600kcal

Depending on your plan, the 600kcal is configurable and can be changed via the plugin's settings page.

Sources:  [Be Strong](https://mybestrong.com/), [NHS Weight Loss Plan](http://www.nhs.uk/Livewell/weight-loss-guide/Pages/losing-weight-getting-started.aspx)  and  [Day One PDF of the plan](http://www.nhs.uk/Tools/Documents/WEIGHT-LOSS-PACK/week-1.pdf).

Based on  [NHS guidelines](http://www.nhs.uk/Tools/Documents/WEIGHT-LOSS-PACK/week-1.pdf)  this is capped at 1,900kcal for males and 1,400kcal for women. These caps can however be adjusted in the settings page.

## Calorie intake to gain weight

If enabled, you can present your users with the recommended number of calories to gain weight.

    Daily Calorie Need to Gain weight = Total Calorie Need to Maintain Weight + 500kcal

Depending on your plan, the 500kcal is configurable and can be changed via the plugin's settings page.

#### Calorie intake broken down into meal times and snacks

With the “Daily Calorie Need to  **Lose**  weight” and “Total Calorie Need to  **Maintain**  Weight” the plugin can provide recommendations on how to split those calories over the day into meal times / snacks. Based on  [NHS guidelines](http://www.nhs.uk/Tools/Documents/WEIGHT-LOSS-PACK/all-weeks.pdf)  this has been split in the following manner:

-   **Breakfast**  – 20%
-   **Lunch**  – 30%
-   **Dinner**  – 30%
-   **Snacks**  – 20%

Below is an example of a table generated for a user:

[![Example Calories]({{ site.baseurl }}/assets/images/example-calories-per-day.png)]({{ site.baseurl }}/assets/images/example-calories-per-day.png)

This data is used to power [shortcodes]({{ site.baseurl }}/shortcodes.html)  such as [[wt-calories]]({{ site.baseurl }}/shortcodes/wt-calories.html) and [[wt-calories-table]]({{ site.baseurl }}/shortcodes/wt-calories-table.html).

Sources [Be Strong](https://mybestrong.com/) and  [NHS](http://www.nhs.uk/Tools/Documents/WEIGHT-LOSS-PACK/all-weeks.pdf).

#### Macronutrients Calculator

With calories calculated, the plugin's macronutrient calculator can recommend how those calories should be split into Fats, Carbohydrates and Proteins. The  [2010 Dietary Guidelines for Americans](https://health.gov/dietaryguidelines/2010/)  recommends eating within the following ranges:

-   Carbohydrates: 45-65% of calories
-   Fat: 20-35% of calories
-   Protein: 10-35% of calories

and by default the plugin specifies these to be the following:

-   Carbohydrates: 50% of calories
-   Fat: 25% of calories
-   Protein: 25% of calories

Of course, your idea of what these values should be may be different – therefore the plugin allows you to set these percentages within the settings. From this, number of grams of Carbs, Proteins and Fat that you should have for Maintaining weight or losing weight can be calculated.

The calculator takes these totals for the day and splits them across your daily meals / snacks to suggest how many grams of each you should take. The following percentages are used:

Breakfast

-   Breakfast meal = 20% of Daily Protein Grams
-   Breakfast meal = 20% of Daily Carbohydrate Grams
-   Breakfast meal = 20% of Daily Fat Grams

Lunch

-   Lunch meal = 30% of Daily Protein Grams
-   Lunch meal = 30% of Daily Carbohydrate Grams
-   Lunch meal = 30% of Daily Fat Grams

Dinner

-   Dinner meal = 30% of Daily Protein Grams
-   Dinner meal = 30% of Daily Carbohydrate Grams
-   Dinner meal = 30% of Daily Fat Grams

Snacks

-   Snacks = 20% of Daily Protein Grams
-   Snacks = 20% of Daily Carbohydrate Grams
-   Snacks = 20% of Daily Fat Grams

Below is an example Macronutrient table created for a user:

[![Macronutrient table]({{ site.baseurl }}/assets/images/macronutrients-small.png)]({{ site.baseurl }}/assets/images/macronutrients.png)

This data is used to power [shortcodes]({{ site.baseurl }}/shortcodes.html)  such as [[wt-macronutrients]]({{ site.baseurl }}/shortcodes/wt-macronutrients.html) and [[wt-macronutrients-table]]({{ site.baseurl }}/shortcodes/wt-macronutrients-table.html).

Sources:  [Be Strong](https://mybestrong.com/), [My Fitness Pal Blog](http://blog.myfitnesspal.com/ask-the-dietitian-whats-the-best-carb-protein-and-fat-breakdown-for-weight-loss/)  and [2010 Dietary Guidelines](https://health.gov/dietaryguidelines/2010/)

## Suggested Target Weight

Weight Tracker can calculate suggested target weights for user's based upon their BMI and height. The following formula is used:

`Kg = 2.2 x BMI + (3.5 x BMI) x (Height in meters minus 1.5)`

Sources: [Weight Tracker users](https://github.com/alicolville/Weight-Tracker/issues/502).
