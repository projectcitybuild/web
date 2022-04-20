<?php

namespace Domain\Donations\Repositories;

use Entities\Models\Eloquent\StripeProduct;

/**
 * @final
 */
class StripeProductRepository
{
    public function first(string $productId, string $priceId): ?StripeProduct
    {
        return StripeProduct::where('product_id', $productId)
            ->where('price_id', $priceId)
            ->first();
    }
}
