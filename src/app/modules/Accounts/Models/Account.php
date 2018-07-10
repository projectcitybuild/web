<?php

namespace App\Modules\Accounts\Models;

use App\Support\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class Account extends Authenticatable {
    use Notifiable;

    protected $table = 'accounts';

    protected $primaryKey = 'account_id';

    protected $fillable = [
        'email',
        'password',
        'remember_token',
        'last_login_ip',
        'last_login_at',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_login_at',
    ];

    public function minecraftAccount() {
        return $this->belongsTo('App\Modules\Players\Models\MinecraftPlayer', 'account_id', 'account_id');
    }

    public function linkedSocialAccounts() {
        return $this->hasMany('App\Modules\Accounts\Models\AccountLink', 'account_id', 'account_id');
    }

    /**
     * Gets an URL to the 'email change verification' 
     * route with a signed signature to prevent 
     * tampering
     *
     * @return string
     */
    public function getEmailChangeVerificationUrl(string $newEmail) : string {
        return URL::temporarySignedRoute('front.account.settings.email.confirm', now()->addMinutes(15), [
            'old_email' => $this->email,
            'new_email' => $newEmail,
        ]);
    }

}
