<?php

namespace App\Livewire\Withdraw;

use App\Models\Member;
use App\Models\Withdrawal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Withdraw List')]
class Index extends Component
{
    use AuthorizesRequests;

    public string $activeTab = 'withdraw';

    public bool $showDetailModal = false;

    public ?Withdrawal $selectedWithdraw = null;

    public string $confirmPassword = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);
    }

    #[On('withdraw:confirm')]
    public function openDetail(int $rowId): void
    {
        $this->selectedWithdraw = Withdrawal::with('member')->findOrFail($rowId);
        $this->confirmPassword = '';
        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->selectedWithdraw = null;
        $this->confirmPassword = '';
    }

    public function confirmWithdrawal(): void
    {
        $this->validate([
            'confirmPassword' => 'required',
        ]);

        if (! Hash::check($this->confirmPassword, auth()->user()->password)) {
            $this->addError('confirmPassword', __('Invalid password.'));

            return;
        }

        if (! $this->selectedWithdraw || $this->selectedWithdraw->status !== 0) {
            $this->addError('confirmPassword', __('Withdrawal cannot be confirmed.'));

            return;
        }

        $this->selectedWithdraw->update([
            'status' => 1,
            'confirmed_by' => auth()->user()->username,
            'confirmed_at' => now(),
        ]);

        $this->showDetailModal = false;
        $this->selectedWithdraw = null;
        $this->confirmPassword = '';

        $this->dispatch('pg:eventRefresh-withdrawTable');
    }

    public function render()
    {
        return view('livewire.withdraw.index')
            ->layout('layouts.app', ['title' => __('Withdraw List')]);
    }
}
