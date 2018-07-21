<?php
namespace Application\Modules\Accounts\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'account_id'    => $this->id,
            'email'         => $this->email,
            'last_login_at' => $this->last_login_at,
            'created_at'    => $this->created_at->timestamp,
            'updated_at'    => $this->updated_at->timestamp,
        ];
    }
}
