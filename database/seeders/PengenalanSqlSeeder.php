<?php

namespace Database\Seeders;

use App\Models\Member;
use Database\Seeders\Concerns\HasSourceConnection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PengenalanSqlSeeder extends Seeder
{
    use HasSourceConnection;

    public function run(): void
    {
        $this->configureSourceConnection();
        $memberRoleId = DB::table('roles')->where('name', 'Member')->value('id');
        $stockistRoleId = DB::table('roles')->where('name', 'Stockist')->value('id');
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
                    'type' => (int) ($source->as_stockist ?? 0),
                    'wd_status' => max(0, min(2, (int) ($source->wd_status ?? 0))),
                    'wd_min' => max(0, (int) ($source->wd_min ?? 0)),
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

            // Seed Member Bank Details if they exist
            if (! empty($source->bill) && ! empty($source->bill_name)) {
                $bankName = '';
                if (! empty($source->bank)) {
                    $bankName = DB::connection('latihan')
                        ->table('jpb_banks')
                        ->where('id', $source->bank)
                        ->value('nama') ?: '';
                }

                if (empty($bankName)) {
                    $bankName = 'UNKNOWN';
                }

                DB::table('member_banks')->updateOrInsert(
                    ['member_id' => $memberId],
                    [
                        'member_id' => $memberId,
                        'bank_name' => $bankName,
                        'account_number' => $source->bill,
                        'account_holder' => $source->bill_name,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                        'deleted_at' => null,
                    ]
                );
            }

            if ($memberRoleId) {
                DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => $memberRoleId,
                        'model_type' => Member::class,
                        'model_id' => $memberId,
                    ]
                );
            }

            if (($source->as_stockist ?? 0) > 0 && $stockistRoleId) {
                DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => $stockistRoleId,
                        'model_type' => Member::class,
                        'model_id' => $memberId,
                    ]
                );
            }
        }

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
            $uniqueEmail = "{$localPart}+{$memberId}-".substr((string) $memberId, 0, 3)."@{$domain}";
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
