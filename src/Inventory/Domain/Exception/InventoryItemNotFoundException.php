<?php

namespace App\Inventory\Domain\Exception;

final class InventoryItemNotFoundException extends \DomainException
{
    public function __construct(string $productId)
    {
        parent::__construct(sprintf('Inventory item "%s" was not found',$productId));
    }
}