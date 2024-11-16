# MailerLite for Statamic Documentation

## Setup

### Install the add-on

You should install via the Statamic Marketplace at [https://statamic.com/addons/siterig/mailerlite](https://statamic.com/addons/siterig/mailerlite) or you can use composer in your project root:

```
  composer require siterig/mailerlite
```


### Add your MailerLite API key to the `.env` file

In your `.env` file add a new line with your MailerLite API key. This add-on only works with the new MailerLite. MailerLite Classic is no longer supported.

```
  MAILERLITE_API_KEY=your-key-goes-here
```

### Publish the configuration file

Publish the config file to `config/mailerlite.php` by running the following artisan command:

```
  php artisan vendor:publish --tag="mailerlite-config"
```

Now you can configure your form settings within the Control Panel from the MailerLite menu option.


### Create a form in Statamic

Your form only needs an email field as an absolute minimum, but our recommended form setup is:

- Name
- Email
- Marketing Opt-in


### MailerLite settings

For each Statamic Form that you want to connect with MailerLite you can add a form entry in the MailerLite settings.

#### MailerLite API Key

This is a read-only field that displays your current API key that is set in the `.env` file.


#### Form

Select the Statamic Form you would like to capture submissions from.


#### Subscriber Group

If you have setup Subscriber Groups you can select which one submissions from this form should be added to.


#### Name Field

This is the field you want to use to capture someones name, by default the add-on will split this name by the first space character into first name and last name to be sent to your MailerLite list. You can disable Automatic Name Splitting using the setting listed below or by mapping an additional field to MailerLite's `last_name` field.


#### Email Field

This is the only field that is required by MailerLite on a submission. We don't do anything special with this field so you'll need to make sure you have validation setup in Statamic and/or your front-end code if required.


#### Automatically Split Name

When enabled this splits the Name Field into first name and last name using the first space character it finds. This setting is ignored if you map a seperate field to `last_name`.


#### Opt-in Field

This should ideally be an un-ticked checkbox that conforms to data protection regulations in your region. If the user does not tick this checkbox the submission to Statamic will still go through but the details will not be sent to MailerLite.


#### Mapped Fields

This is where you can map any additional MailerLite fields such as `last_name` or `company` as well as any custom fields you've created.


