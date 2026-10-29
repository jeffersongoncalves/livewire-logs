<?php

namespace JeffersonGoncalves\LivewireLogs;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Livewire\Component;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function Livewire\on;

class LivewireLogsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('livewire-logs')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(ComponentTracker::class);
    }

    public function packageBooted(): void
    {
        if (! config('livewire-logs.enabled')) {
            return;
        }

        on('hydrate', fn (Component $component, $memo) => app(ComponentTracker::class)->hydrate($component, (array) $memo));
        on('update', fn (Component $component, $path, $value) => app(ComponentTracker::class)->update($component, (string) $path, $value));
        on('call', fn (Component $component, $method) => app(ComponentTracker::class)->call($component, (string) $method));
        on('destroy', fn (Component $component) => app(ComponentTracker::class)->destroy($component));

        $this->callAfterResolving(ExceptionHandler::class, function ($handler) {
            if (method_exists($handler, 'buildContextUsing')) {
                $handler->buildContextUsing(fn () => app(ComponentTracker::class)->context());
            }
        });
    }
}
