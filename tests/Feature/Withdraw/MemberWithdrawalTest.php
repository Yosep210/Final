<?php

use App\Livewire\Wallet\MemberWallet;
use App\Models\EwalletLog;
use App\Models\Member;
use App\Models\MemberBank;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('prevents manual withdrawal requests under various validation failures', function () {
    $service = app(WithdrawalService::class);

    // Test 1: Admin cannot withdraw
    Role::findOrCreate('Admin', 'web');
    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');
    expect(fn () => $service->requestManualWithdrawal($admin, 100000, 'password'))
        ->toThrow(Exception::class, 'Administrator tidak diperbolehkan melakukan penarikan dana.');

    // Test 2: Inactive member cannot withdraw
    $inactiveMember = Member::factory()->create(['status' => 'inactive']);
    expect(fn () => $service->requestManualWithdrawal($inactiveMember, 100000, 'password'))
        ->toThrow(Exception::class, 'Akun Anda tidak aktif.');

    // Test 3: Member with wd_status != 1 cannot withdraw manually
    $memberNoWd = Member::factory()->active()->create(['wd_status' => 0, 'password' => Hash::make('secretpassword')]);
    expect(fn () => $service->requestManualWithdrawal($memberNoWd, 100000, 'secretpassword'))
        ->toThrow(Exception::class, 'Fitur penarikan manual akun Anda dinonaktifkan.');

    // Test 4: Member without bank details cannot withdraw
    $memberNoBank = Member::factory()->active()->create(['wd_status' => 1, 'password' => Hash::make('secretpassword')]);
    expect(fn () => $service->requestManualWithdrawal($memberNoBank, 100000, 'secretpassword'))
        ->toThrow(Exception::class, 'Data bank belum diisi lengkap.');

    // Test 5: Incorrect password validation
    $member = Member::factory()->active()->create(['wd_status' => 1, 'password' => Hash::make('secretpassword')]);
    MemberBank::create([
        'member_id' => $member->id,
        'bank_name' => 'BCA',
        'account_number' => '12345678',
        'account_holder' => 'Alice',
    ]);
    expect(fn () => $service->requestManualWithdrawal($member, 100000, 'wrongpassword'))
        ->toThrow(Exception::class, 'Password transaksi salah.');

    // Test 6: Below minimum withdrawal limit (50,000)
    expect(fn () => $service->requestManualWithdrawal($member, 40000, 'secretpassword'))
        ->toThrow(Exception::class, 'Nominal penarikan minimal Rp 50.000');

    // Test 7: Insufficient ewallet balance
    expect(fn () => $service->requestManualWithdrawal($member, 100000, 'secretpassword'))
        ->toThrow(Exception::class, 'Saldo eWallet tidak mencukupi.');
});

it('successfully processes manual withdrawal request when valid', function () {
    $service = app(WithdrawalService::class);

    $member = Member::factory()->active()->create(['wd_status' => 1, 'password' => Hash::make('secretpassword')]);
    MemberBank::create([
        'member_id' => $member->id,
        'bank_name' => 'BCA',
        'account_number' => '12345678',
        'account_holder' => 'Alice',
    ]);

    // Give ewallet balance: 200,000
    EwalletLog::create([
        'member_id' => $member->id,
        'nominal' => 200000,
        'amount' => 200000,
        'type' => 'IN',
    ]);

    $withdrawal = $service->requestManualWithdrawal($member, 150000, 'secretpassword');

    expect($withdrawal)->not->toBeNull();
    expect((float) $withdrawal->nominal)->toBe(150000.0);
    expect((float) $withdrawal->nominal_receipt)->toBe(145000.0); // 150,000 - 5,000 fee
    expect((float) $withdrawal->admin_fund)->toBe(5000.0);
    expect($withdrawal->status)->toBe(0); // Pending

    // Ewallet balance should be reduced: 200,000 - 150,000 = 50,000 remaining
    expect($member->ewalletBalance())->toBe(50000.0);

    // Verify EwalletLog OUT was created
    $outLog = EwalletLog::where('member_id', $member->id)->where('type', 'OUT')->first();
    expect($outLog)->not->toBeNull();
    expect((float) $outLog->amount)->toBe(150000.0);
});

