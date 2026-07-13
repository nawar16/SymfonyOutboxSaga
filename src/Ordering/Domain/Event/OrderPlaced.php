<?php

namespace App\Ordering\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class OrderPlaced implements DomainEvent
{
    private string $eventId;
    private DateTimeImmutable $occurredAt;
    public function __construct(
        private string $orderId,
        private string $customerId,
        private int $totalAmount,
        /** @var array<int, array{productId: string, quantity: int}> */
        private array $items,
        ?DateTimeImmutable $occurredAt = null
    ) {
        $this->eventId = Uuid::v4()->toRfc4122();
        $this->items = $items;
        $this->occurredAt = $occurredAt ?? new DateTimeImmutable();
    }
    public function getEventId(): string {return $this->eventId;}
    public function getOrderId(): string { return $this->orderId; }
    public function getCustomerId(): string { return $this->customerId; }
    public function getTotalAmount(): int { return $this->totalAmount; }
    public function getOccurredAt(): DateTimeImmutable { return $this->occurredAt; }
    /** @return array<int, array{productId: string, quantity: int}> */
    public function getItems(): array { return $this->items; }
    public function toPayload(): array
    {
        return [
            'eventId' => $this->eventId,
            'orderId' => $this->orderId,
            'customerId' => $this->customerId,
            'totalAmount' => $this->totalAmount,
            'items' => $this->items,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
    public static function fromPayload(array $payload): self
    {
        $event = new self(
            $payload['orderId'],
            $payload['customerId'],
            $payload['totalAmount'],
            $payload['items']
        );
        $event->eventId = $payload['eventId'];
        $event->occurredAt = new DateTimeImmutable($payload['occurredAt']);
        return $event;
    }

}
