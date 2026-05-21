<?php

namespace App\Actions\Member\Profile;

use App\Data\MemberProfileData;
use App\Models\MemberProfile;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateProfileAction
{
    use AsAction;

    public function handle(MemberProfileData $data): MemberProfile
    {
        return MemberProfile::create($data->toArray());
    }
}
