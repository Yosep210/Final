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
        $this->call(PengenalanSqlSeeder::class);
        $this->call(CommissionSystemSeeder::class);
        $this->call(ProductOrderSeeder::class);
        $this->call(StaffSeeder::class);
        $this->call(VillagesSeeder::class);
        $this->call(BankSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(RewardConfigSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(VideoSeeder::class);
    }
}
