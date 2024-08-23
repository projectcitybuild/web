<?php

namespace Repositories;

use App\Models\StripeProduct;

/**
 * @deprecated
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
