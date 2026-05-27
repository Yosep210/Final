<?php

namespace App\Actions\Member;

use App\Data\MemberData;
use App\Events\MemberRegistered;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\Permission\Models\Role;

class CreateMemberAction
{
    use AsAction;

    public function handle(MemberData $data): Member
    {
        return DB::transaction(function () use ($data) {
            $attributes = $data->toArray();

            if (empty($attributes['referral_code'])) {
                $attributes['referral_code'] = $this->generateUniqueReferralCode();
            }

            $member = Member::query()->create($attributes);

            // Gunakan role default dari config
            $defaultRole = config('mlm.default_member_role', 'Member');
            Role::findOrCreate($defaultRole, 'web');
            $member->assignRole($defaultRole);

            event(new MemberRegistered($member));

            return $member;
        });
    }

    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Member::where('referral_code', $code)->exists());

        return $code;
    }
}
