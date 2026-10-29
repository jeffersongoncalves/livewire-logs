<?php

namespace JeffersonGoncalves\LivewireLogs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\LivewireLogs\LivewireLogs
 */
class LivewireLogs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'livewire-logs';
    }
}
