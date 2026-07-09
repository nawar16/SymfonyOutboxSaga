<?php

namespace App\Ordering\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use Symfony\Component\Uid\Uuid;

final class OrderPlaced implements DomainEvent
{
    private string $eventId;
    public function __construct(
        private string $orderId,
        private string $customerId,
        private int $totalAmount,
        /** @var array<int, array{productId: string, quantity: int}> */
        private array $items,
        private \DateTimeImmutable $occurredAt = new \DateTimeImmutable()
    ) {
        $this->eventId = Uuid::v4()->toRfc4122();
    }
    public function getEventId(): string {return $this->eventId;}
    public function getOrderId(): string { return $this->orderId; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getTotalAmount(): int { return $this->totalAmount; }
    public function getOccurredAt(): \DateTimeImmutable { return $this->occurredAt; }
    /** @return array<int, array{productId: string, quantity: int}> */
    public function getItems(): array { return $this->items; }
}
