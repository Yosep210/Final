<?php

namespace App\Actions\Member\Profile;

use App\Models\MemberProfile;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteProfileAction
{
    use AsAction;

    public function handle(MemberProfile $profile): ?bool
    {
        return $profile->delete();
    }
}
