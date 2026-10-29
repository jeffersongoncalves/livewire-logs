<?php

namespace JeffersonGoncalves\LivewireLogs;

use Illuminate\Support\Str;
use Livewire\Component;

/**
 * Remembers which Livewire component is being updated, so exceptions reported
 * mid-update can say where they happened.
 */
class ComponentTracker
{
    public const REDACTED = '[redacted]';

    /** @var array<string, mixed>|null */
    protected ?array $current = null;

    /**
     * @param  array<string, mixed>  $memo
     */
    public function hydrate(Component $component, array $memo): void
    {
        $this->current = [
            'component' => $component->getName(),
            'class' => $component::class,
            'id' => $component->getId(),
            'path' => $memo['path'] ?? null,
            'step' => 'hydrate',
            'updates' => [],
            'calls' => [],
        ];
    }

    public function update(Component $component, string $path, mixed $value): void
    {
        if (! $this->tracks($component)) {
            return;
        }

        $this->current['step'] = "update: {$path}";
        $this->current['updates'][$path] = $this->describe($path, $value);
    }

    public function call(Component $component, string $method): void
    {
        if (! $this->tracks($component)) {
            return;
        }

        $this->current['step'] = "call: {$method}";
        $this->current['calls'][] = $method;
    }

    public function destroy(Component $component): void
    {
        if ($this->tracks($component)) {
            $this->current = null;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function context(): array
    {
        if ($this->current !== null) {
            return ['livewire' => $this->current];
        }

        // Failed before any component hydrated (e.g. a typed property rejecting the snapshot).
        $components = request()->input('components');

        if (! is_array($components) || ! request()->hasHeader('X-Livewire')) {
            return [];
        }

        return ['livewire' => [
            'step' => 'snapshot',
            'components' => array_values(array_map(
                fn ($payload) => is_array($payload) && is_string($payload['snapshot'] ?? null)
                    ? (json_decode($payload['snapshot'], true)['memo']['name'] ?? null)
                    : null,
                $components,
            )),
        ]];
    }

    protected function tracks(Component $component): bool
    {
        return $this->current !== null && $this->current['id'] === $component->getId();
    }

    protected function describe(string $path, mixed $value): string
    {
        $type = get_debug_type($value);

        if (! config('livewire-logs.include_values')) {
            return $type;
        }

        if ($this->isSensitive($path)) {
            return $type.' '.self::REDACTED;
        }

        $value = $this->redact($value);
        $encoded = is_string($value) ? $value : json_encode($value, JSON_PARTIAL_OUTPUT_ON_ERROR);

        return $type.' '.Str::limit((string) $encoded, (int) config('livewire-logs.value_max_length', 200));
    }

    protected function redact(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->isSensitive((string) $key) ? self::REDACTED : $this->redact($item);
        }

        return $value;
    }

    /**
     * True when any segment of the path (e.g. "form.currentPassword") contains a sensitive key.
     */
    protected function isSensitive(string $path): bool
    {
        $normalized = preg_replace('/[^a-z0-9]/', '', strtolower($path));

        foreach ((array) config('livewire-logs.sensitive_keys', []) as $key) {
            $key = preg_replace('/[^a-z0-9]/', '', strtolower((string) $key));

            if ($key !== '' && str_contains((string) $normalized, (string) $key)) {
                return true;
            }
        }

        return false;
    }
}
