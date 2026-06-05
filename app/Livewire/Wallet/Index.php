<?php

namespace App\Livewire\Wallet;

use App\Models\EwalletLog;
use App\Models\Member;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('eWallet Logs')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?Member $selectedMember = null;

    public array $memberWalletLogs = [];

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    #[On('wallet:view-detail')]
    public function openDetail(int $memberId): void
    {
        $this->selectedMember = Member::findOrFail($memberId);
        $this->memberWalletLogs = EwalletLog::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->all();

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedMember = null;
        $this->memberWalletLogs = [];
    }

    public function render()
    {
        return view('livewire.wallet.index')
            ->layout('layouts.app', ['title' => __('eWallet Logs')]);
    }
}
