<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\Permission\Models\Role;

#[MapName(SnakeCaseMapper::class)]
class RoleData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $guardName,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            name: (string) $normalized['name'],
            guardName: (string) $normalized['guardName'],
        );
    }

    public static function fromModel(Role $role): self
    {
        return new self(
            name: (string) $role->name,
            guardName: (string) $role->guard_name,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    protected static function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'guardName' => trim((string) ($data['guard_name'] ?? $data['guardName'] ?? '')),
        ];
    }
}
