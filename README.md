# UserForms Pushover
Enables Pushover.net Support in UserForms module and also Supports custom Pushover templates.

[![Packagist](https://img.shields.io/packagist/v/stewartcossey/userforms-pushover.svg)]() [![license](https://img.shields.io/github/license/Cossey/userforms-pushover.svg)]()


## Requirements
The SilverStripe UserForms Module.

## Installation
```sh
$ composer require stewartcossey/userforms-pushover
```
You will need to run `dev/build?flush=all` after installing this module.

## Pushover Templates
Like the UserForm emails, Pushover messages utilize templates. Templates are stored in `userforms-pushover/templates/pushover` and are applied per recipient.

The following values are available in the Pushover Template:

Name        | Description
----------- | ------------------------------------------------
$Fields     | User Form Fields
$PageTitle  | Title of Page where Form was Submitted
$UserKey    | The Pushover User/Group Key used to send message
$Devices    | The Names of Devices that were sent messages

## Screenshots
![Screenshot](https://github.com/Cossey/userforms-pushover/blob/master/screenshot-recp.png)

The Application Key and Pushover recipients get added to the Recipients tab.

![Screenshot](https://github.com/Cossey/userforms-pushover/blob/master/screenshot-pousers.png)

Editing Pushover recipient.