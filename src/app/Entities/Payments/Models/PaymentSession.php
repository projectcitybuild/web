<?php

namespace App\Entities\Payments\Models;

use App\Model;

final class PaymentSession extends Model
{
    protected $table = 'payment_session';
    protected $primaryKey = 'payment_session_id';

    protected $fillable = [
        'external_session_id',
        'data',
        'expires_at',
    ];

    protected $dates = [
        'expires_at',
    ];

}