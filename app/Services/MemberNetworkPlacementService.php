<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberNetwork;
use Illuminate\Support\Facades\DB;

class MemberNetworkPlacementService
{
    public function resolvePlacement(Member $member, ?string $referralCode = null, ?int $explicitSponsorId = null, ?int $explicitParentId = null, ?string $position = null): array
    {
        return DB::transaction(function () use ($member, $referralCode, $explicitSponsorId, $explicitParentId, $position) {
            // Gunakan pessimistic locking untuk cegah race condition
            // Lock sponsor jika ada untuk ensure atomic placement
            if ($explicitSponsorId) {
                MemberNetwork::where('member_id', $explicitSponsorId)
                    ->lockForUpdate()
                    ->first();
            } elseif ($referralCode) {
                $refMember = Member::where('referral_code', $referralCode)->lockForUpdate()->first();
                if ($refMember) {
                    MemberNetwork::where('member_id', $refMember->id)
                        ->lockForUpdate()
                        ->first();
                }
            }

            return $this->performPlacement($member, $referralCode, $explicitSponsorId, $explicitParentId, $position);
        });
    }

    private function performPlacement(Member $member, ?string $referralCode = null, ?int $explicitSponsorId = null, ?int $explicitParentId = null, ?string $position = null): array
    {
        $sponsor = $this->findSponsor($referralCode, $explicitSponsorId);
        $parent = $this->findParent($explicitParentId);
        $sponsoredId = $sponsor?->id;
        $group = 0;
        $generation = 0;
        $path = (string) $member->id;
        $parentId = $parent?->id;

        $sponsorNetwork = null;

        if ($sponsor) {
            $sponsorNetwork = MemberNetwork::where('member_id', $sponsor->id)->first();
            $group = $sponsorNetwork?->group ?? 0;
        }

        if ($parent) {
            [$position, $parentId] = $this->resolvePositionForParent($parent, $position);
            $parentNetwork = MemberNetwork::where('member_id', $parent->id)->first();
            $generation = $parentNetwork?->generation !== null ? $parentNetwork->generation + 1 : 1;
            $path = $parentNetwork?->path ? ($parentNetwork->path.'/'.$member->id) : (string) $member->id;
            $group = $this->resolveGroup($parentNetwork, $sponsorNetwork, $position);
        } elseif ($sponsor) {
            $placement = $this->findPlacementUnderSponsor($sponsor, $position);
            $parentId = $placement['parent_id'];
            $position = $placement['position'];
            $parentNetwork = $placement['parent_network'];
            $generation = $parentNetwork?->generation !== null ? $parentNetwork->generation + 1 : 1;
            $path = $parentNetwork?->path ? ($parentNetwork->path.'/'.$member->id) : (string) $member->id;
            $group = $this->resolveGroup($parentNetwork, $sponsorNetwork, $position);
        }

        return [
            'member_id' => $member->id,
            'sponsored_id' => $sponsoredId,
            'parent_id' => $parentId,
            'position' => $position,
            'path' => $path,
            'generation' => $generation,
            'group' => $group,
            'rank' => $this->resolveRank($position),
        ];
    }

    public function updateSponsorRank(?Member $sponsor): void
    {
        if (! $sponsor) {
            return;
        }

        $sponsorNetwork = MemberNetwork::where('member_id', $sponsor->id)->first();

        if (! $sponsorNetwork) {
            return;
        }

        $sponsorNetwork->rank = MemberNetwork::where('sponsored_id', $sponsor->id)->count();
        $sponsorNetwork->save();
    }

    private function findSponsor(?string $referralCode, ?int $explicitSponsorId): ?Member
    {
        if ($explicitSponsorId !== null) {
            return Member::find($explicitSponsorId);
        }

        if (! $referralCode) {
            return null;
        }

        return Member::where('referral_code', $referralCode)->first();
    }

    private function findParent(?int $explicitParentId): ?Member
    {
        if ($explicitParentId === null) {
            return null;
        }

        return Member::find($explicitParentId);
    }

