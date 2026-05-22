<?php

namespace App\Events;

use App\Models\Member;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberPromoted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Member $member;

    public array $payload;

    public function __construct(Member $member, array $payload = [])
    {
        $this->member = $member;
        $this->payload = $payload;
    }
}
