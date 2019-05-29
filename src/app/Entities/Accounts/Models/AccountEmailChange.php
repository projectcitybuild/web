<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Support\Facades\URL;

final class AccountEmailChange extends Model
{
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


    public function account()
    {
        return $this->belongsTo(Account::class, Account::COLUMN_ACCOUNT_ID, Account::COLUMN_ACCOUNT_ID);
    }

    public function getCurrentEmailUrl(int $expiryInMins = 20)
    {
        return URL::temporarySignedRoute('front.account.settings.email.confirm', now()->addMinutes($expiryInMins), [
            'token' => $this->token,
            'email' => $this->email_previous,
        ]);
    }

    public function getNewEmailUrl(int $expiryInMins = 20)
    {
        return URL::temporarySignedRoute('front.account.settings.email.confirm', now()->addMinutes($expiryInMins), [
            'token' => $this->token,
            'email' => $this->email_new,
        ]);
    }
}
