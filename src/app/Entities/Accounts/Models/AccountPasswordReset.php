<?php

namespace App\Entities\Accounts\Models;

use App\Model;
use Illuminate\Support\Facades\URL;

/**
 * App\Entities\Accounts\Models\AccountPasswordReset
 *
 * @property string $email
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountPasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountPasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountPasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountPasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountPasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Accounts\Models\AccountPasswordReset whereToken($value)
 * @mixin \Eloquent
 */
class AccountPasswordReset extends Model
{
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

    public function getPasswordResetUrl()
    {
        return URL::temporarySignedRoute('front.password-reset.edit', now()->addMinutes(20), [
            'token' => $this->token,
        ]);
    }
}
