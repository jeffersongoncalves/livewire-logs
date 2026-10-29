<div class="filament-hidden">

![Livewire Logs](https://raw.githubusercontent.com/jeffersongoncalves/livewire-logs/2.x/art/jeffersongoncalves-livewire-logs.png)

</div>

# Livewire Logs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/livewire-logs.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/livewire-logs)
[![Tests](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/livewire-logs/tests.yml?branch=2.x&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/livewire-logs/actions?query=workflow%3ATests+branch%3A2.x)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/livewire-logs.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/livewire-logs)

When a Livewire update throws a `TypeError` in production, Livewire answers `419` ("This page has expired") and nothing reaches your logs: Livewire 3 never reports the error, and Livewire 4 reports it without saying which component failed.

This package makes that error show up in your logs (and in Sentry, Bugsnag, Flare...) with the component that caused it:

```
local.ERROR: App\Livewire\EditReadings::store(): Argument #1 ($value) must be of type int, string given
{
    "livewire": {
        "component": "edit-readings",
        "class": "App\\Livewire\\EditReadings",
        "id": "kXz3...",
        "path": "readings/42/edit",
        "step": "call: save",
        "updates": {"form.value": "string"},
        "calls": ["save"]
    },
    "exception": "[object] (TypeError(code: 0): ..."
}
```

The `livewire` context is added to **every** exception reported during a Livewire update, not only to `TypeError`s.

## Compatibility

| Package | Livewire |
|---------|----------|
| 1.x     | 3.x      |
| 2.x     | 4.x      |

## Installation

```bash
composer require jeffersongoncalves/livewire-logs
```

That's it. The user still gets the `419` response; only the logging changes. With `APP_DEBUG=true` the `TypeError` is thrown as before.

Optionally publish the config:

```bash
php artisan vendor:publish --tag="livewire-logs-config"
```

```php
return [
    'enabled' => env('LIVEWIRE_LOGS_ENABLED', true),

    // Updated values may hold personal data: by default only their type is logged.
    'include_values' => env('LIVEWIRE_LOGS_INCLUDE_VALUES', false),

    'value_max_length' => 200,
];
```

## How it works

- Livewire 4 already calls `report()` before `abort(419)`; this branch only adds the component context. Livewire 4 still answers `419` without reporting when a request sets a typed public property to a value of the wrong type (it treats that as a bot probing the component); that stays as is.
- Livewire 3 (`1.x`) also replaces Livewire's `HandleRequests` mechanism with a copy whose `handleUpdate()` reports the error.
- Both versions listen to Livewire's `hydrate`, `update`, `call` and `destroy` hooks to remember the component being updated, and adds it to the exception context through `buildContextUsing()`.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
