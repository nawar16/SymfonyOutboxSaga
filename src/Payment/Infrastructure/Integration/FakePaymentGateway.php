<?php

namespace App\Payment\Infrastructure\Integration;

use App\Payment\Domain\Service\PaymentGatewayInterface;

final class FakePaymentGateway implements PaymentGatewayInterface
{
    public function charge(string $orderId,int $amount): bool {
        return random_int(1, 100) <= 70;
    }
}