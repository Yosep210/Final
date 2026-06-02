<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    use HasSourceConnection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->configureSourceConnection();

        $this->command?->info('Seeding master banks...');
        $sourceBanks = DB::connection('latihan')
            ->table('jpb_banks')
            ->orderBy('id')
            ->get();

        foreach ($sourceBanks as $source) {
            DB::table('banks')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'name' => $source->nama,
                    'code' => $source->kode,
                    'type' => $source->type ?: 'bank',
                    'flipcode' => $source->flipcode,
                    'espaycode' => $source->espaycode,
                    'linkitacode' => $source->linkitacode,
                    'logo' => $source->logo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Append new digital banks and e-wallets
        $this->command?->info('Appending modern digital banks and e-wallets...');
        $newBanks = [
            [
                'id' => 100,
                'name' => 'BANK JAGO',
                'code' => '542',
                'type' => 'bank',
                'flipcode' => 'jago',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 101,
                'name' => 'SEABANK',
                'code' => '535',
                'type' => 'bank',
                'flipcode' => 'seabank',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 102,
                'name' => 'ALLO BANK',
                'code' => '567',
                'type' => 'bank',
                'flipcode' => 'allo',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 103,
                'name' => 'BANK NEO COMMERCE',
                'code' => '490',
                'type' => 'bank',
                'flipcode' => 'neo',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 104,
                'name' => 'BCA DIGITAL (blu)',
                'code' => '501',
                'type' => 'bank',
                'flipcode' => 'bca_digital',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 105,
                'name' => 'GOPAY',
                'code' => 'GPY',
                'type' => 'ewallet',
                'flipcode' => 'gopay',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 106,
                'name' => 'OVO',
                'code' => 'OVO',
                'type' => 'ewallet',
                'flipcode' => 'ovo',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 107,
                'name' => 'DANA',
                'code' => 'DAN',
                'type' => 'ewallet',
                'flipcode' => 'dana',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 108,
                'name' => 'LINKAJA',
                'code' => 'LAJ',
                'type' => 'ewallet',
                'flipcode' => 'linkaja',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
            [
                'id' => 109,
                'name' => 'SHOPEEPAY',
                'code' => 'SPY',
                'type' => 'ewallet',
                'flipcode' => 'shopeepay',
                'espaycode' => null,
                'linkitacode' => null,
                'logo' => null,
            ],
        ];

        foreach ($newBanks as $newBank) {
            DB::table('banks')->updateOrInsert(
                ['id' => $newBank['id']],
                array_merge($newBank, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
