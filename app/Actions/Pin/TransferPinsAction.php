<?php

namespace App\Actions\Pin;

use App\Models\Pin;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class TransferPinsAction
{
    use AsAction;

    /**
     * Transfer unused owned PINs to another member.
     */
    public function handle(array $serialNumbers, int $fromOwnerId, int $toOwnerId): int
    {
        return DB::transaction(function () use ($serialNumbers, $fromOwnerId, $toOwnerId) {
            return Pin::whereIn('serial_number', $serialNumbers)
                ->where('owner_id', $fromOwnerId)
                ->where('status', 'unused')
                ->update([
                    'owner_id' => $toOwnerId,
                ]);
        });
    }
}
