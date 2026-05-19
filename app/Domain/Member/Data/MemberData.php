<?php

namespace App\Domain\Member\Data;

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
        public readonly ?string $referral_code,
        public readonly ?string $email_verified_at,
        public readonly ?string $last_login_at,
    ) {}

    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            name: $normalized['name'],
            username: $normalized['username'],
            email: $normalized['email'],
            password: $normalized['password'],
            status: $normalized['status'],
            referral_code: $normalized['referral_code'],
            email_verified_at: $normalized['email_verified_at'],
            last_login_at: $normalized['last_login_at'],
        );
    }

    public static function fromModel(Member $member): self
    {
        return self::from($member);
    }

    protected static function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'username' => trim((string) ($data['username'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'password' => isset($data['password']) && $data['password'] !== '' ? (string) $data['password'] : null,
            'status' => trim((string) ($data['status'] ?? '')),
            'referral_code' => isset($data['referral_code']) ? trim((string) $data['referral_code']) : null,
            'email_verified_at' => isset($data['email_verified_at']) && $data['email_verified_at'] !== '' ? trim((string) $data['email_verified_at']) : null,
            'last_login_at' => isset($data['last_login_at']) && $data['last_login_at'] !== '' ? trim((string) $data['last_login_at']) : null,
        ];
    }
}