    private function resolvePositionForParent(Member $parent, ?string $position): array
    {
        if ($position && ! $this->positionExistsForParent($parent->id, $position)) {
            return [$position, $parent->id];
        }

        if (! $this->positionExistsForParent($parent->id, 'left')) {
            return ['left', $parent->id];
        }

        if (! $this->positionExistsForParent($parent->id, 'right')) {
            return ['right', $parent->id];
        }

        return $this->findAvailablePositionRecursively($parent->id);
    }

    private function positionExistsForParent(int $parentId, string $position): bool
    {
        return MemberNetwork::where('parent_id', $parentId)
            ->where('position', $position)
            ->exists();
    }

    private function findPlacementUnderSponsor(Member $sponsor, ?string $position): array
    {
        if (! $this->positionExistsForParent($sponsor->id, 'left')) {
            $parentNetwork = MemberNetwork::where('member_id', $sponsor->id)->first();

            return [
                'parent_id' => $sponsor->id,
                'position' => 'left',
                'parent_network' => $parentNetwork,
            ];
        }

        if (! $this->positionExistsForParent($sponsor->id, 'right')) {
            $parentNetwork = MemberNetwork::where('member_id', $sponsor->id)->first();

            return [
                'parent_id' => $sponsor->id,
                'position' => 'right',
                'parent_network' => $parentNetwork,
            ];
        }

        return $this->findAvailablePositionRecursively($sponsor->id);
    }

    private function findAvailablePositionRecursively(int $startParentId): array
    {
        // Pre-load all networks once to avoid N queries
        $allNetworks = MemberNetwork::query()
            ->select('member_id', 'parent_id', 'position', 'generation', 'path', 'group')
            ->get()
            ->keyBy('member_id');

        // Create indexed lookup for parent -> children mapping
        $childrenByParent = collect();
        foreach ($allNetworks as $network) {
            if ($network->parent_id) {
                if (! $childrenByParent->has($network->parent_id)) {
                    $childrenByParent->put($network->parent_id, collect());
                }
                $childrenByParent->get($network->parent_id)->push($network->member_id);
            }
        }

        // Create position lookup: parent_id + position => exists?
        $positionMap = collect();
        foreach ($allNetworks as $network) {
            if ($network->parent_id) {
                $key = "{$network->parent_id}:{$network->position}";
                $positionMap->put($key, true);
            }
        }

        $queue = [$startParentId];

        while (! empty($queue)) {
            $current = array_shift($queue);

            if (! $positionMap->has("{$current}:left")) {
                return [
                    'parent_id' => $current,
                    'position' => 'left',
                    'parent_network' => $allNetworks->get($current),
                ];
            }

            if (! $positionMap->has("{$current}:right")) {
                return [
                    'parent_id' => $current,
                    'position' => 'right',
                    'parent_network' => $allNetworks->get($current),
                ];
            }

            // Add children to queue
            $children = $childrenByParent->get($current, collect())->all();
            foreach ($children as $childId) {
                $queue[] = $childId;
            }
        }

        return [
            'parent_id' => null,
            'position' => null,
            'parent_network' => null,
        ];
    }

    private function resolveRank(?string $position): int
    {
        return match ($position) {
            'left' => 1,
            'right' => 2,
            default => 0,
        };
    }

    private function resolveGroup(?MemberNetwork $parentNetwork, ?MemberNetwork $sponsorNetwork, ?string $position): int
    {
        if ($parentNetwork?->group && $parentNetwork->group > 0) {
            return $parentNetwork->group;
        }

        if ($parentNetwork?->position === 'left') {
            return 1;
        }

        if ($parentNetwork?->position === 'right') {
            return 2;
        }

        if ($sponsorNetwork?->group && $sponsorNetwork->group > 0) {
            return $sponsorNetwork->group;
        }

        return match ($position) {
            'left' => 1,
            'right' => 2,
            default => 0,
        };
    }
}
