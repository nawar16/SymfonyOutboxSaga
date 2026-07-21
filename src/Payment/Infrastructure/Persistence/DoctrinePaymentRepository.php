<?php

namespace App\Payment\Infrastructure\Persistence;

use App\Payment\Domain\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePaymentRepository
{
    public function __construct(private EntityManagerInterface $entityManager) {}
    public function find(string $orderId): ?Payment
    {
        return $this->entityManager->find(Payment::class,$orderId);
    }

    public function save(Payment $payment): void
    {
        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }
}