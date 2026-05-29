<?php

namespace App\Livewire\Network;

use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Jaringan Binary')]
class Index extends Component
{
    use AuthorizesRequests;

    public bool $showDetailModal = false;

    public ?MemberNetwork $selectedNetwork = null;

    public Collection $networkNodes;

    public ?MemberNetwork $rootNode = null;

    public array $treeCounts = [];

    public function mount(): void
    {
        $this->authorize('viewAny', Member::class);

        $this->loadTree();
    }

    #[On('network:view')]
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

    public function loadTree(): void
    {
        $query = MemberNetwork::query()
            ->with(['member', 'sponsor', 'parent'])
            ->orderBy('path')
            ->orderBy('position')
            ->orderBy('member_id')
            ->get();

        $this->networkNodes = $query;

        $this->rootNode = $this->resolveRootNode($this->networkNodes);

        $this->treeCounts = $this->buildTreeCounts();
    }

    private function resolveRootNode(Collection $nodes): ?MemberNetwork
    {
        $authMemberId = auth()->id();

        if ($authMemberId) {
            $currentNode = $nodes->firstWhere('member_id', $authMemberId);
            if ($currentNode) {
                return $currentNode;
            }
        }

        return $nodes->firstWhere('parent_id', null) ?? $nodes->first();
    }

    private function buildTreeCounts(): array
    {
        $childrenByParent = $this->networkNodes->groupBy('parent_id');
        $counts = [];

        foreach ($this->networkNodes as $node) {
            $leftChildren = $childrenByParent->get($node->member_id, collect())->where('position', 'left');
            $rightChildren = $childrenByParent->get($node->member_id, collect())->where('position', 'right');

            $counts[$node->id] = [
                'left' => $this->countBranch($leftChildren->first(), $childrenByParent),
                'right' => $this->countBranch($rightChildren->first(), $childrenByParent),
                'has_left' => $leftChildren->isNotEmpty(),
                'has_right' => $rightChildren->isNotEmpty(),
            ];
        }

        return $counts;
    }

    private function countBranch(?MemberNetwork $node, Collection $childrenByParent): int
    {
        if (! $node) {
            return 0;
        }

        $children = $childrenByParent->get($node->member_id, collect());
        if ($children->isEmpty()) {
            return 0;
        }

        $count = $children->count();
        foreach ($children as $child) {
            $count += $this->countBranch($child, $childrenByParent);
        }

        return $count;
    }

    public function render()
    {
        return view('livewire.network.index')
            ->layout('layouts.app', ['title' => __('Jaringan Binary')]);
    }
}
