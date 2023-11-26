<?php
namespace Plokko\LaravelFirebase\Facades;

use Illuminate\Support\Facades\Facade;
use Plokko\LaravelFirebase\RealtimeDb;

class FirebaseDb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RealtimeDb::class;
    }
}
