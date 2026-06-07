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

            // Assign roles & permissions dynamically based on legacy data
            $member = Member::find($memberId);
            if ($member) {
                $isAllAccess = (strtolower($source->access) === 'all');

                if ($isAllAccess) {
                    $member->syncRoles(['Admin']);
                    $member->syncPermissions([]); // Admins get all access via Gate before filter
                } else {
                    $member->syncRoles(['Staff']);

                    // Parse serialized role (access IDs)
                    $permissionsToSync = [];
                    $legacyRoles = @unserialize($source->role);
                    if (is_array($legacyRoles)) {
                        $mapping = [
                            1 => 'access-member-new',
                            2 => 'access-member-list',
                            3 => 'access-member-sponsor',
                            4 => 'access-member-generation',
                            5 => 'access-member-tree',
                            6 => 'access-member-edit',
                            7 => 'access-member-reset-password',
                            8 => 'access-member-assume',
                            9 => 'access-stockist-new',
                            10 => 'access-stockist-list',
                            11 => 'access-stockist-stock',
                            12 => 'access-stockist-transfer',
                            13 => 'access-finance-bonus',
                            14 => 'access-finance-statement',
                            15 => 'access-finance-ewallet',
                            16 => 'access-finance-autoro',
                            17 => 'access-finance-withdraw',
                            18 => 'access-finance-withdraw-transfer',
                            19 => 'access-eproduct',
                            20 => 'access-pin-generate',
                            21 => 'access-pin-stock',
                            22 => 'access-pin-transfer',
                            23 => 'access-pin-order',
                            24 => 'access-pin-order-stockist',
                            25 => 'access-report-registration',
                            26 => 'access-report-upgrade',
                            27 => 'access-report-ro',
                            28 => 'access-report-pairing',
                            29 => 'access-report-omzet-posting',
                            30 => 'access-report-omzet-posting-daily',
                            31 => 'access-report-omzet-posting-monthly',
                            32 => 'access-report-omzet-order',
                            33 => 'access-report-budgeting',
                            34 => 'access-report-reward',
                            35 => 'access-master-product',
                            36 => 'access-master-supplier',
                            37 => 'access-master-purchase',
                            38 => 'access-master-stock',
                            39 => 'access-master-stock-opname',
                            40 => 'access-setting-staff',
                            41 => 'access-setting-general',
                            42 => 'access-setting-notification',
                            43 => 'access-setting-reward',
                            44 => 'access-setting-withdraw',
                            45 => 'access-setting-flip',
                            46 => 'access-setting-budget',
                            47 => 'access-setting-subsidi-ongkir',
                            48 => 'access-news',
                            49 => 'access-report-tax',
                            50 => 'access-flip',
                            51 => 'access-linkita',
                            52 => 'access-setting-linkita',
                            53 => 'access-eprofit',
                            54 => 'access-eshipping',
                            55 => 'access-report-activation',
                            56 => 'access-stockist-adjustment',
                        ];

                        foreach ($legacyRoles as $legacyRoleId) {
                            if (isset($mapping[(int) $legacyRoleId])) {
                                $permissionsToSync[] = $mapping[(int) $legacyRoleId];
                            }
                        }
                    }

                    $member->syncPermissions($permissionsToSync);
                }
            }

            $this->command?->info("Staff {$source->username} (ID: {$memberId}) imported and permissions synced successfully.");
        }
    }
}
