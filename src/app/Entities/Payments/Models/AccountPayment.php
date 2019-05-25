<?php

namespace App\Entities\Payments\Models;

use App\Model;
use App\Entities\Accounts\Models\Account;

/**
 * App\Entities\Payments\Models\AccountPayment
 *
 * @property int $account_payment_id
 * @property string $payment_type What the payment was for: donation, purchase, etc
 * @property int $payment_id Id in its corresponding table
 * @property float $payment_amount
 * @property string $payment_source
 * @property int|null $account_id
 * @property mixed $is_processed
 * @property mixed $is_refunded
 * @property mixed $is_subscription_payment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Entities\Accounts\Models\Account $account
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $associated
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereAccountPaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereIsProcessed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereIsRefunded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereIsSubscriptionPayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment wherePaymentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment wherePaymentSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\Payments\Models\AccountPayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccountPayment extends Model
{
    protected $table = 'account_payments';
    protected $primaryKey = 'donation_id';

    protected $fillable = [
        'payment_type',
        'payment_id',
        'payment_amount',
        'payment_source',
        'account_id',
        'is_processed',
        'is_refunded',
        'is_subscription_payment',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

    public function associated()
    {
        return $this->morphTo('payment_id', 'payment_type');
    }
}
