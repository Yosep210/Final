<?php

namespace App\Livewire\Generation;

use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Gen List')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?MemberNetwork $selectedNetwork = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    #[On('generation:view')]
    public function openDetail(int $rowId): void
    {
        $this->selectedNetwork = MemberNetwork::query()
            ->with(['member', 'sponsor', 'parent'])
            ->findOrFail($rowId);

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedNetwork = null;
    }

    public function render()
    {
        return view('livewire.generation.index')
            ->layout('layouts.app', ['title' => __('Gen List')]);
    }
}
