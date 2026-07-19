<?php

namespace App\Payment\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class PaymentFailed implements DomainEvent
{
    private string $eventId;
    public function __construct(private string $orderId,private string $reason,private ?DateTimeImmutable $occurredAt = null) 
    {
        $this->eventId = Uuid::v4()->toRfc4122();
        $this->occurredAt ??= new DateTimeImmutable();
    }
    public function getEventId(): string{return $this->eventId;}
    public function getOccurredAt(): DateTimeImmutable{return $this->occurredAt;}
    public function getOrderId(): string{return $this->orderId;}
    public function getReason(): string{return $this->reason;}
    public function toPayload(): array
    {
        return [
            'eventId' => $this->eventId,
            'orderId' => $this->orderId,
            'reason' => $this->reason,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
    public static function fromPayload(array $payload): self
    {
        $event = new self($payload['orderId'],$payload['reason'],
            new DateTimeImmutable($payload['occurredAt'])
        );
        $event->eventId = $payload['eventId'];
        return $event;
    }
}