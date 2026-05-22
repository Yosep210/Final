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

        // Placeholder: update rank/generation based on promotion payload.
        $network = MemberNetwork::where('member_id', $member->id)->first();

        if (! $network) {
            return;
        }

        if (isset($event->payload['rank'])) {
            $network->rank = $event->payload['rank'];
        }

        if (isset($event->payload['generation'])) {
            $network->generation = $event->payload['generation'];
        }

        $network->save();
    }
}
