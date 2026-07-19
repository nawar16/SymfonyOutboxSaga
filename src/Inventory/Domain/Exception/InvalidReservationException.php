<?php

namespace App\Inventory\Domain\Exception;

final class InvalidReservationException extends \DomainException
{
    public function __construct(string $message = 'Invalid inventory reservation') 
    {
        parent::__construct($message);
    }
}