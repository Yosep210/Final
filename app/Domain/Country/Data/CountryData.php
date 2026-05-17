<?php

namespace App\Domain\Country\Data;

use App\Models\Country;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class CountryData extends Data
{
    public function __construct(
        public readonly string $iso,
        public readonly string $name,
        public readonly string $niceName,
        public readonly ?string $iso3,
        public readonly ?int $numcode,
        public readonly int $phonecode,
        public readonly bool $status
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            iso: (string) $normalized['iso'],
            name: (string) $normalized['name'],
            niceName: (string) $normalized['niceName'],
            iso3: $normalized['iso3'] !== null ? (string) $normalized['iso3'] : null,
            numcode: $normalized['numcode'] !== null ? (int) $normalized['numcode'] : null,
            phonecode: (int) $normalized['phonecode'],
            status: (bool) $normalized['status'],
        );
    }

    /**
     * Create a DTO from the Country model.
     */
    public static function fromModel(Country $country): self
    {
        return self::from($country);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|int|bool|null>
     */
    protected static function normalize(array $data): array
    {
        return [
            'iso' => strtoupper(trim((string) ($data['iso'] ?? ''))),
            'name' => trim((string) ($data['name'] ?? '')),
            'niceName' => trim((string) ($data['nice_name'] ?? $data['niceName'] ?? '')),
            'iso3' => isset($data['iso3']) && $data['iso3'] !== null && $data['iso3'] !== ''
                ? strtoupper(trim((string) $data['iso3']))
                : null,
            'numcode' => isset($data['numcode']) && $data['numcode'] !== null && $data['numcode'] !== ''
                ? (int) $data['numcode']
                : null,
            'phonecode' => (int) ($data['phonecode'] ?? 0),
            'status' => (bool) ($data['status'] ?? false),
        ];
    }
}
