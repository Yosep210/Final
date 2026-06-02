<?php

namespace App\Livewire\AutoRo;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Auto RO Logs')]
class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function render()
    {
        return view('livewire.auto-ro.index')
            ->layout('layouts.app', ['title' => __('Auto RO Logs')]);
    }
}
