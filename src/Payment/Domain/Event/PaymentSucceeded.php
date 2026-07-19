<?php

namespace App\Payment\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

final class PaymentSucceeded implements DomainEvent
{
    private string $eventId;
    public function __construct(private string $orderId,private int $amount,private ?DateTimeImmutable $occurredAt = null) 
    {
        $this->eventId = Uuid::v4()->toRfc4122();
        $this->occurredAt ??= new DateTimeImmutable();
    }
    public function getEventId(): string{return $this->eventId;}
    public function getOccurredAt(): DateTimeImmutable{return $this->occurredAt;}
    public function getOrderId(): string{return $this->orderId;}
    public function getAmount(): int{return $this->amount;}
    public function toPayload(): array{
        return [
            'eventId' => $this->eventId,
            'orderId' => $this->orderId,
            'amount' => $this->amount,
            'occurredAt' => $this->occurredAt->format(DATE_ATOM),
        ];
    }
    public static function fromPayload(array $payload): self
    {
        $event = new self(
            $payload['orderId'],
            $payload['amount'],
            new DateTimeImmutable($payload['occurredAt'])
        );
        $event->eventId = $payload['eventId'];
        return $event;
    }
}