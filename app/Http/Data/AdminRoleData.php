<?php

namespace App\Http\Data;

use App\Models\Role;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AdminRoleData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $alias,
        public bool $is_default,
        public bool $is_admin,
        public ?string $minecraft_name,
        public ?string $minecraft_display_name,
        public ?string $minecraft_hover_text,
        public ?int $display_priority,
        public ?string $role_type,
        public ?int $additional_homes,
        public Optional|int|null $accounts_count,
        public Optional|int|null $permissions_count,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            alias: $role->alias,
            is_default: $role->is_default,
            is_admin: $role->is_admin,
            minecraft_name: $role->minecraft_name,
            minecraft_display_name: $role->minecraft_display_name,
            minecraft_hover_text: $role->minecraft_hover_text,
            display_priority: $role->display_priority,
            role_type: $role->role_type,
            additional_homes: $role->additional_homes,
            accounts_count: isset($role->accounts_count) ? (int) $role->accounts_count : Optional::create(),
            permissions_count: isset($role->permissions_count) ? (int) $role->permissions_count : Optional::create(),
        );
    }
}
