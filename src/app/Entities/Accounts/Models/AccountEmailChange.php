<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Support\Facades\URL;

/**
 * App\Entities\Accounts\Models\AccountEmailChange
 *
 * @property int $account_email_change_id
 * @property int $account_id
 * @property string $token
 * @property string $email_previous
 * @property string $email_new
 * @property mixed $is_previous_confirmed
 * @property mixed $is_new_confirmed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Accounts\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereAccountEmailChangeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereEmailNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereEmailPrevious($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereIsNewConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereIsPreviousConfirmed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountEmailChange whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountEmailChange extends Model
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

    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->belongsTo('App\Entities\Accounts\Models\Account', 'account_id', 'account_id');
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
