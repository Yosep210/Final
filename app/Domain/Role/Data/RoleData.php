<?php

namespace App\Domain\Role\Data;

use App\Models\Role;

class RoleData
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $guard_name
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) $data['name']),
            guard_name: trim((string) $data['guard_name'])
        );
    }

    /**
     * Create a DTO from the Role model.
     */
    public static function fromModel(Role $role): self
    {
        return new self(
            name: $role->name,
            guard_name: $role->guard_name
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ];
    }
}
