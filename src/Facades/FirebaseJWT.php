<?php
namespace Plokko\LaravelFirebase\Facades;


use Illuminate\Support\Facades\Facade;
use Plokko\LaravelFirebase\JWT;

class FirebaseJWT extends Facade
{
    protected static function getFacadeAccessor()
    {
        return  JWT::class;
    }
}