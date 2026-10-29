<?php

namespace JeffersonGoncalves\LivewireLogs;

use Livewire\Features\SupportScriptsAndAssets\SupportScriptsAndAssets;
use Livewire\Mechanisms\HandleRequests\HandleRequests;

use function Livewire\trigger;

/**
 * Livewire 3 catches the TypeError of a component update and answers 419
 * without reporting it. This copy of handleUpdate() reports it first, as
 * Livewire 4 does.
 */
class ReportingHandleRequests extends HandleRequests
{
    public static function from(HandleRequests $original): self
    {
        $instance = new self;
        $instance->updateRoute = $original->updateRoute;

        return $instance;
    }

    // ponytail: copied from Livewire 3.8.11; Livewire 3 only gets fixes, so the copy stays in sync.
    public function handleUpdate()
    {
        $requestPayload = request('components');

        if (! is_array($requestPayload) || empty($requestPayload)) {
            abort(404);
        }

        foreach ($requestPayload as $component) {
            if (! is_array($component)
                || ! is_string($component['snapshot'] ?? null)
                || ! is_array($component['updates'] ?? null)
                || ! is_array($component['calls'] ?? null)
            ) {
                abort(404);
            }
        }

        $finish = trigger('request', $requestPayload);

        $requestPayload = $finish($requestPayload);

        $componentResponses = [];

        foreach ($requestPayload as $componentPayload) {
            $snapshot = json_decode($componentPayload['snapshot'], associative: true);
            $updates = $componentPayload['updates'];
            $calls = $componentPayload['calls'];

            try {
                [$snapshot, $effects] = app('livewire')->update($snapshot, $updates, $calls);
            } catch (\TypeError $e) {
                if (config('app.debug')) {
                    throw $e;
                }

                report($e);

                abort(419);
            }

            $componentResponses[] = [
                'snapshot' => json_encode($snapshot),
                'effects' => $effects,
            ];
        }

        $responsePayload = [
            'components' => $componentResponses,
            'assets' => SupportScriptsAndAssets::getAssets(),
        ];

        $finish = trigger('response', $responsePayload);

        return $finish($responsePayload);
    }
}
