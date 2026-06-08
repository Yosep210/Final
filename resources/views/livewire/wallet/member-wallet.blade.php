<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <!-- Header -->
    <div>
        <flux:heading size="xl">{{ __('My Wallet & Withdrawal') }}</flux:heading>
        <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
            {{ __('Manage your eWallet balance, request manual payouts, and view transaction history.') }}
        </flux:text>
    </div>

    <!-- Balance and Withdrawal Request Cards -->
    <div class="grid gap-6 md:grid-cols-2">
        <!-- Balance Card -->
        <div class="flex flex-col justify-between rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <div>
                <flux:heading size="lg" class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold">{{ __('Saldo eWallet Anda') }}</flux:heading>
                <div class="mt-4 flex items-baseline text-zinc-900 dark:text-white">
                    <span class="text-3xl font-extrabold tracking-tight">
                        Rp {{ number_format($balance, 0, ',', '.') }}
                    </span>
                </div>
                <flux:text class="mt-2 text-xs text-zinc-500">
                    {{ __('Semua komisi Anda dikreditkan langsung ke eWallet ini.') }}
                </flux:text>
            </div>

            <!-- Bank account details -->
            <div class="mt-6 border-t border-neutral-100 dark:border-neutral-800 pt-4">
                <flux:heading size="sm" class="text-zinc-700 dark:text-zinc-300 font-medium mb-2">{{ __('Rekening Tujuan Payout') }}</flux:heading>
                @if ($bank && !empty($bank->bank_name) && !empty($bank->account_number) && !empty($bank->account_holder))
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Bank Name') }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ strtoupper($bank->bank_name) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Account Number') }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-white font-mono">{{ $bank->account_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Account Holder') }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ strtoupper($bank->account_holder) }}</span>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 p-3 text-xs text-rose-700 dark:text-rose-400">
                        {{ __('Anda belum melengkapi data bank. Silakan lengkapi data bank di menu pengaturan profil terlebih dahulu agar dapat melakukan penarikan.') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Withdrawal Form Card -->
        <div class="rounded-xl border border-neutral-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-zinc-900">
            <flux:heading size="lg" class="mb-4">{{ __('Ajukan Penarikan Manual') }}</flux:heading>
            
            <form wire:submit.prevent="requestWithdrawal" class="space-y-4">
                <!-- Nominal Input -->
                <div>
                    <flux:input 
                        wire:model.live.debounce.300ms="nominal" 
                        :label="__('Nominal Penarikan (Rp)')" 
                        placeholder="e.g. 150.000" 
                    />
                    <flux:text class="mt-1 text-[11px] text-zinc-500 block">
                        {{ __('Minimal penarikan: Rp :min. Biaya transfer: Rp :fee.', ['min' => number_format($minWd, 0, ',', '.'), 'fee' => number_format($fee, 0, ',', '.')]) }}
                    </flux:text>
                </div>

                <!-- Live Payout Summary -->
                @if ((float) str_replace(['.', ','], '', $nominal) > 0)
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-xs space-y-1">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Nominal Kotor') }}:</span>
                            <span class="text-zinc-900 dark:text-white font-medium">Rp {{ number_format((float) str_replace(['.', ','], '', $nominal), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Biaya Transfer (Fee)') }}:</span>
                            <span class="text-zinc-900 dark:text-white font-medium">- Rp {{ number_format($fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-neutral-200 dark:border-neutral-700 pt-1 mt-1 font-semibold">
                            <span class="text-zinc-900 dark:text-white">{{ __('Diterima Bersih') }}:</span>
                            <span class="text-emerald-600 dark:text-emerald-400">Rp {{ number_format($receiptAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Password Input -->
                <div>
                    <flux:input 
                        type="password" 
                        wire:model="password" 
                        :label="__('Password Konfirmasi')" 
                        placeholder="Masukkan password akun Anda" 
                    />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <flux:button 
                        type="submit" 
                        variant="primary" 
                        class="w-full" 
                        :disabled="!$bank || empty($bank->bank_name) || (float) str_replace(['.', ','], '', $nominal) < $minWd || (float) str_replace(['.', ','], '', $nominal) > $balance"
                    >
                        {{ __('Kirim Pengajuan') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div class="flex flex-col gap-3">
        <flux:heading size="lg">{{ __('Riwayat Transaksi eWallet') }}</flux:heading>

        <div class="rounded-xl border border-neutral-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-zinc-900 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-800 border-b border-neutral-200 dark:border-neutral-700">
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">#</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Tanggal') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Tipe') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Kategori') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-right">{{ __('Nominal') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-right">{{ __('Pajak') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-right">{{ __('Auto RO') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300 text-right">{{ __('Jumlah Net') }}</th>
                            <th class="p-4 font-semibold text-zinc-700 dark:text-zinc-300">{{ __('Keterangan') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @forelse ($logs as $index => $log)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="p-4 align-middle text-zinc-500">{{ $logs->firstItem() + $index }}</td>
                                <td class="p-4 align-middle text-zinc-500 font-mono text-xs">
                                    {{ $log->created_at ? $log->created_at->format('Y-m-d H:i') : '-' }}
                                </td>
                                <td class="p-4 align-middle">
                                    @if ($log->type === 'IN')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            {{ $log->type }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                            {{ $log->type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle text-zinc-900 dark:text-white capitalize">
                                    {{ $log->category ?: '-' }}
                                </td>
                                <td class="p-4 align-middle text-right text-zinc-900 dark:text-white font-mono">
                                    Rp {{ number_format($log->nominal, 0, ',', '.') }}
                                </td>
                                <td class="p-4 align-middle text-right text-zinc-500 dark:text-zinc-400 font-mono text-xs">
                                    Rp {{ number_format($log->tax, 0, ',', '.') }}
                                </td>
                                <td class="p-4 align-middle text-right text-zinc-500 dark:text-zinc-400 font-mono text-xs">
                                    Rp {{ number_format($log->autoro, 0, ',', '.') }}
                                </td>
                                <td class="p-4 align-middle text-right font-semibold font-mono {{ $log->type === 'IN' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                    Rp {{ number_format($log->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-4 align-middle text-zinc-600 dark:text-zinc-300 text-xs max-w-xs truncate" title="{{ $log->description }}">
                                    {{ $log->description ?: '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('Tidak ada riwayat transaksi wallet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-neutral-200 dark:border-neutral-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
