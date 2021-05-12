# Webhooks, Zapier and Slack

> The following shortcode is only available in the [Pro]({{ site.baseurl }}/upgrade.html) version of the plugin.

## What is a webhook?

A webhook is HTTP push protocol that allows one application to push data to another. If enabled, Weight Tracker can push weight and target data to endpoints. This data can then be read by the other application.  _For example,_  [Zapier](https://zapier.com/)  can receive incoming webhooks as a trigger and one or more actions can be performed based upon it. Perhaps you want to save them to a Google Sheet, or send an SMS, or perform various additional calculations. Whatever you decide, having the ability to fire the data to another service allows you to extend Weight Tracker in ways that suits your business and it's workflows.

[Zapier](https://www.zapier.com/)  is just one example of a service that can process incoming webhooks. There are lots more out there which may greatly benefit your business e.g. IFTTT – that said, you may have your own endpoint that you wish to push data to.

## Data formats

Below illustrates the data sent to a web hook when fired. All data is send in JSON format.

**Weight entry added or updated:**

```
{
  "_event": {
    "type": "weight",
    "mode": "add"
  },
  "entry-id": "25",
  "user-id": "1",
  "user-display-name": "YeKen User",
  "date-iso": "2021-02-20 00:00:00",
  "date-display": "20/02/2021",
  "weight-kg": "56",
  "weight-display": "56kg",
  "weight-first-kg": "12",
  "weight-first-display": "1st 12.46lbs",
  "weight-difference-from-start-kg": 44,
  "weight-difference-from-start-display": "6st 13lbs",
  "notes": "Some notes here ",
  "url-user-profile": "http://yeken.uk/wp-admin/admin.php?page=ws-ls-data-home&mode=user&user-id=1",
  "url-entry-edit": "http://yeken.uk/wp-admin/admin.php?page=ws-ls-data-home&mode=entry&user-id=1&entry-id=25",
  "custom-fields": {
    "cups-of-water-drunk-today": "3",
    "love-it": "Yes",
    "photo": "http://one.wordpress.test/wp-content/uploads/2021/02/60313a97781f3.png"
  }
}
```

**Target updated:**

```
{
  "_event": {
    "type": "target",
    "mode": "update"
  },
  "user-id": "1",
  "user-display-name": "admin",
  "weight-kg": "34",
  "weight-display": "5st 4.96lbs",
  "url-user-profile": "http://one.wordpress.test/wp-admin/admin.php?page=ws-ls-data-home&mode=user&user-id=1"
}
```

## Slack

[![Slack]({{ site.baseurl }}/assets/images/slack.png)]({{ site.baseurl }}/assets/images/slack.png)

Unlike Zapier and other webhooks,  [Slack](https://slack.com/)  is a messaging app for collaborating between teams. When Weight Tracker determines that a Slack web endpoint has been specified, it will build a message like the example to the left. It will summarise the user entry and provide links to WordPress admin panel.

_How to setup_

1.  Add the “Incoming Webhooks” app to your Slack account.
2.  Choose the Channel that notifications should be posted to.
3.  Click “Add”.
4.  Copy the Webhook URL (e.g. https://hooks.slack.com/services/XXXXXXXXX/YYYYYYYYYYY/DnBwK4A5xmjnjnfjenne7ob3F)
5.  Paste the above URL into one of the Endpoint URL fields within Weight Tracker > Settings > Integrations.

## Zapier

Create a new Zap with the Trigger “Webhooks by Zapier” and select “Catch Hook” as the trigger event. Click “Continue”.

[![Slack]({{ site.baseurl }}/assets/images/zapier-one.png)]({{ site.baseurl }}/assets/images/zapier-one.png)

Copy the “Custom Webhook URL” and paste into one of the Endpoint URL fields within Weight Tracker > Settings > Integrations.

[![Slack]({{ site.baseurl }}/assets/images/zapier-two.png)]({{ site.baseurl }}/assets/images/zapier-two.png)

Click “Continue”. From the Weight Tracker settings, enable Webhooks and cause a webhook to be fired (i.e. add a weight entry or new target for a user). Return to Zapier and Test the trigger. If a success, you should see something like the following. This means the Zap is now listening and ready to capture the relevant fields. You can then set up Zapier actions based upon the Webhook trigger to perform additional tasks.

[![Slack]({{ site.baseurl }}/assets/images/zapier-one.png)]({{ site.baseurl }}/assets/images/zapier-three.png)