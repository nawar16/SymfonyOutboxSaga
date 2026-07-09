<?php

namespace App\Shared\Domain\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function getEventId(): string;
    public function getOccurredAt(): DateTimeImmutable;
    public function toPayload(): array;
}