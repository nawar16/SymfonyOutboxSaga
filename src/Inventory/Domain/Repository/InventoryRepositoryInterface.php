<?php

namespace App\Inventory\Domain\Repository;

use App\Inventory\Domain\Entity\InventoryItem;

interface InventoryRepositoryInterface
{
    public function findByProductId(string $productId): ?InventoryItem;
    public function save(InventoryItem $item): void;
}
