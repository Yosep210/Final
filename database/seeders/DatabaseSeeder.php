<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionsSeeder::class);
        $this->call(MemberSeeder::class);
        $this->call(VillagesSeeder::class);
    }
}
