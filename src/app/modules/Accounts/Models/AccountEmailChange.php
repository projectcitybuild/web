<?php

namespace App\Modules\Accounts\Models;

use App\Core\Model;
use Illuminate\Support\Facades\URL;

class AccountEmailChange extends Model {

    protected $table = 'account_email_changes';

    protected $primaryKey = 'account_email_change_id';

    protected $fillable = [
        'account_id',
        'token',
        'email_previous',
        'email_new',
        'is_previous_confirmed',
        'is_new_confirmed',
    ];

    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account() {
        return $this->belongsTo('App\Modules\Accounts\Models\Account', 'account_id', 'account_id');
    }

    public function getCurrentEmailUrl(int $expiryInMins = 20) {
        return URL::temporarySignedRoute('front.password-reset.recovery', now()->addMinutes($expiryInMins), [
            'token' => $this->token,
            'email' => $this->email_previous,
        ]);
    }

    public function getNewEmailUrl(int $expiryInMins = 20) {
        return URL::temporarySignedRoute('front.password-reset.recovery', now()->addMinutes($expiryInMins), [
            'token' => $this->token,
            'email' => $this->email_new,
        ]);
    }

}
