<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Report the TypeError Livewire turns into a 419 and attach the component
    | context (name, id, updated properties, called methods) to every exception
    | reported while a Livewire component is being updated.
    |
    */

    'enabled' => env('LIVEWIRE_LOGS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Include values
    |--------------------------------------------------------------------------
    |
    | Updated property values may hold personal data, so by default only their
    | type is logged. Turn this on to log the (truncated) values as well.
    |
    */

    'include_values' => env('LIVEWIRE_LOGS_INCLUDE_VALUES', false),

    'value_max_length' => 200,

    /*
    |--------------------------------------------------------------------------
    | Sensitive keys
    |--------------------------------------------------------------------------
    |
    | When values are included, a property whose path contains one of these
    | keys (case, "_", "-" and "." ignored: "form.currentPassword" matches
    | "password") is logged as "[redacted]". Array values are redacted key by
    | key, at any depth.
    |
    */

    'sensitive_keys' => [
        'password',
        'passwd',
        'secret',
        'token',
        'api_key',
        'private_key',
        'credential',
        'authorization',
        'cookie',
        'session',
        'card_number',
        'cvv',
        'cvc',
    ],

];
