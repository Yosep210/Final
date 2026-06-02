<?php

namespace Database\Seeders;

use App\Models\Member;
use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    use HasSourceConnection;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->configureSourceConnection();

        $this->command?->info('Seeding staff members from legacy jpb_staff...');
        $sourceStaff = DB::connection('latihan')
            ->table('jpb_staff')
            ->orderBy('id')
            ->get();

        if ($sourceStaff->isEmpty()) {
            $this->command?->warn('No staff found in latihan.jpb_staff.');

            return;
        }

        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');

        $now = Carbon::now();

        foreach ($sourceStaff as $source) {
            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;

            // Find or create in members table by username
            $memberId = DB::table('members')->where('username', $source->username)->value('id');

            $status = $source->status == 1 ? 'active' : 'inactive';

            if ($memberId) {
                // Update existing member record
                DB::table('members')->where('id', $memberId)->update([
                    'name' => $source->name,
                    'email' => $source->email,
                    'password' => $source->password,
                    'status' => $status,
                    'updated_at' => $updatedAt,
                ]);
            } else {
                // Insert new member record without forcing the ID to avoid collision with members
                $memberId = DB::table('members')->insertGetId([
                    'name' => $source->name,
                    'username' => $source->username,
                    'email' => $source->email,
                    'password' => $source->password,
                    'status' => $status,
                    'email_verified_at' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);
            }

            // Create or update member profile details
            DB::table('member_profile')->updateOrInsert(
                ['member_id' => $memberId],
                [
                    'member_id' => $memberId,
                    'phone' => $source->phone ?: null,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );

            // Assign Spatie Admin role
            if ($adminRoleId) {
                DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => $adminRoleId,
                        'model_type' => Member::class,
                        'model_id' => $memberId,
                    ]
                );
            }

            $this->command?->info("Staff {$source->username} (ID: {$memberId}) imported successfully.");
        }
    }
}
