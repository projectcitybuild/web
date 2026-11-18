<?php

namespace App\Http\Data;

use App\Models\Permission;
use Spatie\LaravelData\Data;

class AdminPermissionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
        );
    }
}
