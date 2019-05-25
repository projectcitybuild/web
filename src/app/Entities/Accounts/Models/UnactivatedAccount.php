<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;

/**
 * App\Entities\Accounts\Models\UnactivatedAccount
 *
 * @property int $unactivated_account_id
 * @property string $email
 * @property string $password
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount whereUnactivatedAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\UnactivatedAccount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UnactivatedAccount extends Model
{
    use Notifiable;

    protected $table = 'accounts_unactivated';

    protected $primaryKey = 'unactivated_account_id';

    protected $fillable = [
        'email',
        'password',
    ];
    
    protected $hidden = [
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Gets an URL to the activation route with a
     * signed signature to prevent tampering
     *
     * @return string
     */
    public function getActivationUrl() : string
    {
        return URL::temporarySignedRoute('front.register.activate', now()->addDay(), [
            'email' => $this->email,
        ]);
    }
}
