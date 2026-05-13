<?php

namespace App\Domain\Country\Data;

use App\Models\Country;

final class CountryData
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
        $iso = strtoupper(trim((string) $data['iso']));
        $name = trim((string) $data['name']);
        $niceName = trim((string) $data['nice_name']);
        $iso3 = isset($data['iso3']) && $data['iso3'] !== null && $data['iso3'] !== ''
            ? strtoupper(trim((string) $data['iso3']))
            : null;
        $numcode = isset($data['numcode']) && $data['numcode'] !== null && $data['numcode'] !== ''
            ? (int) $data['numcode']
            : null;

        return new self(
            iso: $iso,
            name: $name,
            niceName: $niceName,
            iso3: $iso3,
            numcode: $numcode,
            phonecode: $data['phonecode'],
            status: $data['status']
        );
    }

    /**
     * Create a DTO from the Country model.
     */
    public static function fromModel(Country $country): self
    {
        return new self(
            iso: $country->iso,
            name: $country->name,
            niceName: $country->nice_name,
            iso3: $country->iso3,
            numcode: $country->numcode,
            phonecode: $country->phonecode,
            status: $country->status
        );
    }

    /**
     * @return array<string, string|int|bool|null>
     */
    public function toArray(): array
    {
        return [
            'iso' => $this->iso,
            'name' => $this->name,
            'nice_name' => $this->niceName,
            'iso3' => $this->iso3,
            'numcode' => $this->numcode,
            'phonecode' => $this->phonecode,
            'status' => $this->status,
        ];
    }
}
