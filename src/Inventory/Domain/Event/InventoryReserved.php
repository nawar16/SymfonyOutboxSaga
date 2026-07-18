<?php

namespace App\Inventory\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class InventoryReserved implements DomainEvent
{
    private string $eventId;
    public function __construct(private string $orderId, private string $productId,
        private int $quantity,
        private ?DateTimeImmutable $occurredAt = null) 
    {
        $this->eventId = Uuid::v4()->toRfc4122();
        $this->occurredAt ??= new DateTimeImmutable();
    }

    public function getEventId(): string {return $this->eventId;}
    public function getOccurredAt(): DateTimeImmutable{return $this->occurredAt;}
    public function getOrderId(): string{return $this->orderId;}
    public function getProductId(): string{return $this->productId;}
    public function getQuantity(): int{return $this->quantity;}
    public function toPayload(): array
    {
        return [
            'eventId' => $this->eventId,
            'orderId' => $this->orderId,
            'productId' => $this->productId,
            'quantity' => $this->quantity,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
    public static function fromPayload(array $payload): self
    {
        $event = new self(
            $payload['orderId'],
            $payload['productId'],
            $payload['quantity'],
            new DateTimeImmutable($payload['occurredAt'])
        );
        $event->eventId = $payload['eventId'];
        return $event;
    }
}