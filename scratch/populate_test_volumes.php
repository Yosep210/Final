<?php

require 'C:/Users/LENOVO/Project/Layout/Final/vendor/autoload.php';
$app = require_once 'C:/Users/LENOVO/Project/Layout/Final/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

use App\Models\MemberNetwork;
use App\Services\MemberRankService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

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
    echo "1. Fetching carry forward volumes from reference database...\n";
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

    echo "2. Setting carry-forward volumes for members in database...\n";
    $updatedCount = 0;
    foreach ($latestByMember as $memberId => $r) {
        $network = MemberNetwork::where('member_id', $memberId)->first();
        if ($network) {
            $network->left_volume = (float) $r->carry_left;
            $network->right_volume = (float) $r->carry_right;
            $network->total_volume = (float) ($r->carry_left + $r->carry_right);
            $network->save();
            $updatedCount++;
        }
    }
    echo "Updated $updatedCount member networks with carry-forward volumes.\n";

    echo "3. Simulating new volume propagation up the tree...\n";
    // We will choose 5 random active members who are deep in the tree and add new registration volumes under them.
    // This will propagate volumes up the tree to create matched pairing volumes for their ancestors!
    $deepNetworks = MemberNetwork::query()
        ->where('generation', '>', 5)
        ->limit(5)
        ->get();

    if ($deepNetworks->isEmpty()) {
        $deepNetworks = MemberNetwork::query()->limit(5)->get();
    }

    $rankService = app(MemberRankService::class);
    foreach ($deepNetworks as $net) {
        $amount = 15000.0; // 15,000 BV to ensure it triggers capping and Auto-RO
        $side = rand(0, 1) === 0 ? 'left' : 'right';
        echo "- Propagating $amount BV on the {$side} side of member ID {$net->member_id} (username: {$net->member->username})...\n";

        $rankService->propagateVolume($net, $amount, $side);
    }

    echo "Done! Test volumes populated and propagated successfully.\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}
