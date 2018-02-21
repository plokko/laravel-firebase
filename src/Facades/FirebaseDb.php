<?php
namespace Plokko\LaravelFirebase\Facades;

use Illuminate\Support\Facades\Facade;
use Plokko\Firebase\IO\Database;

class FirebaseDb extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Database::class;
    }
}