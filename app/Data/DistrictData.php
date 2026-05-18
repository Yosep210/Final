<?php

namespace App\Data;

use App\Models\District;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class DistrictData extends Data
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?int $city_id,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            id: isset($normalized['id']) && $normalized['id'] !== null ? (int) $normalized['id'] : null,
            name: isset($normalized['name']) && $normalized['name'] !== null ? (string) $normalized['name'] : '',
            city_id: isset($normalized['city_id']) && $normalized['city_id'] !== null ? (int) $normalized['city_id'] : null,
        );
    }

    public static function fromModel(District $district): self
    {
        return self::from($district);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int|string|null>
     */
    protected static function normalize(array $data): array
    {
        return [
            'id' => isset($data['id']) && $data['id'] !== null ? (int) $data['id'] : null,
            'name' => isset($data['name']) && $data['name'] !== null ? trim((string) $data['name']) : '',
            'city_id' => isset($data['city_id']) && $data['city_id'] !== null ? (int) $data['city_id'] : null,
        ];
    }
}
