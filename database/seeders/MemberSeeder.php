<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 active members
        Member::factory(10)->active()->create();

        // Create 5 suspended members
        Member::factory(5)->suspended()->create();

        // Create 3 inactive members
        Member::factory(3)->inactive()->create();

        // Create 2 members with 2FA enabled
        Member::factory(2)->active()->withTwoFactor()->create();
    }
}
