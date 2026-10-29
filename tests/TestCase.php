<?php

namespace JeffersonGoncalves\LivewireLogs\Tests;

use JeffersonGoncalves\LivewireLogs\LivewireLogsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireLogsServiceProvider::class,
        ];
    }
}
