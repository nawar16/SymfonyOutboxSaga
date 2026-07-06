<?php

namespace App\Ordering\Domain\Exception;

use DomainException;

final class DuplicateOrderItemException extends DomainException
{
    public function __construct(string $productId)
    {
        parent::__construct("Product '{$productId}' is already in the order");
    }
}