<?php

namespace App\Data;

use App\Models\Pin;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class PinData extends Data
{
    public function __construct(
        public readonly string $serialNumber,
        public readonly string $pinCode,
        public readonly string $status,
        public readonly ?int $ownerId,
        public readonly ?int $activatedMemberId,
        public readonly ?string $activatedAt,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            serialNumber: (string) $normalized['serial_number'],
            pinCode: (string) $normalized['pin_code'],
            status: (string) $normalized['status'],
            ownerId: $normalized['owner_id'] !== null ? (int) $normalized['owner_id'] : null,
            activatedMemberId: $normalized['activated_member_id'] !== null ? (int) $normalized['activated_member_id'] : null,
            activatedAt: $normalized['activated_at'] !== null ? (string) $normalized['activated_at'] : null,
        );
    }

    public static function fromModel(Pin $pin): self
    {
        return self::from($pin);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int|string|null>
     */
    protected static function normalize(array $data): array
    {
        return [
            'serial_number' => trim((string) ($data['serial_number'] ?? '')),
            'pin_code' => trim((string) ($data['pin_code'] ?? '')),
            'status' => trim((string) ($data['status'] ?? 'unused')),
            'owner_id' => isset($data['owner_id']) && $data['owner_id'] !== '' ? (int) $data['owner_id'] : null,
            'activated_member_id' => isset($data['activated_member_id']) && $data['activated_member_id'] !== '' ? (int) $data['activated_member_id'] : null,
            'activated_at' => isset($data['activated_at']) && $data['activated_at'] !== '' ? trim((string) $data['activated_at']) : null,
        ];
    }
}
