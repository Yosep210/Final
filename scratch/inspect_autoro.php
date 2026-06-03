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

    $sources = $db->table('jpb_auto_ro')
        ->select('source', $db->raw('count(*) as count'), $db->raw('sum(amount) as total_amount'))
        ->groupBy('source')
        ->get();

    echo "=== Auto-RO Sources Summary ===\n";
    foreach ($sources as $s) {
        echo '- Source: '.($s->source ?: 'NULL').", Count: {$s->count}, Total: {$s->total_amount}\n";
    }

    $outRecords = $db->table('jpb_auto_ro_out')->get();
    echo "\n=== Auto-RO Out Table (Count: ".$outRecords->count().") ===\n";
    foreach ($outRecords->take(5) as $out) {
        echo "- ID: {$out->id}, Member ID: {$out->id_member}, Amount: {$out->amount}, Status: {$out->status}, Date: {$out->datecreated}\n";
    }

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
