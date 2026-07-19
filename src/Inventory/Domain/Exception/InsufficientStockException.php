<?php

namespace App\Inventory\Domain\Exception;

final class InsufficientStockException extends \DomainException
{
    public function __construct(string $productId,int $requested,int $available)
    {
        parent::__construct(sprintf('Insufficient stock for product "%s". Requested: %d, Available: %d.',$productId,$requested,$available));
    }
}