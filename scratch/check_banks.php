<?php

require 'C:/Users/LENOVO/Project/Layout/Final/vendor/autoload.php';
$app = require_once 'C:/Users/LENOVO/Project/Layout/Final/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Bank;

try {
    $banks = Bank::all();
    echo "=== Current Banks in Database (Count: " . $banks->count() . ") ===\n";
    foreach ($banks as $b) {
        echo "- ID: {$b->id}, Name: {$b->name}, Code: {$b->code}, Logo: {$b->logo}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
