<?php

namespace App\Livewire\Group;

use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Group List')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?MemberNetwork $selectedGroup = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    #[On('group:view')]
    public function openDetail(int $rowId): void
    {
        $this->selectedGroup = MemberNetwork::query()
            ->with(['member', 'parent'])
            ->findOrFail($rowId);

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedGroup = null;
    }

    public function render()
    {
        return view('livewire.group.index')
            ->layout('layouts.app', ['title' => __('Group List')]);
    }
}
