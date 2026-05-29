<?php

namespace App\Livewire\Sponsor;

use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Sponsor List')]
class Index extends Component
{
    use AuthorizesRequests;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    public function render()
    {
        return view('livewire.sponsor.index')
            ->layout('layouts.app', ['title' => __('Sponsor List')]);
    }
}
