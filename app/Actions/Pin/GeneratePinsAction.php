<?php

namespace App\Actions\Pin;

use App\Models\Pin;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;

class GeneratePinsAction
{
    use AsAction;

    /**
     * Generate N new PINs and assign them to an optional owner.
     *
     * @return array<Pin>
     */
    public function handle(int $count, ?int $ownerId = null): array
    {
        $pins = [];

        for ($i = 0; $i < $count; $i++) {
            $serial = $this->generateUniqueSerial();
            $code = strtoupper(Str::random(10));

            $pins[] = Pin::query()->create([
                'serial_number' => $serial,
                'pin_code' => $code,
                'status' => 'unused',
                'owner_id' => $ownerId,
            ]);
        }

        return $pins;
    }

    private function generateUniqueSerial(): string
    {
        do {
            $serial = 'SN'.strtoupper(Str::random(10));
        } while (Pin::where('serial_number', $serial)->exists());

        return $serial;
    }
}
