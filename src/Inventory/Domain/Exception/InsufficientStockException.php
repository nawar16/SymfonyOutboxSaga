<?php

namespace App\Inventory\Domain\Exception;

final class InsufficientStockException extends \DomainException
{
    public function __construct(string $productId)
    {
        parent::__construct(sprintf('Insufficient stock for product "%s".',$productId));
    }
}