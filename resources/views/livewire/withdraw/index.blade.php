<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Withdraw List') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Manage and monitor member withdrawal requests and disbursement status.') }}
            </flux:text>
        </div>
    </div>

    <!-- Tab switcher -->
    <div class="flex border-b border-zinc-200 dark:border-zinc-700">
        <button wire:click="$set('activeTab', 'withdraw')" 
            class="py-2.5 px-4 font-semibold text-sm border-b-2 transition-all {{ $activeTab === 'withdraw' ? 'border-zinc-900 text-zinc-900 dark:border-white dark:text-white' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
            {{ __('List Withdraw') }}
        </button>
        <button wire:click="$set('activeTab', 'total_withdraw')" 
            class="py-2.5 px-4 font-semibold text-sm border-b-2 transition-all {{ $activeTab === 'total_withdraw' ? 'border-zinc-900 text-zinc-900 dark:border-white dark:text-white' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
            {{ __('List Total Transaksi') }}
        </button>
    </div>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
        @if ($activeTab === 'withdraw')
            <livewire:withdraw.withdraw-table />
        @else
            <livewire:withdraw.withdraw-total-table />
        @endif
    </div>

    <flux:modal name="withdraw-detail-modal" class="max-w-xl" wire:model="showDetailModal"
        @close="$wire.closeDetail()">
        <form wire:submit.prevent="confirmWithdrawal" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Confirm Withdrawal') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Are you sure you want to confirm this withdrawal request?') }}
                </flux:text>
            </div>

            @if ($selectedWithdraw)
                <div class="space-y-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="text-zinc-500">{{ __('Username') }}:</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ strtoupper($selectedWithdraw->member?->username ?? '') }}</div>
                        
                        <div class="text-zinc-500">{{ __('Name') }}:</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $selectedWithdraw->member?->name }}</div>
                        
                        <div class="text-zinc-500">{{ __('Bank') }}:</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ strtoupper($selectedWithdraw->bank_name ?? '') }}</div>
                        
                        <div class="text-zinc-500">{{ __('Account Number') }}:</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $selectedWithdraw->account_number }}</div>
                        
                        <div class="text-zinc-500">{{ __('Account Holder') }}:</div>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ strtoupper($selectedWithdraw->account_holder ?? '') }}</div>
                        
                        <div class="col-span-2 border-t border-zinc-200 my-2 dark:border-zinc-700"></div>

                        <div class="text-zinc-500">{{ __('Nominal Receipt') }}:</div>
                        <div class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format((float) ($selectedWithdraw->nominal_receipt ?? 0), 0) }}</div>
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:input type="password" wire:model.defer="confirmPassword" :label="__('Transaction Password')" placeholder="{{ __('Enter your password to confirm') }}" required />
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="closeDetail">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Confirm') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
