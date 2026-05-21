<?php

namespace App\Data;

use App\Models\MemberProfile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class MemberProfileData extends Data
{
    public function __construct(
        public readonly int $memberId,
        public readonly ?string $gender,
        public readonly ?string $birthDate,
        public readonly ?string $phone,
        public readonly ?string $profilePhoto,
        public readonly ?int $countryId,
        public readonly ?int $provinceId,
        public readonly ?int $cityId,
        public readonly ?int $districtId,
        public readonly ?int $villageId,
        public readonly ?string $address,
    ) {}

    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            memberId: (int) $normalized['memberId'],
            gender: $normalized['gender'] !== null ? (string) $normalized['gender'] : null,
            birthDate: $normalized['birthDate'] !== null ? (string) $normalized['birthDate'] : null,
            phone: $normalized['phone'] !== null ? (string) $normalized['phone'] : null,
            profilePhoto: $normalized['profilePhoto'] !== null ? (string) $normalized['profilePhoto'] : null,
            countryId: $normalized['countryId'] !== null ? (int) $normalized['countryId'] : null,
            provinceId: $normalized['provinceId'] !== null ? (int) $normalized['provinceId'] : null,
            cityId: $normalized['cityId'] !== null ? (int) $normalized['cityId'] : null,
            districtId: $normalized['districtId'] !== null ? (int) $normalized['districtId'] : null,
            villageId: $normalized['villageId'] !== null ? (int) $normalized['villageId'] : null,
            address: $normalized['address'] !== null ? (string) $normalized['address'] : null,
        );
    }

    public static function fromModel(MemberProfile $profile): self
    {
        return new self(
            memberId: (int) $profile->member_id,
            gender: $profile->gender !== null ? (string) $profile->gender : null,
            birthDate: $profile->birth_date?->format('Y-m-d'),
            phone: $profile->phone !== null ? (string) $profile->phone : null,
            profilePhoto: $profile->profile_photo !== null ? (string) $profile->profile_photo : null,
            countryId: $profile->country_id !== null ? (int) $profile->country_id : null,
            provinceId: $profile->province_id !== null ? (int) $profile->province_id : null,
            cityId: $profile->city_id !== null ? (int) $profile->city_id : null,
            districtId: $profile->district_id !== null ? (int) $profile->district_id : null,
            villageId: $profile->village_id !== null ? (int) $profile->village_id : null,
            address: $profile->address !== null ? (string) $profile->address : null,
        );
    }

    protected static function normalize(array $data): array
    {
        return [
            'memberId' => (int) $data['member_id'],
            'gender' => isset($data['gender']) && $data['gender'] !== '' ? (string) $data['gender'] : null,
            'birthDate' => isset($data['birth_date']) && $data['birth_date'] !== '' ? (string) $data['birth_date'] : null,
            'phone' => isset($data['phone']) && $data['phone'] !== '' ? (string) $data['phone'] : null,
            'profilePhoto' => isset($data['profile_photo']) && $data['profile_photo'] !== '' ? (string) $data['profile_photo'] : null,
            'countryId' => isset($data['country_id']) && $data['country_id'] !== '' ? (int) $data['country_id'] : null,
            'provinceId' => isset($data['province_id']) && $data['province_id'] !== '' ? (int) $data['province_id'] : null,
            'cityId' => isset($data['city_id']) && $data['city_id'] !== '' ? (int) $data['city_id'] : null,
            'districtId' => isset($data['district_id']) && $data['district_id'] !== '' ? (int) $data['district_id'] : null,
            'villageId' => isset($data['village_id']) && $data['village_id'] !== '' ? (int) $data['village_id'] : null,
            'address' => isset($data['address']) && $data['address'] !== '' ? (string) $data['address'] : null,
        ];
    }
}
