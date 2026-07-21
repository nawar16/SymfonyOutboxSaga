<?php

namespace App\Payment\Domain\Service;

interface PaymentGatewayInterface
{
    public function charge(string $orderId,int $amount): bool;
}