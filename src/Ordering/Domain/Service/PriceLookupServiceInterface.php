<?php

namespace App\Ordering\Domain\Service;

interface PriceLookupServiceInterface
{
    /**
     * @param string $productId
     * @return int Price in cents
     * @throws \InvalidArgumentException If the product does not exist
     */
    public function getLivePrice(string $productId): int;
}
