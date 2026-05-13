<?php

namespace App\Domain\Country\Data;

use App\Models\Country;

class CountryData
{
    public function __construct(
        public readonly string $iso,
        public readonly string $name,
        public readonly string $niceName,
        public readonly ?string $iso3, // Bisa null
        public readonly ?string $numcode, // Bisa null
        public readonly int $phonecode, // Mengubah ke int
        public readonly bool $status
    ) {}

    /**
     * Membuat DTO dari array request.
     *
     * @param  array<string, mixed>  $data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            iso: $data['iso'],
            name: $data['name'],
            niceName: $data['nice_name'],
            iso3: $data['iso3'],
            numcode: $data['numcode'],
            phonecode: $data['phonecode'],
            status: $data['status']
        );
    }

    /**
     * Membuat DTO dari instance model Country.
     */
    public static function fromModel(Country $country): self
    {
        return new self(
            iso: $country->iso,
            name: $country->name,
            niceName: $country->nice_name,
            iso3: $country->iso3,
            numcode: (string) $country->numcode, // Konversi int ke string untuk DTO
            phonecode: $country->phonecode,
            status: (bool) $country->status
        );
    }

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
