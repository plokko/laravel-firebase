# Laravel Firebase

[![Build Status](https://travis-ci.org/plokko/laravel-firebase.svg?branch=master)](https://travis-ci.org/plokko/laravel-firebase)
[![Packagist](https://img.shields.io/packagist/v/plokko/laravel-firebase.svg)](https://packagist.org/packages/plokko/laravel-firebase)
[![Packagist](https://poser.pugx.org/plokko/laravel-firebase/d/total.svg)](https://packagist.org/packages/plokko/laravel-firebase)
[![Packagist](https://img.shields.io/packagist/l/plokko/laravel-firebase.svg)](https://packagist.org/packages/plokko/laravel-firebase)

Laravel Firebase integration

This package includes:
 - Firebase OAuthV2.0 authentication, with token caching
 - Centralized ServiceAccount credential management
 - Firebase FCM Http V1 API and Firebase Realtime database REST api via OAuth authentication
 - Firebase JWT token generator (via php-jwt)
 - Automatic sync for Eloquent models to Firebase Realtime db
 - Automatic sync triggers on related model changes

## Installation

Install via composer
```bash
composer require plokko/laravel-firebase
```
The package will be auto registered in laravel >=5.5;
**If you use laravel <5.5 follow the next two steps**

1. Add service provider to `config/app.php` in `providers` section
```php
Plokko\LaravelFirebase\ServiceProvider::class,
```

2. Register package facade in `config/app.php` in `aliases` section
```php
Plokko\LaravelFirebase\Facades\LaravelFirebase::class,
```
Your file in `config/laravel-firebase.php` should now look like this:
```php
<?php

return [
    'read_only' => env('FIREBASEDB_READONLY',false),//DEBUG

    /**
     * Firebase service account information, can be either:
     * - string : absolute path to serviceaccount json file
     * - string : content of serviceaccount (json string)
     * - array : php array conversion of the serviceaccount
     * @var array|string
     */
    'service_account' => base_path('.firebase-credentials.json'),

    /**
     * If set to true will enable Google OAuth2.0 token cache storage
     */
    'cache' => true,

    /**
     * Cache driver for OAuth token cache,
     * if null default cache driver will be used
     * @var string|null
     */
    'cache_driver' => null,

    /**
     * Specify if and what event to trigger if an invalid token is returned
     * @var string|null
     */
    'FCMInvalidTokenTriggerEvent' => null,
];

```

### Configuration
Publish the configuration file via
```bash
php artisan vendor:publish --provider="Plokko\LaravelFirebase\ServiceProvider" --tag="config"
```



## Usage
### JWT token
You can easly create a Firebase JWT token (for auth) with `FirebaseJWT::encode`:

```php
FirebaseJWT::encode($uid,['optional'=>'custom-claims-array']);
```

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

##### Settings:
You can enable read-only access to database setting 
```
FIREBASEDB_READONLY=true
```
on your `.env` file, this is usefull for testing purpuses, the writes will not return any error but will not be executed on Firebase.
#### Query the Realtime database
To get an instance of the database use the `FirebaseDb` facade:

```php
$test = FirebaseDb::getReference('test'); //get the reference for item /test
$test->get('01');//Get /test/01 as an array
$test01 = $test->getReference('01');//Get a reference for /test/01
$test01->set('label','value');//Set /test/01/label = value
```
#### Sync models to Firebase
[see Firebase database sync](docs/DB_sync.md)
