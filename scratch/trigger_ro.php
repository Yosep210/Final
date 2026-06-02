<?php

require 'C:/Users/LENOVO/Project/Layout/Final/vendor/autoload.php';
$app = require_once 'C:/Users/LENOVO/Project/Layout/Final/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\AutoRoLog;
use App\Models\Member;
use App\Models\MemberNetwork;
use App\Models\Pin;
use App\Services\CommissionCalculationService;
use Illuminate\Contracts\Console\Kernel;

try {
    $member = Member::find(23); // eternalglory
    $network = MemberNetwork::where('member_id', 23)->first();

    echo "Initial volumes for eternalglory:\n";
    echo "- Left: {$network->left_volume}\n";
    echo "- Right: {$network->right_volume}\n";

    echo "\n1. Adding 50,000,000 BV to both legs directly for testing...\n";
    $network->left_volume += 50000000.0;
    $network->right_volume += 50000000.0;
    $network->save();

    echo "New volumes:\n";
    echo "- Left: {$network->left_volume}\n";
    echo "- Right: {$network->right_volume}\n";

    echo "\n2. Calculating binary commission for 2026-8...\n";
    $calcService = app(CommissionCalculationService::class);
    $commission = $calcService->calculateBinaryCommission($member, 2026, 8);

    if ($commission) {
        echo "Commission calculated successfully!\n";
        echo "- Gross: {$commission->gross_commission}\n";
        echo "- Tax: {$commission->tax_amount}\n";
        echo "- Net: {$commission->net_commission}\n";
        echo "- Notes: {$commission->notes}\n";
    } else {
        echo "Commission NOT calculated.\n";
    }

    echo "\n3. Checking Auto-RO logs after calculation...\n";
    $roLogs = AutoRoLog::where('member_id', 23)->orderBy('id', 'desc')->limit(4)->get();
    foreach ($roLogs as $ro) {
        echo "- ID: {$ro->id}, Source: {$ro->source}, Amount: {$ro->amount}, Desc: {$ro->description}\n";
    }

    echo "\n4. Checking generated PINs for eternalglory...\n";
    $pins = Pin::where('owner_id', 23)->orderBy('id', 'desc')->limit(3)->get();
    foreach ($pins as $pin) {
        echo "- ID: {$pin->id}, Serial: {$pin->serial_number}, Status: {$pin->status}, Created: {$pin->created_at}\n";
    }

    echo "\n5. final Auto-RO balance: ".AutoRoLog::where('member_id', 23)->sum('amount')."\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
