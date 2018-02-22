<?php
namespace Plokko\LaravelFirebase\Facades;


use Illuminate\Support\Facades\Facade;

class FCM extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Plokko\LaravelFirebase\FcmMessageBuilder::class;
    }
}