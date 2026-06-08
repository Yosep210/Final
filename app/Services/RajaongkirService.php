<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaongkirService
{
    /**
     * Fetch shipping cost from Rajaongkir API with automatic mock fallback on failure.
     */
    public function getCost(int $origin, string $originType, int $destination, string $destinationType, int $weight, string $courier): array
    {
        $url = config('mlm.shipping.rajaongkir_url', 'https://rajaongkir.komerce.id/api/v1/') . 'cost';
        $token = config('mlm.shipping.rajaongkir_token', '14086d4d07f3a24feff8a2fad320d909');
        $active = config('mlm.shipping.rajaongkir_active', true);

        if ($active) {
            try {
                $response = Http::withHeaders([
                    'key' => $token,
                ])
                ->asForm()
                ->post($url, [
                    'origin' => $origin,
                    'originType' => $originType,
                    'destination' => $destination,
                    'destinationType' => $destinationType,
                    'weight' => $weight,
                    'courier' => $courier,
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['rajaongkir']['results'])) {
                        return $json['rajaongkir']['results'];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Rajaongkir cost API query failed, triggering mock fallback', ['error' => $e->getMessage()]);
            }
        }

        // Return mock shipping costs if API is disabled or fails
        return $this->getMockCosts($destination, $weight, $courier);
    }

    /**
     * Generate simulated shipping costs based on destination city ID.
     */
    protected function getMockCosts(int $destination, int $weight, string $courier): array
    {
        $weightInKg = max(1.0, ceil($weight / 1000.0));
        
        // Base flat rates by city ID
        $baseRate = 12000.0; // Java/Local region default
        
        if ($destination > 350) {
            $baseRate = 45000.0; // Far regions (e.g. Papua/Maluku)
        } elseif ($destination > 150) {
            $baseRate = 22000.0; // Mid regions (e.g. Sumatra/Sulawesi)
        }

        $costVal = $baseRate * $weightInKg;

        return [
            [
                'code' => $courier,
                'name' => strtoupper($courier),
                'costs' => [
                    [
                        'service' => 'REG',
                        'description' => 'Layanan Reguler (Simulasi)',
                        'cost' => [
                            [
                                'value' => $costVal,
                                'etd' => '2-4 Hari',
                                'note' => '',
                            ]
                        ]
                    ],
                    [
                        'service' => 'OKE',
                        'description' => 'Layanan Ekonomis (Simulasi)',
                        'cost' => [
                            [
                                'value' => max(9000.0, $costVal - 4000.0),
                                'etd' => '4-6 Hari',
                                'note' => '',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
