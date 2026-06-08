@php
    $member = $node->member;
    $children = $nodesByParent->get($node->member_id, collect());
    $left = $children->firstWhere('position', 'left');
    $right = $children->firstWhere('position', 'right');
    $depth = $depth ?? 0;
    $maxDepth = 3;
    $isRoot = $depth === 0;
    $counts = $treeCounts[$node->id] ?? ['left' => 0, 'right' => 0];
    $isActiveMember = (string) ($member?->status ?? '') === 'active';
    $rankLabel = strtoupper((string) ($member?->network?->current_rank ?? 'member'));
    $leftVolume = (float) ($member?->network?->left_volume ?? 0);
    $rightVolume = (float) ($member?->network?->right_volume ?? 0);
    $qualifiedLegs = (int) ($member?->network?->qualified_legs ?? 0);
@endphp

<li>
    @if ($isRoot)
    <div class="row mb-4 mb-sm-0">
        <div class="col-6">
            <div class="text text-center">
                <div class="text-center mb-2" style="display: block;">
                    <button class="btn btn-sm btn-default px-3" type="button">Level Kiri Terbawah</button>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="text text-center">
                <div class="text-center mb-2" style="display: block;">
                    <button class="btn btn-sm btn-default px-3" type="button">Level Kanan Terbawah</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <a href="#" class="tree-member-link" wire:click.prevent="$dispatch('network:view', { rowId: {{ $node->id }} })"
        title="Sponsor : <b>{{ $member?->sponsor?->username ?? '-' }}</b><br/>Join : {{ optional($member?->created_at)->format('d-M-y') }}">
        <div class="photo-wrapper photo-wrapper-tooltip {{ $isRoot ? 'photo-me' : '' }}">
            <div class="photo-content">
                <div class="photo-image mb-0">
                    <div class="tree-icon {{ $isRoot ? 'tree-icon-root' : '' }}">
                        <flux:icon.user class="size-6" />
                    </div>
                </div>
            </div>
            <div class="photo-name {{ $isRoot ? 'admin' : 'member' }}">{{ $member?->username ?? '-' }}</div>
            @if (! $isRoot && $isActiveMember)
            <div class="text text-default font-weight-bolder text-sm">
                <i class="bi bi-star-fill"></i> {{ $rankLabel === 'STAR' ? 'STAR' : $rankLabel }}
            </div>
            @endif
            <div class="phone-node row mt-2" style="padding:0px">
                <div class="col-6 node-one tree-info-tooltip" title="Total Downline" style="padding:0px !important">
                    <b>L</b>: {{ number_format((float) ($counts['left'] ?? 0), 0) }}
                </div>
                <div class="col-6 node-two tree-info-tooltip" title="Total Downline" style="padding:0px !important">
                    <b>R</b>: {{ number_format((float) ($counts['right'] ?? 0), 0) }}
                </div>
            </div>
            @if (! $isRoot && $isActiveMember)
            <div class="phone-node row" style="padding:0px">
                <div class="col-6 node-one tree-info-tooltip" title="Poin Pairing" style="padding:0px !important">
                    <b>PP</b> : {{ number_format($leftVolume, 0) }}
                </div>
                <div class="col-6 node-two tree-info-tooltip" title="Poin Pairing" style="padding:0px !important">
                    <b>PP</b> : {{ number_format($rightVolume, 0) }}
                </div>
            </div>
            <div class="phone-node row" style="padding:0px">
                <div class="col-6 node-one tree-info-tooltip" title="Total SM Kiri" style="padding:0px !important">
                    <b>SM.L</b> : {{ $qualifiedLegs }}
                </div>
                <div class="col-6 node-two tree-info-tooltip" title="Total SM Kanan" style="padding:0px !important">
                    <b>SM.R</b> : {{ $qualifiedLegs }}
                </div>
            </div>
            @endif
        </div>
    </a>

    @if ($depth < $maxDepth) <ul class="{{ $depth === 0 ? 'child-1' : ($depth === 1 ? 'child-2' : 'child-3') }}">
<li>
    @if ($left)
                    @include('livewire.network.tree-node', [
                        'node' => $left,
                        'nodesByParent' => $nodesByParent,
                        'treeCounts' => $treeCounts,
                        'depth' => $depth + 1,
                    ])
    @else
    <a href="{{ route('member.index', ['parent' => $member->username ?? '', 'position' => 'left']) }}" class="add-user" wire:navigate>
        <div class="photo-wrapper">
            <div class="photo-content">
                <div class="photo-image">
                    <div class="tree-icon tree-icon-empty">
                        <flux:icon.user-plus class="size-6" />
                    </div>
                </div>
            </div>
            <div class="photo-name-available">Available</div>
            <div class="photo-name-available2"><span>New Member</span></div>
            <div class="phone-node row mt-2">
                <div class="col-6 node-one" style="padding:0px !important">-</div>
                <div class="col-6 node-two" style="padding:0px !important">-</div>
            </div>
        </div>
    </a>
    @endif
</li>
<li>
    @if ($right)
                    @include('livewire.network.tree-node', [
                        'node' => $right,
                        'nodesByParent' => $nodesByParent,
                        'treeCounts' => $treeCounts,
                        'depth' => $depth + 1,
                    ])
    @else
    <a href="{{ route('member.index', ['parent' => $member->username ?? '', 'position' => 'right']) }}" class="add-user" wire:navigate>
        <div class="photo-wrapper">
            <div class="photo-content">
                <div class="photo-image">
                    <div class="tree-icon tree-icon-empty">
                        <flux:icon.user-plus class="size-6" />
                    </div>
                </div>
            </div>
            <div class="photo-name-available">Available</div>
            <div class="photo-name-available2"><span>New Member</span></div>
            <div class="phone-node row mt-2">
                <div class="col-6 node-one" style="padding:0px !important">-</div>
                <div class="col-6 node-two" style="padding:0px !important">-</div>
            </div>
        </div>
    </a>
    @endif
</li>
</ul>
@endif
</li>
