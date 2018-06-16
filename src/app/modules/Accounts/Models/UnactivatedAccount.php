<?php

namespace App\Modules\Accounts\Models;

use App\Core\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notifiable;

class UnactivatedAccount extends Model {
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
    public function getActivationUrl() : string {
        return URL::temporarySignedRoute('front.register.activate', now()->addDay(), [
            'email' => $this->email,
        ]);
    }

}
