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
    // Get all records ordered by ID desc so we get the latest record first
    $records = DB::connection('latihan')
        ->table('jpb_pairing_qualified')
        ->orderBy('id', 'desc')
        ->get();

    $latestByMember = [];
    foreach ($records as $r) {
        $memberId = (int) $r->id_member;
        if (! isset($latestByMember[$memberId])) {
            $latestByMember[$memberId] = $r;
        }
    }

    $countWithLeft = 0;
    $countWithRight = 0;
    $totalLeft = 0;
    $totalRight = 0;
    $countWithBoth = 0;

    foreach ($latestByMember as $memberId => $r) {
        $left = (float) $r->carry_left;
        $right = (float) $r->carry_right;

        if ($left > 0) {
            $countWithLeft++;
            $totalLeft += $left;
        }
        if ($right > 0) {
            $countWithRight++;
            $totalRight += $right;
        }
        if ($left > 0 && $right > 0) {
            $countWithBoth++;
        }
    }

    echo 'Total members with carry records: '.count($latestByMember)."\n";
    echo "Members with left carry > 0: $countWithLeft (Sum: $totalLeft)\n";
    echo "Members with right carry > 0: $countWithRight (Sum: $totalRight)\n";
    echo "Members with both carry > 0: $countWithBoth\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
