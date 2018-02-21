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

WIP

