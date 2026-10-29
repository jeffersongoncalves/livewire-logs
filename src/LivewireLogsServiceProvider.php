<?php

namespace JeffersonGoncalves\LivewireLogs;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireLogsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-logs')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
