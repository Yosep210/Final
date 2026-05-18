<?php

namespace App\Data;

use App\Models\Province;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ProvinceData extends Data
{
    public function __construct(
        public readonly int $countryId,
        public readonly string $name,
        public readonly ?string $code = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            countryId: $normalized['country_id'],
            name: $normalized['name']
        );
    }

    public static function fromModel(Province $province): self
    {
        return self::from($province);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int|string>
     */
    protected static function normalize(array $data): array
    {
        return [
            'country_id' => (int) ($data['country_id'] ?? 0),
            'name' => trim((string) ($data['name'] ?? '')),
            'code' => isset($data['code']) ? trim((string) $data['code']) : null,
        ];
    }
}
