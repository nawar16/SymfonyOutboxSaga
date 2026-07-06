<?php

namespace App\Ordering\Infrastructure\Service;

use App\Ordering\Domain\Service\PriceLookupServiceInterface;

final class PriceLookupService implements PriceLookupServiceInterface
{
    public function getLivePrice(string $productId): int
    {
        //TODO: handle the temp price 
        return random_int(100, 5000);
    }
}