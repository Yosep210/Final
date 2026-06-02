<?php

namespace App\Events;

use App\Models\Member;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Member $member;

    public ?array $placementData;

    public function __construct(Member $member, ?array $placementData = null)
    {
        $this->member = $member;
        $this->placementData = $placementData;
    }
}
