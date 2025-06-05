# MailerLite for Statamic [![Latest Version](https://img.shields.io/github/release/siterig/statamic-mailerlite.svg?style=flat-square)](https://github.com/siterig/statamic-mailerlite/releases)

MailerLite for Statamic lets you subscribe contact form submissions to your MailerLite subscriber lists.

You can optionally add subscribers to groups, automatically split single name fields into name and last name, use an opt-in field, collect GDPR compliant marketing preferences and of course map any custom fields that you like. You can also map multiple form fields into a single field on MailerLite.

This is not an official add-on by MailerLite and as such support requests should be submitted [here](https://rockandscissor.atlassian.net/servicedesk/customer/portal/2) on our support centre.

This addon uses [Forma](https://statamic.com/addons/silentz/forma) by Erin Dalzell and will be automatically installed for you.


## Documentation

Read it on the [Statamic Marketplace](https://statamic.com/addons/siterig/mailerlite/docs) or contribute to it [here on GitHub](DOCUMENTATION.md).


## Requirements

* PHP 8.2 or higher
* Laravel 10, 11 or 12
* Statamic v4.0 or higher
* MailerLite New API Key (no longer compatible with MailerLite Classic)


## Installation

You should install via the Statamic Marketplace at [https://statamic.com/addons/siterig/mailerlite](https://statamic.com/addons/siterig/mailerlite) or you can use composer in your project root:

```
  composer require siterig/mailerlite
```

Set your MailerLite API key in the `env` file within your project:

```
  MAILERLITE_API_KEY=your-api-key-goes-here
```

Then all that's left to do is publish the config file to `config/mailerlite.php`:

```
  php artisan vendor:publish --tag="mailerlite-config"
```

Now you can configure your form settings within the Control Panel from the MailerLite menu option.


## Developers

Matt Stone, Craig Bowler, Jamie McGrory


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Contributing

See our [contributing guide](CONTRIBUTING.md) for more information.


## License

This is commercial software. You may use the package for your sites. Each site requires it's own license.
