<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require 'C:/Users/LENOVO/Project/Layout/Final/vendor/autoload.php';
$app = require_once 'C:/Users/LENOVO/Project/Layout/Final/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

config([
    'database.connections.latihan' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => 'latihan',
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
]);

try {
    $db = DB::connection('latihan');

    // Find some pairing bonuses
    $bonuses = $db->table('jpb_bonus')
        ->where('type', 2) // pairing
        ->limit(3)
        ->get();

    echo "=== Reference Database Splits Analysis ===\n";
    foreach ($bonuses as $b) {
        echo "\nPairing Bonus ID: {$b->id}\n";
        echo "- Member ID: {$b->id_member}\n";
        echo "- Gross Amount: {$b->amount}\n";
        echo "- Tax: {$b->tax}\n";
        echo "- Net Amount: {$b->amount_tax}\n";

        // Find matching ewallet logs
        $ewallet = $db->table('jpb_ewallet')
            ->where('id_source', $b->id)
            ->where('source', 'bonus')
            ->get();

        echo "- Ewallet logs:\n";
        foreach ($ewallet as $e) {
            echo "  * Category: {$e->category}, Nominal: {$e->nominal}, Percent: {$e->percent}%, AutoRO: {$e->autoro}, Tax: {$e->tax}, Amount: {$e->amount}, Type: {$e->type}\n";
        }

        // Find matching auto_ro logs
        $autoro = $db->table('jpb_auto_ro')
            ->where('id_source', $b->id)
            ->where('source', 'bonus')
            ->get();

        echo "- Auto-RO logs:\n";
        foreach ($autoro as $ar) {
            echo "  * Nominal: {$ar->nominal}, Percent: {$ar->percent}%, Amount: {$ar->amount}\n";
        }
    }

    echo "\n=== Check Auto-RO Deductions (Repeat Orders) in Reference DB ===\n";
    $roDeductions = $db->table('jpb_auto_ro')
        ->where('amount', '<', 0)
        ->limit(3)
        ->get();

    foreach ($roDeductions as $ro) {
        echo "- Member ID: {$ro->id_member}, Amount: {$ro->amount}, Description: {$ro->description}, Date: {$ro->datecreated}\n";
    }

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
