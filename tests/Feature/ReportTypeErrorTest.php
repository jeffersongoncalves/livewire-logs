<?php

use Illuminate\Support\Facades\Log;
use JeffersonGoncalves\LivewireLogs\Tests\Fixtures\Readings;
use Livewire\Livewire;

function livewireUpdate(array $calls = [], array $updates = [])
{
    $snapshot = Livewire::test(Readings::class)->snapshot;

    return test()->withHeaders(['X-Livewire' => 'true'])->postJson(app('livewire')->getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($snapshot),
            'calls' => $calls,
            'updates' => $updates,
        ]],
    ]);
}

it('reports the TypeError hidden behind the 419 with the component context', function () {
    Log::spy();

    livewireUpdate(calls: [['method' => 'save', 'params' => [], 'path' => '']])->assertStatus(419);

    Log::shouldHaveReceived('error')->withArgs(function ($message, $context) {
        return str_contains($message, 'must be of type int')
            && $context['exception'] instanceof TypeError
            && $context['livewire']['class'] === Readings::class
            && $context['livewire']['step'] === 'call: save'
            && $context['livewire']['calls'] === ['save'];
    })->once();
});

it('logs the type of the updated property', function () {
    Log::spy();

    livewireUpdate(updates: ['reading' => 'abc'])->assertStatus(419);

    Log::shouldHaveReceived('error')->withArgs(function ($message, $context) {
        return $context['livewire']['step'] === 'update: reading'
            && $context['livewire']['updates'] === ['reading' => 'string'];
    })->once();
});

it('reports a typed property set to a value of the wrong type', function () {
    Log::spy();

    livewireUpdate(updates: ['value' => 'abc'])->assertStatus(419);

    Log::shouldHaveReceived('error')->withArgs(
        fn ($message, $context) => $context['livewire']['step'] === 'update: value'
    )->once();
});

it('logs the updated value only when asked to', function () {
    config(['livewire-logs.include_values' => true]);
    Log::spy();

    livewireUpdate(updates: ['reading' => 'abc'])->assertStatus(419);

    Log::shouldHaveReceived('error')->withArgs(
        fn ($message, $context) => $context['livewire']['updates'] === ['reading' => 'string abc']
    )->once();
});

it('leaves successful updates alone', function () {
    Log::spy();

    livewireUpdate(updates: ['value' => 5])->assertOk();

    Log::shouldNotHaveReceived('error');
});

it('still throws the TypeError when debugging', function () {
    config(['app.debug' => true]);

    livewireUpdate(calls: [['method' => 'save', 'params' => [], 'path' => '']])->assertStatus(500);
});
