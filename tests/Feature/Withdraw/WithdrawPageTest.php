<?php

use App\Livewire\Withdraw\Index as WithdrawIndex;
use App\Livewire\Withdraw\WithdrawTable;
use App\Livewire\Withdraw\WithdrawTotalTable;
use App\Models\Member;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

function createAdminForWithdraw(): Member
{
    Role::findOrCreate('Admin', 'web');

    $admin = Member::factory()->active()->create();
    $admin->assignRole('Admin');

    return $admin;
}

it('redirects guests from withdraw page', function () {
    $this->get(route('withdraw.index'))->assertRedirect(route('login'));
});

it('forbids non-admin users from withdraw page', function () {
    $nonAdmin = Member::factory()->active()->create();

    $this->actingAs($nonAdmin)->get(route('withdraw.index'))->assertForbidden();
});

it('renders the withdraw page for admins', function () {
    $admin = createAdminForWithdraw();

    $this->actingAs($admin)->get(route('withdraw.index'))
        ->assertOk()
        ->assertSee('Withdraw List');
});

it('displays individual withdrawals correctly in the table', function () {
    $admin = createAdminForWithdraw();
    $member1 = Member::factory()->active()->create(['name' => 'Alice', 'username' => 'alice123']);
    $member2 = Member::factory()->active()->create(['name' => 'Bob', 'username' => 'bob456']);

    Withdrawal::create([
        'member_id' => $member1->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'bank_code' => '014',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 500000,
        'nominal_receipt' => 450000,
        'admin_fund' => 20000,
        'tax' => 10000,
        'auto_ro' => 20000,
        'status' => 1, // Success
    ]);

    Withdrawal::create([
        'member_id' => $member2->id,
        'type' => 'auto',
        'bank_name' => 'Mandiri',
        'bank_code' => '008',
        'account_number' => '0987654321',
        'account_holder' => 'Bob',
        'nominal' => 2000000,
        'nominal_receipt' => 1800000,
        'admin_fund' => 100000,
        'tax' => 50000,
        'auto_ro' => 50000,
        'status' => 0, // Pending
    ]);

    Livewire::actingAs($admin)
        ->test(WithdrawTable::class)
        ->assertSee('ALICE123')
        ->assertSee('BOB456')
        ->assertSee('MANUAL')
        ->assertSee('AUTO')
        ->assertSee('450,000')
        ->assertSee('1,800,000')
        ->assertSee('Withdrawal :')
        ->assertSee('Pajak :');
});

it('can switch tabs and render the total transactions table', function () {
    $admin = createAdminForWithdraw();

    Livewire::actingAs($admin)
        ->test(WithdrawIndex::class)
        ->assertSet('activeTab', 'withdraw')
        ->set('activeTab', 'total_withdraw')
        ->assertSet('activeTab', 'total_withdraw');
});

it('aggregates daily withdrawals correctly in the total transactions table', function () {
    $admin = createAdminForWithdraw();
    $member = Member::factory()->active()->create(['name' => 'Alice', 'username' => 'alice123']);

    Withdrawal::create([
        'member_id' => $member->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 500000,
        'nominal_receipt' => 450000,
        'admin_fund' => 20000,
        'tax' => 10000,
        'auto_ro' => 20000,
        'status' => 1,
        'created_at' => now(),
    ]);

    Withdrawal::create([
        'member_id' => $member->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 300000,
        'nominal_receipt' => 280000,
        'admin_fund' => 10000,
        'tax' => 5000,
        'auto_ro' => 5000,
        'status' => 1,
        'created_at' => now(),
    ]);

    Livewire::actingAs($admin)
        ->test(WithdrawTotalTable::class)
        ->assertSee('2')
        ->assertSee('730,000')
        ->assertSee('30,000')
        ->assertSee('760,000')
        ->assertSee('Withdrawal :');
});

