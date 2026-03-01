<?php

namespace App\Models;

use App\Core\Utilities\Traits\HasStaticTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Payment extends Model
{
    use HasFactory;
    use HasStaticTable;

    protected $table = 'payments';
    protected $fillable = [
        'account_id',
        'stripe_price',
        'stripe_product',
        'paid_currency',
        'paid_unit_amount',
        'original_currency',
        'original_unit_amount',
        'unit_quantity',
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(
            related: Account::class,
            foreignKey: 'account_id',
        );
    }
}
