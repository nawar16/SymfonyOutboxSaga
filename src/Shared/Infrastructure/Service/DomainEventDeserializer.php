<?php

namespace App\Shared\Infrastructure\Service;

use App\Inventory\Domain\Event\InventoryReserved;
use App\Ordering\Domain\Event\OrderPlaced;
use App\Payment\Domain\Event\PaymentFailed;
use App\Payment\Domain\Event\PaymentSucceeded;
use App\Shared\Domain\Event\DomainEvent;

class DomainEventDeserializer
{
    public function deserialize(
        string $eventType,
        array $payload
    ): DomainEvent {
        return match ($eventType) {
            OrderPlaced::class => OrderPlaced::fromPayload($payload),
            InventoryReserved::class => InventoryReserved::fromPayload($payload),
            PaymentSucceeded::class => PaymentSucceeded::fromPayload($payload),
            PaymentFailed::class => PaymentFailed::fromPayload($payload),
            default =>
                throw new \RuntimeException(
                    "Unknown event type: ".$eventType
                ),
        };
    }
}