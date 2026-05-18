<?php

namespace App\Data;

use App\Models\Village;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class VillageData extends Data
{
    public function __construct(
        public readonly ?int $districtId,
        public readonly ?string $name,
        public readonly ?string $postalCode,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            districtId: isset($normalized['district_id']) && $normalized['district_id'] !== null ? (int) $normalized['district_id'] : null,
            name: isset($normalized['name']) && $normalized['name'] !== null ? trim((string) $normalized['name']) : null,
            postalCode: isset($normalized['postal_code']) && $normalized['postal_code'] !== null ? trim((string) $normalized['postal_code']) : null,
        );
    }

    public static function fromModel(Village $village): self
    {
        return self::from($village);
    }

    protected static function normalize(array $data): array
    {
        return [
            'district_id' => isset($data['district_id']) && $data['district_id'] !== null ? (int) $data['district_id'] : null,
            'name' => isset($data['name']) && $data['name'] !== null ? trim((string) $data['name']) : null,
            'postal_code' => isset($data['postal_code']) && $data['postal_code'] !== null ? trim((string) $data['postal_code']) : null,
        ];
    }
}
