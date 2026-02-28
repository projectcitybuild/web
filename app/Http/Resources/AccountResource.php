<?php

namespace App\Http\Resources;

use App\Core\Utilities\Redact;
use App\Domains\Permissions\AuthorizesPermissions;
use App\Domains\Permissions\WebManagePermission;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    use AuthorizesPermissions;

    public function toArray($request)
    {
        $canViewEmail = $this->can(WebManagePermission::ACCOUNTS_VIEW_EMAIL);

        return [
            'account_id' => $this->getKey(),
            'username' => $this->username,
            'email' => $canViewEmail
                ? $this->email
                : Redact::email($this->email),
            'activated' => $this->activated,
            'terms_accepted' => $this->terms_accepted,
            'last_login_at' => $this->last_login_at,
            'is_totp_enabled' => $this->is_totp_enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'roles' => $this->whenLoaded('roles'),
            'badges' => $this->whenLoaded('badges'),
            'minecraft_account' => $this->whenLoaded('minecraftAccount'),
            'donations' => $this->whenLoaded('donations'),
            'email_change_requests' => $this->whenLoaded('emailChangeRequests'),
            'activations' => $this->whenLoaded('activations'),
        ];
    }
}
