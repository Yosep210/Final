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
        public readonly ?int $city_id,
        public readonly string $name,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            city_id: isset($normalized['city_id']) && $normalized['city_id'] !== null ? (int) $normalized['city_id'] : null,
            name: isset($normalized['name']) && $normalized['name'] !== null ? (string) $normalized['name'] : '',
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
            'city_id' => isset($data['city_id']) && $data['city_id'] !== null ? (int) $data['city_id'] : null,
            'name' => isset($data['name']) && $data['name'] !== null ? trim((string) $data['name']) : '',
        ];
    }
}
