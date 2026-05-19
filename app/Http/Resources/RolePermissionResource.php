<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolePermissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'role_id' => $this->role_id,
            'permission_id' => $this->permission_id,
            'role' => [
                'id' => $this->role_id,
                'name' => $this->role?->name,
            ],
            'permission' => [
                'id' => $this->permission_id,
                'name' => $this->permission?->name,
            ],
        ];
    }
}
