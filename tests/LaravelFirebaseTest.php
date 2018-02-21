<?php

namespace Plokko\LaravelFirebase\Tests;

use Plokko\LaravelFirebase\Facades\LaravelFirebase;
use Plokko\LaravelFirebase\ServiceProvider;
use Orchestra\Testbench\TestCase;

class LaravelFirebaseTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'laravel-firebase' => LaravelFirebase::class,
        ];
    }

    public function testExample()
    {
        assertEquals(1, 1);
    }
}
