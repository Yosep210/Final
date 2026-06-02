<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VideoSeeder extends Seeder
{
    use HasSourceConnection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->configureSourceConnection();

        $now = Carbon::now();
        $this->command?->info('Seeding educational videos...');

        $sourceVideos = DB::connection('latihan')
            ->table('jpb_video')
            ->orderBy('id')
            ->get();

        foreach ($sourceVideos as $source) {
            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            DB::table('videos')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'title' => $source->title,
                    'url' => $source->url,
                    'sequence' => (int) $source->sequence,
                    'image' => $source->image,
                    'status' => (bool) $source->status,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }
    }
}
