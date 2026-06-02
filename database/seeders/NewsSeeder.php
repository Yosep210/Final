<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NewsSeeder extends Seeder
{
    use HasSourceConnection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->configureSourceConnection();

        $now = Carbon::now();
        $this->command?->info('Seeding news and announcements...');

        $sourceNews = DB::connection('latihan')
            ->table('jpb_news')
            ->orderBy('id')
            ->get();

        foreach ($sourceNews as $source) {
            $createdAt = $source->created_at ? Carbon::parse($source->created_at) : $now;
            $updatedAt = $source->updated_at ? Carbon::parse($source->updated_at) : $createdAt;

            DB::table('news')->updateOrInsert(
                ['id' => $source->id],
                [
                    'id' => $source->id,
                    'title' => $source->title,
                    'slug' => $source->slug ?: str($source->title)->slug()->value(),
                    'content' => $source->content,
                    'mime_type' => $source->mime_type,
                    'url' => $source->url,
                    'status' => in_array($source->status, ['publish', 'draft', 'delete']) ? $source->status : 'publish',
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }
    }
}
