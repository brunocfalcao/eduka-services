<?php

namespace Eduka\Services\Concerns;

use Eduka\Cube\Models\Product;

trait InteractsWithAffiliate
{
    public function addProduct(Product $product, bool $notify = false)
    {
        $this->product()->associate($product)->save();
    }
}
