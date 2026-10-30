<div class="filament-hidden">

![Livewire Logs](https://raw.githubusercontent.com/jeffersongoncalves/livewire-logs/1.x/art/jeffersongoncalves-livewire-logs.png)

</div>

# Livewire Logs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/livewire-logs.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/livewire-logs)
[![Tests](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/livewire-logs/tests.yml?branch=1.x&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/livewire-logs/actions?query=workflow%3ATests+branch%3A1.x)
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
composer require jeffersongoncalves/livewire-logs:^1.0
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

    // With values on, these keys are logged as "[redacted]"...
    'sensitive_keys' => ['password', 'passwd', 'secret', 'token', 'api_key', 'private_key', 'credential', 'authorization', 'cookie', 'session', 'card_number', 'cvv', 'cvc'],
];
```

With `include_values` on, a property whose path contains a sensitive key is logged as `[redacted]`. Matching ignores case, `_`, `-` and `.`, so `form.currentPassword` matches `password` and `apiToken` matches `token`. Array values are redacted key by key, at any depth:

```json
"updates": {
    "currentPassword": "string [redacted]",
    "form": "array {\"name\":\"Ana\",\"api_token\":\"[redacted]\"}"
}
```

## How it works

- Livewire 3 (`1.x`): replaces Livewire's `HandleRequests` mechanism with a copy whose `handleUpdate()` calls `report()` before `abort(419)`, like Livewire 4 does.
- Both versions: listens to Livewire's `hydrate`, `update`, `call` and `destroy` hooks to remember the component being updated, and adds it to the exception context through `buildContextUsing()`.

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
