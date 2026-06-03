<?php

require 'C:/Users/LENOVO/Project/Layout/Final/vendor/autoload.php';
$app = require_once 'C:/Users/LENOVO/Project/Layout/Final/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$filename = 'bca.png';
$path = public_path("assets/img/bank/{$filename}");
echo "path: $path\n";
echo "exists: " . (file_exists($path) ? 'YES' : 'NO') . "\n";

$path2 = public_path("assets/img/bank/mandiri.png");
echo "mandiri exists: " . (file_exists($path2) ? 'YES' : 'NO') . "\n";
