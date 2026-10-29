<?php

namespace JeffersonGoncalves\LivewireLogs\Tests\Fixtures;

use Livewire\Component;

class Readings extends Component
{
    public int $value = 0;

    public string $reading = '';

    public function save(): void
    {
        $this->store('not a number');
    }

    public function updatedReading(): void
    {
        $this->store($this->reading);
    }

    protected function store(int $value): void
    {
        $this->value = $value;
    }

    public function render(): string
    {
        return '<div>{{ $value }}</div>';
    }
}
