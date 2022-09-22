<?php

namespace Entities\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

final class ShowcaseWarpResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'creators' => $this->creators,
            'location_world' => $this->location_world,
            'location_x' => $this->location_x,
            'location_y' => $this->location_y,
            'location_z' => $this->location_z,
            'location_pitch' => $this->location_pitch,
            'location_yaw' => $this->location_yaw,
            'built_at' => $this->built_at->getTimestamp(),
            'created_at' => $this->created_at->getTimestamp(),
            'updated_at' => $this->updated_at->getTimestamp(),
        ];
    }
}
