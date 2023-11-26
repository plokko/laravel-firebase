<?php
namespace Plokko\LaravelFirebase\Facades;


use Illuminate\Support\Facades\Facade;
use Plokko\LaravelFirebase\FcmMessageBuilder;

class FCM extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FcmMessageBuilder::class;
    }
}
