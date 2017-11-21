# Rachio PHP SDK

A simple client to interact with the Rachio irrigation control system. See
[http://rachio.readme.io/v1.0/docs/](http://rachio.readme.io/v1.0/docs/).

This package is compliant with [PSR-2] and [PSR-4].

[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## System Requirements


This SDK requires **PHP >= 5.5.0**.

## README Contents


* [Installation](#install)
* [Creating a Rachio Instance](#start)
* [Getting User Data](#user-data)
* [Getting Device and Schedule Data](#device-schedule-data)


<a name="install"/>

## Installation

The Rachio SDK may be installed through Composer.

```bash
composer require etelford/rachio
```

Make sure you're using Composer's [autoload](https://getcomposer.org/doc/00-intro.md#autoloading):

```php
require_once('vendor/autoload.php');
```

<a name="start">

## Creating a Rachio Instance

First, import the class.

```php
use ETelford\Rachio;
```

Next, create a new instance of `Rachio` and pass in your API key.

```php
$rachio = new Rachio('your-api-key');
```

With a `Rachio` object created, you can do a number of things.


<a name="user-data">

## Getting User data

### Get the id of the authorized user

```php
$id = $rachio->personId();
```

This is merely a more friendly way of retrieving the authorized user from a
`Rachio` instance. It is identical to doing:

```php
$rachio = new Rachio('your-api-key');
$id = $rachio->personId;
```

### Get the profile data for the authorized user

```php
$person = $rachio->person();
```

<a name="device-schedule-data">

## Getting Device and Schedule Data

### Get all the devices associated with an account

```php
$devices = $rachio->devices();
```

### Get info for a specific device associated with an account

This method can accept a `device id`.

```php
$devices = $rachio->devices();
$device = $rachio->device($devices[0]->id);
```

Since many people will have just a single Rachio system, the SDK will automatically use the first device in your account if you don't pass an id.

```php
$device = $rachio->device();
```

### Get the currently running schedule

```php
$schedule = $rachio->currentSchedule($deviceId);
```

Or, use the default device.

```php
$schedule = $rachio->currentSchedule();
```

_Note: If the Rachio system isn't running, this will return `null`._

### Get the upcoming schedules for the next two weeks

```php
$schedule = $rachio->upcomingSchedule($deviceId);
```

Again, for the default device:

```php
$schedule = $rachio->upcomingSchedule();
```
