<?php

namespace App\Inventory\Domain\Repository;

use App\Inventory\Domain\Entity\InventoryReservation;

interface InventoryReservationRepositoryInterface
{
    public function findByOrderId(string $orderId): array;
    public function save(InventoryReservation $reservation): void;
}