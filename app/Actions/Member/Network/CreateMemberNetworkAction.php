<?php

namespace App\Actions\Member\Network;

use App\Models\MemberNetwork;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateMemberNetworkAction
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): MemberNetwork
    {
        return MemberNetwork::query()->create($data);
    }
}
