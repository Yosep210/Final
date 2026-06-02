<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = Request::capture();
$response = $kernel->handle($request);
$kernel->terminate($request, $response);

// Run the integration test inside Tinker
$artisan = $app->make(Kernel::class);
$status = $artisan->call('tinker', [
    '--execute' => <<<'PHP'
echo "=== TESTING MEMBER BANK & PIN CREATION ===" . PHP_EOL;

// Clean up old test data if exists
$m = \App\Models\Member::where('username', 'testmember002')->first();
if ($m) {
    if ($m->network) {
        $m->network->forceDelete();
    }
    if ($m->bank) {
        $m->bank->forceDelete();
    }
    $m->profile()?->forceDelete();
    \App\Models\Pin::where('activated_member_id', $m->id)->forceDelete();
    $m->forceDelete();
}

// Truncate queue to avoid locks
\DB::table('jobs')->truncate();

// 1. Generate a test activation PIN
$pinSerial = 'SR-TEST-12345';
$pinCode = 'CODE-TEST-12345';

// Clear existing PIN if any to avoid duplication errors
\App\Models\Pin::where('serial_number', $pinSerial)->forceDelete();

$pin = \App\Models\Pin::create([
    'serial_number' => $pinSerial,
    'pin_code' => $pinCode,
    'status' => 'unused',
]);
echo "Activation PIN generated: " . $pin->serial_number . PHP_EOL;

// 2. Prepare registration data with PIN
$data = \App\Data\MemberData::fromArray([
    'name' => 'Root Member with Bank',
    'email' => 'root_bank@example.com',
    'username' => 'testmember002',
    'password' => 'password123',
    'status' => 'active',
    'referral_code' => 'TEST002',
    'pin_serial' => $pinSerial,
    'pin_code' => $pinCode,
]);

$testMember = \App\Actions\Member\CreateMemberAction::run($data);
echo 'Member created: ' . $testMember->id . PHP_EOL;

// Verify PIN has been marked as used and bound to the member
$pin->refresh();
echo 'PIN Status: ' . $pin->status . PHP_EOL;
echo 'PIN Activated Member ID: ' . $pin->activated_member_id . PHP_EOL;

if ($pin->status === 'used' && (int)$pin->activated_member_id === (int)$testMember->id) {
    echo 'PIN Verification: SUCCESS' . PHP_EOL;
} else {
    echo 'PIN Verification: FAILED' . PHP_EOL;
}

$bankData = \App\Data\MemberBankData::fromArray([
    'member_id' => $testMember->id,
    'bank_name' => 'BCA',
    'account_number' => '987654321',
    'account_holder' => 'ROOT MEMBER BANK'
]);

$bankRecord = \App\Actions\MemberBank\CreateMemberBankAction::run($bankData);
echo 'Member Bank record created: ' . $bankRecord->id . PHP_EOL;

// Load with relation
$loadedMember = \App\Models\Member::with(['bank', 'activationPin'])->find($testMember->id);
echo 'Loaded bank_name: ' . $loadedMember->bank->bank_name . PHP_EOL;
echo 'Loaded activation PIN serial: ' . $loadedMember->activationPin->serial_number . PHP_EOL;

echo "=== TEST COMPLETED SUCCESSFULLY ===" . PHP_EOL;
PHP,
]);

echo $artisan->output();
