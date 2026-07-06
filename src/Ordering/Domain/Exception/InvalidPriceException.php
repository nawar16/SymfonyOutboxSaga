<?php

namespace App\Ordering\Domain\Exception;

use DomainException;

final class InvalidPriceException extends DomainException
{
    public function __construct(int $price)
    {
        parent::__construct("Invalid price '{$price}'. Price cannot be negative");
    }
}