<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\Permission\Models\Permission;

#[MapName(SnakeCaseMapper::class)]
class PermissionData extends Data
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
            name: $normalized['name'],
            guardName: $normalized['guard_name'],
        );
    }

    public static function fromModel(Permission $permission): self
    {
        return self::from($permission);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    protected static function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'guard_name' => trim((string) ($data['guard_name'] ?? '')),
        ];
    }
}
