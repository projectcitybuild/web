<?php

namespace App\Http\Data;

use App\Models\Role;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AdminAccountRoleData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $alias,
        public bool $is_default,
        public bool $is_admin,
        public ?string $minecraft_name,
        public string $minecraft_display_name,
        public string $minecraft_hover_text,
        public ?int $display_priority,
        public ?string $role_type,
        public ?int $additional_homes,
        public Optional|AdminAccountRolePivotData|null $pivot,
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
            pivot: $role->pivot === null ? Optional::create() : AdminAccountRolePivotData::fromModel($role->pivot),
        );
    }
}
