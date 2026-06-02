<?php

namespace App\Data;

use App\Models\MemberBank;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class MemberBankData extends Data
{
    public function __construct(
        public readonly ?int $memberId,
        public readonly string $bankName,
        public readonly string $accountNumber,
        public readonly string $accountHolder,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            memberId: $normalized['member_id'] !== null ? (int) $normalized['member_id'] : null,
            bankName: (string) $normalized['bank_name'],
            accountNumber: (string) $normalized['account_number'],
            accountHolder: (string) $normalized['account_holder'],
        );
    }

    public static function fromModel(MemberBank $bank): self
    {
        return self::from($bank);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int|string|null>
     */
    protected static function normalize(array $data): array
    {
        return [
            'member_id' => isset($data['member_id']) && $data['member_id'] !== '' ? (int) $data['member_id'] : null,
            'bank_name' => trim((string) ($data['bank_name'] ?? '')),
            'account_number' => trim((string) ($data['account_number'] ?? '')),
            'account_holder' => trim((string) ($data['account_holder'] ?? '')),
        ];
    }
}
