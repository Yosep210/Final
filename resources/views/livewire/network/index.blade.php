<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Tree Network') }}</flux:heading>
            <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                {{ __('Binary tree view of the member network.') }}
            </flux:text>
        </div>
    </div>

    <style>
        .tree-table, .tree-table ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        .tree-table {
            text-align: center;
        }

        .tree-table ul {
            position: relative;
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding-top: 2rem;
        }

        .tree-table li {
            position: relative;
            text-align: center;
            padding: 0 .25rem;
        }

        .tree-table li::before,
        .tree-table li::after {
            content: '';
            position: absolute;
            top: 0;
            width: 50%;
            height: 1.5rem;
            border-top: 1px solid #cbd5e1;
        }

        .tree-table li::before {
            right: 50%;
            border-right: 1px solid #cbd5e1;
        }

        .tree-table li::after {
            left: 50%;
            border-left: 1px solid #cbd5e1;
        }

        .tree-table li:only-child::before,
        .tree-table li:only-child::after {
            display: none;
        }

        .photo-wrapper {
            display: inline-block;
            position: relative;
            width: 148px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #dbe3ea;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
            padding: 10px 8px 8px;
        }

        .photo-wrapper.photo-me {
            border-color: #8b5cf6;
        }

        .photo-content {
            display: flex;
            justify-content: center;
        }

        .photo-image img {
            width: 52px;
            height: 52px;
            display: block;
            margin: 0 auto;
        }

        .tree-icon {
            width: 52px;
            height: 52px;
            border-radius: 9999px;
            background: #f8fafc;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto;
            border: 1px solid #dbe3ea;
        }

        .tree-icon-root {
            background: #f5f3ff;
            color: #7c3aed;
            border-color: #c4b5fd;
        }

        .tree-icon-empty {
            background: #ecfdf5;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        .photo-name {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.1;
            text-align: center;
            text-transform: lowercase;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .photo-name.admin {
            text-transform: uppercase;
            color: #7c3aed;
        }

        .photo-name.member {
            color: #0f172a;
        }

        .phone-node {
            margin-top: 8px;
            font-size: 11px;
            font-weight: 600;
            color: #334155;
        }

        .node-one, .node-two {
            padding: 0 !important;
        }

        .photo-name-available,
        .photo-name-available2,
        .photo-name-notavailable,
        .photo-name-notavailable2 {
            text-align: center;
            font-size: 11px;
            line-height: 1.1;
        }

        .photo-name-available {
            margin-top: 6px;
            font-weight: 700;
            color: #16a34a;
        }

        .photo-name-available2 {
            color: #16a34a;
        }

        .photo-name-notavailable {
            margin-top: 6px;
            font-weight: 700;
            color: #ef4444;
        }

        .photo-name-notavailable2 {
            color: #ef4444;
        }

        .add-user {
            text-decoration: none;
            display: inline-block;
        }
    </style>

    <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-zinc-900 overflow-x-auto">
        @php
            $nodesByParent = $networkNodes->groupBy('parent_id');
        @endphp

        @if ($rootNode)
            <div class="min-w-max py-6">
                <ul class="tree-table">
                    @include('livewire.network.tree-node', [
                        'node' => $rootNode,
                        'nodesByParent' => $nodesByParent,
                        'treeCounts' => $treeCounts,
                        'depth' => 0,
                    ])
                </ul>
            </div>
        @else
            <div class="py-10 text-center text-zinc-500">
                {{ __('No network data available.') }}
            </div>
        @endif
    </div>

    <flux:modal name="network-detail-modal" class="max-w-3xl md:min-w-3xl" wire:model="showDetailModal"
        @close="$wire.closeDetail()">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Network Detail') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-600 dark:text-zinc-400">
                    {{ __('Detailed binary network information.') }}
                </flux:text>
            </div>

            @if ($selectedNetwork)
                <div class="grid gap-4 md:grid-cols-2">
                    <flux:input :label="__('Member')" :value="$selectedNetwork->member?->name" readonly />
                    <flux:input :label="__('Username')" :value="$selectedNetwork->member?->username" readonly />
                    <flux:input :label="__('Sponsor')" :value="$selectedNetwork->sponsor?->name ?? '-'" readonly />
                    <flux:input :label="__('Parent')" :value="$selectedNetwork->parent?->name ?? '-'" readonly />
                    <flux:input :label="__('Position')" :value="ucfirst($selectedNetwork->position ?? '-')" readonly />
                    <flux:input :label="__('Left Volume')" :value="number_format((float) ($selectedNetwork->left_volume ?? 0), 0)" readonly />
                    <flux:input :label="__('Right Volume')" :value="number_format((float) ($selectedNetwork->right_volume ?? 0), 0)" readonly />
                    <flux:input :label="__('Total Volume')" :value="number_format((float) ($selectedNetwork->total_volume ?? 0), 0)" readonly />
                    <flux:input :label="__('Rank')" :value="ucfirst($selectedNetwork->current_rank ?? 'member')" readonly />
                    <flux:input :label="__('Generation')" :value="$selectedNetwork->generation ?? 0" readonly />
                </div>
            @endif

            <div class="flex justify-end">
                <flux:button variant="ghost" wire:click="closeDetail">{{ __('Close') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
