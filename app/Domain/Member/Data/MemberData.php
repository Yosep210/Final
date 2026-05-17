<?php

namespace App\Domain\Member\Data;

use App\Models\Member;
use DateTime;
use DateTimeInterface;

final class MemberData
{
    public function __construct(
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $password,
        public readonly string $status,
        public readonly ?string $referralCode,
        public readonly ?DateTimeInterface $emailVerifiedAt,
        public readonly ?DateTimeInterface $lastLoginAt
    ) {}

    public static function fromModel(Member $member): self
    {
        return new self(
            name: $member->name,
            username: $member->username,
            email: (string) $member->email,
            password: null,
            status: $member->status,
            referralCode: $member->referral_code,
            emailVerifiedAt: $member->email_verified_at,
            lastLoginAt: $member->last_login_at
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) $data['name']),
            username: trim((string) $data['username']),
            email: strtolower(trim((string) $data['email'])),
            password: isset($data['password']) && $data['password'] !== null && $data['password'] !== ''
                ? (string) $data['password']
                : null,
            status: trim((string) $data['status']),
            referralCode: isset($data['referral_code']) && $data['referral_code'] !== null && $data['referral_code'] !== ''
                ? trim((string) $data['referral_code'])
                : null,
            emailVerifiedAt: isset($data['email_verified_at']) && $data['email_verified_at'] !== null && $data['email_verified_at'] !== ''
                ? new DateTime((string) $data['email_verified_at'])
                : null,
            lastLoginAt: isset($data['last_login_at']) && $data['last_login_at'] !== null && $data['last_login_at'] !== ''
                ? new DateTime((string) $data['last_login_at'])
                : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'status' => $this->status,
            'referral_code' => $this->referralCode,
            'email_verified_at' => $this->emailVerifiedAt?->format('Y-m-d H:i:s'),
            'last_login_at' => $this->lastLoginAt?->format('Y-m-d H:i:s'),
        ];

        if ($this->password !== null) {
            $payload['password'] = $this->password;
        }

        return $payload;
    }
}
