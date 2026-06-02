<?php

use Illuminate\Contracts\Console\Kernel;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
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
        'collation' => 'utf8mb4_unicode_ci',
    ],
]);

try {
    $packages = DB::connection('latihan')->table('jpb_product_package')->get();
    foreach ($packages as $pkg) {
        echo "ID: {$pkg->id} | Name: {$pkg->name} | Price: {$pkg->price} | Qty: {$pkg->total_qty}\n";
    }
} catch (Exception $e) {
    echo 'Error connecting to latihan: '.$e->getMessage()."\n";
}