it('allows admin to confirm withdrawal with correct password', function () {
    $admin = createAdminForWithdraw();
    $admin->update(['password' => Hash::make('secretpassword')]);

    $member = Member::factory()->active()->create(['username' => 'alice123']);

    $withdrawal = Withdrawal::create([
        'member_id' => $member->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 500000,
        'nominal_receipt' => 450000,
        'admin_fund' => 20000,
        'tax' => 10000,
        'auto_ro' => 20000,
        'status' => 0, // Pending
    ]);

    Livewire::actingAs($admin)
        ->test(WithdrawIndex::class)
        ->assertSet('showDetailModal', false)
        ->dispatch('withdraw:confirm', rowId: $withdrawal->id)
        ->assertSet('showDetailModal', true)
        ->assertSet('selectedWithdraw.id', $withdrawal->id)
        ->set('confirmPassword', 'wrongpassword')
        ->call('confirmWithdrawal')
        ->assertHasErrors(['confirmPassword'])
        ->set('confirmPassword', 'secretpassword')
        ->call('confirmWithdrawal')
        ->assertHasNoErrors()
        ->assertSet('showDetailModal', false);

    expect($withdrawal->fresh()->status)->toBe(1);
    expect($withdrawal->fresh()->confirmed_by)->toBe($admin->username);
    expect($withdrawal->fresh()->confirmed_at)->not->toBeNull();
});

it('filters withdrawals by bank correctly using select filter', function () {
    $admin = createAdminForWithdraw();
    $member1 = Member::factory()->active()->create(['username' => 'alice123']);
    $member2 = Member::factory()->active()->create(['username' => 'bob456']);

    Withdrawal::create([
        'member_id' => $member1->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'bank_code' => '014',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 500000,
        'nominal_receipt' => 450000,
        'status' => 1,
    ]);

    Withdrawal::create([
        'member_id' => $member2->id,
        'type' => 'manual',
        'bank_name' => 'Mandiri',
        'bank_code' => '008',
        'account_number' => '987654321',
        'account_holder' => 'Bob',
        'nominal' => 500000,
        'nominal_receipt' => 450000,
        'status' => 1,
    ]);

    Livewire::actingAs($admin)
        ->test(WithdrawTable::class)
        ->assertSee('ALICE123')
        ->assertSee('BOB456')
        ->set('filters', [
            'select' => [
                'bank_formatted' => 'BCA',
            ],
        ])
        ->assertSee('ALICE123')
        ->assertDontSee('BOB456');
});

it('filters withdraw totals correctly by aggregate fields', function () {
    $admin = createAdminForWithdraw();
    $member = Member::factory()->active()->create(['username' => 'alice123']);

    $w1 = Withdrawal::create([
        'member_id' => $member->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 500000,
        'nominal_receipt' => 450000,
        'admin_fund' => 20000,
        'status' => 1,
    ]);
    $w1->created_at = now()->subDays(2);
    $w1->save();

    $w2 = Withdrawal::create([
        'member_id' => $member->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 300000,
        'nominal_receipt' => 280000,
        'admin_fund' => 10000,
        'status' => 1,
    ]);
    $w2->created_at = now()->subDays(1);
    $w2->save();

    $w3 = Withdrawal::create([
        'member_id' => $member->id,
        'type' => 'manual',
        'bank_name' => 'BCA',
        'account_number' => '1234567890',
        'account_holder' => 'Alice',
        'nominal' => 200000,
        'nominal_receipt' => 190000,
        'admin_fund' => 10000,
        'status' => 1,
    ]);
    $w3->created_at = now()->subDays(1);
    $w3->save();

    $day2Formatted = now()->subDays(2)->locale('id')->isoFormat('DD MMM YY');
    $day1Formatted = now()->subDays(1)->locale('id')->isoFormat('DD MMM YY');

    Livewire::actingAs($admin)
        ->test(WithdrawTotalTable::class)
        ->assertSee($day2Formatted)
        ->assertSee($day1Formatted)
        ->set('filters', [
            'input_text' => [
                'trx_formatted' => '2',
            ],
        ])
        ->assertSee($day1Formatted)
        ->assertDontSee($day2Formatted)
        ->set('filters', [])
        ->set('filters', [
            'input_text' => [
                'nominal_formatted' => '450000',
            ],
        ])
        ->assertSee($day2Formatted)
        ->assertDontSee($day1Formatted);
});
