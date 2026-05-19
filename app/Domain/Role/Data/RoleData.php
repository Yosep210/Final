<?php

namespace App\Domain\Role\Data;

use App\Models\Role;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RoleData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $guard_name,
    ) {}

    public static function fromArray(array $data): self
    {
        $normalized = self::normalize($data);

        return new self(
            name: $normalized['name'],
            guard_name: $normalized['guard_name'],
        );
    }

    public static function fromModel(Role $role): self
    {
        return self::from($role);
    }

    protected static function normalize(array $data): array
    {
        return [
            'name' => trim((string) ($data['name'] ?? '')),
            'guard_name' => trim((string) ($data['guard_name'] ?? '')),
        ];
    }
}
