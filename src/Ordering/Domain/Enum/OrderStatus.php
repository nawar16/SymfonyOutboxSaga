<?php

namespace App\Ordering\Domain\Enum;

enum OrderStatus: string
{
    case Pending = 'PENDING';
    case Shipped = 'SHIPPED';
    case Paid = 'PAID';
    case Cancelled = 'CANCELLED';
}