<?php

namespace App\Inventory\Domain\Enum;

enum ReservationStatus: string
{
    case Reserved = 'RESERVED';
    case Confirmed = 'CONFIRMED';
    case Released = 'RELEASED';
}