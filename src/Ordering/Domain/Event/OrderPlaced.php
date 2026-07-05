<?php

namespace App\Ordering\Domain\Event;

final class OrderPlaced
{
    private string $orderId;
    private string $customerId;
    private string $inventoryHoldId;
    private int $totalAmount;
    private \DateTimeImmutable $occurredAt;

    public function __construct(string $orderId, string $customerId, string $inventoryHoldId, int $totalAmount)
    {
        $this->orderId = $orderId;
        $this->customerId = $customerId;
        $this->inventoryHoldId = $inventoryHoldId;
        $this->totalAmount = $totalAmount;
        $this->occurredAt = new \DateTimeImmutable();
    }
    public function getOrderId(): string { return $this->orderId; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getInventoryHoldId(): string { return $this->inventoryHoldId; }
    public function getTotalAmount(): int { return $this->totalAmount; }
    public function getOccurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
