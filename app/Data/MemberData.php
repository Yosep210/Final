<?php

namespace App\Data;

use App\Models\Member;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class MemberData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $password,
        public readonly string $status,
        public readonly ?string $referralCode,
        public readonly ?string $emailVerifiedAt,
        public readonly ?string $lastLoginAt,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            name: (string) $normalized['name'],
            username: (string) $normalized['username'],
            email: (string) $normalized['email'],
            password: $normalized['password'] !== null ? (string) $normalized['password'] : null,
            status: (string) $normalized['status'],
            referralCode: $normalized['referralCode'] !== null ? (string) $normalized['referralCode'] : null,
            emailVerifiedAt: $normalized['emailVerifiedAt'] !== null ? (string) $normalized['emailVerifiedAt'] : null,
            lastLoginAt: $normalized['lastLoginAt'] !== null ? (string) $normalized['lastLoginAt'] : null,
        );
    }

    public static function fromModel(Member $member): self
    {
        return new self(
            name: (string) $member->name,
            username: (string) $member->username,
            email: (string) $member->email,
            password: null,
            status: (string) $member->status,
            referralCode: $member->referral_code !== null ? (string) $member->referral_code : null,
            emailVerifiedAt: $member->email_verified_at?->format('Y-m-d H:i:s'),
            lastLoginAt: $member->last_login_at?->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|null>
     */
    protected static function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'username' => trim((string) ($data['username'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'password' => isset($data['password']) && $data['password'] !== '' ? (string) $data['password'] : null,
            'status' => trim((string) ($data['status'] ?? '')),
            'referralCode' => isset($data['referral_code']) && $data['referral_code'] !== ''
                ? trim((string) $data['referral_code'])
                : null,
            'emailVerifiedAt' => isset($data['email_verified_at']) && $data['email_verified_at'] !== ''
                ? trim((string) $data['email_verified_at'])
                : null,
            'lastLoginAt' => isset($data['last_login_at']) && $data['last_login_at'] !== ''
                ? trim((string) $data['last_login_at'])
                : null,
        ];
    }
}
