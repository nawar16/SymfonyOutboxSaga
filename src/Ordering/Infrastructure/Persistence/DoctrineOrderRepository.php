<?php

namespace App\Ordering\Infrastructure\Persistence;

use App\Ordering\Domain\Entity\Order;
use App\Ordering\Domain\Repository\OrderRepositoryInterface;
use App\Shared\Infrastructure\Persistence\OutboxMessage;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Order $order): void
    {
        $this->entityManager->wrapInTransaction(function () use ($order) {
            $this->entityManager->persist($order);
            foreach ($order->pullDomainEvents() as $event) 
                $this->entityManager->persist(OutboxMessage::fromDomainEvent($event));
            $this->entityManager->flush();
        });
    }
    public function find(string $id): ?Order
    {
        return $this->entityManager->find(Order::class, $id);
    }
}