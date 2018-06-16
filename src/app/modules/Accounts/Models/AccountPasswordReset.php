<?php

namespace App\Modules\Accounts\Models;

use App\Core\Model;
use Illuminate\Support\Facades\URL;

class AccountPasswordReset extends Model {

    protected $table = 'account_password_resets';

    protected $primaryKey = 'email';
    
    public $incrementing = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
    ];

    public $timestamps = false;

    public function getPasswordResetUrl() {
        return URL::temporarySignedRoute('front.password-reset.recovery', now()->addMinutes(20), [
            'token' => $this->token,
        ]);
    }

}
