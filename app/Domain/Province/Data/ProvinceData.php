<?php

namespace App\Domain\Province\Data;

class ProvinceData
{
    public function __construct(
        public readonly int $countryId,
        public readonly string $name,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryId: (int) $data['country_id'],
            name: trim((string) $data['name']),
        );
    }

    /**
     * Create a DTO from the Province model.
     */
    public static function fromModel(Province $province): self
    {
        return new self(
            countryId: (int) $province->country_id,
            name: $province->name,
        );
    }

    /**
     * @return array<string, string|int|bool|null>
     */
    public function toArray(): array
    {
        return [
            'country_id' => $this->countryId,
            'name' => $this->name,
        ];
    }
}
