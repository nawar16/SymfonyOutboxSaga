<?php

namespace App\Ordering\Application\Command;

final class PlaceOrderCommand
{
    private string $customerId;
    private string $inventoryHoldId; // for saga & 2step stock
    /** @var array<array{product_id: string, quantity: int}> */
    private array $items;

    /** @param array<array{product_id: string, quantity: int}> $items */
    public function __construct(string $customerId, string $inventoryHoldId, array $items)
    {
        $this->customerId = $customerId;
        $this->inventoryHoldId = $inventoryHoldId;
        $this->items = $items;
    }

    public function getCustomerId(): string { return $this->customerId; }
    public function getInventoryHoldId(): string { return $this->inventoryHoldId; }
    /** @return array<array{product_id: string, quantity: int}> */
    public function getItems(): array { return $this->items; }
}
