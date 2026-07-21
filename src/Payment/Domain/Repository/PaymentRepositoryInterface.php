<?php
namespace App\Payment\Domain\Repository;

use App\Payment\Domain\Entity\Payment;

interface PaymentRepositoryInterface
{
    public function save(Payment $payment): void;
    public function find(string $id): ?Payment;
}