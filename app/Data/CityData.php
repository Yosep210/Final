<?php

namespace App\Data;

use App\Models\City;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CityData extends Data
{
    public function __construct(
        public ?int $provinceId = null,
        public ?string $name = null,
        public ?string $type = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            provinceId: isset($normalized['province_id']) && $normalized['province_id'] !== null ? (int) $normalized['province_id'] : null,
            name: isset($normalized['name']) && $normalized['name'] !== null ? (string) $normalized['name'] : null,
            type: isset($normalized['type']) && $normalized['type'] !== null ? (string) $normalized['type'] : null,
        );
    }

    public static function fromModel(City $city): self
    {
        return self::from($city);
    }

    protected static function normalize(array $data): array
    {
        return [
            'province_id' => isset($data['province_id']) && $data['province_id'] !== null ? (int) $data['province_id'] : null,
            'name' => isset($data['name']) && $data['name'] !== null ? trim((string) $data['name']) : null,
            'type' => isset($data['type']) && $data['type'] !== null ? trim((string) $data['type']) : null,
        ];
    }
}
