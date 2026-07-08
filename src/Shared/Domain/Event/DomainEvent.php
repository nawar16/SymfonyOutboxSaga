<?php

namespace App\Shared\Domain\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function getOccurredAt(): DateTimeImmutable;
}