<?php
namespace Plokko\LaravelFirebase;

use Plokko\Firebase\IO\Database;
use Plokko\Firebase\ServiceAccount;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const
        CONFIG_PATH = __DIR__ . '/../config/laravel-firebase.php';

    protected
        $defer = false;

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('laravel-firebase.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel-firebase'
        );

        // Provides the Firebase ServiceAccount
        $this->app->singleton(ServiceAccount::class,function($app){
            $sa = new ServiceAccount(config('laravel-firebase.service_account'));
            // Add cache handler if cache is enabled
            if(config('laravel-firebase.cache')){
                $sa->setCacheHandler(new ServiceAccountCacheItemPool());
            }
            return $sa;
        });

        // Provide Firebase Database
        $this->app->singleton(Database::class,function($app){
            return new Database($app->make(ServiceAccount::class));
        });
        $this->app->bind(FcmMessageBuilder::class,function($app){
            $fcm = new FcmMessageBuilder($app->make(ServiceAccount::class));

            $event = $app->config('laravel-firebase.FCMInvalidTokenTriggerEvent');
            if($event){
                $fcm->setInvalidTokenEvent($event);
            }
            return $fcm;
        });

        $this->app->singleton(JWT::class,function($app){
            return new JWT($app->make(ServiceAccount::class));
        });
    }


    public function provides()
    {
        return [
            ServiceAccount::class,
            Database::class,
        ];
    }

}
