<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RewardConfigSeeder extends Seeder
{
    use HasSourceConnection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->configureSourceConnection();

        $now = Carbon::now();
        $this->command?->info('Seeding MLM reward configs...');

        $sourceRewards = DB::connection('latihan')
            ->table('jpb_reward_config')
            ->orderBy('id')
            ->get();

        foreach ($sourceRewards as $source) {
            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            DB::table('reward_configs')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'type' => $source->type,
                    'reward' => $source->reward,
                    'nominal' => (float) $source->nominal,
                    'point' => (int) $source->point,
                    'packages' => $source->packages,
                    'rank' => $source->rank,
                    'message' => $source->message,
                    'start_date' => $source->start_date,
                    'end_date' => $source->end_date,
                    'is_lifetime' => (bool) $source->is_lifetime,
                    'is_active' => (bool) $source->is_active,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }
    }
}
