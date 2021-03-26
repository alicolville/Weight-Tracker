# [wt-message]

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

The message shortcode allows custom content to be displayed when a user has gained or lost weight over a number of consecutive weight entries. This will allow you to motivate people when they gain weight or congratulate if they lose weight (or vice versa).

Below are some examples of its use:

    [wt-message type=lost]
        Well done you have lost weight (since last weight)
    [/wt-message]

    [wt-message type=lost consecutive=3] 
        Well done you have lost weight 3 times
    [/wt-message]

    [wt-message type=gain]
        Unlucky, you have gained weight since your last entry.
    [/wt-message]
    
    [wt-message type=gain consecutive=3]
        Oh no… you have gained weight 3 times…
    [/wt-message]


**Shortcode Arguments**
 
The shortcode supports the following arguments:
 
| Argument | Description | Options | Example |
|--|--|--|--|
|consecutive|The number of previous consecutive weight entries to consider. For example, if set to 3, then the previous 3 weight entries must have consistently gained or lost weight.|A number between 1 and 30. Default 1.|[wt-message consecutive=3]Message here[/wt-message]
|type|The type determines whether to check for weight gain or weight loss.|'gained' (default)- Gained weight since previous chronlogical entry. 'lost' - Lost weight since previous chronlogical entry|[wt-message type='lost']Message here[/wt-message]

			

			