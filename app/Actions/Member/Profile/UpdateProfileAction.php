<?php

namespace App\Actions\Member\Profile;

use App\Data\MemberProfileData;
use App\Models\MemberProfile;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateProfileAction
{
    use AsAction;

    public function handle(MemberProfile $profile, MemberProfileData $data): MemberProfile
    {
        $profile->fill($data->toArray());
        $profile->save();

        return $profile->refresh();
    }
}
