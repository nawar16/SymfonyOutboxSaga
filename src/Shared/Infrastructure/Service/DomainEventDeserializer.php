<?php

namespace App\Shared\Infrastructure\Service;

use App\Ordering\Domain\Event\OrderPlaced;

final class DomainEventDeserializer
{
    public function deserialize(
        string $eventType,
        array $payload
    ): object {
        return match ($eventType) {
            OrderPlaced::class =>
                new OrderPlaced(
                    $payload['orderId'],
                    $payload['customerId'],
                    $payload['totalAmount'],
                    $payload['items']
                ),
            default =>
                throw new \RuntimeException(
                    "Unknown event type: ".$eventType
                ),
        };
    }
}