<?php

namespace App\Livewire\Wallet;

use App\Models\EwalletLog;
use App\Services\WithdrawalService;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('My Wallet')]
class MemberWallet extends Component
{
    use WithPagination;

    // Withdrawal Form Fields
    public string $nominal = '';

    public string $password = '';

    // Config cache
    public float $minWd = 50000.0;

    public float $fee = 5000.0;

    public function mount(): void
    {
        $this->minWd = (float) config('mlm.payout.minimum_commission', 50000);
        $this->fee = (float) config('mlm.payout.fee', 5000);
    }

    public function getBalanceProperty(): float
    {
        return auth()->user()->ewalletBalance();
    }

    public function getReceiptAmountProperty(): float
    {
        $rawNominal = (float) str_replace(['.', ','], '', $this->nominal ?: '0');
        if ($rawNominal <= 0) {
            return 0.0;
        }

        return max(0.0, $rawNominal - $this->fee);
    }

    public function requestWithdrawal(WithdrawalService $withdrawalService): void
    {
        $this->validate([
            'nominal' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $rawNominal = (float) str_replace(['.', ','], '', $this->nominal);
        $member = auth()->user();

        try {
            $withdrawalService->requestManualWithdrawal($member, $rawNominal, $this->password);

            $this->nominal = '';
            $this->password = '';

            Flux::toast(
                variant: 'success',
                text: __('Penarikan dana sebesar Rp :nominal berhasil diajukan dan sedang diproses.', [
                    'nominal' => number_format($rawNominal, 0, ',', '.'),
                ])
            );

            $this->resetPage();
        } catch (\Exception $e) {
            $this->addError('nominal', $e->getMessage());
            Flux::toast(variant: 'danger', text: $e->getMessage());
        }
    }

    public function render()
    {
        $member = auth()->user();

        // Fetch paginated eWallet logs for the logged-in member
        $logs = EwalletLog::where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.wallet.member-wallet', [
            'logs' => $logs,
            'bank' => $member->bank,
            'balance' => $this->balance,
            'receiptAmount' => $this->receiptAmount,
        ])->layout('layouts.app', ['title' => __('My Wallet')]);
    }
}
