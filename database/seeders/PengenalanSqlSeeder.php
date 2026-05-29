<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PengenalanSqlSeeder extends Seeder
{
    public function run(): void
    {
        $this->configureSourceConnection();
        $seenEmails = [];
        $seenIdCards = [];
        $seenNpwp = [];

        $sourceMembers = DB::connection('latihan')
            ->table('jpb_member')
            ->orderBy('id')
            ->get();

        if ($sourceMembers->isEmpty()) {
            $this->command?->warn('No rows found in latihan.jpb_member.');

            return;
        }

        $now = Carbon::now();

        foreach ($sourceMembers as $source) {
            $memberId = (int) $source->id;
            $createdAt = $source->datecreated ? Carbon::parse($source->datecreated) : $now;
            $updatedAt = $source->datemodified ? Carbon::parse($source->datemodified) : $createdAt;
            $lastLoginAt = $source->last_login ? Carbon::parse($source->last_login) : null;

            $memberStatus = match ((int) $source->status) {
                1 => 'active',
                2 => 'suspended',
                3 => 'inactive',
                default => 'inactive',
            };

            $memberName = trim((string) $source->name);
            $memberUsername = trim((string) $source->username);
            $memberEmail = $this->makeUniqueEmail(trim((string) $source->email), $memberUsername, $memberId, $seenEmails);

            DB::table('members')->updateOrInsert(
                ['id' => $memberId],
                [
                    'id' => $memberId,
                    'name' => $memberName,
                    'username' => $memberUsername,
                    'email' => $memberEmail,
                    'password' => Hash::make('password'),
                    'status' => $memberStatus,
                    'referral_code' => $source->referral_code ?: null,
                    'email_verified_at' => $memberStatus === 'active' ? $createdAt : null,
                    'last_login_at' => $lastLoginAt,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => null,
                ]
            );

            DB::table('member_profile')->updateOrInsert(
                ['member_id' => $memberId],
                [
                    'member_id' => $memberId,
                    'gender' => $this->normalizeGender($source->gender),
                    'birth_date' => $source->birthdate,
                    'id_card_number' => $this->makeUniqueNullableString($source->idcard, 'idcard', $memberUsername, $memberId, $seenIdCards),
                    'npwp_number' => $this->makeUniqueNullableString($source->npwp, 'npwp', $memberUsername, $memberId, $seenNpwp),
                    'phone' => $this->normalizeNullableString($source->phone),
                    'country_id' => $this->mapCountryId($source->country),
                    'province_id' => null,
                    'city_id' => null,
                    'district_id' => null,
                    'village_id' => null,
                    'address' => $source->address,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => null,
                ]
            );

            DB::table('member_networks')->updateOrInsert(
                ['member_id' => $memberId],
                [
                    'member_id' => $memberId,
                    'sponsored_id' => $this->mapMaybeInt($source->sponsor),
                    'parent_id' => $this->mapMaybeInt($source->parent),
                    'position' => in_array($source->position, ['left', 'right'], true) ? $source->position : null,
                    'path' => $this->buildPath($source->tree),
                    'generation' => (int) ($source->gen ?? 0),
                    'group' => (int) ($source->group ?? 0),
                    'rank' => (int) ($source->level ?? 0),
                    'left_volume' => 0,
                    'right_volume' => 0,
                    'total_volume' => (float) ($source->total_omzet ?? 0),
                    'qualified_legs' => 0,
                    'current_rank' => $this->normalizeRank($source->rank),
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                    'deleted_at' => null,
                ]
            );
        }

    }

    private function configureSourceConnection(): void
    {
        config([
            'database.connections.latihan' => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => 'latihan',
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'unix_socket' => env('DB_SOCKET', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => false,
                'engine' => null,
                'options' => extension_loaded('pdo_mysql') ? array_filter([
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
                ]) : [],
            ],
        ]);
    }

    private function normalizeGender(?string $value): ?string
    {
        $gender = strtolower(trim((string) $value));

        return match ($gender) {
            'male', 'laki-laki', 'laki laki', 'pria' => 'male',
            'female', 'perempuan', 'wanita' => 'female',
            default => null,
        };
    }

    private function normalizeRank(?string $value): ?string
    {
        $rank = strtolower(trim((string) $value));

        return $rank !== '' ? $rank : null;
    }

    private function mapCountryId(?string $countryCode): ?int
    {
        $countryCode = strtolower(trim((string) $countryCode));

        if ($countryCode === '') {
            return null;
        }

        return DB::table('countries')->where('iso', $countryCode)->value('id');
    }

    private function mapMaybeInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $intValue = (int) $value;

        return $intValue > 0 ? $intValue : null;
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function makeUniqueEmail(string $email, string $username, int $memberId, array &$seenEmails): string
    {
        $email = strtolower(trim($email));

        if ($email === '') {
            $email = "{$username}+{$memberId}@latihan.local";
        }

        if (! isset($seenEmails[$email])) {
            $seenEmails[$email] = true;

            return $email;
        }

        $parts = explode('@', $email, 2);
        $localPart = $parts[0] ?? $username;
        $domain = $parts[1] ?? 'latihan.local';
        $uniqueEmail = "{$localPart}+{$memberId}@{$domain}";

        while (isset($seenEmails[$uniqueEmail])) {
            $uniqueEmail = "{$localPart}+{$memberId}-" . substr((string) $memberId, 0, 3) . "@{$domain}";
        }

        $seenEmails[$uniqueEmail] = true;

        return $uniqueEmail;
    }

    private function makeUniqueNullableString(mixed $value, string $field, string $username, int $memberId, array &$seenValues): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (! isset($seenValues[$value])) {
            $seenValues[$value] = true;

            return $value;
        }

        $uniqueValue = "{$value}-{$field}-{$username}-{$memberId}";
        while (isset($seenValues[$uniqueValue])) {
            $uniqueValue .= "-{$memberId}";
        }

        $seenValues[$uniqueValue] = true;

        return $uniqueValue;
    }

    private function buildPath(?string $tree): ?string
    {
        $tree = trim((string) $tree);

        return $tree !== '' ? $tree : null;
    }
}
