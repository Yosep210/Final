<div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
    <div class="items-center gap-2 mb-2 hidden lg:flex">
        <flux:icon name="calendar" class="size-4 text-zinc-500" />
        <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">{{
            now()->locale('id')->isoFormat('dddd, DD MMM YYYY') }}</span>
    </div>
    <!-- Header Greeting -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <flux:heading size="xl" class="tracking-tight">
                {{ __('Welcome back') }}, <span
                    class="bg-linear-to-r from-blue-600 to-indigo-500 bg-clip-text text-transparent font-extrabold">{{
                    auth()->user()->name }}</span>!
            </flux:heading>
            <flux:text class="mt-1 text-zinc-500 dark:text-zinc-400">
                {{ $isAdmin ? __('System Overview & Business Performance') : __('Your personal business and commission
                summary.') }}
            </flux:text>
        </div>
    </div>

    @if ($isAdmin)
    <!-- Admin Dashboard Content -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Card: Active Members -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <flux:icon name="users" class="size-20" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Members') }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <flux:icon name="users" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">{{
                    number_format($activeMembers) }}</span>
                <span class="text-sm text-zinc-400"> / {{ number_format($totalMembers) }} {{ __('Active') }}</span>
            </div>
        </div>

        <!-- Card: eWallet Balance -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <flux:icon name="currency-dollar" class="size-20" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('eWallet System Balance')
                    }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <flux:icon name="currency-dollar" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{
                    number_format($ewalletBalance, 0) }}</span>
            </div>
        </div>

        <!-- Card: Paid & Pending Commissions -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <flux:icon name="banknotes" class="size-20" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Commissions') }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <flux:icon name="banknotes" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{
                    number_format($totalCommissions, 0) }}</span>
                <div class="mt-1 flex items-center justify-between text-xs text-zinc-500">
                    <span>Paid: Rp {{ number_format($paidCommissions, 0) }}</span>
                    <span>Pending: Rp {{ number_format($pendingCommissions, 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Card: Auto RO & Withdrawals -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity duration-300">
                <flux:icon name="arrow-path" class="size-20" />
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Financial Flows') }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <flux:icon name="arrow-path" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-xl font-bold text-zinc-900 dark:text-white">Auto RO: Rp {{
                    number_format($autoRoAmount, 0) }}</span>
                <div class="mt-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                    Paid WDs: Rp {{ number_format($totalWithdrawals, 0) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events lists -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-2">
        <!-- Left: Recent Members -->
        <div
            class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-zinc-900/50 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <flux:heading size="lg">{{ __('Recent Member Registrations') }}</flux:heading>
                <flux:button variant="ghost" size="sm" :href="route('member.index')" wire:navigate>{{ __('View All') }}
                </flux:button>
            </div>
            <div class="mt-4 flow-root">
                <ul class="-my-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($recentMembers as $rm)
                    <li class="py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 font-extrabold text-sm text-zinc-600 dark:text-zinc-300">
                                {{ strtoupper(substr($rm['name'] ?? '', 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-zinc-900 dark:text-white">{{
                                    strtoupper($rm['username'] ?? '') }}</div>
                                <div class="text-xs text-zinc-500">{{ $rm['name'] }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $rm['status'] === 'active' ? 'bg-green-50 text-green-700 ring-green-600/10 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : 'bg-rose-50 text-rose-700 ring-rose-600/10 dark:bg-rose-500/10 dark:text-rose-400 dark:ring-rose-500/20' }}">
                                {{ ucfirst($rm['status'] ?? '-') }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="py-8 text-center text-zinc-500">{{ __('No registrations yet.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Right: Recent Commissions -->
        <div
            class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-zinc-900/50 p-6 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
                <flux:heading size="lg">{{ __('Recent Commissions') }}</flux:heading>
                <flux:button variant="ghost" size="sm" :href="route('commission.index')" wire:navigate>{{ __('View All')
                    }}</flux:button>
            </div>
            <div class="mt-4 flow-root">
                <ul class="-my-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($recentCommissions as $rc)
                    <li class="py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold ring-1 ring-inset {{ $rc['type'] === 'sponsor' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/10' : ($rc['type'] === 'pairing' ? 'bg-blue-50 text-blue-700 ring-blue-600/10' : 'bg-amber-50 text-amber-700 ring-amber-600/10') }}">
                                {{ ucfirst(substr($rc['type'] ?? '', 0, 2)) }}
                            </span>
                            <div>
                                <div class="text-sm font-bold text-zinc-900 dark:text-white">{{
                                    strtoupper($rc['username'] ?? '') }}</div>
                                <div class="text-xs text-zinc-500">{{ ucfirst($rc['type'] ?? '-') }} • {{
                                    $rc['created_at'] }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">+Rp {{
                                number_format($rc['amount'], 0) }}</div>
                        </div>
                    </li>
                    @empty
                    <li class="py-8 text-center text-zinc-500">{{ __('No commissions logged yet.') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @else
    <!-- Member Dashboard Content -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Rank & Sponsor -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('My Business Status') }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                    <flux:icon name="bookmark" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span
                    class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-500/10 px-2 py-1 text-xs font-bold text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/20 uppercase tracking-widest">{{
                    $myRank }}</span>
                <div class="mt-2 text-xs text-zinc-500">
                    Recruits: <strong class="text-zinc-700 dark:text-zinc-300">{{ $mySponsorsCount }} members</strong>
                </div>
            </div>
        </div>

        <!-- eWallet Balance -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('My eWallet Balance') }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                    <flux:icon name="wallet" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{
                    number_format($myEwalletBalance, 0) }}</span>
            </div>
        </div>

        <!-- My Auto RO -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('My Auto RO') }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                    <flux:icon name="arrow-path" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{
                    number_format($myAutoRo, 0) }}</span>
            </div>
        </div>

        <!-- Total Earned Commission -->
        <div
            class="relative overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-800 bg-linear-to-br from-white to-zinc-50 dark:from-zinc-900 dark:to-zinc-950 p-6 shadow-sm transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Commission Earned')
                    }}</span>
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <flux:icon name="banknotes" class="size-4" />
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">Rp {{
                    number_format($myCommissions, 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Recent eWallet Transactions list -->
    <div
        class="rounded-xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-zinc-900/50 p-6 shadow-sm mt-2">
        <div class="flex items-center justify-between pb-4 border-b border-zinc-100 dark:border-zinc-800">
            <flux:heading size="lg">{{ __('My Recent eWallet Transactions') }}</flux:heading>
        </div>
        <div class="mt-4 flow-root">
            <ul class="-my-4 divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse ($myRecentTransactions as $txn)
                <li class="py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold ring-1 ring-inset {{ $txn['type'] === 'IN' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/10' : 'bg-rose-50 text-rose-700 ring-rose-600/10' }}">
                            {{ $txn['type'] }}
                        </span>
                        <div>
                            <div class="text-sm font-bold text-zinc-900 dark:text-white">{{ $txn['description'] ?:
                                ucfirst($txn['category'] ?? '-') }}</div>
                            <div class="text-xs text-zinc-500">{{ ucfirst($txn['category'] ?? '-') }} • {{
                                $txn['created_at']
                                }}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div
                            class="text-sm font-bold {{ $txn['type'] === 'IN' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                            {{ $txn['type'] === 'IN' ? '+' : '-' }}Rp {{ number_format($txn['amount'], 0) }}
                        </div>
                    </div>
                </li>
                @empty
                <li class="py-8 text-center text-zinc-500">{{ __('No transactions yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
    @endif
</div>