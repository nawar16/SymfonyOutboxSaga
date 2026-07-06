<?php

namespace App\Ordering\Domain\Exception;

use DomainException;

final class InvalidQuantityException extends DomainException
{
    public function __construct(int $quantity)
    {
        parent::__construct("Invalid quantity '{$quantity}'. Quantity must be greater than 0");
    }
}