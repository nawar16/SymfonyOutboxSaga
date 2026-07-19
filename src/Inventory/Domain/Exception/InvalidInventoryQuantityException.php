<?php

namespace App\Inventory\Domain\Exception;

use InvalidArgumentException;

final class InvalidInventoryQuantityException extends InvalidArgumentException
{
    public function __construct(int $quantity)
    {
        parent::__construct(sprintf('Inventory quantity "%d" is invalid',$quantity));
    }
}