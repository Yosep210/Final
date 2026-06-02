<?php

require 'C:/Users/LENOVO/Project/Layout/Final/vendor/autoload.php';
$app = require_once 'C:/Users/LENOVO/Project/Layout/Final/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\AutoRoLog;
use App\Models\CommissionLog;
use App\Models\Pin;
use Illuminate\Contracts\Console\Kernel;

try {
    $logs = CommissionLog::orderBy('id', 'desc')->limit(5)->get();
    echo "Latest 5 Commissions:\n";
    foreach ($logs as $log) {
        echo "- ID: {$log->id}, Member: {$log->member->username}, Type: {$log->type}, Gross: {$log->gross_commission}, Tax: {$log->tax_amount}, Net: {$log->net_commission}, Notes: {$log->notes}\n";
    }

    $autoRo = AutoRoLog::orderBy('id', 'desc')->limit(5)->get();
    echo "\nLatest 5 Auto-RO Logs:\n";
    foreach ($autoRo as $ro) {
        echo "- ID: {$ro->id}, Member: {$ro->member->username}, Source: {$ro->source}, Amount: {$ro->amount}, Description: {$ro->description}\n";
    }

    $pins = Pin::orderBy('id', 'desc')->limit(5)->get();
    echo "\nLatest 5 generated PINs:\n";
    foreach ($pins as $pin) {
        $owner = $pin->owner ? $pin->owner->username : 'none';
        echo "- ID: {$pin->id}, Serial: {$pin->serial_number}, Status: {$pin->status}, Owner: {$owner}\n";
    }

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
