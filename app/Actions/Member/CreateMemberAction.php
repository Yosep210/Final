<?php

namespace App\Actions\Member;

use App\Data\MemberData;
use App\Events\MemberRegistered;
use App\Models\Member;
use App\Models\Pin;
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
            // Validate Activation PIN if configured
            $pin = null;
            if (config('mlm.registration_requires_pin')) {
                if (empty($data->pinSerial) || empty($data->pinCode)) {
                    throw new \InvalidArgumentException('Serial number and PIN code are required.');
                }

                $pin = Pin::where('serial_number', $data->pinSerial)
                    ->where('pin_code', $data->pinCode)
                    ->where('status', 'unused')
                    ->first();

                if (! $pin) {
                    throw new \InvalidArgumentException('Invalid or already used Activation PIN.');
                }
            }

            $attributes = $data->toArray();

            if (empty($attributes['referral_code'])) {
                $attributes['referral_code'] = $this->generateUniqueReferralCode();
            }

            $member = Member::query()->create($attributes);

            // Mark PIN as used and associate it with the created member
            if ($pin) {
                $pin->update([
                    'status' => 'used',
                    'activated_member_id' => $member->id,
                    'activated_at' => now(),
                ]);
            }

            // Gunakan role default dari config
            $defaultRole = config('mlm.default_member_role', 'Member');
            Role::findOrCreate($defaultRole, 'web');
            $member->assignRole($defaultRole);

            event(new MemberRegistered($member, [
                'sponsor_username' => $data->sponsorUsername ?? null,
                'parent_username' => $data->parentUsername ?? null,
                'position' => $data->position ?? null,
            ]));

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
