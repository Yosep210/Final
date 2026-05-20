<?php

use App\Data\MemberData;

it('normalizes member data from array input', function () {
    $data = MemberData::fromArray([
        'name' => ' John Doe ',
        'username' => ' johndoe ',
        'email' => ' john@example.com ',
        'password' => '',
        'status' => ' active ',
        'referral_code' => ' REF001 ',
        'email_verified_at' => '',
        'last_login_at' => '',
    ]);

    expect($data->name)->toBe('John Doe')
        ->and($data->username)->toBe('johndoe')
        ->and($data->email)->toBe('john@example.com')
        ->and($data->password)->toBeNull()
        ->and($data->status)->toBe('active')
        ->and($data->referralCode)->toBe('REF001');
});
