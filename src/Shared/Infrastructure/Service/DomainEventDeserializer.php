<?php

namespace App\Shared\Infrastructure\Service;

use App\Ordering\Domain\Event\OrderPlaced;
use App\Shared\Domain\Event\DomainEvent;

final class DomainEventDeserializer
{
    public function deserialize(
        string $eventType,
        array $payload
    ): DomainEvent {
        return match ($eventType) {
            OrderPlaced::class =>
            OrderPlaced::fromPayload($payload),
                // new OrderPlaced(
                //     $payload['orderId'],
                //     $payload['customerId'],
                //     $payload['totalAmount'],
                //     $payload['items']
                // ),
            default =>
                throw new \RuntimeException(
                    "Unknown event type: ".$eventType
                ),
        };
    }
}