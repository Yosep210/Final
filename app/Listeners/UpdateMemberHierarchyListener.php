<?php

namespace App\Listeners;

use App\Events\MemberPromoted;
use App\Models\MemberNetwork;

class UpdateMemberHierarchyListener
{
    /**
     * Handle the event.
     */
    public function handle(MemberPromoted $event): void
    {
        $member = $event->member;

        // Update rank/generation based on promotion payload.
        $network = MemberNetwork::where('member_id', $member->id)->first();

        if (! $network) {
            return;
        }

        $changed = false;

        if (isset($event->payload['rank']) && $network->current_rank !== $event->payload['rank']) {
            $network->current_rank = $event->payload['rank'];
            $changed = true;
        }

        if (isset($event->payload['generation']) && $network->generation !== $event->payload['generation']) {
            $network->generation = $event->payload['generation'];
            $changed = true;
        }

        if ($changed) {
            $network->save();
        }
    }
}
