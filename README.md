# Laravel Firebase

[![Build Status](https://travis-ci.org/plokko/laravel-firebase.svg?branch=master)](https://travis-ci.org/plokko/laravel-firebase)
[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/plokko/laravel-firebase/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/plokko/laravel-firebase/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)
[![Coverage Status](https://coveralls.io/repos/github/plokko/laravel-firebase/badge.svg?branch=master)](https://coveralls.io/github/plokko/laravel-firebase?branch=master)

[![Packagist](https://img.shields.io/packagist/v/plokko/laravel-firebase.svg)](https://packagist.org/packages/plokko/laravel-firebase)
[![Packagist](https://poser.pugx.org/plokko/laravel-firebase/d/total.svg)](https://packagist.org/packages/plokko/laravel-firebase)
[![Packagist](https://img.shields.io/packagist/l/plokko/laravel-firebase.svg)](https://packagist.org/packages/plokko/laravel-firebase)

Laravel Firebase integration

## Installation

Install via composer
```bash
composer require plokko/laravel-firebase
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
Plokko\LaravelFirebase\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
Plokko\LaravelFirebase\Facades\LaravelFirebase::class,
```

### Publish Configuration File

```bash
php artisan vendor:publish --provider="Plokko\LaravelFirebase\ServiceProvider" --tag="config"
```

## Usage

### FCM
This package allows you to send FCM messages via FCM http v1 api
#### Message builder
You can easly build FCM Messages via the `FCM` facade:
```php

FCM::notificationTitle('My notification title')
  ->notificationBody('my notification body...');
  ->data(['notification' => 'data'])
  ->highPriority()//note: not all devices may use all the fields like priority or ttl
  ->ttl('20.5s')
  ->toDevice('my-device-fcm-token') // or toTopic('topic-name') or toCondition('condition-name') or toTarget(Target)
  ->send();//Submits the message
```
#### FCM Notification channel
You can also send the FCM messages via the `FcmNotificationChannel` channel:
```php
class TestFcmNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmNotificationChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FCM::notificationTitle('Test notification')
                    ->notificationBody('notification body...')
                    ->toDevice($notifiable->deviceToken);
    }
}
```

### Real time database
To get an instance of the database use the `FirebaseDb` facade:
``
$test = FirebaseDb::getReference('test'); //get the reference for item /test
$test->get('01');//Get /test/01 as an array
$test01 = $test->getReference('01');//Get a reference for /test/01
$test01->set('label','value');//Set /test/01/label = value
```
