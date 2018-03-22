<?php

namespace App\Modules\Accounts\Models;

use App\Shared\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\URL;

class UnactivatedAccount extends Model {

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
    public function getActivationUrl() : string {
        return URL::temporarySignedRoute('front.register.activate', now()->addDay(), [
            'email' => $this->email,
        ]);
    }

}
