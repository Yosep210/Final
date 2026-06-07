<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan Semua Peran Utama (Spatie Roles)
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $staff = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $stockist = Role::firstOrCreate(['name' => 'Stockist', 'guard_name' => 'web']);
        $member = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);

        // 2. Definisikan Semua Peran Peringkat (Rank Roles)
        $rankMember = Role::firstOrCreate(['name' => 'rank:member', 'guard_name' => 'web']);
        $rankStar = Role::firstOrCreate(['name' => 'rank:star', 'guard_name' => 'web']);

        // 3. Hak Akses CI3 (56 Permissions)
        $permissions = [
            'access-member-new',
            'access-member-list',
            'access-member-sponsor',
            'access-member-generation',
            'access-member-tree',
            'access-member-edit',
            'access-member-reset-password',
            'access-member-assume',
            'access-stockist-new',
            'access-stockist-list',
            'access-stockist-stock',
            'access-stockist-transfer',
            'access-finance-bonus',
            'access-finance-statement',
            'access-finance-ewallet',
            'access-finance-autoro',
            'access-finance-withdraw',
            'access-finance-withdraw-transfer',
            'access-eproduct',
            'access-pin-generate',
            'access-pin-stock',
            'access-pin-transfer',
            'access-pin-order',
            'access-pin-order-stockist',
            'access-report-registration',
            'access-report-upgrade',
            'access-report-ro',
            'access-report-pairing',
            'access-report-omzet-posting',
            'access-report-omzet-posting-daily',
            'access-report-omzet-posting-monthly',
            'access-report-omzet-order',
            'access-report-budgeting',
            'access-report-reward',
            'access-master-product',
            'access-master-supplier',
            'access-master-purchase',
            'access-master-stock',
            'access-master-stock-opname',
            'access-setting-staff',
            'access-setting-general',
            'access-setting-notification',
            'access-setting-reward',
            'access-setting-withdraw',
            'access-setting-flip',
            'access-setting-budget',
            'access-setting-subsidi-ongkir',
            'access-news',
            'access-report-tax',
            'access-flip',
            'access-linkita',
            'access-setting-linkita',
            'access-eprofit',
            'access-eshipping',
            'access-report-activation',
            'access-stockist-adjustment',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // 4. Sinkronisasikan Izin ke Masing-Masing Role

        // ADMIN: Memiliki seluruh hak akses di sistem
        $admin->syncPermissions($permissions);

        // STAFF: Mengelola administrasi member, produk, verifikasi finansial & laporan
        $staff->syncPermissions([
            'access-member-list',
            'access-member-sponsor',
            'access-member-generation',
            'access-member-tree',
            'access-member-edit',
            'access-member-reset-password',
            'access-member-assume',
            'access-stockist-list',
            'access-stockist-stock',
            'access-stockist-transfer',
            'access-finance-bonus',
            'access-finance-statement',
            'access-finance-ewallet',
            'access-finance-autoro',
            'access-finance-withdraw',
            'access-finance-withdraw-transfer',
            'access-eproduct',
            'access-pin-generate',
            'access-pin-stock',
            'access-pin-transfer',
            'access-pin-order',
            'access-pin-order-stockist',
            'access-report-registration',
            'access-report-upgrade',
            'access-report-ro',
            'access-report-pairing',
            'access-report-omzet-posting-daily',
            'access-report-omzet-posting-monthly',
            'access-report-omzet-order',
            'access-report-budgeting',
            'access-report-reward',
            'access-report-tax',
            'access-report-activation',
            'access-news',
        ]);

        // STOKIS: Mengelola stok produk lokal, mengirim PIN, mendaftarkan member baru, dan melihat jaringan
        $stockist->syncPermissions([
            'access-member-new',
            'access-member-tree',
            'access-stockist-stock',
            'access-stockist-transfer',
            'access-stockist-adjustment',
            'access-pin-order-stockist',
        ]);

        // MEMBER: Mengirim PIN miliknya, request withdrawal, transfer ewallet, melihat jaringan, dan mendaftarkan downline
        $member->syncPermissions([
            'access-member-new',
            'access-member-tree',
            'access-pin-stock',
            'access-pin-transfer',
            'access-finance-ewallet',
            'access-finance-withdraw',
        ]);

        // RANK MEMBER: Hak akses dasar member
        $rankMember->syncPermissions([
            'access-member-tree',
            'access-finance-withdraw',
        ]);

        // RANK STAR: Mendapatkan hak akses tambahan (misal: fitur-fitur laporan/pencairan prioritas)
        $rankStar->syncPermissions([
            'access-member-tree',
            'access-finance-withdraw',
            'access-report-registration',
        ]);
    }
}
