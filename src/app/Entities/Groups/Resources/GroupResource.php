<?php
namespace App\Entities\Groups\Resources;

use Illuminate\Http\Resources\Json\Resource;

class GroupResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'group_id'      => $this->group_id,
            'name'          => $this->name,
            'alias'         => $this->alias,
            'is_default'    => $this->is_default,
            'is_staff'      => $this->is_staff,
            'is_admin'      => $this->is_admin,
        ];
    }
}
