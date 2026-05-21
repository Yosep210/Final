<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date?->toIso8601String(),
            'phone' => $this->phone,
            'profile_photo' => $this->profile_photo,
            'country_id' => $this->country_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'village_id' => $this->village_id,
            'address' => $this->address,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
