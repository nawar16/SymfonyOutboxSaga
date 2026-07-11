<?php

namespace App\Shared\Infrastructure\Persistence;

use Doctrine\ORM\EntityManagerInterface;

final class DoctrineOutboxRepository
{
    public function __construct(private EntityManagerInterface $entityManager) 
    {}
    public function findPending(int $limit =100): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->select('o')
            ->from(OutboxMessage::class, 'o')
            ->where('o.sentAt IS NULL')
            ->orderBy('o.createdAt', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function save(OutboxMessage $message): void
    {
        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }
}