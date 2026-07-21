<?php

namespace App\Payment\Domain\Exception;

final class PaymentProcessingException extends \DomainException
{
    public function __construct(string $message = 'Payment processing failed')
    {
        parent::__construct($message);
    }
}