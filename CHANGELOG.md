# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-03-15

### Changed
- Updated minimum requirements to PHP 8.2, Laravel 9 and Statamic 4.0

## [1.1.5] - 2023-05-02

### Fixed
- Fixed issue with Marketing Opt-in check where it always returned false, thanks to contributor @mikemartin for fixing this


## [1.1.4] - 2023-03-17

### Fixed
- Fixed issue where debugging code left in place prevented submissions unless a preference field was present


## [1.1.3] - 2023-02-22

### Fixed
- Fixed issue with config where only the first form is saved
  

## [1.1.2] - 2022-09-09

### Added
- More detailed installation instructions including publishing the config file as a listed setup
- Permissions to edit config settings to be implemented in a future update

### Fixed
- Issue with config not getting registered

### Removed
- Default values on configuration form fields as they cause confusion if they aren't the field names used on a form


## [1.1.1] - 2022-09-07

### Fixed
- Missing `/resources/dist` directory issue


## [1.1.0] - 2022-09-06

### Added
- Requirement for Statamic v3.2 or higher to take advantage of the updated forms API
- Support for PHP 8.1
- Custom Vue component using v-select for picking form fields in the configuration

### Fixed
- Form field list now updates when you select a different form (previously it only worked if your form was called `newsletter` due to an oversight in development)
  

## [1.0.2] - 2022-06-29

### Changed
- Added Jamie McGrory as a contributor for his work on the Laravel array mapping
- Removed download-dist path from composer.json


## [1.0.1] - 2022-06-29

### Changed
- Updated documentation and other files relating to development

### Fixed
- Patching composer vulnerability issues


## [1.0.0] - 2020-06-28

### Changed
- Updated Forma to use v1.2

### Fixed
- Issue on checking for deleted subscriber groups resolved


## [0.1.2] - 2020-06-27

### Removed
- Dedicated Marketing Permissions field was removed
  
### Fixed
- Check added for if subscriber group exists to avoid error when one is deleted on MailerLite that is saved locally


## [0.1.1] - 2020-05-03

### Security
- Minor security update to fix composer advisories, added requirement for composer 2.2 or higher


## [0.1.0] - 2020-05-02

### Added
- Entirely re-factored codebase from our earlier MailerLite for Statamic v2 addon
