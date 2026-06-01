<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = Request::capture();
$response = $kernel->handle($request);
$kernel->terminate($request, $response);

// Now we can use Laravel
$app->make(Kernel::class)->call('tinker', [
    '--execute' => <<<'PHP'
$testMember = \App\Models\Member::create([
    'name' => 'Test Member 001',
    'email' => 'test001@example.com',
    'username' => 'testmember001',
    'password' => bcrypt('password'),
    'status' => 'active',
    'referral_code' => 'TEST001'
]);
echo 'Member created: ' . $testMember->id . PHP_EOL;

// Check if network created
$network = \App\Models\MemberNetwork::where('member_id', $testMember->id)->first();
if ($network) {
    echo 'Network created successfully: ' . $network->id . PHP_EOL;
} else {
    echo 'Network NOT created' . PHP_EOL;
}
PHP,
]);
