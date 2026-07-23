<?php

namespace App\Inventory\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class InventoryReserved implements DomainEvent
{
    private string $eventId;
    /**
     * @param array<int, array{
     *     productId:string,
     *     quantity:int
     * }> $reservations
     */
    public function __construct(private string $orderId,private array $reservations,
        private ?DateTimeImmutable $occurredAt = null) 
    {
        $this->eventId = Uuid::v4()->toRfc4122();
        $this->occurredAt ??= new DateTimeImmutable();
    }
    public function getEventId(): string{return $this->eventId;}
    public function getOrderId(): string{return $this->orderId;}
    /**
     * @return array<int, array{
     *     productId:string,
     *     quantity:int
     * }>
     */
    public function getReservations(): array{return $this->reservations;}
    public function getOccurredAt(): DateTimeImmutable{return $this->occurredAt;}
    public function toPayload(): array
    {
        return [
            'eventId' => $this->eventId,
            'orderId' => $this->orderId,
            'reservations' => $this->reservations,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
    public static function fromPayload(array $payload): self
    {
        $event = new self($payload['orderId'],$payload['reservations'],
            new DateTimeImmutable($payload['occurredAt'])
        );
        $event->eventId = $payload['eventId'];
        return $event;
    }
}