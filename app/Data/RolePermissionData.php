<?php

namespace App\Data;

use App\Models\RolePermission;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RolePermissionData extends Data
{
    public function __construct(
        public readonly int $roleId,
        public readonly int $permissionId,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            roleId: $normalized['role_id'],
            permissionId: $normalized['permission_id'],
        );
    }

    public static function fromModel(RolePermission $rolePermission): self
    {
        return self::from($rolePermission);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, int>
     */
    protected static function normalize(array $data): array
    {
        return [
            'role_id' => isset($data['role_id']) ? (int) $data['role_id'] : 0,
            'permission_id' => isset($data['permission_id']) ? (int) $data['permission_id'] : 0,
        ];
    }
}
