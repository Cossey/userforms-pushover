# UserForms Pushover
Enables Pushover.net Support in UserForms module and also Supports custom Pushover templates.

## Requirements
The SilverStripe UserForms Module.

## Installation
```sh
$ composer require stewartcossey/userforms-pushover
```
You will need to run `dev/build?flush=all` after installing this module.

## Pushover Templates
Like the UserForm emails, Pushover messages utilize templates. Templates are stored in `userforms-pushover/templates/pushover`

The following values are available in the Pushover Template:
Name        | Description
----------- | ------------------------------------------------
$Fields     | User Form Fields
$PageTitle  | Title of Page where Form was Submitted
$UserKey    | The Pushover User/Group Key used to send message
$Devices    | The Names of Devices that were sent messages

## Screenshots
![Screenshot](https://github.com/Cossey/userforms-pushover/blob/master/screenshot-recp.png)

Pushover users are added to the Recipients tab in the User Defined Form as well as the Pushover Application Key for this page/site.

![Screenshot](https://github.com/Cossey/userforms-pushover/blob/master/screenshot-pousers.png)

Editing the Pushover endpoints as well as the Pushover Messaging Template.