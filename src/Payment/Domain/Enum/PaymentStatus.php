<?php

namespace App\Payment\Domain\Enum;

enum PaymentStatus: string
{
    case Pending = 'PENDING';
    case Succeeded = 'SUCCEEDED';
    case Failed = 'FAILED';
}