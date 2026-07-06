<?php

namespace App\Ordering\Domain\Event;

final class OrderPlaced
{
    public function __construct(
        private string $orderId,
        private string $customerId,
        private int $totalAmount,
        /** @var array<int, array{productId: string, quantity: int}> */
        private array $items,
        private \DateTimeImmutable $occurredAt = new \DateTimeImmutable()
    ) {}

    public function getOrderId(): string { return $this->orderId; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getTotalAmount(): int { return $this->totalAmount; }
    public function getOccurredAt(): \DateTimeImmutable { return $this->occurredAt; }
    /** @return array<int, array{productId: string, quantity: int}> */
    public function getItems(): array { return $this->items; }
}
