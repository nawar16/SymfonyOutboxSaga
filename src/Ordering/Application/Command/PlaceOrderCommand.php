<?php

namespace App\Ordering\Application\Command;

final class PlaceOrderCommand
{
    private string $customerId;
    /** @var array<array{product_id: string, quantity: int}> */
    private array $items;

    /** @param array<array{product_id: string, quantity: int}> $items */
    public function __construct(string $customerId, array $items)
    {
        $this->customerId = $customerId;
        $this->items = $items;
    }

    public function getCustomerId(): string { return $this->customerId; }
    /** @return array<array{product_id: string, quantity: int}> */
    public function getItems(): array { return $this->items; }
}