it('processes auto-withdrawals for eligible members only', function () {
    $service = app(WithdrawalService::class);

    // Member 1: Eligible for auto-withdraw
    $member1 = Member::factory()->active()->create(['wd_status' => 2]); // 2 = Auto
    MemberBank::create([
        'member_id' => $member1->id,
        'bank_name' => 'BCA',
        'account_number' => '111111',
        'account_holder' => 'Alice',
    ]);
    EwalletLog::create([
        'member_id' => $member1->id,
        'nominal' => 150000,
        'amount' => 150000,
        'type' => 'IN',
    ]);

    // Member 2: Ineligible (balance below custom wd_min of 200,000)
    $member2 = Member::factory()->active()->create(['wd_status' => 2, 'wd_min' => 200000]);
    MemberBank::create([
        'member_id' => $member2->id,
        'bank_name' => 'Mandiri',
        'account_number' => '222222',
        'account_holder' => 'Bob',
    ]);
    EwalletLog::create([
        'member_id' => $member2->id,
        'nominal' => 150000,
        'amount' => 150000,
        'type' => 'IN',
    ]);

    // Member 3: Ineligible (wd_status = 1 (Manual), not Auto)
    $member3 = Member::factory()->active()->create(['wd_status' => 1]);
    MemberBank::create([
        'member_id' => $member3->id,
        'bank_name' => 'CIMB',
        'account_number' => '333333',
        'account_holder' => 'Charlie',
    ]);
    EwalletLog::create([
        'member_id' => $member3->id,
        'nominal' => 150000,
        'amount' => 150000,
        'type' => 'IN',
    ]);

    $processed = $service->processAutoWithdrawals();

    expect($processed)->toBe(1); // Only Member 1 processed

    // Verify Member 1 withdrawal log
    $withdrawal1 = Withdrawal::where('member_id', $member1->id)->first();
    expect($withdrawal1)->not->toBeNull();
    expect((float) $withdrawal1->nominal)->toBe(150000.0);
    expect($member1->ewalletBalance())->toBe(0.0);

    // Verify Member 2 and 3 did not get processed
    expect(Withdrawal::where('member_id', $member2->id)->count())->toBe(0);
    expect(Withdrawal::where('member_id', $member3->id)->count())->toBe(0);
});

it('triggers auto withdrawals via CLI artisan command', function () {
    $member = Member::factory()->active()->create(['wd_status' => 2]);
    MemberBank::create([
        'member_id' => $member->id,
        'bank_name' => 'BCA',
        'account_number' => '111111',
        'account_holder' => 'Alice',
    ]);
    EwalletLog::create([
        'member_id' => $member->id,
        'nominal' => 120000,
        'amount' => 120000,
        'type' => 'IN',
    ]);

    $exitCode = Artisan::call('withdraw:process-auto');
    expect($exitCode)->toBe(0);
    expect(Withdrawal::where('member_id', $member->id)->count())->toBe(1);
});

it('renders the MemberWallet Livewire component and processes manual withdrawal submission', function () {
    $member = Member::factory()->active()->create(['wd_status' => 1, 'password' => Hash::make('secretpassword')]);
    MemberBank::create([
        'member_id' => $member->id,
        'bank_name' => 'BCA',
        'account_number' => '12345678',
        'account_holder' => 'Alice',
    ]);

    EwalletLog::create([
        'member_id' => $member->id,
        'nominal' => 200000,
        'amount' => 200000,
        'type' => 'IN',
    ]);

    Livewire::actingAs($member)
        ->test(MemberWallet::class)
        ->assertSee('Rp 200.000') // Display balance
        ->assertSee('BCA') // Display bank details
        ->set('nominal', '150.000')
        ->set('password', 'wrongpassword')
        ->call('requestWithdrawal')
        ->assertHasErrors(['nominal'])
        ->set('nominal', '150.000')
        ->set('password', 'secretpassword')
        ->call('requestWithdrawal')
        ->assertHasNoErrors();

    expect(Withdrawal::where('member_id', $member->id)->count())->toBe(1);
    expect($member->ewalletBalance())->toBe(50000.0);
});
